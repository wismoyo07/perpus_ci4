<?php

namespace App\Controllers;

use App\Models\AppSettingModel;
use App\Services\LoggingService;

class AppSetting extends BaseController
{
    public function index()
    {
        $settingModel = new AppSettingModel();
        $setting = $settingModel->find(1);

        // Self-healing jika data baris pertama kosong
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

        $data = [
            'title' => 'Pengaturan Identitas Sekolah',
            'setting' => $setting,
            'validation' => \Config\Services::validation()
        ];

        return view('settings/index', $data);
    }

    public function update()
    {
        $settingModel = new AppSettingModel();
        $setting = $settingModel->find(1);

        if (!$setting) {
            return redirect()->to('/admin/settings')->with('error', 'Data pengaturan tidak ditemukan.');
        }

        // Aturan validasi
        $rules = [
            'nama_lembaga' => [
                'rules'  => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required'   => 'Nama sekolah / lembaga wajib diisi.',
                    'min_length' => 'Nama sekolah / lembaga minimal 3 karakter.',
                    'max_length' => 'Nama sekolah / lembaga maksimal 200 karakter.',
                ],
            ],
            'kode_lembaga' => [
                'rules'  => 'permit_empty|max_length[50]',
                'errors' => ['max_length' => 'Kode lembaga maksimal 50 karakter.'],
            ],
            'kode_barang_buku' => [
                'rules'  => 'required|max_length[30]|regex_match[/^[0-9][0-9.]*$/]',
                'errors' => [
                    'required'    => 'Kode barang buku wajib diisi.',
                    'max_length'  => 'Kode barang buku maksimal 30 karakter.',
                    'regex_match' => 'Kode barang buku hanya boleh berisi angka dan titik, diawali angka (contoh: 03.11.01.01.001).',
                ],
            ],
            'maks_hari_denda' => [
                'rules'  => 'required|is_natural|less_than_equal_to[365]',
                'errors' => [
                    'required'           => 'Batas maksimal hari denda wajib diisi.',
                    'is_natural'         => 'Batas maksimal hari denda harus berupa angka bulat 0 atau lebih.',
                    'less_than_equal_to' => 'Batas maksimal hari denda paling banyak 365 hari.',
                ],
            ],
            'status_akreditasi' => [
                'rules'  => 'permit_empty|max_length[50]',
                'errors' => ['max_length' => 'Status akreditasi maksimal 50 karakter.'],
            ],
            'alamat' => 'permit_empty',
            'kepala_sekolah' => [
                'rules'  => 'permit_empty|max_length[150]',
                'errors' => ['max_length' => 'Nama kepala sekolah maksimal 150 karakter.'],
            ],
            'wakil_kepala_sekolah' => [
                'rules'  => 'permit_empty|max_length[150]',
                'errors' => ['max_length' => 'Nama wakil kepala sekolah maksimal 150 karakter.'],
            ],
            'website_url' => [
                'rules'  => 'permit_empty|max_length[255]',
                'errors' => ['max_length' => 'Website / kontak maksimal 255 karakter.'],
            ],
        ];

        $logoFile = $this->request->getFile('logo');
        $logoName = $setting['logo'];

        // Jika ada logo diunggah, tambahkan validasi logo
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $rules['logo'] = [
                'rules'  => 'uploaded[logo]|is_image[logo]|mime_in[logo,image/jpg,image/jpeg,image/png]|max_size[logo,2048]',
                'errors' => [
                    'uploaded' => 'Logo gagal diunggah.',
                    'is_image' => 'Logo harus berupa gambar.',
                    'mime_in'  => 'Logo harus berformat PNG, JPG, atau JPEG.',
                    'max_size' => 'Ukuran logo maksimal 2 MB.',
                ],
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Proses upload logo
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            // Hapus logo lama jika ada fisik filenya
            if ($setting['logo'] && file_exists(FCPATH . 'uploads/settings/' . $setting['logo'])) {
                @unlink(FCPATH . 'uploads/settings/' . $setting['logo']);
            }

            // Pindahkan file logo baru
            $newName = $logoFile->getRandomName();
            $logoFile->move(FCPATH . 'uploads/settings/', $newName);
            $logoName = $newName;
        }

        // Simpan perubahan ke database
        $settingModel->update(1, [
            'nama_lembaga' => $this->request->getPost('nama_lembaga'),
            'logo' => $logoName,
            'kode_lembaga' => $this->request->getPost('kode_lembaga'),
            'kode_barang_buku' => trim($this->request->getPost('kode_barang_buku')),
            'maks_hari_denda' => (int) $this->request->getPost('maks_hari_denda'),
            'status_akreditasi' => $this->request->getPost('status_akreditasi'),
            'alamat' => $this->request->getPost('alamat'),
            'kepala_sekolah' => $this->request->getPost('kepala_sekolah'),
            'wakil_kepala_sekolah' => $this->request->getPost('wakil_kepala_sekolah'),
            'website_url' => $this->request->getPost('website_url'),
        ]);

        // Catat aktivitas log (beserta IP & user agent)
        (new LoggingService())->log('Mengupdate identitas sekolah');

        return redirect()->to('/admin/settings')->with('success', 'Identitas sekolah berhasil diperbarui!');
    }
}
