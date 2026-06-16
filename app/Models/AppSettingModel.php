<?php

namespace App\Models;

use CodeIgniter\Model;

class AppSettingModel extends Model
{
    protected $table = 'app_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'logo', 'kode_lembaga', 'nama_lembaga', 'status_akreditasi',
        'alamat', 'kepala_sekolah', 'wakil_kepala_sekolah', 'website_url'
    ];
    protected $useTimestamps = true;
}
