<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ClassModel;
use App\Models\ActivityLogModel;
use CodeIgniter\Controller;

use App\Services\LoggingService;
use PhpParser\Builder\Function_;

class Auth extends Controller
{
    protected $userModel;
    protected $logModel;
    protected $session;


    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel  = new ActivityLogModel();
        $this->session   = session();

        helper(['form', 'url', 'password']);
    }

    // =====================================================
    // REGISTER MEMBER
    // =====================================================
    public function register()
    {
        if ($this->request->getMethod() === 'post') {

            helper('password');

            $userModel = new UserModel();

            $data = [
                'name'                  => trim($this->request->getPost('name')),
                'member_code'           => trim($this->request->getPost('member_code')),
                'member_code_nation'    => trim($this->request->getPost('member_code_nation')),
                'class'                 => trim($this->request->getPost('class')),
                'phone'                 => trim($this->request->getPost('phone')),
                'whatsapp'              => trim($this->request->getPost('whatsapp')),
                'username'              => trim($this->request->getPost('username')),
                'password'              => hashPassword($this->request->getPost('password')),
                'role'                  => 'member',
                'jenis_pengguna'        => trim($this->request->getPost('jenis_pengguna')),
                'is_validated'          => 0,
                'is_archived'           => 0,
                'must_change_password'  => 0
            ];

            $userModel->insert($data);

            return redirect()->to('/auth/login')
                ->with('success','Registrasi berhasil. Tunggu validasi admin.');
        }

        $classModel = new ClassModel();
        
        // Mengambil data untuk dropdown
        $data['list_kelas'] = $classModel->getClass();
        
        return view('auth/register', $data);

        // return view('auth/register');
    }

    public function processRegister()
    {
        $rules = [

            'name' => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'Nama wajib diisi.',
                    'min_length' => 'Nama minimal 3 karakter.'
                ]
            ],

            'username' => [
                'rules'  => 'required|min_length[4]|is_unique[users.username]',
                'errors' => [
                    'required'   => 'Username wajib diisi.',
                    'min_length' => 'Username minimal 4 karakter.',
                    'is_unique'  => 'Username sudah digunakan.'
                ]
            ],

            'password' => [
                'rules'  => 'required|strong_password[username]',
                'errors' => [
                    'required' => 'Password wajib diisi.',
                ]
            ],

            'confirm_password' => [
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi.',
                    'matches'  => 'Konfirmasi password tidak cocok.'
                ]
            ],

            'jenis_pengguna' => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required' => ' Jenis Pengguna wajib diisi.'
                ]
            ],
            
            'member_code' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'NIS / NIP wajib diisi.'
                ]
            ],
            'phone' => [
                'rules'  => 'required|min_length[9]|max_length[20]',
                'errors' => [
                    'required' => 'Nomor telepon wajib diisi.',
                    'min_length' => 'Nomor telepon minimal 9 karakter.',
                    'max_length' => 'Nomor telepon maksimal 20 karakter.'
                ]
            ],
            'whatsapp' => [
                'rules'  => 'required|min_length[9]|max_length[20]',
                'errors' => [
                    'required' => 'Nomor WhatsApp wajib diisi.',
                    'min_length' => 'Nomor WhatsApp minimal 9 karakter.',
                    'max_length' => 'Nomor WhatsApp maksimal 20 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'      => $this->request->getPost('name'),
            'username'  => $this->request->getPost('username'),
            // 'password'  => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT), password default CI4
            'password'  => hashPassword($this->request->getPost('password')),
            'role'      => 'member',
            'jenis_pengguna' => $this->request->getPost('jenis_pengguna'),
            'member_code' => $this->request->getPost('member_code'),
            'member_code_nation' => $this->request->getPost('member_code_nation'),
            'class' => $this->request->getPost('class'),
            'phone' => trim($this->request->getPost('phone')),
            'whatsapp' => trim($this->request->getPost('whatsapp'))
        ];

        $newUserId = $this->userModel->insert($data);

        $logger = new LoggingService();
        $logger->log(
            "Registrasi akun baru",
            $newUserId,
            $newUserId
        );

        return redirect()->to('/auth/login')
            ->with('success', 'Registrasi berhasil.');
    }


    public function login()
    {
        if ($this->request->getMethod() === 'post') {

            helper('password');

            $identity = $this->request->getPost('identity');
            $password = $this->request->getPost('password');

            if (!$identity || !$password) {
                return redirect()->back()
                    ->with('error','Username/Nomor Induk dan password wajib diisi.');
            }

            $userModel = new UserModel();
            $user = $userModel->getUser($identity);

            if (!$user) {
                return redirect()->back()
                    ->with('error','User tidak ditemukan.');
            }

            if (
                !verifyPassword($password, $user['password']) &&
                !password_verify($password, $user['password'])
            ) {
                return redirect()->back()
                    ->with('error','Password salah.');
            }

            if (!$user['is_validated']) {
                return redirect()->back()
                    ->with('error','Akun belum divalidasi.');
            }

            if ($user['is_archived']) {
                return redirect()->back()
                    ->with('error','Akun diarsipkan.');
            }

            if ($user['must_change_password']) {

                session()->set([
                    'user_id'   => $user['id'],
                    'name'      => $user['name'],
                    'role'      => $user['role'],
                    'logged_in' => true,
                    'force_password_change' => true
                ]);

                return redirect()->to('/force-change-password');
            }

            session()->set([
                'user_id'   => $user['id'],
                'name'      => $user['name'],
                'role'      => $user['role'],
                'logged_in' => true
            ]);

            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }


        // ===============================
        // FORCE CHANGE PASSWORD
        // ===============================
    public function forceChange()
    {
        if (!session()->get('force_password_change')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/force_change');
    }

    public function processForceChange()
    {
        $errors = (new \App\Services\AccountService())->changePassword(
            (int) session('user_id'),
            null,
            (string) $this->request->getPost('password'),
            (string) $this->request->getPost('confirm_password')
        );

        if ($errors !== []) {
            return redirect()->back()->with('errors', $errors);
        }

        session()->remove('force_password_change');

        return redirect()->to('/dashboard')
            ->with('success','Password berhasil diubah.');
    }


    public function processLogin()
    {
        helper('password');

        $identity = trim($this->request->getPost('identity'));
        $password = trim($this->request->getPost('password'));

        if (!$identity || !$password) {
            return redirect()->back()
                ->with('error', 'Username / Nomor Induk dan password wajib diisi.');
        }

        $userModel = new UserModel();
        $user = $userModel->getUser($identity);

        if (!$user) {
            $logger = new LoggingService();

            $logger->log("Login gagal - user tidak ditemukan");

            return redirect()->back()
                ->with('error', 'User tidak ditemukan.');
        }

        // Verifikasi password (support pepper + fallback legacy)
        if (
            !verifyPassword($password, $user['password']) &&
            !password_verify($password, $user['password'])
        ) {
            
            $logger = new LoggingService();

            $logger->log("Login gagal - password salah", $user['id']);
            
            return redirect()->back()
                ->with('error', 'Password salah.');
        }

        // Validasi status akun
        if (!$user['is_validated']) {
            $logger = new LoggingService();

            $logger->log("Login gagal - akun belum divalidasi", $user['id']);

            return redirect()->back()
                ->with('error', 'Akun kamu belum divalidasi, hubungi Staf Perpustakaan untuk Validasi.');
        }

        if ($user['is_archived']) {
            $logger = new LoggingService();

            $logger->log("Login gagal - Akun telah di Archive kan", $user['id']);

            return redirect()->back()
                ->with('error', 'Akun kamu telah diarsipkan, Hubungi Staff Perpustakaan untuk Aktifkan Akun.');
        }

        // Catat waktu login; waktu login sebelumnya ditampilkan di Profil
        session()->set('previous_login', $user['last_login']);
        $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        // ===============================
        // FORCE CHANGE PASSWORD
        // ===============================
        if ($user['must_change_password']) {

            session()->set([
                'user_id'   => $user['id'],
                'name'      => $user['name'],
                'role'      => $user['role'],
                'logged_in' => true,
                'force_password_change' => true
            ]);

            return redirect()->to('/force-change-password');
        }

        // ===============================
        // LOGIN NORMAL
        // ===============================
        session()->set([
            'user_id'   => $user['id'],
            'name'      => $user['name'],
            'role'      => $user['role'],
            'logged_in' => true
        ]);

        $logger = new LoggingService();

        $logger->log(
            "Login berhasil",
            $user['id'],
            $user['id']
        );

        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
    }

    private function loginFailed()
    {
        $attempts = $this->session->get('login_attempts') ?? 0;

        $this->session->set([
            'login_attempts' => $attempts + 1,
            'last_attempt_time' => time()
        ]);
        

        return redirect()->back()
            ->with('error', 'Username atau password salah.');
    }

    // =====================================================
    // LOGOUT
    // =====================================================
    public function logout()
    {
        $userId = session('user_id');

        $logger = new LoggingService();
        $logger->log("Logout", $userId, $userId);

        session()->destroy();
        return redirect()->to('/auth/login');
    }

    // =====================================================
    // Ambil Dropdown Kelas
    // =====================================================
    public function ClassDropdown()
    {
        $classModel = new ClassModel();
        
        // Mengambil data untuk dropdown
        $data['list_kelas'] = $classModel->getClass();
        
        return view('auth/register', $data);
    }
}
