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

    public function admin()
    {
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
        $trxModel = new TransactionModel();

        $data = [
            'trx_active' => $trxModel->where('status','dipinjam')->countAllResults(),
            'trx_late'   => $trxModel->where('status','terlambat')->countAllResults(),
        ];

        return view('dashboard/staff', $data);
    }

    public function member()
    {
        $trxModel = new TransactionModel();
        $userId = session()->get('user_id');

        $data = [
            'my_books' => $trxModel
                ->where('user_id', $userId)
                ->countAllResults()
        ];

        return view('dashboard/member', $data);
    }
}
