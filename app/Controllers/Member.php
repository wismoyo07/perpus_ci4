<?php

namespace App\Controllers;

use App\Controllers\BaseController;
// use Config\Database;

class Member extends BaseController
{
    public function transactions()
    {
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
            'dueSoon'      => $dueSoon
        ]);

        $dueSoon = $db->table('transactions')
            ->where('user_id', session('user_id'))
            ->where('status', 'dipinjam')
            ->where('DATEDIFF(jatuh_tempo, CURDATE()) =', 2)
            ->countAllResults();
    }
}