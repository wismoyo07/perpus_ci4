<?php

namespace App\Services;

use Config\Database;
use Config\Services;

class LoggingService
{
    protected $db;
    protected $request;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->request = Services::request();
    }

    public function log(
        string $activity,
        ?int $targetUserId = null,
        ?int $actorId = null
    ): void {

        $this->db->table('activity_logs')->insert([
            'user_id'        => $actorId ?? session('user_id'),
            'target_user_id' => $targetUserId,
            'aktivitas'      => $activity,
            'ip_address'     => $this->request->getIPAddress(),
            'user_agent'     => $this->request->getUserAgent()->getAgentString(),
            'created_at'     => date('Y-m-d H:i:s')
        ]);
    }
}