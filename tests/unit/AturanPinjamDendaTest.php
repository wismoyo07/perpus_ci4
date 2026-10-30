<?php

use App\Services\TransactionService;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Aturan bisnis peminjaman (lihat README bagian 3.B):
 * - Guru/Pegawai: maks 5 buku per transaksi, denda Rp 5.000/hari.
 * - Siswa: maks 3 buku per transaksi, denda Rp 1.000/hari.
 *
 * @internal
 */
final class AturanPinjamDendaTest extends CIUnitTestCase
{
    public static function batasPinjamProvider(): array
    {
        return [
            'siswa'   => ['siswa', 3],
            'guru'    => ['guru', 5],
            'pegawai' => ['pegawai', 5],
        ];
    }

    #[DataProvider('batasPinjamProvider')]
    public function testBatasBukuPerTransaksi(string $jenis, int $batas): void
    {
        $this->assertSame($batas, TransactionService::bookLimitFor($jenis));
    }

    public static function tarifDendaProvider(): array
    {
        return [
            'siswa'   => ['siswa', 1000],
            'guru'    => ['guru', 5000],
            'pegawai' => ['pegawai', 5000],
        ];
    }

    #[DataProvider('tarifDendaProvider')]
    public function testTarifDendaPerHari(string $jenis, int $tarif): void
    {
        $this->assertSame($tarif, TransactionService::fineRateFor($jenis));
    }

    public static function dendaProvider(): array
    {
        return [
            'kembali tepat jatuh tempo'   => ['siswa', '2026-07-10', '2026-07-10', 0],
            'kembali sebelum jatuh tempo' => ['guru', '2026-07-10', '2026-07-05', 0],
            'siswa telat 1 hari'          => ['siswa', '2026-07-10', '2026-07-11', 1000],
            'siswa telat 10 hari'         => ['siswa', '2026-07-10', '2026-07-20', 10000],
            'guru telat 3 hari'           => ['guru', '2026-07-10', '2026-07-13', 15000],
            'pegawai telat 7 hari'        => ['pegawai', '2026-07-10', '2026-07-17', 35000],
            'telat melewati akhir bulan'  => ['siswa', '2026-01-30', '2026-02-02', 3000],
            'telat melewati akhir tahun'  => ['guru', '2026-12-30', '2027-01-02', 15000],
        ];
    }

    #[DataProvider('dendaProvider')]
    public function testHitungDenda(string $jenis, string $jatuhTempo, string $tanggalKembali, int $denda): void
    {
        $this->assertSame($denda, TransactionService::calculateFine($jenis, $jatuhTempo, $tanggalKembali));
    }
}
