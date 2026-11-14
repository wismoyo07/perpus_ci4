<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2 class="text-dark fw-bold"><i class="fa-solid fa-users text-primary me-2"></i> Manajemen User</h2>
        <div class="d-flex gap-2">
            <a href="/users/import" class="btn btn-outline-success shadow-sm">
                <i class="fa-solid fa-file-excel me-1"></i> Impor Excel
            </a>
            <a href="/users/create" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-user-plus me-1"></i> Tambah User
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- FILTER BAR -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded-3 py-3">
            <form method="get" action="/users" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="active" <?= ($status=='active')?'selected':'' ?>>Aktif</option>
                        <option value="archived" <?= ($status=='archived')?'selected':'' ?>>Archived</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary">Role</label>
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Role</option>
                        <option value="admin" <?= ($role=='admin')?'selected':'' ?>>Admin</option>
                        <option value="staff" <?= ($role=='staff')?'selected':'' ?>>Staff</option>
                        <option value="member" <?= ($role=='member')?'selected':'' ?>>Member</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label small fw-bold text-secondary">Cari Pengguna</label>
                    <input type="text" name="search" value="<?= esc($search ?? '') ?>" class="form-control" placeholder="Nama / Username / Nomor Induk (NIS / NIP)">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100 fw-bold"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                    <a href="/users" class="btn btn-outline-secondary w-100 fw-semibold <?= (!$status && !$role && !$search) ? 'disabled' : '' ?>"><i class="fa-solid fa-arrow-rotate-left"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- HIDDEN SINGLE FORM FOR ROW ACTIONS (SOLVES HTML NESTED FORMS BUG) -->
    <form id="rowActionForm" method="post" action="" style="display:none;">
        <?= csrf_field() ?>
    </form>

    <!-- BULK ACTION FORM -->
    <form method="post" action="/users/bulk-action">
        <?= csrf_field() ?>

        <div class="d-flex align-items-center gap-2 mb-3 bg-white p-2 rounded border" style="width: fit-content;">
            <select name="action" class="form-select form-select-sm" style="width: 150px;">
                <option value="">Bulk Action</option>
                <option value="archive">Arsipkan</option>
                <option value="activate">Aktifkan</option>
            </select>
            <button type="submit" class="btn btn-sm btn-warning fw-bold text-dark"><i class="fa-solid fa-bolt"></i> Apply</button>
            <button type="submit" formaction="/users/cards" formmethod="get" formtarget="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                <i class="fa-solid fa-id-card"></i> Cetak Kartu
            </button>
        </div>

        <!-- TABLE OF USERS -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7 text-muted">
                            <tr>
                                <th class="ps-4" width="60">No</th>
                                <th width="40">
                                    <input type="checkbox" id="checkAll" class="form-check-input">
                                </th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Nomor Induk (NIS/NIP)</th>
                                <th>Jenis</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="pe-4" width="300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($users)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-user-slash fa-2x mb-3 text-secondary"></i>
                                        <p class="mb-0">Tidak ada data pengguna.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $perPage = 10;
                                $page = (int)($pager->getCurrentPage('default') ?: 1);
                                $i = ($page - 1) * $perPage + 1;
                                ?>
                                <?php foreach($users as $u): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?= $i++ ?></td>
                                    <td>
                                        <input type="checkbox" name="selected[]" value="<?= $u['id'] ?>" class="form-check-input">
                                    </td>
                                    <td><strong><?= esc($u['name']) ?></strong></td>
                                    <td><code><?= esc($u['username']) ?></code></td>
                                    <td><?= esc($u['member_code'] ?: '-') ?></td>
                                    <td class="text-capitalize"><?= esc($u['jenis_pengguna']) ?></td>
                                    <td>
                                        <?php
                                        $roleClass = 'bg-secondary';
                                        if ($u['role'] === 'admin') {
                                            $roleClass = 'bg-danger';
                                        } elseif ($u['role'] === 'staff') {
                                            $roleClass = 'bg-warning text-dark';
                                        } elseif ($u['role'] === 'member') {
                                            $roleClass = 'bg-info text-dark';
                                        }
                                        ?>
                                        <span class="badge <?= $roleClass ?>" style="font-size: 0.75rem; text-transform: uppercase;">
                                            <?= esc($u['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if(!$u['is_validated']): ?>
                                            <span class="badge bg-warning text-dark py-1.5 px-2"><i class="fa-solid fa-circle-exclamation me-1"></i>Belum Validasi</span>
                                        <?php elseif($u['is_archived']): ?>
                                            <span class="badge bg-secondary py-1.5 px-2 text-white"><i class="fa-solid fa-box-archive me-1"></i>Archived</span>
                                        <?php else: ?>
                                            <span class="badge bg-success py-1.5 px-2 text-white"><i class="fa-solid fa-circle-check me-1"></i>Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-nowrap">
                                        <div class="d-flex gap-1 justify-content-start align-items-center">
                                            <?php
                                            $currentRole = session('role');
                                            $isValidated = $u['is_validated'];
                                            ?>

                                            <?php if (!$isValidated): ?>
                                                <!-- ============================= -->
                                                <!-- USER BELUM VALIDASI           -->
                                                <!-- ============================= -->
                                                <?php if ($currentRole === 'admin' || $currentRole === 'staff'): ?>
                                                    <button type="button" class="btn btn-sm btn-success fw-bold" 
                                                            onclick="submitRowAction('/users/validate/<?= $u['id'] ?>', 'Apakah Anda yakin ingin memvalidasi user ini?')">
                                                        <i class="fa-solid fa-check"></i> Validasi
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($currentRole === 'admin'): ?>
                                                    <button type="button" class="btn btn-sm btn-danger fw-bold" 
                                                            onclick="submitRowAction('/users/delete/<?= $u['id'] ?>', 'Apakah Anda yakin ingin menghapus user ini secara permanen?')">
                                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                                    </button>
                                                <?php endif; ?>

                                            <?php else: ?>
                                                <!-- ============================= -->
                                                <!-- USER SUDAH VALIDASI           -->
                                                <!-- ============================= -->
                                                <?php if ($currentRole === 'admin' || $currentRole === 'staff'): ?>
                                                    <a href="/users/edit/<?= $u['id'] ?>" class="btn btn-sm btn-primary fw-bold">
                                                        <i class="fa-solid fa-pen-to-square"></i> Ubah
                                                    </a>

                                                    <a href="/users/cards?selected[]=<?= $u['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                                                        <i class="fa-solid fa-id-card"></i> Kartu
                                                    </a>

                                                    <button type="button" class="btn btn-sm btn-warning fw-bold text-dark" 
                                                            onclick="submitRowAction('/users/reset-password/<?= $u['id'] ?>', 'Apakah Anda yakin ingin mereset sandi user ini ke default (123456)?')">
                                                        <i class="fa-solid fa-key"></i> Reset Sandi
                                                    </button>

                                                    <?php 
                                                    $confirmMsg = $u['is_archived'] ? 'Apakah Anda yakin ingin mengaktifkan kembali user ini?' : 'Apakah Anda yakin ingin mengarsipkan user ini?';
                                                    $btnLabel = $u['is_archived'] ? 'Aktifkan' : 'Arsipkan';
                                                    $btnClass = $u['is_archived'] ? 'btn-success' : 'btn-secondary';
                                                    $btnIcon = $u['is_archived'] ? 'fa-solid fa-circle-check' : 'fa-solid fa-box-archive';
                                                    ?>
                                                    <button type="button" class="btn btn-sm <?= $btnClass ?> fw-bold text-white" 
                                                            onclick="submitRowAction('/users/archive/<?= $u['id'] ?>', '<?= $confirmMsg ?>')">
                                                        <i class="<?= $btnIcon ?>"></i> <?= $btnLabel ?>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($currentRole === 'admin'): ?>
                                                    <button type="button" class="btn btn-sm btn-danger fw-bold" 
                                                            onclick="submitRowAction('/users/delete/<?= $u['id'] ?>', 'Apakah Anda yakin ingin menghapus user ini secara permanen?')">
                                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-center mt-3">
            <?= $pager->links('default', 'notika') ?>
        </div>
    </form>
</div>

<script>
// Handle Select All Checkboxes
document.getElementById('checkAll').addEventListener('click', function(){
    const checkboxes = document.querySelectorAll('input[name="selected[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// JS Helper to submit row action via single hidden form
function submitRowAction(url, confirmMessage) {
    if (confirm(confirmMessage)) {
        const form = document.getElementById('rowActionForm');
        form.setAttribute('action', url);
        form.submit();
    }
}
</script>

<?= $this->endSection() ?>