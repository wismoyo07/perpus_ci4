<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'kode_transaksi',
        'user_id',
        'tanggal_pinjam',
        'jatuh_tempo',
        'tanggal_kembali',
        'status',
        'total_denda',
        'borrowed_by',
        'returned_by'
    ];

    protected $useTimestamps = true;
}