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
            ->join('users sr', 'sr.id = transactions.returned_by', 'left')
            ->orderBy('transactions.created_at', 'DESC');

        $data['transactions'] = $builder->paginate(10);
        $data['pager'] = $this->transactionModel->pager;

        return view('transactions/index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN VIEW ONLY
    |--------------------------------------------------------------------------
    */
    public function adminIndex()
    {
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
            ->join('users sr', 'sr.id = transactions.returned_by', 'left')
            ->orderBy('transactions.created_at', 'DESC');

        $data['transactions'] = $builder->paginate(10);
        $data['pager'] = $this->transactionModel->pager;

        return view('transactions/admin_index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | FORM CREATE (STAFF)
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $data['members'] = $this->userModel
            ->where('role', 'member')
            ->where('is_validated', 1)
            ->findAll();

        $data['books'] = $this->bookModel
            ->where('is_active', 1)
            ->where('stok_tersedia >', 0)
            ->findAll();

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
            $bookIds  = $this->request->getPost('book_ids');

            $kode = $this->service->borrow(
                $memberId,
                $bookIds,
                session('user_id')
            );

            return redirect()->to('/transactions')
                ->with('success', "Transaksi berhasil: {$kode}");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        $this->logActivity(
            "Create transaksi peminjaman ID {$transactionId}",
            $memberId
        );
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
            ->select('td.*, b.judul')
            ->join('books b', 'b.id = td.book_id')
            ->where('transaction_id', $id)
            ->get()
            ->getResultArray();

        return view('transactions/return', $data);

        $this->logActivity(
            "Create transaksi peminjaman ID {$transactionId}",
            $memberId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESS RETURN
    |--------------------------------------------------------------------------
    */
    public function returnProcess($id)
    {
        try {

            $lostBooks = $this->request->getPost('lost_books') ?? [];

            $denda = $this->service->return(
                $id,
                $lostBooks,
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
                b.judul,
                t.kode_transaksi,
                u.name as member_name,
                t.tanggal_pinjam
            ')
            ->join('transactions t', 't.id = td.transaction_id')
            ->join('books b', 'b.id = td.book_id')
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

        // Ambil transaksi untuk tahu member
        $transaction = $db->table('transactions')
            ->where('id', $transactionId)
            ->get()
            ->getRowArray();

        $memberId = $transaction['user_id'] ?? null;

        // =============================
        // Update status replaced
        // =============================
        $db->table('transaction_details')
            ->where('id', $detailId)
            ->update([
                'lost_settlement_status' => 'replaced'
            ]);

        // =============================
        // Tambah stok kembali
        // =============================
        $book = $db->table('books')
            ->where('id', $detail['book_id'])
            ->get()
            ->getRowArray();

        $db->table('books')
            ->where('id', $detail['book_id'])
            ->update([
                'stok_total'     => $book['stok_total'] + 1,
                'stok_tersedia'  => $book['stok_tersedia'] + 1
            ]);

        // =============================
        // LOG ACTIVITY
        // =============================
        $this->logActivity(
            "Buku hilang telah diganti (Transaksi ID {$transactionId})",
            $memberId
        );

        return redirect()->to('/lost-books')
            ->with('success', 'Buku hilang telah diganti dan stok diperbarui');
    }
}