<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<h2>Dashboard Member</h2>

<div class="card mt-4">
    <div class="card-body">
        Buku Dipinjam
        <h3><?= $my_books ?></h3>
    </div>
</div>

<?= $this->endSection() ?>