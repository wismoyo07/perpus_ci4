<?php

namespace App\Services;

use App\Models\BookModel;
use App\Models\ActivityLogModel;
use Config\Database;

class BookService
{
    protected $bookModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->bookModel = new BookModel();
        $this->logModel  = new ActivityLogModel();
        $this->db        = Database::connect();
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(array $data, int $actorId)
    {
        // AUTO GENERATE JIKA KOSONG
        if (empty($data['kode_buku'])) {
            $data['kode_buku'] = 'BK-' . date('YmdHis');
        }

        $this->validateStock($data['stok_total'], $data['stok_tersedia']);

        $id = $this->bookModel->insert($data);

        $this->logModel->insert([
            'user_id'  => $actorId,
            'aktivitas'=> "Menambahkan buku: {$data['judul']}"
        ]);

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

        $stokDipinjam = $book['stok_total'] - $book['stok_tersedia'];

        if (isset($data['stok_total']) && $data['stok_total'] < $stokDipinjam) {
            throw new \Exception('Tidak bisa mengurangi stok total di bawah jumlah buku dipinjam');
        }

        if (isset($data['stok_tersedia']) &&
            $data['stok_tersedia'] > $data['stok_total']) {
            throw new \Exception('Stok tersedia tidak boleh melebihi stok total');
        }

        $this->bookModel->update($id, $data);

        $this->logModel->insert([
            'user_id'  => $actorId,
            'aktivitas'=> "Mengupdate buku: {$book['judul']}"
        ]);

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

        $this->logModel->insert([
            'user_id'  => $actorId,
            'aktivitas'=> "Menghapus buku: {$book['judul']}"
        ]);

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

        $this->logModel->insert([
            'user_id'  => $actorId,
            'aktivitas'=> "Mengarsipkan buku: {$book['judul']}"
        ]);

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

        $this->logModel->insert([
            'user_id'  => $actorId,
            'aktivitas'=> "Mengaktifkan kembali buku: {$book['judul']}"
        ]);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI STOK
    |--------------------------------------------------------------------------
    */
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