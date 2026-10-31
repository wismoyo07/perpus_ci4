<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h4>Daftar Buku Tersedia</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>No. Panggil</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($books as $b): ?>
            <tr>
                <td><?= esc($b['kode_buku']) ?></td>
                <td><?= esc($b['judul']) ?></td> <!-- judul nantinya bisa di klik dan akan menampilkan view detail buku -->
                <td><?= esc($b['pengarang']) ?></td>
                <td><?= esc(\App\Services\BookService::nomorPanggil($b) ?: '-') ?></td>
                <td><?= $b['stok_tersedia'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="d-flex justify-content-center mt-3">
    <?= $pager->links('default', 'notika') ?>
</div>

<?= $this->endSection() ?>