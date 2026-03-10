<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassModel extends Model
{
    protected $table      = 'class'; // Nama tabel Anda
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['class_name']; // Sesuaikan

    // Fungsi untuk mengambil semua data layanan
    public function getClass()
    {
        return $this->findAll();
    }
}