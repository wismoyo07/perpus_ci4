<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BookModel;
use App\Models\TransactionModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // dd($this->request->getMethod());
        
        if (session()->get('force_password_change')) {
            return redirect()->to('/force-change-password');
        }

        switch (session('role')) {
            case 'admin':
                return redirect()->to('/admin/dashboard');
            case 'staff':
                return redirect()->to('/staff/dashboard');
            default:
                return redirect()->to('/member/dashboard');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AKSES DITOLAK (tujuan redirect RoleFilter)
    |--------------------------------------------------------------------------
    */
    public function unauthorized()
    {
        $this->response->setStatusCode(403);

        return view('auth/unauthorized', [
            'title'   => 'Akses Ditolak',
            'message' => session()->getFlashdata('error') ?? 'Anda tidak memiliki akses ke halaman ini.',
        ]);
    }

    public function admin()
    {
        (new \App\Services\TransactionService())->autoLate(); // status terlambat sejak H+1

        $userModel = new UserModel();
        $bookModel = new BookModel();
        $trxModel  = new TransactionModel();
        $logModel  = new \App\Models\ActivityLogModel();

        // Get 5 latest transactions
        $recentTrx = $trxModel->select('transactions.*, users.name as member_name')
            ->join('users', 'users.id = transactions.user_id', 'left')
            ->orderBy('transactions.id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Get 5 latest activity logs
        $recentLogs = $logModel->select('activity_logs.*, users.name as user_name')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->orderBy('activity_logs.id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $data = [
            'total_users'  => $userModel->countAll(),
            'total_books'  => $bookModel->countAll(),
            'total_trx'    => $trxModel->countAll(),
            'total_late'   => $trxModel->where('status','terlambat')->countAllResults(),
            'recent_trx'   => $recentTrx,
            'recent_logs'  => $recentLogs
        ];

        return view('dashboard/admin', $data);
    }

    public function staff()
    {
        (new \App\Services\TransactionService())->autoLate(); // status terlambat sejak H+1

        $trxModel = new TransactionModel();

        $data = [
            'trx_active' => $trxModel->where('status','dipinjam')->countAllResults(),
            'trx_late'   => $trxModel->where('status','terlambat')->countAllResults(),
        ];

        return view('dashboard/staff', $data);
    }

    public function member()
    {
        (new \App\Services\TransactionService())->autoLate(); // status terlambat sejak H+1

        $trxModel = new TransactionModel();
        $userId = session()->get('user_id');

        $data = [
            // Buku yang masih di tangan anggota (belum dikembalikan dan tidak dilaporkan hilang)
            'my_books' => \Config\Database::connect()->table('transaction_details td')
                ->join('transactions t', 't.id = td.transaction_id')
                ->where('t.user_id', $userId)
                ->whereIn('t.status', ['dipinjam', 'terlambat'])
                ->where('td.is_lost', 0)
                ->where('td.returned_at', null)
                ->countAllResults()
        ];

        return view('dashboard/member', $data);
    }
}
