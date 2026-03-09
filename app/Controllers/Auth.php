<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ActivityLogModel;
use CodeIgniter\Controller;

use App\Services\LoggingService;


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

            $userModel = new \App\Models\UserModel();

            $data = [
                'name'            => trim($this->request->getPost('name')),
                'username'        => trim($this->request->getPost('username')),
                'password'        => hashPassword($this->request->getPost('password')),
                'role'            => 'member',
                'jenis_pengguna'  => $this->request->getPost('jenis_pengguna'),
                'nomor_induk'     => trim($this->request->getPost('nomor_induk')),
                'is_validated'    => 0,
                'is_archived'     => 0,
                'must_change_password' => 0
            ];

            $userModel->insert($data);

            return redirect()->to('/auth/login')
                ->with('success','Registrasi berhasil. Tunggu validasi admin.');
        }

        return view('auth/register');
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
                'rules'  => 'required|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/]',
                'errors' => [
                    'required'    => 'Password wajib diisi.',
                    'regex_match' => 'Password minimal 8 karakter, harus mengandung huruf besar, huruf kecil, angka, dan simbol.'
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
            
            'nomor_induk' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Nomor Induk wajib diisi.'
                ]
            ],
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
            'nomor_induk' => $this->request->getPost('nomor_induk')
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

            $userModel = new \App\Models\UserModel();
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
        helper('password');
        
        $password = $this->request->getPost('password');

        if (strlen($password) < 6) {
            return redirect()->back()
                ->with('error','Password minimal 6 karakter.');
        }

        $userModel = new \App\Models\UserModel();

        $userModel->update(session('user_id'), [
            'password' => hashPassword($password, PASSWORD_DEFAULT),
            'must_change_password' => 0
        ]);

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

        $userModel = new \App\Models\UserModel();
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
}
