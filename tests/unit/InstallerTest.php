<?php

use App\Libraries\Installer;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Logika wizard `php spark perpus:install`: kapan pepper dibuat, dan penulisan nilai .env.
 *
 * @internal
 */
final class InstallerTest extends CIUnitTestCase
{
    public function testPepperDibuatHanyaSaatKosongAtauPublikDanBelumAdaAkun(): void
    {
        $this->assertTrue(Installer::shouldGeneratePepper('', 0));
        $this->assertTrue(Installer::shouldGeneratePepper(null, 0));
        $this->assertTrue(Installer::shouldGeneratePepper('R4nd0mStr1ng_SuperSecret_2026', 0));

        // Sudah ada akun: jangan ganti, password lama tidak akan bisa dipakai login
        $this->assertFalse(Installer::shouldGeneratePepper('R4nd0mStr1ng_SuperSecret_2026', 3));
        // Sudah unik: tidak perlu diganti
        $this->assertFalse(Installer::shouldGeneratePepper('a1b2c3d4e5f6', 0));
    }

    public function testPepperBaruAcakDanPanjang(): void
    {
        $a = Installer::generatePepper();

        $this->assertSame(64, strlen($a));
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $a);
        $this->assertNotSame($a, Installer::generatePepper());
        $this->assertFalse(Installer::isPublicPepper($a));
    }

    public function testMenggantiNilaiEnvYangAda(): void
    {
        $env = "# APP\nAPP_PASSWORD_PEPPER=\ndatabase.default.database = perpus\n";

        $out = Installer::setEnvValue($env, 'APP_PASSWORD_PEPPER', 'abc$1\\x');

        $this->assertStringContainsString("APP_PASSWORD_PEPPER=abc$1\\x\n", $out);
        $this->assertStringContainsString('database.default.database = perpus', $out);
        $this->assertSame('abc$1\\x', Installer::getEnvValue($out, 'APP_PASSWORD_PEPPER'));
    }

    public function testBarisKomentarTidakDiubahDanKunciBaruDitambahkan(): void
    {
        $env = "# APP_PASSWORD_PEPPER=lama\napp.baseURL = 'http://localhost:8080/'";

        $out = Installer::setEnvValue($env, 'APP_PASSWORD_PEPPER', 'baru');

        $this->assertStringContainsString('# APP_PASSWORD_PEPPER=lama', $out);
        $this->assertStringEndsWith("APP_PASSWORD_PEPPER=baru\n", $out);
        $this->assertSame('baru', Installer::getEnvValue($out, 'APP_PASSWORD_PEPPER'));
    }

    public function testMembacaNilaiEnv(): void
    {
        $this->assertNull(Installer::getEnvValue("FOO=1\n", 'APP_PASSWORD_PEPPER'));
        $this->assertSame('', Installer::getEnvValue("APP_PASSWORD_PEPPER=\n", 'APP_PASSWORD_PEPPER'));
        $this->assertSame('xyz', Installer::getEnvValue("APP_PASSWORD_PEPPER = 'xyz'\n", 'APP_PASSWORD_PEPPER'));
    }
}
