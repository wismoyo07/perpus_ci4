<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h4>Ganti Password</h4>

<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger">
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>

<form method="post" action="/force-change-password">
<?= csrf_field() ?>

<div class="mb-3">
    <label>Password Baru</label>
    <input type="password" name="password"
           class="form-control" required>
</div>

<button class="btn btn-primary">
    Simpan
</button>

</form>

<?= $this->endSection() ?>