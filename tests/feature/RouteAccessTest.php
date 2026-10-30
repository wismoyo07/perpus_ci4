<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Halaman khusus admin/staff harus menolak member.
 * Filter berjalan sebelum controller, jadi tes ini tidak butuh database.
 *
 * @internal
 */
final class RouteAccessTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public static function adminStaffOnlyProvider(): array
    {
        return [
            'tambah buku'        => ['get', 'books/create'],
            'simpan buku'        => ['post', 'books/store'],
            'hapus buku'         => ['post', 'books/delete/1'],
            'salinan buku'       => ['get', 'books/items/1'],
            'cetak label buku'   => ['get', 'books/items/labels/1'],
            'cetak kartu anggota' => ['get', 'users/cards'],
        ];
    }

    #[DataProvider('adminStaffOnlyProvider')]
    public function testMemberDitolak(string $method, string $path): void
    {
        $result = $this->withSession(['logged_in' => true, 'role' => 'member', 'user_id' => 5])
            ->call($method, $path);

        $result->assertRedirectTo('/unauthorized');
    }

    #[DataProvider('adminStaffOnlyProvider')]
    public function testTamuDiarahkanKeLogin(string $method, string $path): void
    {
        $result = $this->call($method, $path);

        $result->assertRedirectTo('/auth/login');
    }
}
