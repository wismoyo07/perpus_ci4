<?php

namespace App\Controllers;

use App\Models\AppSettingModel;
use App\Models\FineModel;
use App\Models\UserModel;
use App\Services\AccountService;
use Config\Database;

/**
 * Profil Saya (semua role). Pengguna hanya boleh mengubah kontak (telepon, WhatsApp)
 * dan password sendiri; data identitas (nama, NIS/NIP, NISN/NUPTK, kelas, jenis, role)
 * diubah staf lewat menu Pengguna.
 */
class Profile extends BaseController
{
    public function index()
    {
        $userId = (int) session('user_id');
        $user   = (new UserModel())->find($userId);
        if (!$user) {
            return redirect()->to('/logout');
        }

        $data = ['title' => 'Profil Saya', 'user' => $user];

        if ($user['role'] === 'member') {
            (new \App\Services\TransactionService())->autoLate();

            $data['activeBooks'] = Database::connect()->table('transaction_details td')
                ->join('transactions t', 't.id = td.transaction_id')
                ->where('t.user_id', $userId)
                ->whereIn('t.status', ['dipinjam', 'terlambat'])
                ->where('td.is_lost', 0)
                ->where('td.returned_at', null)
                ->countAllResults();
            $data['fineTotals'] = (new FineModel())->totals($userId);
        }

        return view('profile/index', $data);
    }

    public function updateContact()
    {
        $rules = [
            'phone' => [
                'rules'  => 'required|regex_match[/^[0-9+\-\s]{9,20}$/]',
                'errors' => [
                    'required'    => 'Nomor telepon wajib diisi.',
                    'regex_match' => 'Nomor telepon 9–20 karakter, hanya angka, spasi, + atau -.',
                ],
            ],
            'whatsapp' => [
                'rules'  => 'required|regex_match[/^[0-9+\-\s]{9,20}$/]',
                'errors' => [
                    'required'    => 'Nomor WhatsApp wajib diisi.',
                    'regex_match' => 'Nomor WhatsApp 9–20 karakter, hanya angka, spasi, + atau -.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/profile')->withInput()->with('contact_errors', $this->validator->getErrors());
        }

        (new UserModel())->update((int) session('user_id'), [
            'phone'    => trim((string) $this->request->getPost('phone')),
            'whatsapp' => trim((string) $this->request->getPost('whatsapp')),
        ]);

        return redirect()->to('/profile')->with('success', 'Kontak berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $errors = (new AccountService())->changePassword(
            (int) session('user_id'),
            (string) $this->request->getPost('current_password'),
            (string) $this->request->getPost('password'),
            (string) $this->request->getPost('confirm_password')
        );

        if ($errors !== []) {
            return redirect()->to('/profile#password')->with('password_errors', $errors);
        }

        return redirect()->to('/profile')->with('success', 'Password berhasil diganti.');
    }

    /** Cetak kartu anggota milik sendiri (memakai tampilan kartu dari menu Pengguna). */
    public function card()
    {
        $user = (new UserModel())->find((int) session('user_id'));

        helper('barcode');

        return view('users/cards', [
            'users'   => $user ? [$user] : [],
            'setting' => (new AppSettingModel())->find(1) ?? [
                'nama_lembaga' => 'Perpustakaan Sekolah',
                'logo'         => null,
                'alamat'       => '',
            ],
        ]);
    }
}
