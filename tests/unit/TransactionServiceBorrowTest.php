<?php

use App\Models\FineModel;
use App\Models\TransactionModel;
use App\Models\UserModel;
use App\Services\TransactionService;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\BaseResult;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Memastikan TransactionService::borrow() benar-benar menolak peminjaman
 * yang melanggar aturan, tanpa butuh database (model & koneksi di-mock).
 *
 * @internal
 */
final class TransactionServiceBorrowTest extends CIUnitTestCase
{
    private BaseConnection $dbMock;

    /**
     * @param int[] $counts Hasil hitungan: [pinjaman terlambat, transaksi aktif, denda belum lunas (tabel fines)]
     */
    private function makeService(?array $member, array $counts = [0, 0, 0]): TransactionService
    {
        $userModel = $this->getMockBuilder(UserModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $userModel->method('find')->willReturn($member);

        $transactionModel = $this->getMockBuilder(TransactionModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__call', 'countAllResults', 'insert', 'getInsertID', 'find'])
            ->getMock();
        $transactionModel->method('__call')->willReturnSelf();
        $transactionModel->method('countAllResults')->willReturnOnConsecutiveCalls($counts[0], $counts[1]);
        $transactionModel->method('getInsertID')->willReturn(1);

        $fineModel = $this->getMockBuilder(FineModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__call', 'countAllResults'])
            ->getMock();
        $fineModel->method('__call')->willReturnSelf();
        $fineModel->method('countAllResults')->willReturn($counts[2] ?? 0);

        // Salinan buku tidak ditemukan: borrow() berhenti di sini setelah lolos semua cek aturan.
        $result = $this->createMock(BaseResult::class);
        $result->method('getRowArray')->willReturn(null);

        $db = $this->createMock(BaseConnection::class);
        $db->method('query')->willReturn($result);
        $this->dbMock = $db;

        $service = (new ReflectionClass(TransactionService::class))->newInstanceWithoutConstructor();

        foreach (['db' => $db, 'userModel' => $userModel, 'transactionModel' => $transactionModel, 'fineModel' => $fineModel] as $prop => $value) {
            $property = new ReflectionProperty(TransactionService::class, $prop);
            $property->setValue($service, $value);
        }

        return $service;
    }

    private function member(string $jenis, int $validated = 1): array
    {
        return ['id' => 10, 'jenis_pengguna' => $jenis, 'is_validated' => $validated];
    }

    private function borrowMessage(TransactionService $service, int $jumlahBuku): string
    {
        try {
            $service->borrow(10, $jumlahBuku > 0 ? range(1, $jumlahBuku) : [], 1);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $this->fail('borrow() seharusnya melempar exception');
    }

    public function testSiswaDitolakJikaPinjamLebihDari3Buku(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('siswa')), 4);

        $this->assertSame('Maksimal 3 buku per transaksi', $message);
    }

    public function testSiswaBolehPinjam3Buku(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('siswa')), 3);

        // Lolos cek batas, berhenti di pengecekan salinan buku.
        $this->assertSame('Salinan fisik ID 1 tidak tersedia', $message);
    }

    public function testGuruDitolakJikaPinjamLebihDari5Buku(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('guru')), 6);

        $this->assertSame('Maksimal 5 buku per transaksi', $message);
    }

    public function testGuruBolehPinjam5Buku(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('guru')), 5);

        $this->assertSame('Salinan fisik ID 1 tidak tersedia', $message);
    }

    public function testPegawaiDitolakJikaPinjamLebihDari5Buku(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('pegawai')), 6);

        $this->assertSame('Maksimal 5 buku per transaksi', $message);
    }

    public function testMemberBelumDivalidasiDitolak(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('siswa', 0)), 1);

        $this->assertSame('Member tidak valid', $message);
    }

    public function testMemberTidakDitemukanDitolak(): void
    {
        $message = $this->borrowMessage($this->makeService(null), 1);

        $this->assertSame('Member tidak valid', $message);
    }

    public function testMemberDenganKeterlambatanDitolak(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('guru'), [1, 1, 0]), 1);

        $this->assertSame('Member masih memiliki keterlambatan', $message);
    }

    public function testMemberDengan3TransaksiAktifDitolak(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('guru'), [0, 3, 0]), 1);

        $this->assertSame('Member memiliki 3 transaksi aktif', $message);
    }

    public function testMemberDengan2TransaksiAktifMasihBolehPinjam(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('siswa'), [0, 2, 0]), 1);

        $this->assertSame('Salinan fisik ID 1 tidak tersedia', $message);
    }

    public function testMemberDenganDendaBelumLunasDitolak(): void
    {
        $message = $this->borrowMessage($this->makeService($this->member('siswa'), [0, 0, 1]), 1);

        $this->assertSame('Member masih memiliki denda yang belum dilunasi', $message);
    }

    public function testPinjamTanpaBukuDitolakTanpaMembukaTransaksiDb(): void
    {
        $service = $this->makeService($this->member('siswa'));
        $this->dbMock->expects($this->never())->method('transStart');

        $message = $this->borrowMessage($service, 0);

        $this->assertSame('Pilih minimal 1 buku', $message);
    }

    public function testPinjamDitolakMembatalkanTransaksiDb(): void
    {
        $service = $this->makeService($this->member('siswa'));
        $this->dbMock->expects($this->once())->method('transStart');
        $this->dbMock->expects($this->once())->method('transRollback');
        $this->dbMock->expects($this->never())->method('transComplete');

        $this->assertSame('Maksimal 3 buku per transaksi', $this->borrowMessage($service, 4));
    }

    public function testPinjamGagalDiTengahProsesMembatalkanTransaksiDb(): void
    {
        // Lolos cek aturan & transaksi sudah di-insert, lalu gagal karena salinan buku tidak tersedia.
        $service = $this->makeService($this->member('guru'));
        $this->dbMock->expects($this->once())->method('transRollback');
        $this->dbMock->expects($this->never())->method('transComplete');

        $this->assertSame('Salinan fisik ID 1 tidak tersedia', $this->borrowMessage($service, 2));
    }

    public function testPengembalianTransaksiTidakValidMembatalkanTransaksiDb(): void
    {
        $service = $this->makeService($this->member('siswa'));
        $this->dbMock->expects($this->once())->method('transRollback');
        $this->dbMock->expects($this->never())->method('transComplete');

        $this->expectExceptionMessage('Transaksi tidak valid');

        $service->return(99, [], [], 1);
    }
}
