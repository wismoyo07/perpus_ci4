<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table = 'books';
    protected $allowedFields = [
        'kode_buku','judul','pengarang','penerbit',
        'about','category_id','ddc_number',
        'stok_total','stok_tersedia',
        'is_active'
    ];

    /**
     * Sertakan kelas DDC (tabel category) pada query buku.
     */
    public function withDdc()
    {
        return $this->select('books.*, category.code_ddc, category.desc_ddc')
            ->join('category', 'category.id = books.category_id', 'left');
    }
}
