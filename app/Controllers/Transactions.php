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
        // Status terlambat otomatis sejak H+1 jatuh tempo
        $this->service->autoLate();

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
        $data['transactions']  = (new \App\Models\FineModel())->attachSummary($data['transactions']);

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
        // Status terlambat otomatis sejak H+1 jatuh tempo
        $this->service->autoLate();

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
        $data['transactions']  = (new \App\Models\FineModel())->attachSummary($data['transactions']);

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
        // Status terlambat otomatis sejak H+1 jatuh tempo
        $this->service->autoLate();

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
        $this->service->autoLate();

        $transaction = $this->transactionModel->find($id);
        if (!$transaction || $transaction['status'] === 'selesai') {
            return redirect()->to('/transactions')->with('error', 'Transaksi tidak ditemukan atau sudah selesai');
        }

        $member  = $this->userModel->find($transaction['user_id']);
        $maxDays = $this->service->maxFineDays();
        $today   = date('Y-m-d');

        $details = $this->transactionModel
            ->db->table('transaction_details td')
            ->select('td.*, b.judul, bi.kode_inventaris, f.jumlah AS denda_ditahan, f.hari_dikenakan, f.status AS denda_status')
            ->join('books b', 'b.id = td.book_id')
            ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
            ->join('fines f', 'f.transaction_detail_id = td.id', 'left')
            ->where('td.transaction_id', $id)
            ->get()
            ->getResultArray();

        // Perkiraan denda per buku bila dikembalikan hari ini (dipotong batas maksimal)
        $tarif = TransactionService::fineRateFor($member['jenis_pengguna']);
        foreach ($details as &$d) {
            $d['dipinjam']       = !$d['is_lost'] && empty($d['returned_at']);
            $d['hari_estimasi']  = $d['dipinjam'] ? TransactionService::fineDays($transaction['jatuh_tempo'], $today, $maxDays) : 0;
            $d['denda_estimasi'] = $d['hari_estimasi'] * $tarif;
        }
        unset($d);

        return view('transactions/return', [
            'transaction' => $transaction,
            'member'      => $member,
            'details'     => $details,
            'maxDays'     => $maxDays,
            'tarif'       => $tarif,
            'today'       => $today,
            'terlambat'   => TransactionService::fineDays($transaction['jatuh_tempo'], $today, 0),
            'hilangMulai' => TransactionService::lostReportFrom($transaction['jatuh_tempo']),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LAPOR BUKU HILANG (selama masa pinjam)
    |--------------------------------------------------------------------------
    */
    public function reportLost($detailId)
    {
        $trxId = $this->transactionIdOfDetail($detailId);

        try {
            $tanggal = $this->request->getPost('tanggal_lapor') ?: date('Y-m-d');
            $dini    = $this->service->reportLost((int) $detailId, $tanggal, (int) session('user_id'), (bool) $this->request->getPost('laporan_dini'), $this->request->getPost('alasan_dini'));

            $pesan = $dini
                ? 'Buku dilaporkan hilang (laporan dini). Denda batas maksimal langsung ditagih di menu Denda dan tetap berlaku walaupun buku nanti ditemukan.'
                : 'Buku dilaporkan hilang per ' . date('d/m/Y', strtotime($tanggal)) . '. Denda buku ini berhenti di tanggal tersebut dan ditahan sampai ada penyelesaian di menu Buku Hilang.';

            return redirect()->to('/transactions/return/' . $trxId)->with('success', $pesan);
        } catch (\Exception $e) {
            return redirect()->to('/transactions/return/' . $trxId)->with('error', $e->getMessage());
        }
    }

    private function transactionIdOfDetail($detailId): int
    {
        $row = $this->transactionModel->db->table('transaction_details')->select('transaction_id')->where('id', $detailId)->get()->getRowArray();

        return (int) ($row['transaction_id'] ?? 0);
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESS RETURN
    |--------------------------------------------------------------------------
    */
    public function returnProcess($id)
    {
        try {
            // Aksi per buku: kembali / hilang / (kosong = belum dibawa, tetap dipinjam)
            $aksi = (array) ($this->request->getPost('aksi') ?: []);
            $returnIds = array_keys(array_filter($aksi, static fn ($a) => $a === 'kembali'));
            $lostIds   = array_keys(array_filter($aksi, static fn ($a) => $a === 'hilang'));

            $hasil = $this->service->return((int) $id, $returnIds, $lostIds, (int) session('user_id'));

            $rp = static fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
            $pesan = "{$hasil['dikembalikan']} buku dikembalikan" . ($hasil['hilang'] ? ", {$hasil['hilang']} dinyatakan hilang" : '') . '. ';
            $pesan .= 'Denda ditagih transaksi ini: ' . $rp($hasil['ditagih']);
            if ($hasil['ditahan'] > 0) {
                $pesan .= ' (ditahan untuk buku hilang: ' . $rp($hasil['ditahan']) . ')';
            }
            $pesan .= $hasil['selesai']
                ? '. Transaksi selesai.'
                : ". Masih {$hasil['sisa']} buku dipinjam; dendanya terus berjalan sampai dikembalikan.";

            return redirect()->to($hasil['selesai'] ? '/transactions' : '/transactions/return/' . $id)
                ->with('success', $pesan);

        } catch (\Exception $e) {
            return redirect()->to('/transactions/return/' . $id)
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
        $this->response->setHeader('Cache-Control', 'no-store, max-age=0, no-cache');
        $db = \Config\Database::connect();

        $status    = $this->request->getGet('status');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $builder = $db->table('transaction_details td')
            ->select('
                td.id,
                td.lost_settlement_status,
                td.lost_reported_at,
                f.jumlah AS denda_jumlah,
                f.jumlah_awal AS denda_awal,
                f.status AS denda_status,
                td.lost_note,
                td.lost_settlement_amount,
                td.book_item_id,
                bi.kode_inventaris as original_kode_inventaris,
                b.judul,
                t.kode_transaksi,
                t.tanggal_kembali as tanggal_hilang,
                u.name as member_name,
                u.jenis_pengguna,
                t.tanggal_pinjam,
                v.name as verifier_name,
                td.settled_at
            ')
            ->join('transactions t', 't.id = td.transaction_id')
            ->join('books b', 'b.id = td.book_id')
            ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
            ->join('users u', 'u.id = t.user_id')
            ->join('users v', 'v.id = td.settled_by', 'left')
            ->join('fines f', 'f.transaction_detail_id = td.id', 'left')
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

        $builder->orderBy('t.tanggal_kembali', 'DESC')->orderBy('t.updated_at', 'DESC');

        // Agregasi statistik
        $statBuilder = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->where('td.is_lost', 1);
            
        if (!empty($startDate)) {
            $statBuilder->where('t.tanggal_kembali >=', $startDate);
        }
        if (!empty($endDate)) {
            $statBuilder->where('t.tanggal_kembali <=', $endDate);
        }

        $allStats = $statBuilder->select('
            COUNT(td.id) as total_hilang,
            SUM(CASE WHEN td.lost_settlement_status = "pending" THEN 1 ELSE 0 END) as total_pending,
            SUM(CASE WHEN td.lost_settlement_status = "replaced" THEN 1 ELSE 0 END) as total_replaced,
            SUM(CASE WHEN td.lost_settlement_status = "paid" THEN 1 ELSE 0 END) as total_paid,
            SUM(CASE WHEN td.lost_settlement_status = "written_off" THEN 1 ELSE 0 END) as total_written_off,
            SUM(CASE WHEN td.lost_settlement_status = "paid" THEN td.lost_settlement_amount ELSE 0 END) as total_amount
        ')->get()->getRowArray();

        // Manual Pagination
        $pager = \Config\Services::pager();
        $page  = (int) ($this->request->getGet('page') ?? 1);
        if ($page < 1) {
            $page = 1;
        }
        $perPage = 10;

        $total = $builder->countAllResults(false);
        $lostBooks = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $data = [
            'lostBooks'  => $lostBooks,
            'pager'      => $pager->makeLinks($page, $perPage, $total, 'notika'),
            'page'       => $page,
            'status'     => $status,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'stats'      => $allStats
        ];

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

        if (!$detail['is_lost'] || $detail['lost_settlement_status'] !== 'pending') {
            return redirect()->to('/lost-books')
                ->with('error', 'Buku hilang ini sudah diselesaikan sebelumnya');
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

        // Ambil input dari form modal
        $settlementType = $this->request->getPost('settlement_type') ?: 'replaced';
        $userNotes = $this->request->getPost('lost_note') ?: 'Penyelesaian buku hilang';
        $amount = intval($this->request->getPost('lost_settlement_amount') ?: 0);

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

            $settlementText = "";
            $newKodeInventaris = "";

            if ($settlementType === 'replaced') {
                // A. Ganti Buku Fisik
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

                // Buku pengganti dibeli dengan uang pribadi peminjam: sumber dana PRIBADI, tahun penggantian
                $bookService = new \App\Services\BookService();
                $sumberDana  = \App\Services\BookService::SUMBER_PENGGANTI;
                if (!$db->table('source_fund')->where('kode', $sumberDana)->countAllResults()) {
                    throw new \Exception("Sumber dana {$sumberDana} (Pembelian Pribadi) tidak ada di Data Master. Tambahkan dulu di menu Data Master > Sumber Dana.");
                }
                $newKodeInventaris = $bookService->itemKode($book, $sumberDana, intval(date('Y')), $nextNumber);

                $db->table('book_items')->insert([
                    'book_id'         => $bookId,
                    'kode_inventaris' => $newKodeInventaris,
                    'sumber_dana'     => $sumberDana,
                    'tahun_pengadaan' => intval(date('Y')),
                    'nomor_urut'      => $nextNumber,
                    'status'          => 'available',
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);

                // Update stok total dan tersedia di parent book
                $db->table('books')
                    ->where('id', $bookId)
                    ->update([
                        'stok_total'     => $book['stok_total'] + 1,
                        'stok_tersedia'  => $book['stok_tersedia'] + 1
                    ]);

                $settlementText = "Menggantikan salinan fisik [{$oldKode}] yang hilang dengan salinan baru: [{$newKodeInventaris}]. Catatan: {$userNotes}";

            } elseif ($settlementType === 'found') {
                // D. Buku ditemukan: salinan yang sama kembali ke rak, stok tersedia bertambah
                if ($oldItemId) {
                    $db->table('book_items')
                        ->where('id', $oldItemId)
                        ->update(['status' => 'available']);
                }

                $db->table('books')
                    ->where('id', $bookId)
                    ->update([
                        'stok_tersedia' => $book['stok_tersedia'] + 1
                    ]);

                $settlementText = "Salinan fisik [{$oldKode}] yang dilaporkan hilang telah ditemukan dan dikembalikan. Catatan: {$userNotes}";

            } elseif ($settlementType === 'paid') {
                // B. Ganti Uang
                // Kurangi stok total di parent book karena buku hilang permanen dan diganti uang
                $db->table('books')
                    ->where('id', $bookId)
                    ->update([
                        'stok_total' => max(0, $book['stok_total'] - 1)
                    ]);

                $formattedAmount = number_format($amount, 0, ',', '.');
                $settlementText = "Menggantikan salinan fisik [{$oldKode}] yang hilang dengan denda uang sebesar Rp {$formattedAmount}. Catatan: {$userNotes}";

            } elseif ($settlementType === 'written_off') {
                // C. Pemutihan / Penghapusan
                // Kurangi stok total di parent book karena buku hilang permanen tanpa ganti rugi
                $db->table('books')
                    ->where('id', $bookId)
                    ->update([
                        'stok_total' => max(0, $book['stok_total'] - 1)
                    ]);

                $settlementText = "Pemutihan/penghapusan salinan fisik [{$oldKode}] yang hilang dari inventaris. Alasan: {$userNotes}";
            } else {
                throw new \Exception('Tipe penyelesaian tidak valid');
            }

            // Update transaction detail
            $db->table('transaction_details')
                ->where('id', $detailId)
                ->update([
                    'lost_settlement_status' => $settlementType,
                    'lost_note'              => $settlementText,
                    'lost_settlement_amount' => ($settlementType === 'paid') ? $amount : null,
                    'settled_by'             => session('user_id'),
                    'settled_at'             => date('Y-m-d H:i:s')
                ]);

            // Denda keterlambatan buku ini (ditahan) mengikuti penyelesaian:
            // ditemukan -> ditagih, diganti buku/uang -> dihapus, pemutihan -> sesuai surat
            $fineResult = $this->service->applyLostSettlementToFine(
                (int) $detailId,
                $settlementType,
                [
                    'keputusan' => $this->request->getPost('denda_keputusan'),
                    'nominal'   => $this->request->getPost('denda_nominal'),
                    'catatan'   => $this->request->getPost('denda_surat'),
                ],
                (int) session('user_id')
            );

            // LOG ACTIVITY
            $logMsg = "Penyelesaian buku hilang (Tipe: {$settlementType}): " . ($newKodeInventaris ? "menggantikan [{$oldKode}] dengan [{$newKodeInventaris}]" : ($settlementType === 'found' ? "ditemukan [{$oldKode}]" : "penghapusan [{$oldKode}]")) . " (Transaksi ID {$transactionId})";
            $this->logActivity($logMsg, $memberId);

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new \Exception('Gagal memproses penyelesaian buku hilang');
            }

            $metode = ['replaced' => 'Ganti Buku Fisik', 'paid' => 'Ganti Uang', 'written_off' => 'Pemutihan', 'found' => 'Buku Ditemukan'][$settlementType];
            $pesan  = "Penyelesaian buku hilang berhasil diproses dengan metode: {$metode}.";
            if ($fineResult) {
                $pesan .= $fineResult['status'] === 'dibebaskan'
                    ? ' Denda keterlambatan buku ini dihapus.'
                    : ' Denda keterlambatan Rp ' . number_format($fineResult['jumlah'], 0, ',', '.') . ' kini ditagih (menu Denda).';
            }

            return redirect()->to('/lost-books')->with('success', $pesan);

        } catch (\Exception $e) {
            $db->transRollback();

            return redirect()->to('/lost-books')
                ->with('error', $e->getMessage());
        }
    }
}