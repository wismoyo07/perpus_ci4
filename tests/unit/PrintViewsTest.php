<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Barcode, kartu anggota, label buku, dan halaman login.
 * View dirender langsung dengan data contoh, tanpa database.
 *
 * @internal
 */
final class PrintViewsTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('barcode');
    }

    public function testBarcodeSvgMenghasilkanSvg(): void
    {
        $svg = barcode_svg('SSW-001');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('<rect', $svg);
    }

    public function testBarcodeSvgKosongUntukKodeKosong(): void
    {
        $this->assertSame('', barcode_svg(''));
        $this->assertSame('', qrcode_svg(''));
    }

    public function testQrcodeSvgMenghasilkanSvgUntukKodeInventarisPanjang(): void
    {
        $svg = qrcode_svg('03.11.01.01.001 / BOS / 2026 / 001 / 002');

        $this->assertStringStartsWith('<svg', ltrim($svg));
        $this->assertStringContainsString('<path', $svg);
    }

    public function testKartuAnggotaMenampilkanDataMember(): void
    {
        $html = view('users/cards', [
            'setting' => ['nama_lembaga' => 'SMA Contoh', 'logo' => null],
            'users'   => [
                ['name' => 'Siti Aminah', 'member_code' => 'SSW-001', 'jenis_pengguna' => 'siswa', 'class' => 'X-1'],
                ['name' => 'Endang', 'member_code' => 'GRU-001', 'jenis_pengguna' => 'guru', 'class' => null],
            ],
        ]);

        $this->assertStringContainsString('SMA Contoh', $html);
        $this->assertStringContainsString('Siti Aminah', $html);
        $this->assertStringContainsString('SSW-001', $html);
        $this->assertStringContainsString('Kelas X-1', $html);
        $this->assertStringContainsString('GRU-001', $html);
        $this->assertSame(2, substr_count($html, 'class="member-card"'));
        $this->assertSame(2, substr_count($html, '<svg'));
    }

    public function testKartuAnggotaMengamankanNamaDariHtml(): void
    {
        $html = view('users/cards', [
            'setting' => ['nama_lembaga' => 'SMA Contoh', 'logo' => null],
            'users'   => [['name' => '<script>x</script>', 'member_code' => 'SSW-009', 'jenis_pengguna' => 'siswa', 'class' => '']],
        ]);

        $this->assertStringNotContainsString('<script>x</script>', $html);
    }

    public function testLabelBukuMenampilkanKodeInventaris(): void
    {
        $html = view('books/labels', [
            'setting' => ['nama_lembaga' => 'SMA Contoh'],
            'book'    => ['id' => 1, 'judul' => 'Laskar Pelangi'],
            'items'   => [
                ['kode_inventaris' => 'INV-2026-0001'],
                ['kode_inventaris' => 'INV-2026-0002'],
            ],
        ]);

        $this->assertStringContainsString('Laskar Pelangi', $html);
        $this->assertStringContainsString('INV-2026-0001', $html);
        $this->assertStringContainsString('INV-2026-0002', $html);
        $this->assertSame(2, substr_count($html, 'class="book-label"'));
        $this->assertSame(2, substr_count($html, '<svg'));
    }

    public function testLabelBukuTanpaSalinan(): void
    {
        $html = view('books/labels', [
            'setting' => ['nama_lembaga' => 'SMA Contoh'],
            'book'    => ['id' => 1, 'judul' => 'Laskar Pelangi'],
            'items'   => [],
        ]);

        $this->assertStringContainsString('Belum ada salinan buku', $html);
    }

    public function testLoginTidakLagiPunyaLinkLupaPasswordMati(): void
    {
        // Layout login membaca app_settings dari DB, jadi cukup periksa isi file view-nya.
        $html = file_get_contents(APPPATH . 'Views/auth/login.php');

        $this->assertStringNotContainsString('l-forget-password', $html);
        $this->assertStringContainsString('Hubungi petugas perpustakaan', $html);
    }
}
