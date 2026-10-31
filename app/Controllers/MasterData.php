<?php

namespace App\Controllers;

use Config\Database;

/**
 * Data master untuk penomoran & klasifikasi buku (khusus admin):
 * sumber dana (tabel source_fund) dan kelas DDC (tabel category).
 * Tidak ada hapus permanen: data lama tetap merujuk ke baris yang ada, jadi cukup dinonaktifkan.
 */
class MasterData extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /* ===============================
       SUMBER DANA
    =============================== */

    public function sumberDana()
    {
        $rows = $this->db->table('source_fund sf')
            ->select('sf.*, (SELECT COUNT(*) FROM book_items bi WHERE bi.sumber_dana = sf.kode) AS jumlah_salinan')
            ->orderBy('sf.id', 'ASC')
            ->get()
            ->getResultArray();

        return view('master/sumber_dana', ['title' => 'Data Master: Sumber Dana', 'rows' => $rows]);
    }

    public function storeSumberDana()
    {
        $kode = strtoupper(trim((string) $this->request->getPost('kode')));
        $nama = trim((string) $this->request->getPost('source_name'));

        if (!preg_match('/^[A-Z0-9-]{2,20}$/', $kode)) {
            return $this->back('admin/master/sumber-dana', 'Kode hanya boleh huruf, angka, dan tanda minus (2-20 karakter), misalnya BOS atau DAK-FISIK.');
        }
        if ($nama === '' || mb_strlen($nama) > 100) {
            return $this->back('admin/master/sumber-dana', 'Nama sumber dana wajib diisi (maksimal 100 karakter).');
        }
        if ($this->db->table('source_fund')->where('kode', $kode)->countAllResults()) {
            return $this->back('admin/master/sumber-dana', "Kode {$kode} sudah terdaftar.");
        }

        $this->db->table('source_fund')->insert(['kode' => $kode, 'source_name' => $nama, 'is_active' => 1]);
        $this->logActivity("Menambahkan sumber dana {$kode} ({$nama})");

        return redirect()->to('/admin/master/sumber-dana')->with('success', "Sumber dana {$kode} ditambahkan.");
    }

    public function updateSumberDana($id)
    {
        $row  = $this->db->table('source_fund')->where('id', $id)->get()->getRowArray();
        $nama = trim((string) $this->request->getPost('source_name'));

        if (!$row) {
            return $this->back('admin/master/sumber-dana', 'Data tidak ditemukan.');
        }
        if ($nama === '' || mb_strlen($nama) > 100) {
            return $this->back('admin/master/sumber-dana', 'Nama sumber dana wajib diisi (maksimal 100 karakter).');
        }

        // Kode tidak diubah: sudah tertulis di kode buku & kode inventaris
        $this->db->table('source_fund')->where('id', $id)->update(['source_name' => $nama]);
        $this->logActivity("Mengubah nama sumber dana {$row['kode']} menjadi {$nama}");

        return redirect()->to('/admin/master/sumber-dana')->with('success', "Sumber dana {$row['kode']} diperbarui.");
    }

    public function toggleSumberDana($id)
    {
        return $this->toggle('source_fund', $id, 'kode', 'admin/master/sumber-dana', 'sumber dana');
    }

    /* ===============================
       KELAS DDC
    =============================== */

    public function ddc()
    {
        $tingkat = $this->request->getGet('tingkat') === 'kelas' ? 'kelas' : 'semua';

        $builder = $this->db->table('category c')
            ->select('c.*, (SELECT COUNT(*) FROM books b WHERE b.category_id = c.id) AS jumlah_buku')
            ->orderBy('c.code_ddc', 'ASC');

        if ($tingkat === 'kelas') {
            $builder->like('c.code_ddc', '_00', 'none');
        }

        return view('master/ddc', ['title' => 'Data Master: Klasifikasi DDC', 'rows' => $builder->get()->getResultArray(), 'tingkat' => $tingkat]);
    }

    public function storeDdc()
    {
        $kode = trim((string) $this->request->getPost('code_ddc'));
        $desc = trim((string) $this->request->getPost('desc_ddc'));

        if ($error = $this->validateDdc($kode, $desc)) {
            return $this->back('admin/master/ddc', $error);
        }

        $this->db->table('category')->insert(['code_ddc' => $kode, 'desc_ddc' => $desc, 'is_active' => 1]);
        $this->logActivity("Menambahkan kelas DDC {$kode} ({$desc})");

        return redirect()->to('/admin/master/ddc')->with('success', "Kelas DDC {$kode} ditambahkan.");
    }

    public function updateDdc($id)
    {
        $row  = $this->db->table('category')->where('id', $id)->get()->getRowArray();
        $kode = trim((string) $this->request->getPost('code_ddc'));
        $desc = trim((string) $this->request->getPost('desc_ddc'));

        if (!$row) {
            return $this->back('admin/master/ddc', 'Data tidak ditemukan.');
        }
        if ($error = $this->validateDdc($kode, $desc, (int) $id)) {
            return $this->back('admin/master/ddc', $error);
        }

        // Buku merujuk ke id kelas, jadi kode boleh dikoreksi
        $this->db->table('category')->where('id', $id)->update(['code_ddc' => $kode, 'desc_ddc' => $desc]);
        $this->logActivity("Mengubah kelas DDC {$row['code_ddc']} menjadi {$kode} ({$desc})");

        return redirect()->to('/admin/master/ddc')->with('success', "Kelas DDC {$kode} diperbarui.");
    }

    public function toggleDdc($id)
    {
        return $this->toggle('category', $id, 'code_ddc', 'admin/master/ddc', 'kelas DDC');
    }

    /* ===============================
       HELPER
    =============================== */

    private function validateDdc(string $kode, string $desc, ?int $exceptId = null): ?string
    {
        if (!preg_match('/^\d{3}$/', $kode)) {
            return 'Kode DDC harus 3 digit angka, misalnya 000 atau 600.';
        }
        if ($desc === '' || mb_strlen($desc) > 255) {
            return 'Deskripsi wajib diisi (maksimal 255 karakter).';
        }

        $builder = $this->db->table('category')->where('code_ddc', $kode);
        if ($exceptId) {
            $builder->where('id !=', $exceptId);
        }

        return $builder->countAllResults() ? "Kode DDC {$kode} sudah terdaftar." : null;
    }

    private function toggle(string $table, $id, string $labelField, string $path, string $jenis)
    {
        $row = $this->db->table($table)->where('id', $id)->get()->getRowArray();
        if (!$row) {
            return $this->back($path, 'Data tidak ditemukan.');
        }

        $aktif = $row['is_active'] ? 0 : 1;
        $this->db->table($table)->where('id', $id)->update(['is_active' => $aktif]);

        $aksi = $aktif ? 'mengaktifkan' : 'menonaktifkan';
        $this->logActivity(ucfirst($aksi) . " {$jenis} {$row[$labelField]}");

        return redirect()->to('/' . $path)->with('success', ucfirst($jenis) . " {$row[$labelField]} " . ($aktif ? 'diaktifkan.' : 'dinonaktifkan.'));
    }

    private function back(string $path, string $error)
    {
        return redirect()->to('/' . $path)->withInput()->with('error', $error);
    }
}
