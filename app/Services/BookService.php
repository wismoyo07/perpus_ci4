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
            $tahun = date('Y');
            $sumberDana = 'BOS';
            $kodeAkun = '03.11.01.01.001';

            // Cari nomor urut terakhir untuk tahun ini dan sumber dana ini
            $lastBook = $this->db->table('books')
                ->like('kode_buku', "{$kodeAkun} / {$sumberDana} / {$tahun} /", 'after')
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            $nextNumber = 1;
            if ($lastBook) {
                // Ekstrak nomor urut dari kode terakhir
                $parts = explode('/', $lastBook['kode_buku']);
                $lastSeq = intval(trim(end($parts)));
                $nextNumber = $lastSeq + 1;
            }

            $urutPadded = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $data['kode_buku'] = "{$kodeAkun} / {$sumberDana} / {$tahun} / {$urutPadded}";
        } else {
            // Validasi format jika diinput manual
            $pattern = '/^\d{2}\.\d{2}\.\d{2}\.\d{2}\.\d{3}\s*\/\s*[A-Z0-9-]+\s*\/\s*\d{4}\s*\/\s*\d{3,}$/i';
            if (!preg_match($pattern, $data['kode_buku'])) {
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

        // Validasi format kode buku baru jika diubah
        if (!empty($data['kode_buku']) && $data['kode_buku'] !== $book['kode_buku']) {
            $pattern = '/^\d{2}\.\d{2}\.\d{2}\.\d{2}\.\d{3}\s*\/\s*[A-Z0-9-]+\s*\/\s*\d{4}\s*\/\s*\d{3,}$/i';
            if (!preg_match($pattern, $data['kode_buku'])) {
                throw new \Exception('Format Kode Buku tidak valid. Contoh format: 03.11.01.01.001 / BOS / 2024 / 005');
            }
        }

        // Protect stock total & tersedia from manual changes during generic edit
        unset($data['stok_total']);
        unset($data['stok_tersedia']);

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