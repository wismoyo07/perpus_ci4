<?php

namespace App\Models;

use CodeIgniter\Model;

class BookItemModel extends Model
{
    protected $table = 'book_items';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'book_id',
        'kode_inventaris',
        'sumber_dana',
        'tahun_pengadaan',
        'nomor_urut',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
