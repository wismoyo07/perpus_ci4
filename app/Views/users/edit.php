<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h4>Edit User</h4>

<form method="post" action="/users/update/<?= $user['id'] ?>">
<?= csrf_field() ?>

<div class="mb-3">
    <label>Nama</label>
    <input type="text" name="name"
           value="<?= esc($user['name']) ?>"
           class="form-control" required>
</div>

<div class="mb-3">
    <label>Nomor Induk</label>
    <input type="text" name="nomor_induk"
           value="<?= esc($user['nomor_induk']) ?>"
           class="form-control" required>
</div>

<?php if($user['jenis_pengguna'] !== 'siswa'): ?>
    <!-- tampilkan dropdown -->
     <div class="mb-3">
        <label>Jenis Pengguna</label>
        <select name="jenis_pengguna" class="form-select">
            <option value="siswa" <?= $user['jenis_pengguna']=='siswa'?'selected':'' ?>>Siswa</option>
            <option value="guru" <?= $user['jenis_pengguna']=='guru'?'selected':'' ?>>Guru</option>
            <option value="pegawai" <?= $user['jenis_pengguna']=='pegawai'?'selected':'' ?>>Pegawai</option>
        </select>
    </div>
<?php else: ?>
    <input type="hidden" 
           name="jenis_pengguna"
           value="siswa">
<?php endif; ?>

<?php $currentRole = session('role'); ?>

<?php if($currentRole === 'admin' 
    && in_array($user['jenis_pengguna'], ['guru','pegawai'])): ?>

<div class="mb-3">
    <label>Role</label>
    <select name="role" class="form-select">

        <option value="member"
            <?= $user['role']=='member'?'selected':'' ?>>
            Member
        </option>

        <option value="staff"
            <?= $user['role']=='staff'?'selected':'' ?>>
            Staff
        </option>

        <option value="admin"
            <?= $user['role']=='admin'?'selected':'' ?>>
            Admin
        </option>

    </select>
</div>

<?php endif; ?>

<button class="btn btn-primary">Update</button>

</form>

<?= $this->endSection() ?>