<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Config\Database;

use App\Services\LoggingService;

class Users extends BaseController
{
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db = Database::connect();
    }

    /* ===============================
       LIST USER
    =============================== */

    public function index()
    {
        $this->response->setHeader('Cache-Control', 'no-store, max-age=0, no-cache');

        $status = $this->request->getGet('status');
        $roleFilter = $this->request->getGet('role');
        $search = $this->request->getGet('search');

        $currentRole = session('role');
        $currentUserId = session('user_id');

        $builder = $this->userModel;

        // Staff hanya lihat member
        if ($currentRole === 'staff') {
            $builder = $builder->where('role','member');
        }

        // Admin dan Staff tidak boleh melihat dirinya sendiri
        $builder = $builder->where('id !=', $currentUserId);

        if ($status === 'archived') {
            $builder = $builder->where('is_archived', 1);
        } elseif ($status === 'active') {
            $builder = $builder->where('is_archived', 0);
        }

        // SEARCH
        if (!empty($search)) {
            $builder = $builder->groupStart()
                ->like('name', $search)
                ->orLike('username', $search)
                ->orLike('member_code', $search)
                ->groupEnd();
        }

        if ($roleFilter) {
            $builder = $builder->where('role', $roleFilter);
        }

        $users = $builder->paginate(10); // 10 per halaman
        $pager = $this->userModel->pager;

        return view('users/index', [
            'users' => $users,
            'pager' => $pager,
            'status' => $status,
            'role'   => $roleFilter,
            'search' => $search
        ]);
    }


    /* ===============================
       CREATE & STORE
    =============================== */
    public function create()
    {
        return view('users/create', [
            'list_kelas' => (new \App\Models\ClassModel())->getClass(),
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'Nama wajib diisi.',
                    'min_length' => 'Nama minimal 3 karakter.',
                    'max_length' => 'Nama maksimal 100 karakter.',
                ],
            ],
            'username' => [
                'rules'  => 'required|min_length[4]|max_length[50]|regex_match[/^[A-Za-z0-9_.\-]+$/]|is_unique[users.username]',
                'errors' => [
                    'required'    => 'Username wajib diisi.',
                    'min_length'  => 'Username minimal 4 karakter.',
                    'max_length'  => 'Username maksimal 50 karakter.',
                    'regex_match' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda minus (tanpa spasi).',
                    'is_unique'   => 'Username sudah dipakai pengguna lain.',
                ],
            ],
            'jenis_pengguna' => [
                'rules'  => 'required|in_list[siswa,guru,pegawai]',
                'errors' => [
                    'required' => 'Jenis pengguna wajib dipilih.',
                    'in_list'  => 'Jenis pengguna tidak valid.',
                ],
            ],
        ] + self::identityRules() + self::contactRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $jenis = $this->request->getPost('jenis_pengguna');

        $newId = $this->userModel->insert([
            'name' => trim((string) $this->request->getPost('name')),
            'username' => trim((string) $this->request->getPost('username')),
            'password' => hashPassword('123456'),
            'role' => 'member',
            'jenis_pengguna' => $jenis,
            'member_code' => trim((string) $this->request->getPost('member_code')),
            'member_code_nation' => trim((string) $this->request->getPost('member_code_nation')) ?: null,
            'class' => $jenis === 'siswa' ? (trim((string) $this->request->getPost('class')) ?: null) : null,
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
            'whatsapp' => trim((string) $this->request->getPost('whatsapp')) ?: null,
            'is_validated' => 1,
            'must_change_password' => 1
        ]);

        $this->logActivity('Membuat user baru', (int) $newId);

        return redirect()->to('/users')->with('success', 'User dibuat. Password awal 123456 dan wajib diganti saat login pertama.');
    }

    /**
     * Aturan NIS/NIP & NISN/NUPTK (unik). $exceptId diisi saat update agar data sendiri tidak dianggap ganda.
     */
    private static function identityRules(?int $exceptId = null): array
    {
        $except = $exceptId ? ',id,' . $exceptId : '';

        return [
            'member_code' => [
                'rules'  => 'required|max_length[50]|is_unique[users.member_code' . $except . ']',
                'errors' => [
                    'required'   => 'NIS / NIP wajib diisi.',
                    'max_length' => 'NIS / NIP maksimal 50 karakter.',
                    'is_unique'  => 'NIS / NIP sudah dipakai pengguna lain.',
                ],
            ],
            'member_code_nation' => [
                'rules'  => 'permit_empty|max_length[50]|is_unique[users.member_code_nation' . $except . ']',
                'errors' => [
                    'max_length' => 'NISN / NUPTK maksimal 50 karakter.',
                    'is_unique'  => 'NISN / NUPTK sudah dipakai pengguna lain.',
                ],
            ],
        ];
    }

    /** Aturan kelas, telepon, dan WhatsApp (opsional). */
    private static function contactRules(): array
    {
        return [
            'class' => [
                'rules'  => 'permit_empty|max_length[50]',
                'errors' => ['max_length' => 'Kelas maksimal 50 karakter.'],
            ],
            'phone' => [
                'rules'  => 'permit_empty|regex_match[/^[0-9+\-\s]{9,20}$/]',
                'errors' => ['regex_match' => 'Nomor telepon 9–20 karakter, hanya angka, spasi, + atau -.'],
            ],
            'whatsapp' => [
                'rules'  => 'permit_empty|regex_match[/^[0-9+\-\s]{9,20}$/]',
                'errors' => ['regex_match' => 'Nomor WhatsApp 9–20 karakter, hanya angka, spasi, + atau -.'],
            ],
        ];
    }

    /* ===============================
       ARCHIVE
    =============================== */
    public function archive($id)
    {
        $user = $this->userModel->find($id);

        if (!$user['is_validated']) {
            return redirect()->back()
                ->with('error','User belum divalidasi.');
        }

        if (!$user) {
            return redirect()->back()->with('error','User tidak ditemukan.');
        }

        $this->userModel->update($id, [
            'is_archived' => $user['is_archived'] ? 0 : 1
        ]);

        $action = $user['is_archived'] ? 'Aktifkan' : 'Archive';
        
        $this->logActivity(
            "Archive user",
            $id
        );

        return redirect()->back()
            ->with('success','Status user diperbarui.');
    }

    /* ===============================
       ACTIVATE
    =============================== */

    public function activate($id)
    {
        $this->userModel->update($id,[
            'is_validated' => 1
        ]);

        $this->logActivity(
            "Activate user",
            $id
        );

        return redirect()->back();
        
    }

    /* ===============================
       RESET PASSWORD STAFF
    =============================== */

    public function resetPassword($id)
    {
        $targetUser = $this->userModel->find($id);
        $currentRole = session('role');

        // Jika target belum di validasi
        if (!$targetUser['is_validated']) {
            return redirect()->back()
                ->with('error','User belum divalidasi.');
        }

        // Jika target adalah admin
        if ($targetUser['role'] === 'admin' 
            && $currentRole !== 'admin') {

            return redirect()->back()
                ->with('error','Hanya admin yang bisa reset password admin.');
        }

        $newPassword = '123456';

        $this->userModel->update($id,[
            'password' => hashPassword($newPassword),
            'must_change_password' => 1
        ]);

        $this->logActivity(
            "Reset password",
            $id
        );

        return redirect()->back()
        ->with('success',"Password sementara: {$newPassword}");
    }

    /* ===============================
       STAFF LIST
    =============================== */

    /* ===============================
       CETAK KARTU ANGGOTA
    =============================== */

    public function cards()
    {
        $ids = array_filter(array_map('intval', (array) $this->request->getGet('selected')));

        if (empty($ids)) {
            return redirect()->to('/users')
                ->with('error', 'Pilih minimal 1 user untuk dicetak kartunya.');
        }

        $builder = $this->userModel->whereIn('id', $ids);

        // Staff hanya boleh mencetak kartu member
        if (session('role') === 'staff') {
            $builder = $builder->where('role', 'member');
        }

        $setting = (new \App\Models\AppSettingModel())->find(1) ?? [
            'nama_lembaga' => 'Perpustakaan Sekolah',
            'logo'         => null,
            'alamat'       => '',
        ];

        helper('barcode');

        return view('users/cards', [
            'users'   => $builder->orderBy('name', 'ASC')->findAll(),
            'setting' => $setting,
        ]);
    }

    public function staffIndex()
    {
        $data['staff'] = $this->userModel
            ->where('role','staff')
            ->findAll();

        return view('users/staff_index', $data);
    }


    public function changeRole($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->back()
                ->with('error','User tidak ditemukan.');
        }

        if (!in_array($user['jenis_pengguna'], ['guru','pegawai'])) {
            return redirect()->back()
                ->with('error','Hanya guru/pegawai bisa menjadi staff.');
        }

        // 🔴 Proteksi admin terakhir (sebelum update)
        if ($user['role'] === 'admin') {

            $adminCount = $this->userModel
                ->where('role','admin')
                ->where('is_archived',0)
                ->countAllResults();

            if ($adminCount <= 1) {
                return redirect()->back()
                    ->with('error','Tidak bisa menurunkan admin terakhir.');
            }
        }

        // Tentukan role baru
        $newRole = $user['role'] === 'staff' ? 'member' : 'staff';

        // Update role
        $this->userModel->update($id, [
            'role' => $newRole
        ]);

        // 🔥 Logging
        $logger = new LoggingService();
        $logger->log(
            "Change role menjadi {$newRole}",
            $id
        );

        return redirect()->back()
            ->with('success','Role berhasil diubah.');
    }


    /* ===============================
                DELETE USER
    =============================== */
    public function delete($id)
    {
        $targetUser = $this->userModel->find($id);

        if (!$targetUser) {
            return redirect()->back()
                ->with('error','User tidak ditemukan.');
        }

        // 🔴 Proteksi: Tidak boleh hapus admin terakhir
        if ($targetUser['role'] === 'admin') {

            $adminCount = $this->userModel
                ->where('role','admin')
                ->countAllResults();

            if ($adminCount <= 1) {
                return redirect()->back()
                    ->with('error','Tidak bisa menghapus admin terakhir.');
            }
        }

        // 🔴 Cek histori transaksi
        $hasTransaction = $this->db->table('transactions')
            ->where('user_id',$id)
            ->countAllResults();

        if ($hasTransaction > 0) {
            return redirect()->back()
                ->with('error','User memiliki riwayat transaksi.');
        }

        $this->userModel->delete($id);

        $this->logActivity(
            "Delete user",
            $id
        );

        return redirect()->to('/users')
        ->with('success','User berhasil dihapus.');

        return redirect()->back()
        ->with('error','User memiliki riwayat transaksi.');
    }


    /* ===============================
            EDIT dan UPDATE USER
    =============================== */
    public function edit($id)
    {
        $data['user'] = $this->userModel->find($id);
        if (!$data['user']) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan.');
        }

        $data['list_kelas'] = (new \App\Models\ClassModel())->getClass();
        return view('users/edit', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $currentRole = session('role');

        // Validation rules
        $rules = [
            'name' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'Nama wajib diisi.',
                    'min_length' => 'Nama minimal 3 karakter.',
                    'max_length' => 'Nama maksimal 100 karakter.',
                ],
            ],
            'jenis_pengguna' => [
                'rules'  => 'required|in_list[siswa,guru,pegawai]',
                'errors' => [
                    'required' => 'Jenis pengguna wajib dipilih.',
                    'in_list'  => 'Jenis pengguna tidak valid.',
                ],
            ],
        ] + self::contactRules();

        // Unique validation of NIS/NIP and NISN/NUPTK only if Admin is updating
        if ($currentRole === 'admin') {
            $rules += self::identityRules((int) $id);
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'jenis_pengguna' => $this->request->getPost('jenis_pengguna'),
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
            'whatsapp' => trim((string) $this->request->getPost('whatsapp')) ?: null,
        ];
        if ($data['jenis_pengguna'] === 'siswa') {
            $data['class'] = trim((string) $this->request->getPost('class')) ?: null;
        }

        // Only Admin can update member_code and member_code_nation
        if ($currentRole === 'admin') {
            $data['member_code'] = $this->request->getPost('member_code');
            $data['member_code_nation'] = $this->request->getPost('member_code_nation') ?: null;
        }

        // =========================
        // ROLE UPDATE (ADMIN ONLY)
        // =========================
        if ($currentRole === 'admin'
            && in_array($user['jenis_pengguna'], ['guru','pegawai'])) {

            $newRole = $this->request->getPost('role');

            if ($newRole) {

                // 🔴 Proteksi admin terakhir
                if ($user['role'] === 'admin' 
                    && $newRole !== 'admin') {

                    $adminCount = $this->userModel
                        ->where('role','admin')
                        ->countAllResults();

                    if ($adminCount <= 1) {
                        return redirect()->back()
                            ->with('error',
                            'Tidak bisa menurunkan admin terakhir.');
                    }
                }

                $data['role'] = $newRole;
            }
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/users')
            ->with('success','User berhasil diupdate.');
    }


    // =========================
    // ROLE VALIDASI
    // =========================
    public function validateUser($id)
    {
        $this->userModel->update($id,[
            'is_validated' => 1
        ]);

        $this->logActivity(
            "Validasi user",
            $id
        );

        return redirect()->back()
            ->with('success','User tervalidasi.');
    }

    
    // =========================
    // BulkAction Selection
    // =========================
    public function bulkAction()
    {
        $ids = $this->request->getPost('selected');
        $action = $this->request->getPost('action');

        if (!$ids || !$action) {
            return redirect()->back()
                ->with('error','Tidak ada aksi dipilih.');
        }

        $userModel = $this->userModel;

        // ============================
        // VALIDASI ADMIN TERAKHIR
        // ============================
        if ($action === 'archive') {

            $adminActiveCount = $userModel
                ->where('role','admin')
                ->where('is_archived',0)
                ->countAllResults();

            $adminSelectedCount = $userModel
                ->whereIn('id', $ids)
                ->where('role','admin')
                ->where('is_archived',0)
                ->countAllResults();

            if ($adminActiveCount == $adminSelectedCount) {
                return redirect()->back()
                    ->with('error','Tidak bisa mengarsipkan admin terakhir.');
            }
        }

        // ============================
        // UPDATE
        // ============================

        $value = $action === 'archive' ? 1 : 0;

        foreach ($ids as $id) {
            foreach ($ids as $id) {
                $this->logActivity(
                    "Bulk {$action} user",
                    $id
                );
            }
        }

        $userModel->whereIn('id', $ids)
                ->set(['is_archived' => $value])
                ->update();

        return redirect()->back()
            ->with('success','Bulk action berhasil.');
    }
    
}