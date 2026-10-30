<?php

namespace App\Services;

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

    public function __construct()
    {
        $this->db = Database::connect();
        $this->transactionModel = new TransactionModel();
        $this->bookModel = new BookModel();
        $this->userModel = new UserModel();
    }

    /*
    |--------------------------------------------------------------------------
    | BORROW
    |--------------------------------------------------------------------------
    */
    public function borrow(int $memberId, array $bookItemIds, int $staffId, string $jenisPeminjaman = 'pendek', string $customDueDate = null)
    {
        $this->db->transStart();

        $user = $this->userModel->find($memberId);

        if (!$user || !$user['is_validated']) {
            throw new \Exception('Member tidak valid');
        }

        // 🔴 blok jika ada terlambat
        $late = $this->transactionModel
            ->where('user_id', $memberId)
            ->where('status', 'terlambat')
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

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            throw new \Exception('Gagal transaksi');
        }

        return $kode;
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN
    |--------------------------------------------------------------------------
    */
    public function return(int $transactionId, array $lostBookItemIds, int $staffId)
    {
        $logger = new LoggingService();

        $this->db->transStart();

        $trx = $this->transactionModel->find($transactionId);

        if (!$trx || $trx['status'] === 'selesai') {
            throw new \Exception('Transaksi tidak valid');
        }

        $user = $this->userModel->find($trx['user_id']);
        $memberId = $trx['user_id'];

        $today = date('Y-m-d');
        $denda = self::calculateFine($user['jenis_pengguna'], $trx['jatuh_tempo'], $today);

        $details = $this->db->table('transaction_details')
            ->where('transaction_id', $transactionId)
            ->get()
            ->getResultArray();

        foreach ($details as $detail) {

            $book = $this->bookModel->find($detail['book_id']);
            $itemId = $detail['book_item_id'];

            if (in_array($itemId, $lostBookItemIds)) {

                // Tandai hilang
                $this->db->table('transaction_details')
                    ->where('id', $detail['id'])
                    ->update([
                        'is_lost' => 1,
                        'lost_settlement_status' => 'pending'
                    ]);

                // Update status salinan fisik ke lost
                if ($itemId) {
                    $this->db->table('book_items')
                        ->where('id', $itemId)
                        ->update(['status' => 'lost']);
                }

                // 🔥 LOG LOST
                $logger->log(
                    "Buku '{$book['judul']}' (Copy ID {$itemId}) dinyatakan hilang (Transaksi {$transactionId})",
                    $memberId,
                    $staffId
                );

            } else {

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

                // 🔥 LOG RETURN
                $logger->log(
                    "Buku '{$book['judul']}' (Copy ID {$itemId}) dikembalikan (Transaksi {$transactionId})",
                    $memberId,
                    $staffId
                );
            }
        }

        // Update transaksi
        $this->transactionModel->update($transactionId, [
            'tanggal_kembali' => $today,
            'status'          => 'selesai',
            'total_denda'     => $denda,
            'returned_by'     => $staffId
        ]);

        // 🔥 LOG RETURN
        $logger->log(
            "Transaksi {$transactionId} diselesaikan. Denda: Rp{$denda}",
            $memberId,
            $staffId
        );

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            throw new \Exception('Gagal pengembalian');
        }

        return $denda;
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

    public static function calculateFine(string $jenisPengguna, string $jatuhTempo, string $tanggalKembali): int
    {
        if ($tanggalKembali <= $jatuhTempo) {
            return 0;
        }

        $selisih = (new \DateTimeImmutable($jatuhTempo))->diff(new \DateTimeImmutable($tanggalKembali))->days;

        return $selisih * self::fineRateFor($jenisPengguna);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO LATE
    |--------------------------------------------------------------------------
    */
    public function autoLate()
    {
        return $this->transactionModel
            ->where('status', 'dipinjam')
            ->where('jatuh_tempo <', date('Y-m-d'))
            ->set(['status' => 'terlambat'])
            ->update();
    }
}