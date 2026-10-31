<?php

namespace App\Controllers;

use App\Services\TransactionService;
use Config\Database;

/**
 * Denda keterlambatan per buku (admin & staff).
 * Denda dibuat otomatis saat buku dikembalikan terlambat, atau saat buku dilaporkan hilang
 * (status "ditahan" sampai penyelesaian di menu Buku Hilang). Di sini denda belum lunas
 * ditandai lunas atau dibebaskan.
 */
class Fines extends BaseController
{
    private const STATUSES = ['belum_lunas', 'ditahan', 'lunas', 'dibebaskan'];

    public function index()
    {
        $this->response->setHeader('Cache-Control', 'no-store, max-age=0, no-cache');
        $db = Database::connect();
        (new TransactionService())->autoLate();

        $status    = $this->request->getGet('status') ?? 'belum_lunas';
        $search    = trim((string) $this->request->getGet('search'));
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $builder = $db->table('fines f')
            ->select('f.*, t.kode_transaksi, t.jatuh_tempo, t.tanggal_kembali,
                      td.lost_reported_at, td.lost_settlement_status,
                      b.judul, bi.kode_inventaris,
                      u.name AS member_name, u.member_code, u.jenis_pengguna, p.name AS petugas_name')
            ->join('transactions t', 't.id = f.transaction_id')
            ->join('transaction_details td', 'td.id = f.transaction_detail_id', 'left')
            ->join('books b', 'b.id = td.book_id', 'left')
            ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
            ->join('users u', 'u.id = f.user_id')
            ->join('users p', 'p.id = f.diproses_by', 'left');

        if (in_array($status, self::STATUSES, true)) {
            $builder->where('f.status', $status);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('u.name', $search)
                ->orLike('u.member_code', $search)
                ->orLike('t.kode_transaksi', $search)
                ->orLike('b.judul', $search)
                ->groupEnd();
        }
        if (!empty($startDate)) {
            $builder->where('DATE(f.created_at) >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('DATE(f.created_at) <=', $endDate);
        }

        // Belum lunas paling lama di atas
        $builder->orderBy("FIELD(f.status, 'belum_lunas', 'ditahan', 'lunas', 'dibebaskan')", '', false)
            ->orderBy('f.created_at', 'ASC');

        $stats = $db->table('fines')
            ->select("
                SUM(status = 'belum_lunas') AS jml_belum, COALESCE(SUM(CASE WHEN status = 'belum_lunas' THEN jumlah END), 0) AS rp_belum,
                SUM(status = 'ditahan') AS jml_tahan,     COALESCE(SUM(CASE WHEN status = 'ditahan' THEN jumlah END), 0) AS rp_tahan,
                SUM(status = 'lunas') AS jml_lunas,       COALESCE(SUM(CASE WHEN status = 'lunas' THEN jumlah END), 0) AS rp_lunas,
                SUM(status = 'dibebaskan') AS jml_bebas,  COALESCE(SUM(CASE WHEN status = 'dibebaskan' THEN jumlah_awal END), 0) AS rp_bebas
            ", false)
            ->get()
            ->getRowArray();

        $pager   = \Config\Services::pager();
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 10;
        $total   = $builder->countAllResults(false);

        return view('fines/index', [
            'title'      => 'Denda Keterlambatan',
            'fines'      => $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray(),
            'pager'      => $pager->makeLinks($page, $perPage, $total, 'notika'),
            'page'       => $page,
            'perPage'    => $perPage,
            'status'     => $status,
            'search'     => $search,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'stats'      => $stats,
            'maxDays'    => (new TransactionService())->maxFineDays(),
        ]);
    }

    public function settle($id)
    {
        try {
            $jenis   = (string) $this->request->getPost('jenis');
            $catatan = $this->request->getPost('catatan');

            $fine = (new TransactionService())->settleFine((int) $id, $jenis, $catatan, (int) session('user_id'));

            $nominal = number_format((float) $fine['jumlah'], 0, ',', '.');
            $pesan   = $jenis === 'lunas'
                ? "Denda {$fine['kode_transaksi']} sebesar Rp {$nominal} dicatat LUNAS."
                : "Denda {$fine['kode_transaksi']} sebesar Rp {$nominal} DIBEBASKAN.";

            return redirect()->back()->with('success', $pesan);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
