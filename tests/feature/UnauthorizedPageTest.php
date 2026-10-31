<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;

/**
 * Halaman /unauthorized: tujuan redirect RoleFilter saat role tidak diizinkan.
 * Layout membaca tabel app_settings, jadi tabel itu dibuat di database tes (SQLite in-memory).
 *
 * @internal
 */
final class UnauthorizedPageTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private array $memberSession = [
        'logged_in' => true,
        'role'      => 'member',
        'user_id'   => 5,
        'name'      => 'Siti Siswa',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $forge = Database::forge();
        $forge->addField([
            'id'           => ['type' => 'INTEGER', 'auto_increment' => true],
            'logo'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nama_lembaga' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'alamat'       => ['type' => 'TEXT', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('app_settings', true);
    }

    protected function tearDown(): void
    {
        Database::forge()->dropTable('app_settings', true);

        parent::tearDown();
    }

    public function testMemberMelihatHalamanAksesDitolakDenganStatus403(): void
    {
        $result = $this->withSession($this->memberSession)->get('unauthorized');

        $result->assertStatus(403);
        $result->assertSee('Akses Ditolak');
        $result->assertSee('Anda tidak memiliki akses ke halaman ini.');
        $result->assertSee('Kembali ke Dashboard');
    }

    public function testPesanDariRoleFilterDitampilkan(): void
    {
        $session = $this->memberSession + [
            'error'     => 'Pesan khusus dari filter.',
            '__ci_vars' => ['error' => 'new'],
        ];

        $result = $this->withSession($session)->get('unauthorized');

        $result->assertStatus(403);
        $result->assertSee('Pesan khusus dari filter.');
    }

    public function testTamuDiarahkanKeLogin(): void
    {
        $this->get('unauthorized')->assertRedirectTo('/auth/login');
    }
}
