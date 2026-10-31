<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookModel;
use App\Services\BookService;

class Books extends BaseController
{
    protected $bookModel;
    protected $service;

    public function __construct()
    {
        $this->bookModel = new BookModel();
        $this->service   = new BookService();
    }

    /*
    |--------------------------------------------------------------------------
    | LIST
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $builder = $this->bookModel;

        if ($status === 'active') {
            $builder->where('is_active', 1);
        } elseif ($status === 'archived') {
            $builder->where('is_active', 0);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('judul', $search)
                ->orLike('pengarang', $search)
                ->orLike('kode_buku', $search)
                ->orLike('penerbit', $search)
                ->groupEnd();
        }

        $data['books'] = $builder->orderBy('created_at', 'DESC')->paginate(10);
        $data['pager'] = $this->bookModel->pager;
        $data['search'] = $search;
        $data['status'] = $status;

        return view('books/index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('books/form');
    }

    public function store()
    {
        try {
            $this->service->create(
                $this->request->getPost(),
                session('user_id')
            );

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $data['book'] = $this->bookModel->find($id);

        if (!$data['book']) {
            return redirect()->to('/books')
                ->with('error', 'Buku tidak ditemukan');
        }

        return view('books/form', $data);
    }

    public function update($id)
    {
        try {
            $this->service->update(
                $id,
                $this->request->getPost(),
                session('user_id')
            );

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        try {
            $this->service->delete($id, session('user_id'));

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('/books')
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHIVE dan RESTORE
    |--------------------------------------------------------------------------
    */
    public function archive($id)
    {
        try {
            $this->service->archive($id, session('user_id'));

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil diarsipkan');
        } catch (\Exception $e) {
            return redirect()->to('/books')
                ->with('error', $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $this->service->restore($id, session('user_id'));

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil diaktifkan kembali');
        } catch (\Exception $e) {
            return redirect()->to('/books')
                ->with('error', $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | View List Buku Member
    |--------------------------------------------------------------------------
    */

    public function listMember()
    {
        $data['books'] = $this->bookModel
            ->where('is_active', 1)
            ->orderBy('judul', 'ASC')
            ->paginate(10);

        $data['pager'] = $this->bookModel->pager;

        return view('books/member_list', $data);
    }

    public function items($bookId)
    {
        $book = $this->bookModel->find($bookId);
        if (!$book) {
            return redirect()->to('/books')->with('error', 'Buku tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $items = $db->table('book_items')
            ->where('book_id', $bookId)
            ->orderBy('nomor_urut', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'book'  => $book,
            'items' => $items
        ];

        return view('books/items', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | CETAK LABEL SALINAN BUKU
    |--------------------------------------------------------------------------
    */
    public function labels($bookId)
    {
        $book = $this->bookModel->find($bookId);
        if (!$book) {
            return redirect()->to('/books')->with('error', 'Buku tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('book_items')
            ->where('book_id', $bookId)
            ->where('status !=', 'lost');

        $ids = array_filter(array_map('intval', (array) $this->request->getGet('selected')));
        if (!empty($ids)) {
            $builder->whereIn('id', $ids);
        }

        $setting = (new \App\Models\AppSettingModel())->find(1) ?? [
            'nama_lembaga' => 'Perpustakaan Sekolah',
        ];

        helper('barcode');

        return view('books/labels', [
            'book'    => $book,
            'items'   => $builder->orderBy('nomor_urut', 'ASC')->get()->getResultArray(),
            'setting' => $setting,
        ]);
    }

    public function storeItem($bookId)
    {
        try {
            $book = $this->bookModel->find($bookId);
            if (!$book) {
                return redirect()->to('/books')->with('error', 'Buku tidak ditemukan');
            }

            $db = \Config\Database::connect();
            $sumber = $this->request->getPost('sumber_dana') ?: 'BOS';
            $tahun = intval($this->request->getPost('tahun_pengadaan')) ?: intval(date('Y'));

            // Auto-generate next nomor_urut
            $lastItem = $db->table('book_items')
                ->where('book_id', $bookId)
                ->orderBy('nomor_urut', 'DESC')
                ->get()
                ->getRowArray();

            $nextNumber = 1;
            if ($lastItem) {
                $nextNumber = intval($lastItem['nomor_urut']) + 1;
            }

            $urutCopyPadded = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $kodeInventaris = "{$book['kode_buku']} / {$urutCopyPadded}";

            $db->transStart();

            // Insert copy record
            $db->table('book_items')->insert([
                'book_id'         => $bookId,
                'kode_inventaris' => $kodeInventaris,
                'sumber_dana'     => $sumber,
                'tahun_pengadaan' => $tahun,
                'nomor_urut'      => $nextNumber,
                'status'          => 'available'
            ]);

            // Update counters
            $this->bookModel->update($bookId, [
                'stok_total'     => $book['stok_total'] + 1,
                'stok_tersedia'  => $book['stok_tersedia'] + 1
            ]);

            // Log activity
            $db->table('activity_logs')->insert([
                'user_id'   => session('user_id'),
                'aktivitas' => "Menambahkan salinan fisik book ID {$bookId} dengan kode {$kodeInventaris}",
                'created_at'=> date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new \Exception('Gagal menyimpan salinan fisik');
            }

            return redirect()->to('/books/items/' . $bookId)
                ->with('success', 'Salinan fisik berhasil ditambahkan: ' . $kodeInventaris);

        } catch (\Exception $e) {
            return redirect()->to('/books/items/' . $bookId)
                ->with('error', $e->getMessage());
        }
    }

    public function deleteItem($itemId)
    {
        $db = \Config\Database::connect();
        $item = $db->table('book_items')
            ->where('id', $itemId)
            ->get()
            ->getRowArray();

        if (!$item) {
            return redirect()->back()->with('error', 'Salinan fisik tidak ditemukan');
        }

        $bookId = $item['book_id'];
        $book = $this->bookModel->find($bookId);

        if ($item['status'] !== 'available') {
            return redirect()->to('/books/items/' . $bookId)
                ->with('error', 'Hanya salinan fisik yang tersedia yang dapat dihapus.');
        }

        // Cek riwayat transaksi
        $used = $db->table('transaction_details')
            ->where('book_item_id', $itemId)
            ->countAllResults();

        if ($used > 0) {
            return redirect()->to('/books/items/' . $bookId)
                ->with('error', 'Salinan ini memiliki riwayat transaksi dan tidak dapat dihapus secara fisik.');
        }

        try {
            $db->transStart();

            $db->table('book_items')->where('id', $itemId)->delete();

            $this->bookModel->update($bookId, [
                'stok_total'     => max(0, $book['stok_total'] - 1),
                'stok_tersedia'  => max(0, $book['stok_tersedia'] - 1)
            ]);

            // Log activity
            $db->table('activity_logs')->insert([
                'user_id'   => session('user_id'),
                'aktivitas' => "Menghapus salinan fisik book ID {$bookId} dengan kode {$item['kode_inventaris']}",
                'created_at'=> date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new \Exception('Gagal menghapus salinan fisik');
            }

            return redirect()->to('/books/items/' . $bookId)
                ->with('success', 'Salinan fisik berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->to('/books/items/' . $bookId)
                ->with('error', $e->getMessage());
        }
    }
}

