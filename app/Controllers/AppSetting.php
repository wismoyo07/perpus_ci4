<?php

namespace App\Controllers;

use App\Models\AppSettingModel;
use App\Models\ActivityLogModel;

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
            'nama_lembaga' => 'required|min_length[3]|max_length[200]',
            'kode_lembaga' => 'permit_empty|max_length[50]',
            'kode_barang_buku' => 'required|max_length[30]|regex_match[/^[0-9][0-9.]*$/]',
            'status_akreditasi' => 'permit_empty|max_length[10]',
            'alamat' => 'permit_empty',
            'kepala_sekolah' => 'permit_empty|max_length[150]',
            'wakil_kepala_sekolah' => 'permit_empty|max_length[150]',
            'website_url' => 'permit_empty|max_length[255]'
        ];

        $logoFile = $this->request->getFile('logo');
        $logoName = $setting['logo'];

        // Jika ada logo diunggah, tambahkan validasi logo
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $rules['logo'] = 'uploaded[logo]|is_image[logo]|mime_in[logo,image/jpg,image/jpeg,image/png]|max_size[logo,2048]';
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
            'status_akreditasi' => $this->request->getPost('status_akreditasi'),
            'alamat' => $this->request->getPost('alamat'),
            'kepala_sekolah' => $this->request->getPost('kepala_sekolah'),
            'wakil_kepala_sekolah' => $this->request->getPost('wakil_kepala_sekolah'),
            'website_url' => $this->request->getPost('website_url'),
        ]);

        // Catat aktivitas log
        $logModel = new ActivityLogModel();
        $logModel->insert([
            'user_id' => session()->get('user_id'),
            'aktivitas' => "Mengupdate identitas sekolah",
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()
        ]);

        return redirect()->to('/admin/settings')->with('success', 'Identitas sekolah berhasil diperbarui!');
    }
}
