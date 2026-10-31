<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Denda keterlambatan per buku (satu baris per transaction_detail).
 *
 * Status: ditahan (buku dilaporkan hilang, menunggu penyelesaian),
 * belum_lunas, lunas, dibebaskan.
 */
class FineModel extends Model
{
    protected $table = 'fines';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'transaction_id',
        'transaction_detail_id',
        'user_id',
        'hari_terlambat',
        'hari_dikenakan',
        'tarif',
        'jumlah_awal',
        'jumlah',
        'status',
        'catatan',
        'diproses_by',
        'diproses_at',
    ];
}
