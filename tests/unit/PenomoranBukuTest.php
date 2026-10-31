<?php

use App\Services\BookService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Format kode buku, nomor urut, dan nomor panggil (DDC).
 *
 * @internal
 */
final class PenomoranBukuTest extends CIUnitTestCase
{
    public function testFormatKodeMemakaiTigaDigitNomorUrut(): void
    {
        $this->assertSame('03.11.01.01.001 / APBD / 2025 / 007', BookService::formatKode('03.11.01.01.001', 'APBD', 2025, 7));
        $this->assertSame('03.11.01.01.001 / BOS / 2026 / 1234', BookService::formatKode('03.11.01.01.001', 'BOS', 2026, 1234));
    }

    public function testNomorUrutPertamaAdalahSatu(): void
    {
        $this->assertSame(1, BookService::nextSequence([], '03.11.01.01.001 / BOS / 2026'));
    }

    public function testNomorUrutMemakaiNilaiTerbesarBukanYangTerakhir(): void
    {
        $prefix = '03.11.01.01.001 / BOS / 2026';
        $kode = [
            "{$prefix} / 010",
            "{$prefix} / 002",   // tersimpan paling akhir, tapi bukan yang terbesar
        ];

        $this->assertSame(11, BookService::nextSequence($kode, $prefix));
    }

    public function testNomorUrutMenghitungKodeBukuDanKodeSalinan(): void
    {
        $prefix = '03.11.01.01.001 / APBD / 2025';
        $kode = [
            "{$prefix} / 003",          // kode buku
            "{$prefix} / 005 / 006",    // salinan buku lain yang dialokasikan ke prefix ini
        ];

        $this->assertSame(6, BookService::nextSequence($kode, $prefix));
    }

    public function testNomorUrutMengabaikanPrefixLain(): void
    {
        $kode = [
            '03.11.01.01.001 / BOS / 2026 / 050',
            '03.11.01.01.001 / APBD / 2026 / 009',
            '03.11.01.01.001 / BOS / 2025 / 070',
        ];

        $this->assertSame(10, BookService::nextSequence($kode, '03.11.01.01.001 / APBD / 2026'));
    }

    public function testParseKode(): void
    {
        $this->assertSame(
            ['kode_barang' => '03.11.01.01.001', 'sumber' => 'BOS', 'tahun' => 2026, 'seq' => 12],
            BookService::parseKode('03.11.01.01.001 / BOS / 2026 / 012')
        );
        $this->assertNull(BookService::parseKode('KODE-LAMA-001'));
    }

    public function testPolaKodeBukuMenerimaKodeBarangLain(): void
    {
        $this->assertMatchesRegularExpression(BookService::KODE_BUKU_PATTERN, '03.11.01.01.001 / BOS / 2024 / 005');
        $this->assertMatchesRegularExpression(BookService::KODE_BUKU_PATTERN, '1.5.3.01.01.001 / HIBAH / 2024 / 005');
        $this->assertDoesNotMatchRegularExpression(BookService::KODE_BUKU_PATTERN, 'BOS / 2024 / 005');
    }

    public function testNomorPanggil(): void
    {
        $this->assertSame('005.133 AND p', BookService::callNumber('005.133', 'Andi Wijaya', 'Pemrograman Laravel Dasar'));
        $this->assertSame('800 PRA b', BookService::callNumber('800', 'Pramoedya Ananta Toer', 'Bumi Manusia'));
    }

    public function testNomorPanggilTanpaPengarangMemakaiJudul(): void
    {
        $this->assertSame('000 ENS e', BookService::callNumber('000', '', 'Ensiklopedia Umum'));
    }

    public function testKandidatKlasifikasiDariYangPalingSpesifik(): void
    {
        $this->assertSame(['371', '370', '300'], BookService::ddcCandidates('371.3'));
        $this->assertSame(['297', '290', '200'], BookService::ddcCandidates('297.4'));
        $this->assertSame(['959', '950', '900'], BookService::ddcCandidates('959.8'));
    }

    public function testKandidatKlasifikasiTanpaDuplikat(): void
    {
        $this->assertSame(['005', '000'], BookService::ddcCandidates('005.133'));
        $this->assertSame(['370', '300'], BookService::ddcCandidates('370'));
        $this->assertSame(['800'], BookService::ddcCandidates('800'));
    }

    public function testKandidatKlasifikasiKosongUntukNomorTidakValid(): void
    {
        $this->assertSame([], BookService::ddcCandidates('5.1'));
        $this->assertSame([], BookService::ddcCandidates(''));
    }

    public function testTingkatKodeDdc(): void
    {
        $this->assertSame('kelas', \App\Libraries\DdcData::level('300'));
        $this->assertSame('divisi', \App\Libraries\DdcData::level('370'));
        $this->assertSame('seksi', \App\Libraries\DdcData::level('297'));
        $this->assertSame('kelas', \App\Libraries\DdcData::level('000'));
    }

    public function testNomorPanggilKosongJikaBelumDiklasifikasi(): void
    {
        $this->assertSame('', BookService::callNumber(null, 'Andi Wijaya', 'Pemrograman Laravel'));
        $this->assertSame('', BookService::callNumber('', 'Andi Wijaya', 'Pemrograman Laravel'));
    }
}
