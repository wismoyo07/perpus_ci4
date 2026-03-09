<?php

namespace App\Controllers;


class ActivityLogs extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $logs = $db->table('activity_logs')
            ->orderBy('created_at','DESC')
            ->limit(100)
            ->get()
            ->getResultArray();

        return view('logs/index', [
            'logs' => $logs
        ]);
    }
}