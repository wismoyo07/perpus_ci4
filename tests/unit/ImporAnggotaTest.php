<?php

use App\Libraries\MemberImport;
use CodeIgniter\Test\CIUnitTestCase;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Impor anggota dari Excel: judul kolom, validasi tiap baris, duplikat, mode lewati/perbarui,
 * username otomatis, dan template yang bisa dibaca ulang. Tanpa database.
 *
 * @internal
 */
final class ImporAnggotaTest extends CIUnitTestCase
{
    private const HEADER = ['Nama Lengkap *', 'Jenis Pengguna *', 'NIS / NIP *', 'NISN / NUPTK', 'Kelas', 'Telepon', 'WhatsApp', 'Username'];

    private static function ctx(): array
    {
        return [
            'by_code' => [
                'ssw-001' => ['id' => 6, 'role' => 'member', 'jenis_pengguna' => 'siswa', 'name' => 'Siti Aminah'],
                'stf-001' => ['id' => 2, 'role' => 'staff', 'jenis_pengguna' => 'pegawai', 'name' => 'Staff'],
            ],
            'usernames' => ['admin' => 1, 'staff' => 2, 'siti_siswa' => 6, 'ssw-900' => 9],
            'nations'   => ['0098765432' => 6],
            'classes'   => ['VII-A', 'VIII-B'],
        ];
    }

    private static function row(int $n, array $values): array
    {
        return ['row' => $n, 'values' => $values + [
            'name' => '', 'jenis_pengguna' => '', 'member_code' => '', 'member_code_nation' => '',
            'class' => '', 'phone' => '', 'whatsapp' => '', 'username' => '',
        ]];
    }

    private static function siswa(int $n, string $nis, array $extra = []): array
    {
        return self::row($n, $extra + ['name' => 'Budi Santoso', 'jenis_pengguna' => 'siswa', 'member_code' => $nis, 'class' => 'VII-A']);
    }

    // --- judul kolom & baris

    public function testJudulKolomTemplateDikenaliWalauUrutanBerbeda(): void
    {
        $map = MemberImport::mapHeaders(['NIS', 'nama', 'Jenis', 'Kelas']);

        $this->assertSame(['member_code' => 0, 'name' => 1, 'jenis_pengguna' => 2, 'class' => 3], $map);
    }

    public function testKolomWajibHilangDitolak(): void
    {
        $this->assertStringContainsString('NIS / NIP', MemberImport::mapHeaders(['Nama Lengkap', 'Jenis Pengguna']));
    }

    public function testBarisKosongDilewatiDanBatasJumlahBaris(): void
    {
        $read = MemberImport::rowsFromTable([1 => self::HEADER, 2 => ['A B C', 'siswa', '1'], 3 => ['', '', ''], 4 => ['D E F', 'guru', '2']]);
        $this->assertNull($read['error']);
        $this->assertSame([2, 4], array_column($read['rows'], 'row'));

        $table = [1 => self::HEADER];
        for ($i = 2; $i <= MemberImport::MAX_ROWS + 2; $i++) {
            $table[$i] = ['Nama ' . $i, 'siswa', (string) $i];
        }
        $this->assertStringContainsString('Maksimal', MemberImport::rowsFromTable($table)['error']);
        $this->assertNotNull(MemberImport::rowsFromTable([1 => self::HEADER])['error']);
    }

    // --- analisis

    public function testBarisValidMenjadiAnggotaBaru(): void
    {
        [$a] = MemberImport::analyze([self::siswa(2, '2026001', ['class' => 'vii-a', 'phone' => '81234567890'])], self::ctx(), 'lewati');

        $this->assertSame('baru', $a['status']);
        $this->assertSame('VII-A', $a['data']['class'], 'kelas dicocokkan tanpa peka huruf');
        $this->assertSame('081234567890', $a['data']['phone'], 'angka 0 di depan dikembalikan');
        $this->assertSame('2026001', $a['data']['username'], 'username otomatis dari NIS');
    }

