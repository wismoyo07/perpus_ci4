<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\AppSettingModel;

class Home extends BaseController
{
    public function index()
    {
        // Redirect jika user sudah login
        if (session()->get('user_id')) {
            switch (session('role')) {
                case 'admin':
                    return redirect()->to('/admin/dashboard');
                case 'staff':
                    return redirect()->to('/staff/dashboard');
                default:
                    return redirect()->to('/member/dashboard');
            }
        }

        $bookModel = new BookModel();
        $settingModel = new AppSettingModel();

        // Ambil data identitas sekolah
        $setting = $settingModel->find(1);
        if (!$setting) {
            $settingModel->insert([
                'id' => 1,
                'nama_lembaga' => 'Perpustakaan Sekolah',
                'logo' => null,
                'kode_lembaga' => '',
                'status_akreditasi' => '',
                'alamat' => '',
                'kepala_sekolah' => '',
                'wakil_kepala_sekolah' => '',
                'website_url' => ''
            ]);
            $setting = $settingModel->find(1);
        }

        // Logika Pencarian Buku
        $search = $this->request->getVar('q');
        if ($search) {
            $books = $bookModel
                ->groupStart()
                    ->like('judul', $search)
                    ->orLike('pengarang', $search)
                    ->orLike('penerbit', $search)
                ->groupEnd()
                ->where('is_active', 1)
                ->findAll(12);
        } else {
            // Default: 8 buku terpopuler/terbaru
            $books = $bookModel
                ->where('is_active', 1)
                ->orderBy('created_at', 'DESC')
                ->findAll(8);
        }

        $data = [
            'setting' => $setting,
            'books' => $books,
            'search' => $search
        ];

        return view('landing_page', $data);
    }
}
