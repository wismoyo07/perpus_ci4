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
    public function borrow(int $memberId, array $bookIds, int $staffId)
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
        $limit = in_array($user['jenis_pengguna'], ['guru','pegawai']) ? 5 : 3;

        if (count($bookIds) > $limit) {
            throw new \Exception("Maksimal {$limit} buku per transaksi");
        }

        $today = date('Y-m-d');
        $due   = date('Y-m-d', strtotime('+10 days'));
        $kode  = 'TRX-' . date('YmdHis') . rand(10,99);

        $this->transactionModel->insert([
            'kode_transaksi' => $kode,
            'user_id'        => $memberId,
            'tanggal_pinjam' => $today,
            'jatuh_tempo'    => $due,
            'status'         => 'dipinjam',
            'borrowed_by'    => $staffId
        ]);

        $trxId = $this->transactionModel->getInsertID();

        foreach ($bookIds as $bookId) {

            // LOCK stok
            $book = $this->db->query(
                "SELECT * FROM books WHERE id = ? FOR UPDATE",
                [$bookId]
            )->getRowArray();

            if (!$book || $book['stok_tersedia'] <= 0) {
                throw new \Exception("Stok buku ID {$bookId} tidak tersedia");
            }

            // cek tidak pinjam buku sama
            $duplicate = $this->db->table('transaction_details td')
                ->select('td.id')
                ->join('transactions t', 't.id = td.transaction_id')
                ->where('t.user_id', $memberId)
                ->whereIn('t.status', ['dipinjam','terlambat'])
                ->where('td.book_id', $bookId)
                ->get()
                ->getNumRows();

            if ($duplicate > 0) {
                throw new \Exception("Buku ID {$bookId} masih dipinjam");
            }

            $this->db->table('transaction_details')->insert([
                'transaction_id' => $trxId,
                'book_id'        => $bookId
            ]);

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
    public function return(int $transactionId, array $lostBooks, int $staffId)
    {
        $logger = new LoggingService();

        $this->db->transStart();

        $trx = $this->transactionModel->find($transactionId);

        if (!$trx || $trx['status'] === 'selesai') {
            throw new \Exception('Transaksi tidak valid');
        }

        $user = $this->userModel->find($trx['user_id']);
        $memberId = $trx['user_id'];

        $tarif = ($user['jenis_pengguna'] === 'siswa') ? 1000 : 5000;

        $today = date('Y-m-d');
        $denda = 0;

        if ($today > $trx['jatuh_tempo']) {
            $selisih = (strtotime($today) - strtotime($trx['jatuh_tempo'])) / 86400;
            $denda = $selisih * $tarif;
        }

        $details = $this->db->table('transaction_details')
            ->where('transaction_id', $transactionId)
            ->get()
            ->getResultArray();

        foreach ($details as $detail) {

            $book = $this->bookModel->find($detail['book_id']);

            if (in_array($detail['book_id'], $lostBooks)) {

                // Tandai hilang
                $this->db->table('transaction_details')
                    ->where('id', $detail['id'])
                    ->update([
                        'is_lost' => 1,
                        'lost_settlement_status' => 'pending'
                    ]);

                // 🔥 LOG LOST
                $logger->log(
                    "Buku '{$book['judul']}' dinyatakan hilang (Transaksi {$transactionId})",
                    $memberId,
                    $staffId
                );

            } else {

                // Kembalikan stok
                $this->bookModel->update($detail['book_id'], [
                    'stok_tersedia' => $book['stok_tersedia'] + 1
                ]);
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