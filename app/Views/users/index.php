<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>


    <h2>Manajemen User</h2>

    <a href="/users/create" class="btn btn-primary mb-3">
        + Tambah User
    </a>


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

<form method="get" class="row mb-4">

            <div class="row align-items-end g-2">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="active" <?= ($status=='active')?'selected':'' ?>>
                            Aktif
                        </option>
                        <option value="archived" <?= ($status=='archived')?'selected':'' ?>>
                            Archived
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold">Role</label>
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Role</option>
                        <option value="admin" <?= ($role=='admin')?'selected':'' ?>>
                            Admin
                        </option>
                        <option value="staff" <?= ($role=='staff')?'selected':'' ?>>
                            Staff
                        </option>
                        <option value="member" <?= ($role=='member')?'selected':'' ?>>
                            Member
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Search</label>
                    <input type="text"
                        name="search"
                        value="<?= esc($search ?? '') ?>"
                        class="form-control"
                        placeholder="Nama / Username / Nomor Induk">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-50">
                        Cari
                    </button>

                    <a href="/users"
                        class="btn btn-secondary w-50
                        <?= (!$status && !$role && !$search) ? 'disabled' : '' ?>">
                            Reset
                    </a>
                </div>
                
            </div>

        </form >

        <form method="post" action="/users/bulk-action">
        <?= csrf_field() ?>

        <div class="row mb-2">
            <div class="col-md-3 d-flex gap-2">

                <select name="action" class="form-select">
                    <option value="">Bulk Action</option>
                    <option value="archive">Archive</option>
                    <option value="activate">Activate</option>
                </select>

                <button type="submit" class="btn btn-warning">
                    Apply
                </button>

            </div>
        </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th width="40">
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th>Nama</th>
                        <th>Nomor Induk</th>
                        <th>Jenis</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th width="260">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>

                    


                    <?php foreach($users as $u): ?>
                    <tr>
                        <td></td>
                        <td>
                            <input type="checkbox"
                                name="selected[]"
                                value="<?= $u['id'] ?>">
                        </td>
                        <td><?= esc($u['name']) ?></td>
                        <td><?= esc($u['member_code']) ?></td>
                        <td><?= esc($u['jenis_pengguna']) ?></td>
                        <td>
                            <span class="badge bg-info">
                                <?= esc($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if(!$u['is_validated']): ?>
                                <span class="badge bg-warning">Belum Validasi</span>

                            <?php elseif($u['is_archived']): ?>
                                <span class="badge bg-secondary">Archived</span>

                            <?php else: ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $currentRole = session('role');
                                $isValidated = $u['is_validated'];
                                ?>

                                <?php if (!$isValidated): ?>

                                    <!-- ============================= -->
                                    <!-- USER BELUM VALIDASI -->
                                    <!-- ============================= -->

                                    <?php if ($currentRole === 'admin' || $currentRole === 'staff'): ?>

                                        <!-- Tombol Validasi -->
                                        <form method="post"
                                            action="/users/validate/<?= $u['id'] ?>"
                                            style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-success">
                                                Validasi
                                            </button>
                                        </form>

                                    <?php endif; ?>

                                    <?php if ($currentRole === 'admin'): ?>

                                        <!-- Tombol Delete hanya Admin -->
                                        <form method="post"
                                            action="/users/delete/<?= $u['id'] ?>"
                                            style="display:inline;"
                                            onsubmit="return confirm('Hapus user ini?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>

                                    <?php endif; ?>


                                <?php else: ?>

                                    <!-- ============================= -->
                                    <!-- USER SUDAH VALIDASI -->
                                    <!-- ============================= -->

                                    <?php if ($currentRole === 'admin' || $currentRole === 'staff'): ?>

                                        <a href="/users/edit/<?= $u['id'] ?>"
                                        class="btn btn-sm btn-primary">
                                            Edit
                                        </a>

                                        <form method="post"
                                            action="/users/reset-password/<?= $u['id'] ?>"
                                            style="display:inline;"
                                            onsubmit="return confirm('Reset password user ini ke default?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-warning">
                                                Reset
                                            </button>
                                        </form>

                                        <form method="post"
                                            action="/users/archive/<?= $u['id'] ?>"
                                            style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-secondary">
                                                <?= $u['is_archived'] ? 'Aktifkan' : 'Archive' ?>
                                            </button>
                                        </form>

                                    <?php endif; ?>

                                    <?php if ($currentRole === 'admin'): ?>

                                        <form method="post"
                                            action="/users/delete/<?= $u['id'] ?>"
                                            style="display:inline;"
                                            onsubmit="return confirm('Hapus user ini?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>

                                    <?php endif; ?>

                                <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>

            <div class="mt-3">
                <?= $pager->links() ?>
            </div>
        </form>
<script>
document.getElementById('checkAll').addEventListener('click', function(){
    const checkboxes = document.querySelectorAll('input[name="selected[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

<?= $this->endSection() ?>