<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    

    protected $allowedFields = [
        'name',
        'username',
        'password',
        'role',
        'jenis_pengguna',
        'nomor_induk',
        'is_validated',
        'is_archived',
        'must_change_password',
        'last_login'
    ];

    public function getUser(string $identity)
    {
        $identity = trim($identity);
        $identity = preg_replace('/\s+/', '', $identity);

        return $this->groupStart()
                        ->where('username', $identity)
                        ->orWhere('nomor_induk', $identity)
                    ->groupEnd()
                    ->first();
    }

    
    // protected $useTimestamps = false;
    protected $useTimestamps = true;
    protected $returnType = 'array';
}
