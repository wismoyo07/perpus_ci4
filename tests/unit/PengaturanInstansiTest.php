<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Form Pengaturan Identitas Sekolah: nilai tersimpan harus tampil utuh (di-escape),
 * termasuk yang mengandung tanda kutip, supaya tidak terpotong saat disimpan ulang.
 *
 * @internal
 */
final class PengaturanInstansiTest extends CIUnitTestCase
{
    private static function setting(array $override = []): array
    {
        return $override + [
            'nama_lembaga'         => 'SMP "Harapan" Bangsa',
            'kode_lembaga'         => '20100001',
            'kode_barang_buku'     => '03.11.01.01.001',
            'maks_hari_denda'      => 10,
            'status_akreditasi'    => 'Baik Sekali',
            'website_url'          => 'https://smp.sch.id/?a=1&b=2',
            'alamat'               => "Jl. Merdeka \"1\"\n</textarea><b>x</b>",
            'kepala_sekolah'       => 'Drs. O\'Neil',
            'wakil_kepala_sekolah' => '<script>alert(1)</script>',
            'logo'                 => null,
        ];
    }

    public function testNilaiDenganTandaKutipTampilUtuh(): void
    {
        $html = view('settings/_form', ['setting' => self::setting()]);

        $this->assertStringContainsString('value="SMP &quot;Harapan&quot; Bangsa"', $html);
        $this->assertStringContainsString('value="Drs. O&#039;Neil"', $html);
        $this->assertStringContainsString('value="https://smp.sch.id/?a=1&amp;b=2"', $html);
        $this->assertStringContainsString('value="Baik Sekali"', $html);
    }

    public function testNilaiTidakMerusakHtml(): void
    {
        $html = view('settings/_form', ['setting' => self::setting()]);

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('</textarea><b>x</b>', $html);
        $this->assertSame(1, substr_count($html, '</textarea>'));
    }

    public function testTeksPembantuKodeBarangTidakDitampilkan(): void
    {
        $html = view('settings/_form', ['setting' => self::setting()]);

        $this->assertStringNotContainsString('Menjadi awal kode buku baru', $html);
        $this->assertStringContainsString('name="kode_barang_buku"', $html);
    }

    public function testStatusAkreditasiBolehHingga50Karakter(): void
    {
        $html = view('settings/_form', ['setting' => self::setting()]);

        $this->assertMatchesRegularExpression('/name="status_akreditasi"[^>]*maxlength="50"/', $html);
    }
}
