<?php

namespace App\Services;

use App\Models\FineModel;
use App\Models\TransactionModel;
use App\Models\BookModel;
use App\Models\UserModel;
use Config\Database;
use App\Services\LoggingService;

class TransactionService
{
    protected $db;
    protected $transactionModel;
    protected $bookModel;
    protected $userModel;
    protected $fineModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->transactionModel = new TransactionModel();
        $this->bookModel = new BookModel();
        $this->userModel = new UserModel();
        $this->fineModel = new FineModel();
    }

    /*
    |--------------------------------------------------------------------------
    | BORROW
    |--------------------------------------------------------------------------
    */
    public function borrow(int $memberId, array $bookItemIds, int $staffId, string $jenisPeminjaman = 'pendek', ?string $customDueDate = null)
    {
        if (empty($bookItemIds)) {
            throw new \Exception('Pilih minimal 1 buku');
        }

        return $this->runInTransaction(
            fn () => $this->processBorrow($memberId, $bookItemIds, $staffId, $jenisPeminjaman, $customDueDate),
            'Gagal transaksi'
        );
    }

    protected function processBorrow(int $memberId, array $bookItemIds, int $staffId, string $jenisPeminjaman, ?string $customDueDate)
    {
        $user = $this->userModel->find($memberId);

        if (!$user || !$user['is_validated']) {
            throw new \Exception('Member tidak valid');
        }

        // 🔴 blok jika ada pinjaman terlambat (sejak H+1 jatuh tempo, tanpa menunggu cron)
        $late = $this->transactionModel
            ->where('user_id', $memberId)
            ->whereIn('status', ['dipinjam','terlambat'])
            ->where('jatuh_tempo <', date('Y-m-d'))
            ->countAllResults();

        if ($late > 0) {
            throw new \Exception('Member masih memiliki keterlambatan');
        }

        // 🔴 limit transaksi aktif max 3
        $activeTrx = $this->transactionModel
            ->where('user_id', $memberId)
            ->whereIn('status', ['dipinjam','terlambat'])
            ->countAllResults();

        if ($activeTrx >= 3) {
            throw new \Exception('Member memiliki 3 transaksi aktif');
        }

        // 🔴 blok jika masih ada denda keterlambatan yang belum dilunasi
        // (denda buku hilang yang masih DITAHAN tidak memblokir)
        $unpaidFine = $this->fineModel
            ->where('user_id', $memberId)
            ->where('status', 'belum_lunas')
            ->countAllResults();

        if ($unpaidFine > 0) {
            throw new \Exception('Member masih memiliki denda yang belum dilunasi');
        }

        // 🔴 limit buku per transaksi
        $limit = self::bookLimitFor($user['jenis_pengguna']);

        if (count($bookItemIds) > $limit) {
            throw new \Exception("Maksimal {$limit} buku per transaksi");
        }

        $today = date('Y-m-d');
        if (!empty($customDueDate)) {
            $due = $customDueDate;
        } else {
            if ($jenisPeminjaman === 'panjang') {
                $due = date('Y-m-d', strtotime('+180 days'));
            } else {
                $due = date('Y-m-d', strtotime('+10 days'));
            }
        }
        $kode  = 'TRX-' . date('YmdHis') . rand(10,99);

        $this->transactionModel->insert([
            'kode_transaksi'   => $kode,
            'user_id'          => $memberId,
            'tanggal_pinjam'   => $today,
            'jatuh_tempo'      => $due,
            'status'           => 'dipinjam',
            'borrowed_by'      => $staffId,
            'jenis_peminjaman' => $jenisPeminjaman
        ]);

        $trxId = $this->transactionModel->getInsertID();

        foreach ($bookItemIds as $itemId) {

            // LOCK item stok
            $item = $this->db->query(
                "SELECT * FROM book_items WHERE id = ? FOR UPDATE",
                [$itemId]
            )->getRowArray();

            if (!$item || $item['status'] !== 'available') {
                throw new \Exception("Salinan fisik ID {$itemId} tidak tersedia");
            }

            $bookId = $item['book_id'];

            // LOCK parent book stok
            $book = $this->db->query(
                "SELECT * FROM books WHERE id = ? FOR UPDATE",
                [$bookId]
            )->getRowArray();

            if (!$book || $book['stok_tersedia'] <= 0) {
                throw new \Exception("Stok buku '{$book['judul']}' tidak tersedia");
            }

            // cek tidak pinjam buku sama (berdasarkan judul/book_id)
            $duplicate = $this->db->table('transaction_details td')
                ->select('td.id')
                ->join('transactions t', 't.id = td.transaction_id')
                ->where('t.user_id', $memberId)
                ->whereIn('t.status', ['dipinjam','terlambat'])
                ->where('td.book_id', $bookId)
                ->where('td.is_lost', 0)
                ->where('td.returned_at', null)
                ->get()
                ->getNumRows();

            if ($duplicate > 0) {
                throw new \Exception("Buku '{$book['judul']}' masih dipinjam");
            }

            $this->db->table('transaction_details')->insert([
                'transaction_id' => $trxId,
                'book_id'        => $bookId,
                'book_item_id'   => $itemId
            ]);

            // Update status salinan fisik
            $this->db->table('book_items')
                ->where('id', $itemId)
                ->update(['status' => 'borrowed']);

            // Update stok tersedia di catalog
            $this->bookModel->update($bookId, [
                'stok_tersedia' => $book['stok_tersedia'] - 1
            ]);
        }

        return $kode;
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN
    |--------------------------------------------------------------------------
    */
    /**
     * Pengembalian per buku. Buku yang tidak dipilih tetap dipinjam (dendanya terus berjalan).
     * Transaksi selesai setelah semua buku dikembalikan atau dinyatakan hilang.
     *
     * @param array $returnItemIds book_item_id yang dikembalikan sekarang
     * @param array $lostItemIds   book_item_id yang dinyatakan hilang sekarang (mulai H+1)
     */
    public function return(int $transactionId, array $returnItemIds, array $lostItemIds, int $staffId): array
    {
        return $this->runInTransaction(
            fn () => $this->processReturn($transactionId, $returnItemIds, $lostItemIds, $staffId),
            'Gagal pengembalian'
        );
    }

    protected function processReturn(int $transactionId, array $returnItemIds, array $lostItemIds, int $staffId): array
    {
        $logger = new LoggingService();

        $trx = $this->transactionModel->find($transactionId);

        if (!$trx || $trx['status'] === 'selesai') {
            throw new \Exception('Transaksi tidak valid');
        }

        $user     = $this->userModel->find($trx['user_id']);
        $memberId = $trx['user_id'];
        $today    = date('Y-m-d');

        $details = $this->db->table('transaction_details')
            ->where('transaction_id', $transactionId)
            ->get()
            ->getResultArray();

        // Buku yang masih di tangan peminjam: belum dikembalikan dan tidak dilaporkan hilang
        $outstanding = array_filter($details, static fn ($d) => !$d['is_lost'] && empty($d['returned_at']));

        $returnItemIds = array_map('intval', $returnItemIds);
        $lostItemIds   = array_map('intval', $lostItemIds);

        if (array_intersect($returnItemIds, $lostItemIds)) {
            throw new \Exception('Satu buku tidak bisa sekaligus dikembalikan dan dinyatakan hilang');
        }
        if ($outstanding && !array_intersect(array_map('intval', array_column($outstanding, 'book_item_id')), array_merge($returnItemIds, $lostItemIds))) {
            throw new \Exception('Pilih minimal satu buku yang dikembalikan atau dinyatakan hilang');
        }

        $dikembalikan = 0;
        $hilang       = 0;

        foreach ($outstanding as $detail) {

            $book   = $this->bookModel->find($detail['book_id']);
            $itemId = (int) $detail['book_item_id'];

            if (in_array($itemId, $lostItemIds, true)) {

                // Buku baru bisa dinyatakan hilang mulai H+1 jatuh tempo
                if ($today <= $trx['jatuh_tempo']) {
                    throw new \Exception("Buku '{$book['judul']}' belum bisa dinyatakan hilang sebelum " . self::lostReportFrom($trx['jatuh_tempo'], 'd/m/Y') . ' (H+1 jatuh tempo). Gunakan Laporkan Hilang dengan opsi laporan dini bila memang harus dicatat sekarang.');
                }

                // Dinyatakan hilang saat pengembalian: tanggal laporan = hari ini
                $this->markLost($trx, $detail, $user, $today);
                $hilang++;

                // 🔥 LOG LOST
                $logger->log(
                    "Buku '{$book['judul']}' (Copy ID {$itemId}) dinyatakan hilang (Transaksi {$transactionId})",
                    $memberId,
                    $staffId
                );

            } elseif (in_array($itemId, $returnItemIds, true)) {

                $this->db->table('transaction_details')->where('id', $detail['id'])->update([
                    'returned_at' => $today,
                    'returned_by' => $staffId,
                ]);

                // Kembalikan status salinan fisik ke available
                if ($itemId) {
                    $this->db->table('book_items')
                        ->where('id', $itemId)
                        ->update(['status' => 'available']);
                }

                // Kembalikan stok catalog
                $this->bookModel->update($detail['book_id'], [
                    'stok_tersedia' => $book['stok_tersedia'] + 1
                ]);

                // Denda keterlambatan buku ini sampai hari ini (dipotong batas maksimal)
                $this->createFine($trx, $detail, $user, $today, 'belum_lunas');
                $dikembalikan++;

                // 🔥 LOG RETURN
                $logger->log(
                    "Buku '{$book['judul']}' (Copy ID {$itemId}) dikembalikan (Transaksi {$transactionId})",
                    $memberId,
                    $staffId
                );
            }
            // selain itu: belum dibawa, tetap dipinjam dan dendanya terus berjalan
        }

        $sisa    = count($outstanding) - $dikembalikan - $hilang;
        $selesai = $sisa === 0;

        if ($selesai) {
            $this->transactionModel->update($transactionId, [
                'tanggal_kembali' => $today,
                'status'          => 'selesai',
                'returned_by'     => $staffId
            ]);
        }

        $total = $this->recalcTotalDenda($transactionId);

        // 🔥 LOG RETURN
        $logger->log(
            $selesai
                ? "Transaksi {$transactionId} diselesaikan. Denda ditagih: Rp{$total['ditagih']}, ditahan (buku hilang): Rp{$total['ditahan']}"
                : "Transaksi {$transactionId}: {$dikembalikan} buku dikembalikan, {$hilang} hilang, {$sisa} buku masih dipinjam",
            $memberId,
            $staffId
        );

        return $total + ['dikembalikan' => $dikembalikan, 'hilang' => $hilang, 'sisa' => $sisa, 'selesai' => $selesai];
    }

    /*
    |--------------------------------------------------------------------------
    | LAPOR BUKU HILANG (di tengah masa pinjam)
    |--------------------------------------------------------------------------
    | Buku baru bisa dinyatakan hilang mulai H+1 jatuh tempo. Denda buku berhenti
    | bertambah di tanggal laporan dan DITAHAN sampai ada penyelesaian di menu
    | Buku Hilang (ditemukan / diganti / dibayar / pemutihan).
    |
    | Laporan dini (sebelum H+1, alasan wajib): denda langsung sebesar batas maksimal,
    | langsung ditagih, dan tetap berlaku walaupun buku nanti ditemukan atau diganti.
    */
    /**
     * @return bool true jika dicatat sebagai laporan dini (denda maksimal)
     */
    public function reportLost(int $detailId, string $tanggalLapor, int $staffId, bool $dini = false, ?string $alasan = null): bool
    {
        return $this->runInTransaction(function () use ($detailId, $tanggalLapor, $staffId, $dini, $alasan) {
            [$trx, $detail] = $this->activeDetail($detailId);

            if ($detail['is_lost']) {
                throw new \Exception('Buku ini sudah dilaporkan hilang');
            }

            // Laporan dini hanya berlaku untuk tanggal laporan sebelum H+1
            $dini = $dini && $tanggalLapor <= $trx['jatuh_tempo'];

            if ($error = self::lostReportDateError($tanggalLapor, $trx['tanggal_pinjam'], $trx['jatuh_tempo'], date('Y-m-d'), $dini)) {
                throw new \Exception($error);
            }
            if ($dini) {
                if (trim((string) $alasan) === '') {
                    throw new \Exception('Alasan laporan dini wajib diisi');
                }
                if ($this->maxFineDays() === 0) {
                    throw new \Exception('Laporan dini membutuhkan batas maksimal hari denda. Atur dulu di menu Pengaturan (saat ini tanpa batas).');
                }
            }

            $user = $this->userModel->find($trx['user_id']);
            $this->markLost($trx, $detail, $user, $tanggalLapor, $dini ? trim((string) $alasan) : null);
            $this->recalcTotalDenda((int) $trx['id']);

            $book = $this->bookModel->find($detail['book_id']);
            (new LoggingService())->log(
                "Buku '{$book['judul']}' (Copy ID {$detail['book_item_id']}) dilaporkan hilang per {$tanggalLapor}" . ($dini ? ' (LAPORAN DINI, denda maksimal)' : '') . " (Transaksi {$trx['kode_transaksi']})",
                $trx['user_id'],
                $staffId
            );

            return $dini;
        }, 'Gagal melaporkan buku hilang');
    }

    /*
    |--------------------------------------------------------------------------
    | DENDA PER BUKU (helper)
    |--------------------------------------------------------------------------
    */

    /**
     * Batas maksimal hari denda dari Pengaturan (0 = tanpa batas).
     */
    public function maxFineDays(): int
    {
        $row = $this->db->table('app_settings')->select('maks_hari_denda')->where('id', 1)->get()->getRowArray();

        return max(0, (int) ($row['maks_hari_denda'] ?? 10));
    }

    /**
     * Tandai salinan hilang per tanggal laporan dan bekukan dendanya (status ditahan).
     * Laporan dini ($alasanDini terisi): denda batas maksimal, langsung ditagih.
     */
    private function markLost(array $trx, array $detail, array $user, string $tanggalLapor, ?string $alasanDini = null): void
    {
        $this->db->table('transaction_details')->where('id', $detail['id'])->update([
            'is_lost'                => 1,
            'lost_reported_at'       => $tanggalLapor,
            'lost_settlement_status' => 'pending',
        ]);

        if ($detail['book_item_id']) {
            $this->db->table('book_items')->where('id', $detail['book_item_id'])->update(['status' => 'lost']);
        }

        if ($alasanDini !== null) {
            $this->createEarlyLostFine($trx, $detail, $user, $alasanDini);

            return;
        }

        $this->createFine($trx, $detail, $user, $tanggalLapor, 'ditahan');
    }

    /**
     * Denda laporan dini: batas maksimal hari x tarif, status belum_lunas.
     * Tidak ditahan, sehingga tidak berubah oleh penyelesaian buku hilang.
     */
    private function createEarlyLostFine(array $trx, array $detail, array $user, string $alasan): void
    {
        $maks   = $this->maxFineDays();
        $tarif  = self::fineRateFor($user['jenis_pengguna']);
        $jumlah = $maks * $tarif;

        $this->db->table('fines')->insert([
            'transaction_id'        => $trx['id'],
            'transaction_detail_id' => $detail['id'],
            'user_id'               => $trx['user_id'],
            'hari_terlambat'        => 0,
            'hari_dikenakan'        => $maks,
            'tarif'                 => $tarif,
            'jumlah_awal'           => $jumlah,
            'jumlah'                => $jumlah,
            'status'                => 'belum_lunas',
            'catatan'               => mb_substr("Laporan dini (sebelum jatuh tempo): denda maksimal {$maks} hari. Alasan: {$alasan}", 0, 255),
            'created_at'            => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Buat baris denda satu buku jika ada hari terlambat sampai $tanggalAkhir.
     */
    private function createFine(array $trx, array $detail, array $user, string $tanggalAkhir, string $status): void
    {
        $hariTerlambat = self::fineDays($trx['jatuh_tempo'], $tanggalAkhir, 0);
        if ($hariTerlambat === 0) {
            return;
        }

        $hariDikenakan = self::fineDays($trx['jatuh_tempo'], $tanggalAkhir, $this->maxFineDays());
        $tarif         = self::fineRateFor($user['jenis_pengguna']);
        $jumlah        = $hariDikenakan * $tarif;

        $this->db->table('fines')->insert([
            'transaction_id'        => $trx['id'],
            'transaction_detail_id' => $detail['id'],
            'user_id'               => $trx['user_id'],
            'hari_terlambat'        => $hariTerlambat,
            'hari_dikenakan'        => $hariDikenakan,
            'tarif'                 => $tarif,
            'jumlah_awal'           => $jumlah,
            'jumlah'                => $jumlah,
            'status'                => $status,
            'created_at'            => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Hitung ulang transactions.total_denda (semua denda kecuali yang dibebaskan).
     *
     * @return array{ditagih: int, ditahan: int}
     */
    public function recalcTotalDenda(int $transactionId): array
    {
        $rows = $this->db->table('fines')
            ->select('status, SUM(jumlah) AS total')
            ->where('transaction_id', $transactionId)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $sum     = array_column($rows, 'total', 'status');
        $ditagih = (int) (($sum['belum_lunas'] ?? 0) + ($sum['lunas'] ?? 0));
        $ditahan = (int) ($sum['ditahan'] ?? 0);

        $this->transactionModel->update($transactionId, ['total_denda' => $ditagih + $ditahan]);

        return ['ditagih' => $ditagih, 'ditahan' => $ditahan];
    }

    /**
     * Terapkan penyelesaian buku hilang ke denda yang ditahan untuk buku itu.
     *
     * @param array{keputusan?: string|null, nominal?: int|string|null, catatan?: string|null} $pemutihan
     *
     * @return array{status: string, jumlah: int}|null null jika buku itu tidak punya denda ditahan
     */
    public function applyLostSettlementToFine(int $detailId, string $settlementType, array $pemutihan, int $staffId): ?array
    {
        $fine = $this->db->table('fines')->where('transaction_detail_id', $detailId)->get()->getRowArray();
        if (!$fine || $fine['status'] !== 'ditahan') {
            return null;
        }

        $result = self::lostFineDecision(
            $settlementType,
            (int) $fine['jumlah_awal'],
            $pemutihan['keputusan'] ?? null,
            $pemutihan['nominal'] ?? null,
            $pemutihan['catatan'] ?? null
        );
        if (isset($result['error'])) {
            throw new \Exception($result['error']);
        }

        $this->db->table('fines')->where('id', $fine['id'])->update([
            'status'      => $result['status'],
            'jumlah'      => $result['jumlah'],
            'catatan'     => $result['catatan'],
            'diproses_by' => $result['status'] === 'dibebaskan' ? $staffId : null,
            'diproses_at' => $result['status'] === 'dibebaskan' ? date('Y-m-d H:i:s') : null,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        $this->recalcTotalDenda((int) $fine['transaction_id']);

        return ['status' => $result['status'], 'jumlah' => $result['jumlah']];
    }

    /**
     * Denda ditahan milik satu buku (untuk form penyelesaian buku hilang), atau null.
     */
    public function heldFineForDetail(int $detailId): ?array
    {
        return $this->db->table('fines')
            ->where('transaction_detail_id', $detailId)
            ->where('status', 'ditahan')
            ->get()
            ->getRowArray();
    }

    /**
     * Detail transaksi yang masih aktif (dipinjam/terlambat).
     *
     * @return array{0: array, 1: array}
     */
    private function activeDetail(int $detailId): array
    {
        $detail = $this->db->table('transaction_details')->where('id', $detailId)->get()->getRowArray();
        $trx    = $detail ? $this->transactionModel->find($detail['transaction_id']) : null;

        if (!$detail || !$trx || $trx['status'] === 'selesai') {
            throw new \Exception('Buku tidak ditemukan pada transaksi yang masih aktif');
        }

        if (!empty($detail['returned_at'])) {
            throw new \Exception('Buku ini sudah dikembalikan');
        }

        return [$trx, $detail];
    }

    /*
    |--------------------------------------------------------------------------
    | DB TRANSACTION
    |--------------------------------------------------------------------------
    | Batalkan semua perubahan jika terjadi exception di tengah proses.
    */
    protected function runInTransaction(callable $callback, string $failMessage)
    {
        $this->db->transStart();

        try {
            $result = $callback();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            throw new \Exception($failMessage);
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | ATURAN PINJAM & DENDA
    |--------------------------------------------------------------------------
    */
    public static function bookLimitFor(string $jenisPengguna): int
    {
        return in_array($jenisPengguna, ['guru','pegawai']) ? 5 : 3;
    }

    public static function fineRateFor(string $jenisPengguna): int
    {
        return ($jenisPengguna === 'siswa') ? 1000 : 5000;
    }

    /**
     * Denda untuk satu buku: hari terlambat (dipotong batas maksimal) x tarif.
     * $maksHari = 0 berarti tanpa batas.
     */
    public static function calculateFine(string $jenisPengguna, string $jatuhTempo, string $tanggalKembali, int $maksHari = 0): int
    {
        return self::fineDays($jatuhTempo, $tanggalKembali, $maksHari) * self::fineRateFor($jenisPengguna);
    }

    /**
     * Jumlah hari terlambat yang dikenakan denda: dihitung sejak H+1 jatuh tempo
     * sampai $tanggalAkhir (tanggal kembali, atau tanggal laporan hilang),
     * dipotong $maksHari (0 = tanpa batas).
     */
    public static function fineDays(string $jatuhTempo, string $tanggalAkhir, int $maksHari = 0): int
    {
        if ($tanggalAkhir <= $jatuhTempo) {
            return 0;
        }

        $hari = (new \DateTimeImmutable($jatuhTempo))->diff(new \DateTimeImmutable($tanggalAkhir))->days;

        return $maksHari > 0 ? min($hari, $maksHari) : $hari;
    }

    /**
     * Pesan kesalahan tanggal laporan buku hilang, atau null jika sah.
     * Tanpa laporan dini, tanggal laporan minimal H+1 jatuh tempo.
     */
    public static function lostReportDateError(string $tanggalLapor, string $tanggalPinjam, string $jatuhTempo, string $hariIni, bool $dini = false): ?string
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLapor) || !strtotime($tanggalLapor)) {
            return 'Tanggal laporan tidak valid';
        }
        if ($tanggalLapor < $tanggalPinjam) {
            return 'Tanggal laporan tidak boleh sebelum tanggal pinjam';
        }
        if ($tanggalLapor > $hariIni) {
            return 'Tanggal laporan tidak boleh melewati hari ini';
        }
        if (!$dini && $tanggalLapor <= $jatuhTempo) {
            return 'Buku baru bisa dinyatakan hilang mulai ' . self::lostReportFrom($jatuhTempo, 'd/m/Y') . ' (H+1 jatuh tempo). Gunakan opsi laporan dini bila memang harus dicatat sebelum itu.';
        }

        return null;
    }

    /**
     * Tanggal paling awal buku boleh dinyatakan hilang (H+1 jatuh tempo).
     */
    public static function lostReportFrom(string $jatuhTempo, string $format = 'Y-m-d'): string
    {
        return (new \DateTimeImmutable($jatuhTempo))->modify('+1 day')->format($format);
    }

    /*
    |--------------------------------------------------------------------------
    | PENYELESAIAN BUKU HILANG -> DENDA KETERLAMBATAN BUKU ITU
    |--------------------------------------------------------------------------
    | found        : buku ditemukan, denda sampai tanggal laporan tetap dibayar
    | replaced/paid: diganti buku baru / uang, denda dihapus
    | written_off  : pemutihan, sesuai surat keputusan: dihapus / sebagian / penuh
    */
    public const PEMUTIHAN_DENDA = ['dihapus', 'sebagian', 'penuh'];

    /**
     * @return array{status: string, jumlah: int, catatan: string|null}|array{error: string}
     */
    public static function lostFineDecision(string $settlementType, int $jumlahAwal, ?string $keputusan = null, $nominal = null, ?string $surat = null): array
    {
        switch ($settlementType) {
            case 'found':
                return ['status' => 'belum_lunas', 'jumlah' => $jumlahAwal, 'catatan' => 'Buku ditemukan: denda sampai tanggal laporan tetap dibayar'];

            case 'replaced':
            case 'paid':
                return ['status' => 'dibebaskan', 'jumlah' => 0, 'catatan' => 'Dihapus otomatis: buku ' . ($settlementType === 'replaced' ? 'diganti buku baru' : 'diganti uang')];

            case 'written_off':
                $surat = trim((string) $surat);
                if ($surat === '') {
                    return ['error' => 'Nomor/isi surat keputusan pemutihan denda wajib diisi'];
                }
                if (!in_array($keputusan, self::PEMUTIHAN_DENDA, true)) {
                    return ['error' => 'Pilih keputusan denda pemutihan: dihapus, bayar sebagian, atau bayar penuh'];
                }
                if ($keputusan === 'dihapus') {
                    return ['status' => 'dibebaskan', 'jumlah' => 0, 'catatan' => "Pemutihan, denda dihapus. Surat: {$surat}"];
                }
                if ($keputusan === 'penuh') {
                    return ['status' => 'belum_lunas', 'jumlah' => $jumlahAwal, 'catatan' => "Pemutihan, denda dibayar penuh. Surat: {$surat}"];
                }
                $nominal = (int) $nominal;
                if ($nominal <= 0 || $nominal > $jumlahAwal) {
                    return ['error' => 'Nominal bayar sebagian harus lebih dari 0 dan tidak melebihi denda (Rp ' . number_format($jumlahAwal, 0, ',', '.') . ')'];
                }

                return ['status' => 'belum_lunas', 'jumlah' => $nominal, 'catatan' => "Pemutihan, denda dibayar sebagian. Surat: {$surat}"];
        }

        return ['error' => 'Jenis penyelesaian buku hilang tidak valid'];
    }

    /*
    |--------------------------------------------------------------------------
    | PEMBAYARAN DENDA KETERLAMBATAN (per buku)
    |--------------------------------------------------------------------------
    */
    public const FINE_SETTLEMENTS = ['lunas', 'dibebaskan'];

    /**
     * Tandai satu baris denda lunas atau dibebaskan.
     */
    public function settleFine(int $fineId, string $jenis, ?string $catatan, int $staffId): array
    {
        $fine = $this->fineModel->find($fineId);

        if ($error = self::fineSettlementError($fine, $jenis, $catatan)) {
            throw new \Exception($error);
        }

        $catatan = trim((string) $catatan);
        if ($catatan === '' && $jenis === 'lunas') {
            $catatan = $fine['catatan'];   // pertahankan catatan sebelumnya (misalnya nomor surat pemutihan)
        }

        $this->fineModel->update($fineId, [
            'status'      => $jenis,
            'catatan'     => $catatan === '' ? null : $catatan,
            'diproses_by' => $staffId,
            'diproses_at' => date('Y-m-d H:i:s'),
        ]);
        $this->recalcTotalDenda((int) $fine['transaction_id']);

        $trx     = $this->transactionModel->find($fine['transaction_id']);
        $nominal = number_format((float) $fine['jumlah'], 0, ',', '.');
        (new LoggingService())->log(
            "Denda transaksi {$trx['kode_transaksi']} (denda #{$fineId}) sebesar Rp{$nominal} " . ($jenis === 'lunas' ? 'dilunasi' : "dibebaskan. Alasan: {$catatan}"),
            $fine['user_id'],
            $staffId
        );

        return $this->fineModel->find($fineId) + ['kode_transaksi' => $trx['kode_transaksi']];
    }

    /**
     * Pesan kesalahan jika penyelesaian denda tidak sah, atau null jika sah.
     */
    public static function fineSettlementError(?array $fine, string $jenis, ?string $catatan): ?string
    {
        if (!$fine) {
            return 'Denda tidak ditemukan';
        }
        if (($fine['status'] ?? '') === 'ditahan') {
            return 'Denda ini masih ditahan: selesaikan dulu status buku hilangnya di menu Buku Hilang';
        }
        if (($fine['status'] ?? '') !== 'belum_lunas') {
            return 'Denda ini tidak dalam status belum lunas';
        }
        if (!in_array($jenis, self::FINE_SETTLEMENTS, true)) {
            return 'Jenis penyelesaian denda tidak valid';
        }
        if ($jenis === 'dibebaskan' && trim((string) $catatan) === '') {
            return 'Alasan pembebasan denda wajib diisi';
        }
        if (mb_strlen(trim((string) $catatan)) > 255) {
            return 'Catatan maksimal 255 karakter';
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | KWITANSI PEMBAYARAN DENDA
    |--------------------------------------------------------------------------
    */
    /**
     * Lunasi satu atau beberapa denda belum lunas milik anggota yang sama dalam satu kwitansi.
     *
     * @param int[] $fineIds
     *
     * @return array baris fine_payments yang baru dibuat
     */
    public function payFines(array $fineIds, int $staffId, ?string $catatan = null): array
    {
        $fineIds = array_values(array_unique(array_filter(array_map('intval', $fineIds))));
        $catatan = trim((string) $catatan);

        return $this->runInTransaction(function () use ($fineIds, $staffId, $catatan) {
            $fines = $fineIds === [] ? [] : $this->db->query(
                'SELECT * FROM fines WHERE id IN (' . implode(',', $fineIds) . ') FOR UPDATE'
            )->getResultArray();

            if ($error = self::finePaymentError($fines, $fineIds, $catatan)) {
                throw new \Exception($error);
            }

            $now    = date('Y-m-d H:i:s');
            $tahun  = (int) date('Y');
            $last   = $this->db->query('SELECT MAX(urutan) AS urutan FROM fine_payments WHERE tahun = ? FOR UPDATE', [$tahun])->getRowArray();
            $urutan = (int) ($last['urutan'] ?? 0) + 1;
            $total  = array_sum(array_map(static fn ($f) => (float) $f['jumlah'], $fines));

            $paymentModel = new \App\Models\FinePaymentModel();
            $paymentId    = $paymentModel->insert([
                'nomor_kwitansi' => self::receiptNumber($tahun, $urutan),
                'tahun'          => $tahun,
                'urutan'         => $urutan,
                'user_id'        => $fines[0]['user_id'],
                'total'          => $total,
                'dibayar_at'     => $now,
                'diterima_by'    => $staffId,
                'catatan'        => $catatan === '' ? null : $catatan,
            ]);

            $transactionIds = [];
            foreach ($fines as $fine) {
                $this->fineModel->update($fine['id'], [
                    'status'      => 'lunas',
                    'payment_id'  => $paymentId,
                    'diproses_by' => $staffId,
                    'diproses_at' => $now,
                ]);
                $transactionIds[$fine['transaction_id']] = true;
            }
            foreach (array_keys($transactionIds) as $transactionId) {
                $this->recalcTotalDenda((int) $transactionId);
            }

            $payment = $paymentModel->find($paymentId);
            (new LoggingService())->log(
                "Pembayaran denda kwitansi {$payment['nomor_kwitansi']} (" . count($fines) . ' denda) sebesar Rp'
                    . number_format($total, 0, ',', '.'),
                $payment['user_id'],
                $staffId
            );

            return $payment;
        }, 'Gagal mencatat pembayaran denda');
    }

    /** Nomor kwitansi berurutan per tahun, misalnya KW/2026/0001. */
    public static function receiptNumber(int $tahun, int $urutan): string
    {
        return sprintf('KW/%04d/%04d', $tahun, $urutan);
    }

    /**
     * Validasi pembayaran denda: semua denda ditemukan, berstatus belum lunas,
     * dan milik anggota yang sama.
     *
     * @param array $fines   baris fines yang ditemukan
     * @param int[] $fineIds id yang diminta
     */
    public static function finePaymentError(array $fines, array $fineIds, string $catatan = ''): ?string
    {
        if ($fineIds === []) {
            return 'Pilih minimal satu denda yang akan dibayar';
        }
        if (count($fines) !== count($fineIds)) {
            return 'Sebagian denda tidak ditemukan';
        }
        foreach ($fines as $fine) {
            if ($fine['status'] === 'ditahan') {
                return 'Ada denda yang masih ditahan: selesaikan dulu status buku hilangnya di menu Buku Hilang';
            }
            if ($fine['status'] !== 'belum_lunas') {
                return 'Ada denda yang tidak dalam status belum lunas';
            }
        }
        if (count(array_unique(array_column($fines, 'user_id'))) > 1) {
            return 'Satu kwitansi hanya untuk denda milik satu anggota';
        }
        if (mb_strlen($catatan) > 255) {
            return 'Catatan maksimal 255 karakter';
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO LATE
    |--------------------------------------------------------------------------
    */
    /**
     * Status terlambat otomatis sejak H+1 jatuh tempo. Dipanggil saat halaman
     * sirkulasi/dashboard dibuka, sehingga tidak bergantung pada cron.
     */
    public function autoLate()
    {
        return $this->db->table('transactions')
            ->where('status', 'dipinjam')
            ->where('jatuh_tempo <', date('Y-m-d'))
            ->update(['status' => 'terlambat']);
    }
}