    public static function barisErrorProvider(): array
    {
        return [
            'nama terlalu pendek'     => [['name' => 'Bu'], 'Nama wajib'],
            'jenis salah'             => [['jenis_pengguna' => 'murid'], 'Jenis pengguna'],
            'NIS kosong'              => [['member_code' => ''], 'NIS / NIP wajib'],
            'kelas tidak ada'         => [['class' => 'X-Z'], 'tidak ada di data kelas'],
            'telepon salah'           => [['phone' => 'abc'], 'Nomor Telepon'],
            'NISN milik orang lain'   => [['member_code_nation' => '0098765432'], 'NISN / NUPTK sudah dipakai'],
            'username sudah dipakai'  => [['username' => 'Admin'], 'Username sudah dipakai'],
            'username tidak valid'    => [['username' => 'ab cd'], 'Username 4–50'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('barisErrorProvider')]
    public function testBarisTidakValid(array $override, string $pesan): void
    {
        [$a] = MemberImport::analyze([self::siswa(2, '2026001', $override)], self::ctx(), 'lewati');

        $this->assertSame('error', $a['status']);
        $this->assertStringContainsString($pesan, implode(' ', $a['errors']));
    }

    public function testDuplikatDiDalamFile(): void
    {
        $res = MemberImport::analyze([
            self::siswa(2, '2026001', ['member_code_nation' => '111', 'username' => 'budi1']),
            self::siswa(3, '2026001'),
            self::siswa(4, '2026002', ['member_code_nation' => '111']),
            self::siswa(5, '2026003', ['username' => 'BUDI1']),
        ], self::ctx(), 'lewati');

        $this->assertSame(['baru', 'error', 'error', 'error'], array_column($res, 'status'));
        $this->assertStringContainsString('baris 2', $res[1]['errors'][0]);
        $this->assertStringContainsString('baris 2', implode(' ', $res[2]['errors']));
        $this->assertStringContainsString('Username sudah dipakai', implode(' ', $res[3]['errors']));
    }

    public function testNisTerdaftarDilewatiAtauDiperbarui(): void
    {
        $rows = [self::siswa(2, 'SSW-001', ['class' => 'VIII-B', 'phone' => 'salah'])];

        [$skip] = MemberImport::analyze($rows, self::ctx(), MemberImport::MODE_SKIP);
        $this->assertSame('lewati', $skip['status']);
        $this->assertSame([], $skip['errors'], 'baris yang dilewati tidak dinilai');

        $rows = [self::siswa(2, 'ssw-001', ['class' => 'VIII-B', 'member_code_nation' => '0098765432'])];
        [$upd] = MemberImport::analyze($rows, self::ctx(), MemberImport::MODE_UPDATE);
        $this->assertSame('perbarui', $upd['status'], 'NISN milik akun yang sama boleh');
        $this->assertSame(6, $upd['user_id']);
        $this->assertSame('VIII-B', $upd['data']['class']);
    }

    public function testPerbaruiDitolakUntukStafAtauJenisBerbeda(): void
    {
        [$staf] = MemberImport::analyze([self::row(2, ['name' => 'Staff Baru', 'jenis_pengguna' => 'pegawai', 'member_code' => 'STF-001'])], self::ctx(), 'perbarui');
        $this->assertStringContainsString('staf/admin', implode(' ', $staf['errors']));

        [$jenis] = MemberImport::analyze([self::row(2, ['name' => 'Siti Aminah', 'jenis_pengguna' => 'guru', 'member_code' => 'SSW-001'])], self::ctx(), 'perbarui');
        $this->assertStringContainsString('Jenis pengguna berbeda', implode(' ', $jenis['errors']));
    }

    public function testKelasDiabaikanUntukGuru(): void
    {
        [$a] = MemberImport::analyze([self::row(2, ['name' => 'Pak Guru', 'jenis_pengguna' => 'Guru', 'member_code' => '197', 'class' => 'VII-A'])], self::ctx(), 'lewati');

        $this->assertSame('baru', $a['status']);
        $this->assertSame('', $a['data']['class']);
        $this->assertSame('agt_197', $a['data']['username']);
    }

    public function testUsernameOtomatisSelaluUnik(): void
    {
        $this->assertSame('ssw-900_2', MemberImport::suggestUsername('SSW-900', ['ssw-900' => 9]));
        $this->assertSame('nis_2026_01', MemberImport::suggestUsername('NIS 2026/01', []));

        $res = MemberImport::analyze([self::siswa(2, 'A 1234'), self::siswa(3, 'A/1234')], self::ctx(), 'lewati');
        $this->assertSame(['a_1234', 'a_1234_2'], array_column(array_column($res, 'data'), 'username'));
    }

    public function testRingkasan(): void
    {
        $sum = MemberImport::summarize([['status' => 'baru'], ['status' => 'baru'], ['status' => 'error'], ['status' => 'lewati']]);

        $this->assertSame(['baru' => 2, 'perbarui' => 0, 'lewati' => 1, 'error' => 1], $sum);
    }

    // --- template

    public function testTemplateBisaDibacaUlang(): void
    {
        if (!extension_loaded('zip')) {
            $this->markTestSkipped('Ekstensi zip tidak aktif.');
        }

        $path = tempnam(sys_get_temp_dir(), 'tpl') . '.xlsx';
        (new Xlsx(MemberImport::buildTemplate(['VII-A', 'VIII-B'])))->save($path);

        $read = MemberImport::readFile($path);
        @unlink($path);

        $this->assertNull($read['error']);
        $this->assertCount(2, $read['rows']);
        $this->assertSame('0098765432', $read['rows'][0]['values']['member_code_nation'], 'kolom teks menjaga angka 0 di depan');
        $this->assertSame('VII-A', $read['rows'][0]['values']['class']);

        $res = MemberImport::analyze($read['rows'], ['by_code' => [], 'usernames' => [], 'nations' => [], 'classes' => ['VII-A']], 'lewati');
        $this->assertSame(['baru', 'baru'], array_column($res, 'status'));
    }

    public function testFileBukanExcelDitolak(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'bad');
        file_put_contents($path, 'bukan excel');

        $this->assertNotNull(MemberImport::readFile($path)['error']);
        @unlink($path);
    }
}
