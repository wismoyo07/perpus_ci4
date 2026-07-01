<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\TransactionService;
use App\Models\TransactionModel;
use App\Models\UserModel;
use App\Models\BookModel;

class Transactions extends BaseController
{
    protected $service;
    protected $transactionModel;
    protected $userModel;
    protected $bookModel;

    public function __construct()
    {
        $this->service = new TransactionService();
        $this->transactionModel = new TransactionModel();
        $this->userModel = new UserModel();
        $this->bookModel = new BookModel();
    }

    /*
    |--------------------------------------------------------------------------
    | STAFF INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $search        = $this->request->getGet('search');
        $jenis_pinjam  = $this->request->getGet('jenis_pinjam');
        $status        = $this->request->getGet('status');
        $start_pinjam  = $this->request->getGet('start_pinjam');
        $end_pinjam    = $this->request->getGet('end_pinjam');
        $start_kembali = $this->request->getGet('start_kembali');
        $end_kembali   = $this->request->getGet('end_kembali');

        $builder = $this->transactionModel
            ->select('
                transactions.*,
                u.name as member_name,
                u.jenis_pengguna,
                sb.name as staff_borrow,
                sr.name as staff_return
            ')
            ->join('users u', 'u.id = transactions.user_id')
            ->join('users sb', 'sb.id = transactions.borrowed_by')
            ->join('users sr', 'sr.id = transactions.returned_by', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('transactions.kode_transaksi', $search)
                ->orLike('u.name', $search)
                ->orLike('sb.name', $search)
                ->orLike('sr.name', $search)
                ->groupEnd();
        }

        if (!empty($jenis_pinjam)) {
            $builder->where('transactions.jenis_peminjaman', $jenis_pinjam);
        }

        if (!empty($status)) {
            $builder->where('transactions.status', $status);
        }

        if (!empty($start_pinjam)) {
            $builder->where('transactions.tanggal_pinjam >=', $start_pinjam);
        }
        if (!empty($end_pinjam)) {
            $builder->where('transactions.tanggal_pinjam <=', $end_pinjam);
        }

        if (!empty($start_kembali)) {
            $builder->where('transactions.tanggal_kembali >=', $start_kembali);
        }
        if (!empty($end_kembali)) {
            $builder->where('transactions.tanggal_kembali <=', $end_kembali);
        }

        $builder->orderBy('transactions.created_at', 'DESC');

        $data['transactions']  = $builder->paginate(10);
        $data['pager']         = $this->transactionModel->pager;

        $db = \Config\Database::connect();
        foreach ($data['transactions'] as &$trx) {
            $trx['details'] = $db->table('transaction_details td')
                ->select('
                    td.*,
                    b.kode_buku,
                    b.judul,
                    b.pengarang,
                    b.penerbit,
                    bi.kode_inventaris
                ')
                ->join('books b', 'b.id = td.book_id')
                ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
                ->where('td.transaction_id', $trx['id'])
                ->get()
                ->getResultArray();
        }

        $data['search']        = $search;
        $data['jenis_pinjam']  = $jenis_pinjam;
        $data['status']        = $status;
        $data['start_pinjam']  = $start_pinjam;
        $data['end_pinjam']    = $end_pinjam;
        $data['start_kembali'] = $start_kembali;
        $data['end_kembali']   = $end_kembali;

        return view('transactions/index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN VIEW ONLY
    |--------------------------------------------------------------------------
    |*/
    public function adminIndex()
    {
        $search        = $this->request->getGet('search');
        $jenis_pinjam  = $this->request->getGet('jenis_pinjam');
        $status        = $this->request->getGet('status');
        $start_pinjam  = $this->request->getGet('start_pinjam');
        $end_pinjam    = $this->request->getGet('end_pinjam');
        $start_kembali = $this->request->getGet('start_kembali');
        $end_kembali   = $this->request->getGet('end_kembali');

        $builder = $this->transactionModel
            ->select('
                transactions.*,
                u.name as member_name,
                u.jenis_pengguna,
                sb.name as staff_borrow,
                sr.name as staff_return
            ')
            ->join('users u', 'u.id = transactions.user_id')
            ->join('users sb', 'sb.id = transactions.borrowed_by')
            ->join('users sr', 'sr.id = transactions.returned_by', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('transactions.kode_transaksi', $search)
                ->orLike('u.name', $search)
                ->orLike('sb.name', $search)
                ->orLike('sr.name', $search)
                ->groupEnd();
        }

        if (!empty($jenis_pinjam)) {
            $builder->where('transactions.jenis_peminjaman', $jenis_pinjam);
        }

        if (!empty($status)) {
            $builder->where('transactions.status', $status);
        }

        if (!empty($start_pinjam)) {
            $builder->where('transactions.tanggal_pinjam >=', $start_pinjam);
        }
        if (!empty($end_pinjam)) {
            $builder->where('transactions.tanggal_pinjam <=', $end_pinjam);
        }

        if (!empty($start_kembali)) {
            $builder->where('transactions.tanggal_kembali >=', $start_kembali);
        }
        if (!empty($end_kembali)) {
            $builder->where('transactions.tanggal_kembali <=', $end_kembali);
        }

        $builder->orderBy('transactions.created_at', 'DESC');

        $data['transactions']  = $builder->paginate(10);
        $data['pager']         = $this->transactionModel->pager;

        $db = \Config\Database::connect();
        foreach ($data['transactions'] as &$trx) {
            $trx['details'] = $db->table('transaction_details td')
                ->select('
                    td.*,
                    b.kode_buku,
                    b.judul,
                    b.pengarang,
                    b.penerbit,
                    bi.kode_inventaris
                ')
                ->join('books b', 'b.id = td.book_id')
                ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
                ->where('td.transaction_id', $trx['id'])
                ->get()
                ->getResultArray();
        }

        $data['search']        = $search;
        $data['jenis_pinjam']  = $jenis_pinjam;
        $data['status']        = $status;
        $data['start_pinjam']  = $start_pinjam;
        $data['end_pinjam']    = $end_pinjam;
        $data['start_kembali'] = $start_kembali;
        $data['end_kembali']   = $end_kembali;

        return view('transactions/admin_index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | FORM CREATE (STAFF)
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | FORM CREATE (STAFF)
    |--------------------------------------------------------------------------
    |*/
    public function create()
    {
        $db = \Config\Database::connect();

        $data['members'] = $this->userModel
            ->where('role', 'member')
            ->where('is_validated', 1)
            ->findAll();

        $data['books'] = $this->bookModel
            ->where('is_active', 1)
            ->where('stok_tersedia >', 0)
            ->findAll();

        // Get all available copies
        $data['book_items'] = $db->table('book_items')
            ->where('status', 'available')
            ->get()
            ->getResultArray();

        return view('transactions/create', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store()
    {
        try {
            $memberId = $this->request->getPost('member_id');
            $bookItemIds = $this->request->getPost('book_item_ids') ?: [];
            $jenisPeminjaman = $this->request->getPost('jenis_peminjaman') ?: 'pendek';
            $customDueDate = $this->request->getPost('jatuh_tempo') ?: null;

            $kode = $this->service->borrow(
                $memberId,
                $bookItemIds,
                session('user_id'),
                $jenisPeminjaman,
                $customDueDate
            );

            // Fetch transaction ID to log activity
            $trx = $this->transactionModel->where('kode_transaksi', $kode)->first();
            $transactionId = $trx ? $trx['id'] : 0;

            $this->logActivity(
                "Mencatat transaksi peminjaman {$kode} (ID {$transactionId})",
                $memberId
            );

            return redirect()->to('/transactions')
                ->with('success', "Transaksi berhasil: {$kode}");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN FORM
    |--------------------------------------------------------------------------
    */
    public function returnForm($id)
    {
        $data['transaction'] = $this->transactionModel->find($id);

        $data['details'] = $this->transactionModel
            ->db->table('transaction_details td')
            ->select('td.*, b.judul, bi.kode_inventaris')
            ->join('books b', 'b.id = td.book_id')
            ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
            ->where('transaction_id', $id)
            ->get()
            ->getResultArray();

        return view('transactions/return', $data);
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESS RETURN
    |--------------------------------------------------------------------------
    */
    public function returnProcess($id)
    {
        try {
            $lostBookItemIds = $this->request->getPost('lost_books') ?: [];

            $denda = $this->service->return(
                $id,
                $lostBookItemIds,
                session('user_id')
            );

            return redirect()->to('/transactions')
                ->with('success', "Pengembalian selesai. Denda: Rp {$denda}");

        } catch (\Exception $e) {
            return redirect()->to('/transactions')
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PROCESS LOST BOOKS
    |--------------------------------------------------------------------------
    */
    public function lostIndex()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('transaction_details td')
            ->select('
                td.id,
                td.lost_settlement_status,
                td.lost_note,
                td.book_item_id,
                bi.kode_inventaris as original_kode_inventaris,
                b.judul,
                t.kode_transaksi,
                u.name as member_name,
                t.tanggal_pinjam
            ')
            ->join('transactions t', 't.id = td.transaction_id')
            ->join('books b', 'b.id = td.book_id')
            ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
            ->join('users u', 'u.id = t.user_id')
            ->where('td.is_lost', 1)
            ->orderBy('t.created_at', 'DESC');

        $data['lostBooks'] = $builder->get()->getResultArray();

        return view('transactions/lost_index', $data);
    }


    public function settleLost($detailId)
    {
        $db = \Config\Database::connect();

        $detail = $db->table('transaction_details')
            ->where('id', $detailId)
            ->get()
            ->getRowArray();

        if (!$detail) {
            return redirect()->back()
                ->with('error', 'Data tidak ditemukan');
        }

        $transactionId = $detail['transaction_id'];
        $bookId = $detail['book_id'];
        $oldItemId = $detail['book_item_id'];

        // Ambil transaksi untuk tahu member
        $transaction = $db->table('transactions')
            ->where('id', $transactionId)
            ->get()
            ->getRowArray();

        $memberId = $transaction['user_id'] ?? null;

        // Ambil data salinan fisik yang hilang
        $oldItem = $db->table('book_items')
            ->where('id', $oldItemId)
            ->get()
            ->getRowArray();

        $oldKode = $oldItem ? $oldItem['kode_inventaris'] : 'Unknown';

        // Ambil input catatan dari form
        $userNotes = $this->request->getPost('lost_note') ?: 'Diganti dengan buku baru';

        try {
            $db->transStart();

            // 1. Dapatkan info buku catalog
            $book = $db->table('books')
                ->where('id', $bookId)
                ->get()
                ->getRowArray();

            if (!$book) {
                throw new \Exception('Buku tidak ditemukan');
            }

            // 2. Buat salinan fisik baru sebagai pengganti
            // Cari nomor urut copy terakhir untuk buku ini
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
            $newKodeInventaris = "{$book['kode_buku']} / {$urutCopyPadded}";

            $db->table('book_items')->insert([
                'book_id'         => $bookId,
                'kode_inventaris' => $newKodeInventaris,
                'sumber_dana'     => $oldItem ? $oldItem['sumber_dana'] : 'Ganti Rugi',
                'tahun_pengadaan' => intval(date('Y')),
                'nomor_urut'      => $nextNumber,
                'status'          => 'available',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            // 3. Update transaction detail: status = replaced, simpan catatan penggantian
            $settlementText = "Menggantikan salinan fisik [{$oldKode}] yang hilang. Kode inventaris baru: [{$newKodeInventaris}]. Catatan: {$userNotes}";

            $db->table('transaction_details')
                ->where('id', $detailId)
                ->update([
                    'lost_settlement_status' => 'replaced',
                    'lost_note' => $settlementText
                ]);

            // 4. Update stok total dan tersedia di parent book
            $db->table('books')
                ->where('id', $bookId)
                ->update([
                    'stok_total'     => $book['stok_total'] + 1,
                    'stok_tersedia'  => $book['stok_tersedia'] + 1
                ]);

            // 5. LOG ACTIVITY
            $this->logActivity(
                "Penyelesaian buku hilang: menggantikan [{$oldKode}] dengan [{$newKodeInventaris}] (Transaksi ID {$transactionId})",
                $memberId
            );

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new \Exception('Gagal memproses penyelesaian buku hilang');
            }

            return redirect()->to('/lost-books')
                ->with('success', 'Penyelesaian buku hilang berhasil. Salinan fisik baru ditambahkan dengan kode: ' . $newKodeInventaris);

        } catch (\Exception $e) {
            return redirect()->to('/lost-books')
                ->with('error', $e->getMessage());
        }
    }
}