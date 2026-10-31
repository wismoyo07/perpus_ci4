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

    private function trxDenda(string $status = 'belum_lunas'): array
    {
        return ['id' => 1, 'transaction_id' => 1, 'user_id' => 5, 'jumlah' => '3000.00', 'catatan' => null, 'status' => $status];
    }

    public function testDendaBelumLunasBolehDilunasiTanpaCatatan(): void
    {
        $this->assertNull(TransactionService::fineSettlementError($this->trxDenda(), 'lunas', null));
    }

    public function testPembebasanDendaWajibAlasan(): void
    {
        $this->assertSame('Alasan pembebasan denda wajib diisi', TransactionService::fineSettlementError($this->trxDenda(), 'dibebaskan', '  '));
        $this->assertNull(TransactionService::fineSettlementError($this->trxDenda(), 'dibebaskan', 'Siswa kurang mampu, SK Kepsek'));
    }

    public static function statusDendaTidakBisaDiprosesProvider(): array
    {
        return [
            'sudah lunas'      => ['lunas'],
            'sudah dibebaskan' => ['dibebaskan'],
        ];
    }

    #[DataProvider('statusDendaTidakBisaDiprosesProvider')]
    public function testDendaYangTidakBelumLunasTidakBisaDiprosesLagi(string $status): void
    {
        $this->assertSame('Denda ini tidak dalam status belum lunas', TransactionService::fineSettlementError($this->trxDenda($status), 'lunas', null));
    }

    public function testPenyelesaianDendaLainDitolak(): void
    {
        $this->assertSame('Denda tidak ditemukan', TransactionService::fineSettlementError(null, 'lunas', null));
        $this->assertSame('Jenis penyelesaian denda tidak valid', TransactionService::fineSettlementError($this->trxDenda(), 'cicil', null));
        $this->assertSame('Catatan maksimal 255 karakter', TransactionService::fineSettlementError($this->trxDenda(), 'lunas', str_repeat('a', 256)));
    }

    public function testDendaDitahanTidakBisaDibayarSebelumBukuHilangDiselesaikan(): void
    {
        $this->assertStringStartsWith('Denda ini masih ditahan', TransactionService::fineSettlementError($this->trxDenda('ditahan'), 'lunas', null));
    }

    public static function hariDendaProvider(): array
    {
        return [
            'tepat jatuh tempo'         => ['2026-07-10', '2026-07-10', 10, 0],
            'H+1'                       => ['2026-07-10', '2026-07-11', 10, 1],
            'tepat batas 10 hari'       => ['2026-07-10', '2026-07-20', 10, 10],
            '15 hari dipotong batas 10' => ['2026-07-10', '2026-07-25', 10, 10],
            '15 hari tanpa batas (0)'   => ['2026-07-10', '2026-07-25', 0, 15],
            'batas 5 hari'              => ['2026-07-10', '2026-07-25', 5, 5],
        ];
    }

    #[DataProvider('hariDendaProvider')]
    public function testHariDendaDipotongBatasMaksimal(string $jatuhTempo, string $tanggalAkhir, int $maks, int $hari): void
    {
        $this->assertSame($hari, TransactionService::fineDays($jatuhTempo, $tanggalAkhir, $maks));
    }

    public function testDendaPerBukuDenganBatas(): void
    {
        // terlambat 15 hari, batas 10 hari: siswa Rp10.000, guru Rp50.000 per buku
        $this->assertSame(10000, TransactionService::calculateFine('siswa', '2026-07-10', '2026-07-25', 10));
        $this->assertSame(50000, TransactionService::calculateFine('guru', '2026-07-10', '2026-07-25', 10));
    }

    public function testTanggalLaporanHilangMulaiHPlusSatu(): void
    {
        // pinjam 01/07, jatuh tempo 10/07, hari ini 20/07
        $this->assertNull(TransactionService::lostReportDateError('2026-07-11', '2026-07-01', '2026-07-10', '2026-07-20'));
        $this->assertStringStartsWith('Buku baru bisa dinyatakan hilang mulai 11/07/2026', TransactionService::lostReportDateError('2026-07-10', '2026-07-01', '2026-07-10', '2026-07-20'));
        $this->assertStringStartsWith('Buku baru bisa dinyatakan hilang mulai 11/07/2026', TransactionService::lostReportDateError('2026-07-05', '2026-07-01', '2026-07-10', '2026-07-20'));
    }

    public function testLaporanDiniBolehSebelumJatuhTempo(): void
    {
        $this->assertNull(TransactionService::lostReportDateError('2026-07-05', '2026-07-01', '2026-07-10', '2026-07-05', true));
    }

    public function testTanggalLaporanDiLuarMasaPinjamDitolak(): void
    {
        $this->assertSame('Tanggal laporan tidak boleh sebelum tanggal pinjam', TransactionService::lostReportDateError('2026-06-30', '2026-07-01', '2026-07-10', '2026-07-20', true));
        $this->assertSame('Tanggal laporan tidak boleh melewati hari ini', TransactionService::lostReportDateError('2026-07-21', '2026-07-01', '2026-07-10', '2026-07-20'));
        $this->assertSame('Tanggal laporan tidak valid', TransactionService::lostReportDateError('15-07-2026', '2026-07-01', '2026-07-10', '2026-07-20'));
    }

    public function testTanggalMulaiBolehDinyatakanHilang(): void
    {
        $this->assertSame('2026-07-11', TransactionService::lostReportFrom('2026-07-10'));
        $this->assertSame('01/08/2026', TransactionService::lostReportFrom('2026-07-31', 'd/m/Y'));
    }

    public function testBukuDitemukanDendaTetapDibayar(): void
    {
        $r = TransactionService::lostFineDecision('found', 3000);
        $this->assertSame(['belum_lunas', 3000], [$r['status'], $r['jumlah']]);
    }

    public function testBukuDigantiBukuAtauUangDendaDihapus(): void
    {
        foreach (['replaced', 'paid'] as $jenis) {
            $r = TransactionService::lostFineDecision($jenis, 3000);
            $this->assertSame(['dibebaskan', 0], [$r['status'], $r['jumlah']], $jenis);
        }
    }

    public function testPemutihanSesuaiSuratKeputusan(): void
    {
        $hapus = TransactionService::lostFineDecision('written_off', 3000, 'dihapus', null, 'SK 12/2026');
        $this->assertSame(['dibebaskan', 0], [$hapus['status'], $hapus['jumlah']]);

        $sebagian = TransactionService::lostFineDecision('written_off', 3000, 'sebagian', '1000', 'SK 12/2026');
        $this->assertSame(['belum_lunas', 1000], [$sebagian['status'], $sebagian['jumlah']]);
        $this->assertStringContainsString('SK 12/2026', $sebagian['catatan']);

        $penuh = TransactionService::lostFineDecision('written_off', 3000, 'penuh', null, 'SK 12/2026');
        $this->assertSame(['belum_lunas', 3000], [$penuh['status'], $penuh['jumlah']]);
    }

    public function testPemutihanTanpaSuratAtauNominalSalahDitolak(): void
    {
        $this->assertArrayHasKey('error', TransactionService::lostFineDecision('written_off', 3000, 'dihapus', null, ''));
        $this->assertArrayHasKey('error', TransactionService::lostFineDecision('written_off', 3000, null, null, 'SK 12/2026'));
        $this->assertArrayHasKey('error', TransactionService::lostFineDecision('written_off', 3000, 'sebagian', '5000', 'SK 12/2026'));
        $this->assertArrayHasKey('error', TransactionService::lostFineDecision('written_off', 3000, 'sebagian', '0', 'SK 12/2026'));
        $this->assertArrayHasKey('error', TransactionService::lostFineDecision('lainnya', 3000));
    }
}
