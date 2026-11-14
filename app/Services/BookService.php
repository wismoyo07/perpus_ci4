<?php

namespace App\Services;

use App\Models\BookModel;
use App\Services\LoggingService;
use Config\Database;

class BookService
{
    /**
     * Kode buku: {kode barang} / {sumber dana} / {tahun} / {nomor urut}
     */
    public const KODE_BUKU_PATTERN = '/^[0-9][0-9.]*\s*\/\s*[A-Z0-9-]+\s*\/\s*\d{4}\s*\/\s*\d{3,}$/i';

    public const DEFAULT_KODE_BARANG = '03.11.01.01.001';

    /**
     * Sumber dana salinan pengganti buku hilang: dibeli dengan uang pribadi peminjam.
     */
    public const SUMBER_PENGGANTI = 'PRIBADI';

    protected $bookModel;
    protected $db;

    public function __construct()
    {
        $this->bookModel = new BookModel();
        $this->db        = Database::connect();
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(array $data, int $actorId)
    {
        $data = $this->normalizeDdc($data);

        // AUTO GENERATE JIKA KOSONG
        if (empty($data['kode_buku'])) {
            $sumberDana = $this->validSumberDana($data['sumber_dana'] ?? 'BOS');
            $tahun      = $this->validTahun($data['tahun_pengadaan'] ?? date('Y'));
            $prefix     = "{$this->kodeBarang()} / {$sumberDana} / {$tahun}";

            $data['kode_buku'] = self::formatKode($this->kodeBarang(), $sumberDana, $tahun, $this->allocateSequence($prefix));
        } else {
            // Validasi format jika diinput manual
            if (!preg_match(self::KODE_BUKU_PATTERN, $data['kode_buku'])) {
                throw new \Exception('Format Kode Buku tidak valid. Contoh format: 03.11.01.01.001 / BOS / 2024 / 005');
            }
        }

        $this->validateStock($data['stok_total'], $data['stok_tersedia']);

        $id = $this->bookModel->insert($data);

        // Auto-create physical copy records (book_items)
        $parts = explode('/', $data['kode_buku']);
        $sumber = (count($parts) >= 2) ? trim($parts[1]) : 'BOS';
        $tahun = (count($parts) >= 3) ? intval(trim($parts[2])) : intval(date('Y'));

        for ($copy = 1; $copy <= $data['stok_total']; $copy++) {
            $urutPadded = str_pad($copy, 3, '0', STR_PAD_LEFT);
            $kodeInventaris = "{$data['kode_buku']} / {$urutPadded}";

            $this->db->table('book_items')->insert([
                'book_id'         => $id,
                'kode_inventaris' => $kodeInventaris,
                'sumber_dana'     => $sumber,
                'tahun_pengadaan' => $tahun,
                'nomor_urut'      => $copy,
                'status'          => 'available'
            ]);
        }

        (new LoggingService())->log("Menambahkan buku: {$data['judul']}", null, $actorId);

        return $id;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(int $id, array $data, int $actorId)
    {
        $book = $this->bookModel->find($id);
        if (!$book) {
            throw new \Exception('Buku tidak ditemukan');
        }

        $data = $this->normalizeDdc($data);

        // Validasi format kode buku baru jika diubah
        if (!empty($data['kode_buku']) && $data['kode_buku'] !== $book['kode_buku']) {
            if (!preg_match(self::KODE_BUKU_PATTERN, $data['kode_buku'])) {
                throw new \Exception('Format Kode Buku tidak valid. Contoh format: 03.11.01.01.001 / BOS / 2024 / 005');
            }
        }

        // Protect stock total & tersedia from manual changes during generic edit
        unset($data['stok_total']);
        unset($data['stok_tersedia']);

        $this->bookModel->update($id, $data);

        (new LoggingService())->log("Mengupdate buku: {$book['judul']}", null, $actorId);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE (Hard Delete)
    |--------------------------------------------------------------------------
    */
    public function delete(int $id, int $actorId)
    {
        $book = $this->bookModel->find($id);
        if (!$book) {
            throw new \Exception('Buku tidak ditemukan');
        }

        $used = $this->db->table('transaction_details')
            ->where('book_id', $id)
            ->countAllResults();

        if ($used > 0) {
            throw new \Exception('Buku memiliki riwayat transaksi. Gunakan archive.');
        }

        $this->bookModel->delete($id);

        (new LoggingService())->log("Menghapus buku: {$book['judul']}", null, $actorId);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHIVE dan RESTORE
    |--------------------------------------------------------------------------
    */
    public function archive(int $id, int $actorId)
    {
        $book = $this->bookModel->find($id);
        if (!$book) {
            throw new \Exception('Buku tidak ditemukan');
        }

        $this->bookModel->update($id, ['is_active' => 0]);

        (new LoggingService())->log("Mengarsipkan buku: {$book['judul']}", null, $actorId);

        return true;
    }

    public function restore(int $id, int $actorId)
    {
        $book = $this->bookModel->find($id);

        if (!$book) {
            throw new \Exception('Buku tidak ditemukan');
        }

        if ($book['is_active'] == 1) {
            throw new \Exception('Buku sudah aktif');
        }

        $this->bookModel->update($id, [
            'is_active' => 1
        ]);

        (new LoggingService())->log("Mengaktifkan kembali buku: {$book['judul']}", null, $actorId);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI STOK
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | PENOMORAN
    |--------------------------------------------------------------------------
    */

    /**
     * Kode inventaris untuk salinan baru sebuah buku.
     *
     * Sumber dana & tahun sama dengan kode buku  -> "{kode_buku} / {nomor}".
     * Berbeda -> buku mendapat nomor urut di prefix "{kode barang} / {sumber} / {tahun}"
     * (dipakai ulang jika buku sudah punya salinan di prefix itu).
     */
    public function itemKode(array $book, string $sumber, int $tahun, int $nomorUrut): string
    {
        $nomor = str_pad((string) $nomorUrut, 3, '0', STR_PAD_LEFT);
        $kode  = self::parseKode($book['kode_buku']);

        if ($kode && strcasecmp($kode['sumber'], $sumber) === 0 && $kode['tahun'] === $tahun) {
            return "{$book['kode_buku']} / {$nomor}";
        }

        $kodeBarang = $kode['kode_barang'] ?? $this->kodeBarang();
        $prefix     = "{$kodeBarang} / {$sumber} / {$tahun}";

        $existing = $this->db->table('book_items')
            ->select('kode_inventaris')
            ->where('book_id', $book['id'])
            ->like('kode_inventaris', "{$prefix} / ", 'after')
            ->get()
            ->getRowArray();

        $seq = $existing
            ? self::sequenceAfterPrefix($existing['kode_inventaris'], $prefix)
            : $this->allocateSequence($prefix);

        return self::formatKode($kodeBarang, $sumber, $tahun, $seq) . " / {$nomor}";
    }

    public function kodeBarang(): string
    {
        $row = $this->db->table('app_settings')->select('kode_barang_buku')->where('id', 1)->get()->getRowArray();

        return trim($row['kode_barang_buku'] ?? '') ?: self::DEFAULT_KODE_BARANG;
    }

    /**
     * Nomor urut berikutnya untuk prefix, dari kode buku dan kode salinan.
     */
    private function allocateSequence(string $prefix): int
    {
        $kodeList = array_merge(
            array_column($this->db->table('books')->select('kode_buku AS kode')->like('kode_buku', "{$prefix} / ", 'after')->get()->getResultArray(), 'kode'),
            array_column($this->db->table('book_items')->select('kode_inventaris AS kode')->like('kode_inventaris', "{$prefix} / ", 'after')->get()->getResultArray(), 'kode')
        );

        return self::nextSequence($kodeList, $prefix);
    }

    public static function formatKode(string $kodeBarang, string $sumber, int|string $tahun, int|string $seq): string
    {
        return "{$kodeBarang} / {$sumber} / {$tahun} / " . str_pad((string) (int) $seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * @param list<string> $kodeList
     */
    public static function nextSequence(array $kodeList, string $prefix): int
    {
        $max = 0;
        foreach ($kodeList as $kode) {
            $max = max($max, self::sequenceAfterPrefix($kode, $prefix));
        }

        return $max + 1;
    }

    private static function sequenceAfterPrefix(string $kode, string $prefix): int
    {
        if (strpos($kode, "{$prefix} / ") !== 0) {
            return 0;
        }

        $rest = explode(' / ', substr($kode, strlen($prefix) + 3));

        return (int) $rest[0];
    }

    /**
     * @return array{kode_barang: string, sumber: string, tahun: int, seq: int}|null
     */
    public static function parseKode(string $kodeBuku): ?array
    {
        $parts = array_map('trim', explode('/', $kodeBuku));
        if (count($parts) < 4) {
            return null;
        }

        return ['kode_barang' => $parts[0], 'sumber' => $parts[1], 'tahun' => (int) $parts[2], 'seq' => (int) $parts[3]];
    }

    /**
     * Nomor panggil untuk baris buku dari BookModel::withDdc() (nomor DDC rinci, atau kode kelasnya).
     */
    public static function nomorPanggil(array $book): string
    {
        $ddc = ($book['ddc_number'] ?? '') ?: ($book['code_ddc'] ?? null);

        return self::callNumber($ddc, $book['pengarang'] ?? '', $book['judul'] ?? '');
    }

    /**
     * Nomor panggil: DDC + 3 huruf pengarang (kapital) + 1 huruf judul (kecil), misalnya "005.133 AND p".
     * Kosong jika buku belum diklasifikasi.
     */
    public static function callNumber(?string $ddc, ?string $pengarang, ?string $judul): string
    {
        $ddc = trim((string) $ddc);
        if ($ddc === '') {
            return '';
        }

        $hurufPengarang = preg_replace('/[^A-Za-z]/', '', (string) $pengarang) ?: preg_replace('/[^A-Za-z]/', '', (string) $judul);
        $hurufJudul     = preg_replace('/[^A-Za-z]/', '', (string) $judul);

        return trim($ddc . ' ' . strtoupper(substr($hurufPengarang, 0, 3)) . ' ' . strtolower(substr($hurufJudul, 0, 1)));
    }

    /**
     * Kode klasifikasi yang dicoba untuk sebuah Nomor DDC, dari yang paling spesifik:
     * seksi -> divisi -> kelas utama. Contoh "371.3" -> ['371', '370', '300'].
     *
     * @return list<string>
     */
    public static function ddcCandidates(string $ddc): array
    {
        $seksi = substr(trim($ddc), 0, 3);
        if (!preg_match('/^\d{3}$/', $seksi)) {
            return [];
        }

        return array_values(array_unique([$seksi, substr($seksi, 0, 2) . '0', $seksi[0] . '00']));
    }

    /**
     * Nomor DDC rinci menentukan klasifikasi aktif yang paling spesifik
     * (297.4 -> 297 Islam, 371.3 -> 370 Pendidikan, 005.1 -> 000).
     */
    private function normalizeDdc(array $data): array
    {
        if (!array_key_exists('category_id', $data) && !array_key_exists('ddc_number', $data)) {
            return $data;
        }

        $ddc = trim((string) ($data['ddc_number'] ?? ''));
        $data['ddc_number']  = $ddc === '' ? null : $ddc;
        $data['category_id'] = empty($data['category_id']) ? null : (int) $data['category_id'];

        if ($ddc !== '') {
            if (!preg_match('/^\d{3}(\.\d+)?$/', $ddc)) {
                throw new \Exception('Nomor DDC tidak valid. Contoh: 005 atau 005.133');
            }

            foreach (self::ddcCandidates($ddc) as $code) {
                $match = $this->db->table('category')->where('code_ddc', $code)->where('is_active', 1)->get()->getRowArray();
                if ($match) {
                    $data['category_id'] = (int) $match['id'];
                    break;
                }
            }
        }

        return $data;
    }

    public function validSumberDana(string $kode): string
    {
        $kode = strtoupper(trim($kode));
        $ada  = $this->db->table('source_fund')->where('kode', $kode)->where('is_active', 1)->countAllResults();

        if (!$ada) {
            throw new \Exception("Sumber dana '{$kode}' tidak terdaftar atau tidak aktif.");
        }

        return $kode;
    }

    public function validTahun($tahun): int
    {
        $tahun = (int) $tahun;
        if ($tahun < 1900 || $tahun > (int) date('Y') + 1) {
            throw new \Exception('Tahun pengadaan tidak valid.');
        }

        return $tahun;
    }

    private function validateStock($total, $tersedia)
    {
        if ($total < 0 || $tersedia < 0) {
            throw new \Exception('Stok tidak valid');
        }

        if ($tersedia > $total) {
            throw new \Exception('Stok tersedia tidak boleh lebih dari stok total');
        }
    }
}