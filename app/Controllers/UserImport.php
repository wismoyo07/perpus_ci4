<?php

namespace App\Controllers;

use App\Libraries\MemberImport;
use App\Models\ClassModel;
use App\Services\LoggingService;
use Config\Database;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Impor anggota dari Excel (admin & staff): unggah -> pratinjau -> simpan.
 * Hasil pembacaan file disimpan sementara di writable/uploads/import/ sampai dikonfirmasi.
 */
class UserImport extends BaseController
{
    private const TMP_DIR = WRITEPATH . 'uploads/import/';

    public function index()
    {
        return view('users/import', [
            'title'    => 'Impor Anggota dari Excel',
            'maxRows'  => MemberImport::MAX_ROWS,
            'columns'  => MemberImport::COLUMNS,
            'classes'  => array_column((new ClassModel())->getClass(), 'class_name'),
        ]);
    }

    /** Unduh template Excel. */
    public function template()
    {
        $book = MemberImport::buildTemplate(array_column((new ClassModel())->getClass(), 'class_name'));

        ob_start();
        (new Xlsx($book))->save('php://output');
        $content = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template-impor-anggota.xlsx"')
            ->setHeader('Cache-Control', 'no-store')
            ->setBody($content);
    }

    public function preview()
    {
        $file = $this->request->getFile('file');
        $mode = $this->request->getPost('mode') === MemberImport::MODE_UPDATE ? MemberImport::MODE_UPDATE : MemberImport::MODE_SKIP;

        if (!$file || !$file->isValid()) {
            return redirect()->to('/users/import')->with('error', 'Pilih file Excel (.xlsx) yang akan diimpor.');
        }
        if (strtolower($file->getClientExtension()) !== 'xlsx') {
            return redirect()->to('/users/import')->with('error', 'File harus berformat .xlsx. Gunakan template dari halaman ini.');
        }
        if ($file->getSize() > 2 * 1024 * 1024) {
            return redirect()->to('/users/import')->with('error', 'Ukuran file maksimal 2 MB.');
        }

        $this->cleanupTmp();
        $read = MemberImport::readFile($file->getTempName());
        if ($read['error']) {
            return redirect()->to('/users/import')->with('error', $read['error']);
        }

        // Simpan hasil baca (bukan file aslinya) sampai petugas mengonfirmasi
        $token = bin2hex(random_bytes(16));
        if (!is_dir(self::TMP_DIR)) {
            mkdir(self::TMP_DIR, 0775, true);
        }
        file_put_contents(self::TMP_DIR . $token . '.json', json_encode([
            'mode'     => $mode,
            'rows'     => $read['rows'],
            'filename' => $file->getClientName(),
            'user_id'  => (int) session('user_id'),
        ]));
        session()->set('member_import_token', $token);

        $analysis = MemberImport::analyze($read['rows'], $this->context(), $mode);

        return view('users/import_preview', [
            'title'    => 'Pratinjau Impor Anggota',
            'analysis' => $analysis,
            'summary'  => MemberImport::summarize($analysis),
            'mode'     => $mode,
            'filename' => $file->getClientName(),
            'token'    => $token,
        ]);
    }

    public function process()
    {
        $token = (string) $this->request->getPost('token');
        $path  = self::TMP_DIR . $token . '.json';

        if (!preg_match('/^[0-9a-f]{32}$/', $token) || $token !== session('member_import_token') || !is_file($path)) {
            return redirect()->to('/users/import')->with('error', 'Data pratinjau tidak ditemukan atau sudah dipakai. Unggah ulang file Anda.');
        }

        $saved = json_decode((string) file_get_contents($path), true);
        @unlink($path);
        session()->remove('member_import_token');

        // Analisis ulang terhadap data terbaru (bisa saja berubah sejak pratinjau)
        $analysis = MemberImport::analyze($saved['rows'], $this->context(), $saved['mode']);
        $summary  = MemberImport::summarize($analysis);

        if ($summary['baru'] + $summary['perbarui'] === 0) {
            return redirect()->to('/users/import')->with('error', 'Tidak ada baris yang bisa disimpan.');
        }

        helper('password');
        $db       = Database::connect();
        $now      = date('Y-m-d H:i:s');
        $password = hashPassword('123456'); // sama untuk semua akun baru, wajib diganti saat login pertama

        $db->transStart();
        foreach ($analysis as $a) {
            $d = $a['data'];
            if ($a['status'] === 'baru') {
                $db->table('users')->insert([
                    'name'                 => $d['name'],
                    'username'             => $d['username'],
                    'password'             => $password,
                    'role'                 => 'member',
                    'jenis_pengguna'       => $d['jenis_pengguna'],
                    'member_code'          => $d['member_code'],
                    'member_code_nation'   => $d['member_code_nation'] ?: null,
                    'class'                => $d['class'] ?: null,
                    'phone'                => $d['phone'] ?: null,
                    'whatsapp'             => $d['whatsapp'] ?: null,
                    'is_validated'         => 1,
                    'is_archived'          => 0,
                    'must_change_password' => 1,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ]);
            } elseif ($a['status'] === 'perbarui') {
                // Isian kosong tidak menimpa data yang sudah ada
                $update = array_filter([
                    'name'               => $d['name'],
                    'member_code_nation' => $d['member_code_nation'],
                    'class'              => $d['class'],
                    'phone'              => $d['phone'],
                    'whatsapp'           => $d['whatsapp'],
                ], static fn ($v) => $v !== '');
                $db->table('users')->where('id', $a['user_id'])->update($update + ['updated_at' => $now]);
            }
        }
        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->to('/users/import')->with('error', 'Impor gagal disimpan. Tidak ada data yang berubah.');
        }

        $pesan = "Impor anggota dari {$saved['filename']}: {$summary['baru']} baru, {$summary['perbarui']} diperbarui, "
            . "{$summary['lewati']} dilewati, {$summary['error']} baris error tidak diimpor.";
        (new LoggingService())->log($pesan);

        return redirect()->to('/users')->with('success', $pesan . ($summary['baru'] ? ' Password awal akun baru: 123456 (wajib diganti saat login pertama).' : ''));
    }

    /** Data pembanding dari database untuk MemberImport::analyze(). */
    private function context(): array
    {
        $users = Database::connect()->table('users')
            ->select('id, name, username, role, jenis_pengguna, member_code, member_code_nation')
            ->get()->getResultArray();

        $ctx = ['by_code' => [], 'usernames' => [], 'nations' => [], 'classes' => []];
        foreach ($users as $u) {
            if ($u['member_code'] !== null && $u['member_code'] !== '') {
                $ctx['by_code'][strtolower($u['member_code'])] = $u;
            }
            $ctx['usernames'][strtolower($u['username'])] = (int) $u['id'];
            if ($u['member_code_nation'] !== null && $u['member_code_nation'] !== '') {
                $ctx['nations'][strtolower($u['member_code_nation'])] = (int) $u['id'];
            }
        }
        $ctx['classes'] = array_column((new ClassModel())->getClass(), 'class_name');

        return $ctx;
    }

    /** Hapus data pratinjau yang lebih dari sehari tidak dikonfirmasi. */
    private function cleanupTmp(): void
    {
        foreach (glob(self::TMP_DIR . '*.json') ?: [] as $f) {
            if (filemtime($f) < time() - 86400) {
                @unlink($f);
            }
        }
    }
}
