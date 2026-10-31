<?php

namespace App\Controllers;

use App\Models\AppSettingModel;
use App\Models\FinePaymentModel;
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
                      td.lost_reported_at, td.lost_settlement_status, fp.nomor_kwitansi,
                      b.judul, bi.kode_inventaris,
                      u.name AS member_name, u.member_code, u.jenis_pengguna, p.name AS petugas_name')
            ->join('transactions t', 't.id = f.transaction_id')
            ->join('transaction_details td', 'td.id = f.transaction_detail_id', 'left')
            ->join('books b', 'b.id = td.book_id', 'left')
            ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
            ->join('users u', 'u.id = f.user_id')
            ->join('users p', 'p.id = f.diproses_by', 'left')
            ->join('fine_payments fp', 'fp.id = f.payment_id', 'left');

        if (in_array($status, self::STATUSES, true)) {
            $builder->where('f.status', $status);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('u.name', $search)
                ->orLike('u.member_code', $search)
                ->orLike('t.kode_transaksi', $search)
                ->orLike('b.judul', $search)
                ->orLike('fp.nomor_kwitansi', $search)
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

        $stats = (new \App\Models\FineModel())->totals();

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

            // Pelunasan selalu lewat kwitansi
            if ($jenis === 'lunas') {
                return $this->paid((new TransactionService())->payFines([(int) $id], (int) session('user_id'), $catatan));
            }

            $fine = (new TransactionService())->settleFine((int) $id, $jenis, $catatan, (int) session('user_id'));

            $nominal = number_format((float) $fine['jumlah'], 0, ',', '.');

            return redirect()->back()->with('success', "Denda {$fine['kode_transaksi']} sebesar Rp {$nominal} DIBEBASKAN.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /** Lunasi beberapa denda milik satu anggota dalam satu kwitansi. */
    public function pay()
    {
        try {
            $payment = (new TransactionService())->payFines(
                (array) $this->request->getPost('fine_ids'),
                (int) session('user_id'),
                $this->request->getPost('catatan')
            );

            return $this->paid($payment);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /** Cetak kwitansi. Cetakan pertama asli, berikutnya diberi tanda SALINAN. */
    public function receipt($id)
    {
        $model   = new FinePaymentModel();
        $payment = $model->receiptData((int) $id);
        if (!$payment) {
            return redirect()->to('/fines')->with('error', 'Kwitansi tidak ditemukan.');
        }

        $model->update($payment['id'], ['jumlah_cetak' => (int) $payment['jumlah_cetak'] + 1]);

        return view('fines/receipt', [
            'payment' => $payment,
            'salinan' => (int) $payment['jumlah_cetak'] > 0,
            'setting' => (new AppSettingModel())->find(1),
            'backUrl' => '/fines?status=lunas',
        ]);
    }

    private function paid(array $payment)
    {
        $nominal = number_format((float) $payment['total'], 0, ',', '.');

        return redirect()->to('/fines?status=lunas')
            ->with('success', "Pembayaran Rp {$nominal} dicatat LUNAS dengan kwitansi {$payment['nomor_kwitansi']}.")
            ->with('receipt_id', $payment['id']);
    }
}
