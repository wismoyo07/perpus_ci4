<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Form Tambah User: isian lengkap (NISN/NUPTK, kelas, kontak), pesan error tampil,
 * dan nilai isian di-escape. Dirender tanpa database.
 *
 * @internal
 */
final class FormTambahUserTest extends CIUnitTestCase
{
    private function render(): string
    {
        return view('users/_create_form', [
            'list_kelas' => [['class_name' => 'VII-A'], ['class_name' => 'VIII-B']],
        ]);
    }

    public function testFormMemuatIsianBaru(): void
    {
        $html = $this->render();

        foreach (['name', 'username', 'jenis_pengguna', 'member_code', 'member_code_nation', 'class', 'phone', 'whatsapp'] as $field) {
            $this->assertStringContainsString('name="' . $field . '"', $html);
        }
        $this->assertStringContainsString('<option value="VIII-B"', $html);
        $this->assertStringContainsString('123456', $html);
    }

    public function testPesanErrorDitampilkanDanDiEscape(): void
    {
        session()->setFlashdata('errors', [
            'username' => 'Username sudah dipakai pengguna lain.',
            'x'        => '<script>alert(1)</script>',
        ]);

        $html = $this->render();

        $this->assertStringContainsString('Username sudah dipakai pengguna lain.', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function testTanpaErrorTidakAdaAlert(): void
    {
        $html = $this->render();

        $this->assertStringNotContainsString('Periksa kembali isian berikut', $html);
    }
}
