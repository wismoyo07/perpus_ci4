<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AppSettingModel;
use App\Models\FineModel;
use App\Models\FinePaymentModel;
// use Config\Database;

class Member extends BaseController
{
    public function transactions()
    {
        (new \App\Services\TransactionService())->autoLate(); // status terlambat sejak H+1

        $db = \Config\Database::connect();

        // Ambil transaksi milik member
        $transactions = $db->table('transactions t')
            ->select('
                t.*,
                sb.name as staff_borrow,
                sr.name as staff_return
            ')
            ->join('users sb', 'sb.id = t.borrowed_by')
            ->join('users sr', 'sr.id = t.returned_by', 'left')
            ->where('t.user_id', session('user_id'))
            ->orderBy('t.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $transactions = (new FineModel())->attachSummary($transactions);

        // Tambahkan detail buku ke masing-masing transaksi
        foreach ($transactions as &$trx) {

            $trx['details'] = $db->table('transaction_details td')
                ->select('
                    td.*,
                    b.judul
                ')
                ->join('books b', 'b.id = td.book_id')
                ->where('td.transaction_id', $trx['id'])
                ->get()
                ->getResultArray();
        }

        // =========================
        // SUMMARY ALERT SECTION
        // =========================

        $lostPending = $db->table('transaction_details td')
            ->join('transactions t', 't.id = td.transaction_id')
            ->where('t.user_id', session('user_id'))
            ->where('td.is_lost', 1)
            ->where('td.lost_settlement_status', 'pending')
            ->countAllResults();

        $lateCount = $db->table('transactions')
            ->where('user_id', session('user_id'))
            ->where('status', 'terlambat')
            ->countAllResults();

        $dueSoon = $db->table('transactions')
            ->where('user_id', session('user_id'))
            ->where('status', 'dipinjam')
            ->where('DATEDIFF(jatuh_tempo, CURDATE()) <=', 3)
            ->countAllResults();

        return view('member/transactions', [
            'transactions' => $transactions,
            'lostPending'  => $lostPending,
            'lateCount'    => $lateCount,
            'dueSoon'      => $dueSoon,
            'fineTotals'   => (new FineModel())->totals((int) session('user_id')),
        ]);
    }

    /** Denda Saya: daftar denda per buku milik anggota yang login. */
    public function fines()
    {
        (new \App\Services\TransactionService())->autoLate();

        $userId = (int) session('user_id');
        $fines  = \Config\Database::connect()->table('fines f')
            ->select('f.*, t.kode_transaksi, t.jatuh_tempo, t.tanggal_kembali, td.is_lost, td.lost_reported_at,
                      b.judul, bi.kode_inventaris, fp.nomor_kwitansi')
            ->join('transactions t', 't.id = f.transaction_id')
            ->join('transaction_details td', 'td.id = f.transaction_detail_id', 'left')
            ->join('books b', 'b.id = td.book_id', 'left')
            ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
            ->join('fine_payments fp', 'fp.id = f.payment_id', 'left')
            ->where('f.user_id', $userId)
            ->orderBy("FIELD(f.status, 'belum_lunas', 'ditahan', 'lunas', 'dibebaskan')", '', false)
            ->orderBy('f.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Pinjaman terlambat yang dendanya masih berjalan (belum tercatat di tabel fines)
        $lateRunning = \Config\Database::connect()->table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'terlambat')
            ->countAllResults();

        return view('member/fines', [
            'title'       => 'Denda Saya',
            'fines'       => $fines,
            'totals'      => (new FineModel())->totals($userId),
            'lateRunning' => $lateRunning,
        ]);
    }

    /** Kwitansi milik sendiri (selalu ditandai salinan; aslinya dicetak petugas). */
    public function receipt($id)
    {
        $payment = (new FinePaymentModel())->receiptData((int) $id);
        if (!$payment || (int) $payment['user_id'] !== (int) session('user_id')) {
            return redirect()->to('/member/fines')->with('error', 'Kwitansi tidak ditemukan.');
        }

        return view('fines/receipt', [
            'payment' => $payment,
            'salinan' => true,
            'setting' => (new AppSettingModel())->find(1),
            'backUrl' => '/member/fines',
        ]);
    }
}