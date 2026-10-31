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
        $totalCategories = $db->table('category')->where('is_active', 1)->like('code_ddc', '_00', 'none')->countAllResults();

        // 4. Jumlah buku tersedia
        $availableBooksResult = $db->table('books')->selectSum('stok_tersedia')->get()->getRowArray();
        $availableBooks = $availableBooksResult['stok_tersedia'] ?? 0;

        // 5. Jumlah buku yang sedang dipinjam (active loans, is_lost = 0)
        $borrowedBooks = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('td.returned_at', null)
            ->countAllResults();

        // 6. Jumlah buku dipinjam jangka pendek
        $shortTermBorrowed = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('td.returned_at', null)
            ->where('t.jenis_peminjaman', 'pendek')
            ->countAllResults();

        // 7. Jumlah buku dipinjam jangka panjang
        $longTermBorrowed = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('td.returned_at', null)
            ->where('t.jenis_peminjaman', 'panjang')
            ->countAllResults();

        // 8. Jumlah buku hilang (hanya yang pending / butuh penggantian)
        $lostBooks = $db->table('transaction_details')
            ->where('is_lost', 1)
            ->where('lost_settlement_status', 'pending')
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
            'ddc_distribution' => $this->ddcDistribution($db),
            'ddc_detail' => $this->ddcDetail($db),
            'total_ddc_detail' => $db->table('category')->where('is_active', 1)->notLike('code_ddc', '_00', 'none')->countAllResults(),
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
        $totalCategories = $db->table('category')->where('is_active', 1)->like('code_ddc', '_00', 'none')->countAllResults();
        $availableBooksResult = $db->table('books')->selectSum('stok_tersedia')->get()->getRowArray();
        $availableBooks = $availableBooksResult['stok_tersedia'] ?? 0;

        $borrowedBooks = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('td.returned_at', null)
            ->countAllResults();

        $shortTermBorrowed = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('td.returned_at', null)
            ->where('t.jenis_peminjaman', 'pendek')
            ->countAllResults();

        $longTermBorrowed = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->whereIn('t.status', ['dipinjam', 'terlambat'])
            ->where('td.is_lost', 0)
            ->where('td.returned_at', null)
            ->where('t.jenis_peminjaman', 'panjang')
            ->countAllResults();

        $lostBooks = $db->table('transaction_details')
            ->where('is_lost', 1)
            ->where('lost_settlement_status', 'pending')
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
            'ddc_distribution' => $this->ddcDistribution($db),
            'ddc_detail' => $this->ddcDetail($db),
            'total_ddc_detail' => $db->table('category')->where('is_active', 1)->notLike('code_ddc', '_00', 'none')->countAllResults(),
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

    public function exportLostBooks()
    {
        $db = \Config\Database::connect();

        $format    = $this->request->getGet('format') ?: 'print';
        $status    = $this->request->getGet('status');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        // Ambil data identitas sekolah
        $settingModel = new AppSettingModel();
        $setting = $settingModel->find(1);

        // Query data
        $builder = $db->table('transaction_details td')
            ->select('
                td.*,
                b.judul,
                b.kode_buku,
                bi.kode_inventaris as original_kode_inventaris,
                t.kode_transaksi,
                t.tanggal_kembali as tanggal_hilang,
                u.name as member_name,
                u.jenis_pengguna,
                sb.name as staff_name,
                v.name as verifier_name
            ')
            ->join('transactions t', 't.id = td.transaction_id')
            ->join('books b', 'b.id = td.book_id')
            ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
            ->join('users u', 'u.id = t.user_id')
            ->join('users sb', 'sb.id = t.returned_by', 'left')
            ->join('users v', 'v.id = td.settled_by', 'left')
            ->where('td.is_lost', 1);

        if (!empty($status)) {
            $builder->where('td.lost_settlement_status', $status);
        }

        if (!empty($startDate)) {
            $builder->where('t.tanggal_kembali >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('t.tanggal_kembali <=', $endDate);
        }

        $lostBooks = $builder->orderBy('t.tanggal_kembali', 'DESC')->orderBy('t.updated_at', 'DESC')->get()->getResultArray();

        // Agregasi rekapitulasi untuk cetak/export
        $statBuilder = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->where('td.is_lost', 1);

        if (!empty($startDate)) {
            $statBuilder->where('t.tanggal_kembali >=', $startDate);
        }
        if (!empty($endDate)) {
            $statBuilder->where('t.tanggal_kembali <=', $endDate);
        }

        $stats = $statBuilder->select('
            COUNT(td.id) as total_hilang,
            SUM(CASE WHEN td.lost_settlement_status = "pending" THEN 1 ELSE 0 END) as total_pending,
            SUM(CASE WHEN td.lost_settlement_status = "replaced" THEN 1 ELSE 0 END) as total_replaced,
            SUM(CASE WHEN td.lost_settlement_status = "paid" THEN 1 ELSE 0 END) as total_paid,
            SUM(CASE WHEN td.lost_settlement_status = "written_off" THEN 1 ELSE 0 END) as total_written_off,
            SUM(CASE WHEN td.lost_settlement_status = "paid" THEN td.lost_settlement_amount ELSE 0 END) as total_amount
        ')->get()->getRowArray();

        $data = [
            'setting'    => $setting,
            'lostBooks'  => $lostBooks,
            'stats'      => $stats,
            'status'     => $status,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'tanggal_cetak' => date('d F Y')
        ];

        if ($format === 'excel') {
            // Bersihkan output buffer jika ada php notice/warning agar tidak menggagalkan header download
            if (ob_get_length()) {
                ob_clean();
            }

            // Paksa browser (terutama Chrome) untuk selalu mendownload file sebagai file biner murni (.xls)
            return $this->response->setHeader('Content-Type', 'application/octet-stream')
                ->setHeader('Content-Disposition', 'attachment; filename="Laporan_Buku_Hilang_' . date('Ymd_His') . '.xls"')
                ->setHeader('Content-Transfer-Encoding', 'binary')
                ->setHeader('Connection', 'Keep-Alive')
                ->setHeader('Pragma', 'no-cache')
                ->setHeader('Expires', '0')
                ->setBody(view('reports/lost_books_excel', $data));
        }

        // Default layout cetak kertas / PDF
        return view('reports/lost_books_print', $data);
    }

    /**
     * Jumlah judul & eksemplar per kelas DDC aktif, ditambah buku yang belum diklasifikasi.
     */
    private function ddcDistribution($db): array
    {
        // Per kelas utama: gabungan buku di kelas itu beserta semua divisi & seksinya
        $rows = $db->query(
            "SELECT k.code_ddc, k.desc_ddc, COUNT(b.id) AS judul, COALESCE(SUM(b.stok_total), 0) AS eksemplar
             FROM category k
             LEFT JOIN category c ON LEFT(c.code_ddc, 1) = LEFT(k.code_ddc, 1)
             LEFT JOIN books b ON b.category_id = c.id
             WHERE k.code_ddc LIKE '_00' AND k.is_active = 1
             GROUP BY k.id, k.code_ddc, k.desc_ddc
             ORDER BY k.code_ddc"
        )->getResultArray();

        $belum = $db->table('books')
            ->select('COUNT(id) AS judul, COALESCE(SUM(stok_total), 0) AS eksemplar')
            ->where('category_id', null)
            ->get()
            ->getRowArray();

        if ((int) $belum['judul'] > 0) {
            $rows[] = ['code_ddc' => '-', 'desc_ddc' => 'Belum diklasifikasi', 'judul' => $belum['judul'], 'eksemplar' => $belum['eksemplar']];
        }

        return $rows;
    }

    /**
     * Rincian per klasifikasi (kelas, divisi, seksi) yang memiliki buku.
     */
    private function ddcDetail($db): array
    {
        return $db->table('category c')
            ->select('c.code_ddc, c.desc_ddc, COUNT(b.id) AS judul, COALESCE(SUM(b.stok_total), 0) AS eksemplar')
            ->join('books b', 'b.category_id = c.id')
            ->groupBy('c.id, c.code_ddc, c.desc_ddc')
            ->orderBy('c.code_ddc', 'ASC')
            ->get()
            ->getResultArray();
    }
}
