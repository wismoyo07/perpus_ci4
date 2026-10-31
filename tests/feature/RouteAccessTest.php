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
            'daftar denda'        => ['get', 'fines'],
            'proses denda'        => ['post', 'fines/settle/1'],
            'lapor buku hilang'   => ['post', 'transactions/report-lost/1'],
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

    public static function adminOnlyProvider(): array
    {
        return [
            'master sumber dana'        => ['get', 'admin/master/sumber-dana'],
            'tambah sumber dana'        => ['post', 'admin/master/sumber-dana/store'],
            'ubah sumber dana'          => ['post', 'admin/master/sumber-dana/update/1'],
            'nonaktifkan sumber dana'   => ['post', 'admin/master/sumber-dana/toggle/1'],
            'master ddc'                => ['get', 'admin/master/ddc'],
            'tambah ddc'                => ['post', 'admin/master/ddc/store'],
            'ubah ddc'                  => ['post', 'admin/master/ddc/update/1'],
            'nonaktifkan ddc'           => ['post', 'admin/master/ddc/toggle/1'],
            'pengaturan (kode barang)'  => ['post', 'admin/settings/update'],
        ];
    }

    #[DataProvider('adminOnlyProvider')]
    public function testStaffDitolakDariDataMaster(string $method, string $path): void
    {
        $result = $this->withSession(['logged_in' => true, 'role' => 'staff', 'user_id' => 2])
            ->call($method, $path);

        $result->assertRedirectTo('/unauthorized');
    }

    #[DataProvider('adminOnlyProvider')]
    public function testMemberDitolakDariDataMaster(string $method, string $path): void
    {
        $result = $this->withSession(['logged_in' => true, 'role' => 'member', 'user_id' => 5])
            ->call($method, $path);

        $result->assertRedirectTo('/unauthorized');
    }

    #[DataProvider('adminOnlyProvider')]
    public function testTamuDitolakDariDataMaster(string $method, string $path): void
    {
        $this->call($method, $path)->assertRedirectTo('/auth/login');
    }
}
