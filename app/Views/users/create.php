<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h4>Tambah User</h4>

<form method="post" action="/users/store">

<?= csrf_field() ?>

<div class="mb-3">
    <label>Nama</label>
    <input type="text" name="name"
           class="form-control" required>
</div>

<div class="mb-3">
    <label>Username</label>
    <input type="text" name="username"
           class="form-control" required>
</div>

<div class="mb-3">
    <label>Jenis Pengguna</label>
    <select name="jenis_pengguna"
            class="form-select" required>
        <option value="siswa">Siswa</option>
        <option value="guru">Guru</option>
        <option value="pegawai">Pegawai</option>
    </select>
</div>

<div class="mb-3">
    <label>NIS / NIP</label>
    <input type="text" name="nomor_induk"
           class="form-control" required>
</div>

<button class="btn btn-primary">
    Simpan
</button>

</form>

<?= $this->endSection() ?>