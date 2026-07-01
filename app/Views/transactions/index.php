<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Daftar Transaksi</h4>
        <a href="/transactions/create" class="btn btn-primary btn-sm">
            + Transaksi Baru
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th width="60">No</th>
                <th>Kode</th>
                <th>Nama Anggota</th>
                <th>Tipe Pinjam</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Denda</th>
                <th>Staff</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        
        <tbody>
            <?php 
            $page = $pager->getCurrentPage();
            $perPage = 10;
            $i = ($page - 1) * $perPage + 1;
            ?>
            <?php foreach ($transactions as $trx): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= esc($trx['kode_transaksi']) ?></td>

                <td>
                    <?= esc(preg_replace('/\s*\(siswa\)|\s*\(guru\)|\s*\(pegawai\)/i', '', $trx['member_name'])) ?><br>
                    <?php
                    $badgeClass = 'bg-secondary';
                    if ($trx['jenis_pengguna'] === 'siswa') {
                        $badgeClass = 'bg-success';
                    } elseif ($trx['jenis_pengguna'] === 'guru') {
                        $badgeClass = 'bg-primary';
                    } elseif ($trx['jenis_pengguna'] === 'pegawai') {
                        $badgeClass = 'bg-info';
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>" style="font-size: 0.75rem; text-transform: capitalize;">
                        <?= esc($trx['jenis_pengguna']) ?>
                    </span>
                </td>

                <td>
                    <?php if(($trx['jenis_peminjaman'] ?? 'pendek') === 'panjang'): ?>
                        <span class="badge bg-dark">Jangka Panjang</span>
                    <?php else: ?>
                        <span class="badge bg-info text-white">Jangka Pendek</span>
                    <?php endif; ?>
                </td>

                <td><?= date('d/m/Y', strtotime($trx['tanggal_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($trx['jatuh_tempo'])) ?></td>

                <td>
                    <?php if($trx['status'] === 'dipinjam'): ?>
                        <span class="badge bg-primary">Dipinjam</span>
                    <?php elseif($trx['status'] === 'terlambat'): ?>
                        <span class="badge bg-danger">Terlambat</span>
                    <?php else: ?>
                        <span class="badge bg-success">Selesai</span>
                    <?php endif; ?>
                </td>

                <td>Rp <?= number_format($trx['total_denda'],0,',','.') ?></td>

                <td>
                    <small>
                        Pinjam: <?= esc($trx['staff_borrow']) ?><br>
                        <?php if($trx['staff_return']): ?>
                            Kembali: <?= esc($trx['staff_return']) ?>
                        <?php endif; ?>
                    </small>
                </td>

                <td>
                    <?php if($trx['status'] !== 'selesai'): ?>
                        <a href="/transactions/return/<?= $trx['id'] ?>"
                        class="btn btn-sm btn-warning">
                        Return
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        <?= $pager->links('default', 'notika') ?>
    </div>

</div>

<?= $this->endSection() ?>