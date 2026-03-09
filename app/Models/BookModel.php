<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table = 'books';
    protected $allowedFields = [
        'kode_buku','judul','pengarang','penerbit',
        'about','stok_total','stok_tersedia',
        'is_active'
    ];
}
