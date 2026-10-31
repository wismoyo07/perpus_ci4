<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = trim(service('uri')->getPath(), '/');

        // PUBLIC PAGES
        $public = [
            '',
            'auth/login',
            'auth/register'
        ];

        if (in_array($uri, $public)) {
            return;
        }

        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        // Akun yang wajib ganti password hanya boleh membuka halaman ganti password atau keluar
        if (session()->get('force_password_change') && !in_array($uri, ['force-change-password', 'logout'], true)) {
            return redirect()->to('/force-change-password');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu logic after
    }
}