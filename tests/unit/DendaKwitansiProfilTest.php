<?php

use App\Libraries\PasswordPolicy;
use App\Services\TransactionService;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Status ringkas denda, kwitansi pembayaran denda (nomor, validasi, terbilang, tampilan),
 * dan aturan password (README bagian 3.B dan 3.C). Tanpa database.
 *
 * @internal
 */
final class DendaKwitansiProfilTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper(['fine', 'terbilang']);
    }

    // ---------------------------------------------------------------
    // Status ringkas denda per transaksi
    // ---------------------------------------------------------------

    private static function agg(int $belum = 0, int $tahan = 0, int $lunas = 0, int $bebas = 0, float $rpBelum = 0, float $rpTahan = 0): array
    {
        return [
            'n_belum' => $belum, 'n_tahan' => $tahan, 'n_lunas' => $lunas, 'n_bebas' => $bebas,
            'rp_belum' => $rpBelum, 'rp_tahan' => $rpTahan, 'rp_lunas' => 0,
        ];
    }

    public static function statusProvider(): array
    {
        return [
            'semua belum lunas'          => [self::agg(belum: 2, rpBelum: 4000), 'Belum Lunas', 0.0],
            'campuran lunas & belum'     => [self::agg(belum: 1, lunas: 1, rpBelum: 3000), 'Belum Lunas', 3000.0],
            'ditahan saja'               => [self::agg(tahan: 1, rpTahan: 2000), 'Ditahan', 0.0],
            'ditahan & lunas'            => [self::agg(tahan: 1, lunas: 2, rpTahan: 2000), 'Ditahan', 2000.0],
            'belum lunas didahulukan'    => [self::agg(belum: 1, tahan: 1, rpBelum: 1000, rpTahan: 2000), 'Belum Lunas', 1000.0],
            'semua lunas'                => [self::agg(lunas: 3), 'Lunas', 0.0],
            'lunas & dibebaskan'         => [self::agg(lunas: 1, bebas: 1), 'Lunas', 0.0],
            'semua dibebaskan'           => [self::agg(bebas: 2), 'Dibebaskan', 0.0],
        ];
    }

    #[DataProvider('statusProvider')]
    public function testStatusRingkasDenda(array $agg, string $label, float $sisa): void
    {
        $status = fine_summary_status($agg);

        $this->assertSame($label, $status['label']);
        $this->assertSame($sisa, $status['sisa']);
    }

    public function testTransaksiTanpaDendaTidakBerstatus(): void
    {
        $this->assertNull(fine_summary_status(null));
        $this->assertNull(fine_summary_status(self::agg()));
        $this->assertSame('', fine_badge(null, 'selesai'));
    }

    public function testBadgeMenandaiDendaMasihBerjalan(): void
    {
        $html = fine_badge(self::agg(lunas: 1, belum: 1, rpBelum: 5000), 'terlambat');

        $this->assertStringContainsString('Belum Lunas', $html);
        $this->assertStringContainsString('Sisa Rp 5.000', $html);
        $this->assertStringContainsString('denda masih berjalan', $html);
    }

    // ---------------------------------------------------------------
    // Kwitansi
    // ---------------------------------------------------------------

    public function testNomorKwitansiBerurutanPerTahun(): void
    {
        $this->assertSame('KW/2026/0001', TransactionService::receiptNumber(2026, 1));
        $this->assertSame('KW/2027/0123', TransactionService::receiptNumber(2027, 123));
        $this->assertSame('KW/2026/12345', TransactionService::receiptNumber(2026, 12345));
    }

    private static function fine(int $id, int $userId, string $status = 'belum_lunas'): array
    {
        return ['id' => $id, 'user_id' => $userId, 'status' => $status, 'jumlah' => 1000];
    }

    public function testPembayaranDendaValid(): void
    {
        $this->assertNull(TransactionService::finePaymentError([self::fine(1, 7), self::fine(2, 7)], [1, 2]));
    }

    public static function pembayaranTidakValidProvider(): array
    {
        return [
            'tidak ada yang dipilih' => [[], [], 'Pilih minimal satu denda'],
            'denda tidak ditemukan'  => [[self::fine(1, 7)], [1, 99], 'tidak ditemukan'],
            'denda ditahan'          => [[self::fine(1, 7, 'ditahan')], [1], 'masih ditahan'],
            'denda sudah lunas'      => [[self::fine(1, 7, 'lunas')], [1], 'tidak dalam status belum lunas'],
            'denda dibebaskan'       => [[self::fine(1, 7, 'dibebaskan')], [1], 'tidak dalam status belum lunas'],
            'beda anggota'           => [[self::fine(1, 7), self::fine(2, 8)], [1, 2], 'satu anggota'],
        ];
    }

    #[DataProvider('pembayaranTidakValidProvider')]
    public function testPembayaranDendaTidakValid(array $fines, array $ids, string $pesan): void
    {
        $this->assertStringContainsString($pesan, (string) TransactionService::finePaymentError($fines, $ids));
    }

    public function testCatatanPembayaranMaksimal255Karakter(): void
    {
        $this->assertNotNull(TransactionService::finePaymentError([self::fine(1, 7)], [1], str_repeat('a', 256)));
    }

    public static function terbilangProvider(): array
    {
        return [
            [0, 'nol'],
            [1, 'satu'],
            [11, 'sebelas'],
            [15, 'lima belas'],
            [20, 'dua puluh'],
            [99, 'sembilan puluh sembilan'],
            [100, 'seratus'],
            [115, 'seratus lima belas'],
            [1000, 'seribu'],
            [1500, 'seribu lima ratus'],
            [12500, 'dua belas ribu lima ratus'],
            [50000, 'lima puluh ribu'],
            [101000, 'seratus satu ribu'],
            [1000000, 'satu juta'],
            [2750300, 'dua juta tujuh ratus lima puluh ribu tiga ratus'],
        ];
    }

    #[DataProvider('terbilangProvider')]
    public function testTerbilang(int $angka, string $kata): void
    {
        $this->assertSame($kata, terbilang($angka));
    }

    private static function payment(): array
    {
        return [
            'id' => 1, 'nomor_kwitansi' => 'KW/2026/0007', 'total' => 6000, 'dibayar_at' => '2026-11-03 10:15:00',
            'catatan' => 'Dibayar orang tua', 'member_name' => 'Siti Aminah', 'member_code' => 'SSW-001',
            'class' => 'VII-A', 'jenis_pengguna' => 'siswa', 'petugas_name' => 'Lina',
            'items' => [
                ['transaction_detail_id' => 3, 'judul' => 'Laskar Pelangi', 'kode_inventaris' => 'INV-1', 'kode_transaksi' => 'TRX-1',
                 'is_lost' => 0, 'hari_dikenakan' => 4, 'tarif' => 1000, 'jumlah' => 4000, 'jumlah_awal' => 4000],
                ['transaction_detail_id' => 4, 'judul' => 'Bumi', 'kode_inventaris' => 'INV-2', 'kode_transaksi' => 'TRX-1',
                 'is_lost' => 1, 'hari_dikenakan' => 10, 'tarif' => 1000, 'jumlah' => 2000, 'jumlah_awal' => 10000],
            ],
        ];
    }

    public function testKwitansiMenampilkanRincianDanTerbilang(): void
    {
        $html = view('fines/receipt', [
            'payment' => self::payment(),
            'salinan' => false,
            'setting' => ['nama_lembaga' => 'SMP Contoh', 'logo' => null, 'alamat' => 'Jl. Merdeka 1'],
            'backUrl' => '/fines',
        ]);

        $this->assertStringContainsString('KW/2026/0007', $html);
        $this->assertStringContainsString('SMP Contoh', $html);
        $this->assertStringContainsString('Siti Aminah', $html);
        $this->assertStringContainsString('VII-A', $html);
        $this->assertStringContainsString('Laskar Pelangi', $html);
        $this->assertStringContainsString('Buku hilang', $html);
        $this->assertStringContainsString('disesuaikan dari Rp 10.000', $html);
        $this->assertStringContainsString('Enam ribu rupiah', $html);
        $this->assertStringContainsString('size: A5', $html);
        $this->assertStringNotContainsString('class="salinan"', $html);
    }

    public function testCetakUlangKwitansiDitandaiSalinan(): void
    {
        $html = view('fines/receipt', [
            'payment' => self::payment(),
            'salinan' => true,
            'setting' => ['nama_lembaga' => 'SMP Contoh', 'logo' => null],
            'backUrl' => '/fines',
        ]);

        $this->assertStringContainsString('class="salinan"', $html);
    }

    // ---------------------------------------------------------------
    // Aturan password
    // ---------------------------------------------------------------

    public function testPasswordKuatDiterima(): void
    {
        $this->assertSame([], PasswordPolicy::errors('Zona##123', 'admin'));
        $this->assertSame([], PasswordPolicy::errors('Ab1~cdefgh'));
    }

    public static function passwordLemahProvider(): array
    {
        return [
            'terlalu pendek'          => ['Ab1#xyz', 'minimal 8'],
            'terlalu panjang'         => ['Ab1#' . str_repeat('x', 61), 'maksimal 64'],
            'tanpa huruf besar'       => ['zona##123', 'huruf besar'],
            'tanpa huruf kecil'       => ['ZONA##123', 'huruf kecil'],
            'tanpa angka'             => ['Zona##abc', 'angka'],
            'tanpa karakter khusus'   => ['Zona12345', 'karakter khusus'],
            'memakai spasi'           => ['Zona ##123', 'spasi'],
            'huruf beraksen'          => ['Zoná##123', 'beraksen'],
            'sama dengan username'    => ['Siti##123', 'username', 'siti##123'],
            'password default'        => ['123456', 'default'],
        ];
    }

    #[DataProvider('passwordLemahProvider')]
    public function testPasswordLemahDitolak(string $password, string $pesan, ?string $username = null): void
    {
        $errors = PasswordPolicy::errors($password, $username);

        $this->assertNotSame([], $errors);
        $this->assertStringContainsString($pesan, implode(' ', $errors));
    }

    public function testAturanValidasiStrongPassword(): void
    {
        $validation = service('validation');
        $validation->setRules(['password' => 'required|strong_password[username]']);

        $this->assertTrue($validation->run(['password' => 'Lina##123', 'username' => 'lina_petugas']));

        $validation->reset()->setRules(['password' => 'required|strong_password[username]']);
        $this->assertFalse($validation->run(['password' => 'lina_petugas', 'username' => 'lina_petugas']));
        $this->assertStringContainsString('username', $validation->getError('password'));
    }
}
