<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\TransactionModel;
use App\Models\AppSettingModel;

class Reports extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Jumlah seluruh buku (sum of stok_total)
        $totalBooksResult = $db->table('books')->selectSum('stok_total')->get()->getRowArray();
        $totalBooks = $totalBooksResult['stok_total'] ?? 0;

        // 2. Jumlah judul buku
        $totalTitles = $db->table('books')->countAllResults();

        // 3. Jumlah kategori buku (DDC)
        $totalCategories = $db->table('category')->countAllResults();

        // 4. Jumlah buku tersedia
        $availableBooksResult = $db->table('books')->selectSum('stok_tersedia')->get()->getRowArray();
        $availableBooks = $availableBooksResult['stok_tersedia'] ?? 0;

        // 5. Jumlah buku yang sedang dipinjam (active loans, is_lost = 0)
        $borrowedBooks = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->countAllResults();

        // 6. Jumlah buku dipinjam jangka pendek
        $shortTermBorrowed = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('t.jenis_peminjaman', 'pendek')
            ->countAllResults();

        // 7. Jumlah buku dipinjam jangka panjang
        $longTermBorrowed = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('t.jenis_peminjaman', 'panjang')
            ->countAllResults();

        // 8. Jumlah buku hilang
        $lostBooks = $db->table('transaction_details')
            ->where('is_lost', 1)
            ->countAllResults();

        // 9. Riwayat transaksi peminjaman & pengembalian buku (recent 10)
        $recentTransactions = $db->table('transactions t')
            ->select('t.*, u.name as member_name, sb.name as staff_name')
            ->join('users u', 'u.id = t.user_id')
            ->join('users sb', 'sb.id = t.borrowed_by')
            ->orderBy('t.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Laporan Transaksi & Inventaris',
            'total_books' => $totalBooks,
            'total_titles' => $totalTitles,
            'total_categories' => $totalCategories,
            'available_books' => $availableBooks,
            'borrowed_books' => $borrowedBooks,
            'short_term' => $shortTermBorrowed,
            'long_term' => $longTermBorrowed,
            'lost_books' => $lostBooks,
            'transactions' => $recentTransactions
        ];

        return view('reports/index', $data);
    }

    public function print()
    {
        $db = \Config\Database::connect();
        
        // Ambil data identitas sekolah
        $settingModel = new AppSettingModel();
        $setting = $settingModel->find(1);

        // Self-healing default
        if (!$setting) {
            $settingModel->insert([
                'id' => 1,
                'nama_lembaga' => 'Perpustakaan Sekolah',
                'logo' => null,
                'kode_lembaga' => '',
                'status_akreditasi' => '',
                'alamat' => '',
                'kepala_sekolah' => '',
                'wakil_kepala_sekolah' => '',
                'website_url' => ''
            ]);
            $setting = $settingModel->find(1);
        }

        // Agregasi statistik data
        $totalBooksResult = $db->table('books')->selectSum('stok_total')->get()->getRowArray();
        $totalBooks = $totalBooksResult['stok_total'] ?? 0;
        $totalTitles = $db->table('books')->countAllResults();
        $totalCategories = $db->table('category')->countAllResults();
        $availableBooksResult = $db->table('books')->selectSum('stok_tersedia')->get()->getRowArray();
        $availableBooks = $availableBooksResult['stok_tersedia'] ?? 0;

        $borrowedBooks = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->countAllResults();

        $shortTermBorrowed = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('t.jenis_peminjaman', 'pendek')
            ->countAllResults();

        $longTermBorrowed = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('t.jenis_peminjaman', 'panjang')
            ->countAllResults();

        $lostBooks = $db->table('transaction_details')
            ->where('is_lost', 1)
            ->countAllResults();

        // Ambil SEMUA transaksi untuk print out laporan resmi
        $allTransactions = $db->table('transactions t')
            ->select('t.*, u.name as member_name, sb.name as staff_name, sr.name as staff_return_name')
            ->join('users u', 'u.id = t.user_id')
            ->join('users sb', 'sb.id = t.borrowed_by')
            ->join('users sr', 'sr.id = t.returned_by', 'left')
            ->orderBy('t.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'setting' => $setting,
            'total_books' => $totalBooks,
            'total_titles' => $totalTitles,
            'total_categories' => $totalCategories,
            'available_books' => $availableBooks,
            'borrowed_books' => $borrowedBooks,
            'short_term' => $shortTermBorrowed,
            'long_term' => $longTermBorrowed,
            'lost_books' => $lostBooks,
            'transactions' => $allTransactions,
            'tanggal_cetak' => date('d F Y')
        ];

        return view('reports/print', $data);
    }
}
