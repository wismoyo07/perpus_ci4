<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Kelola Salinan Fisik (Inventaris Buku)</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/books">Buku</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Inventaris</li>
                </ol>
            </nav>
        </div>
        <a href="/books" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Buku
        </a>
    </div>

    <!-- FLASH MESSAGES -->
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

    <div class="row g-4">
        <!-- BOOK DETAILS & QUICK ADD COPY FORM -->
        <div class="col-md-4">
            <!-- Book Catalog Card -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0 fw-semibold">Detail Katalog Buku</h6>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-dark font-bold mb-3"><?= esc($book['judul']) ?></h5>
                    <table class="table table-borderless table-sm small mb-0">
                        <tr>
                            <td class="text-muted" width="120">Kode Katalog:</td>
                            <td class="fw-medium"><?= esc($book['kode_buku']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pengarang:</td>
                            <td class="fw-medium"><?= esc($book['pengarang'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Penerbit:</td>
                            <td class="fw-medium"><?= esc($book['penerbit'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Klasifikasi:</td>
                            <td class="fw-medium"><?= $book['code_ddc'] ? esc($book['code_ddc'] . ' — ' . $book['desc_ddc']) : '<span class="text-muted">Belum diklasifikasi</span>' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor Panggil:</td>
                            <td class="fw-medium"><?= esc($book['nomor_panggil'] ?: '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Stok Total:</td>
                            <td class="fw-medium"><span class="badge bg-secondary"><?= $book['stok_total'] ?></span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Stok Tersedia:</td>
                            <td class="fw-medium"><span class="badge bg-success"><?= $book['stok_tersedia'] ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Add Copy Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="mb-0 fw-semibold">+ Registrasi Salinan Baru</h6>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('books/items/store/'.$book['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Sumber Dana</label>
                            <select name="sumber_dana" class="form-select" required>
                                <?php foreach ($sources as $s): ?>
                                    <option value="<?= esc($s['kode']) ?>" <?= $s['kode'] === 'BOS' ? 'selected' : '' ?>>
                                        <?= esc($s['kode']) ?> — <?= esc($s['source_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Tahun Pengadaan</label>
                            <input type="number" 
                                   name="tahun_pengadaan" 
                                   class="form-control" 
                                   min="2000" 
                                   max="2100" 
                                   value="<?= date('Y') ?>" 
                                   required>
                        </div>
                        <p class="small text-muted">Jika sumber dana atau tahun berbeda dari kode buku, kode salinan memakai sumber dana &amp; tahun tersebut.</p>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle"></i> Generate Salinan Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- PHYSICAL COPIES LIST -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold text-secondary">Daftar Inventaris Fisik (Buku Copy)</h6>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted small">Total: <?= count($items) ?> Salinan</span>
                        <?php if (!empty($items)): ?>
                            <a href="<?= site_url('books/items/labels/'.$book['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-print"></i> Cetak Label
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No. Urut</th>
                                    <th>Kode Inventaris</th>
                                    <th>Sumber Dana</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-box-seam display-6 d-block mb-3 text-muted"></i>
                                            Belum ada salinan fisik untuk buku ini.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td class="ps-4 fw-medium text-secondary">#<?= $item['nomor_urut'] ?></td>
                                            <td><code class="text-primary fw-bold"><?= esc($item['kode_inventaris']) ?></code></td>
                                            <td><span class="badge bg-light text-dark border"><?= esc($item['sumber_dana']) ?></span></td>
                                            <td><?= esc($item['tahun_pengadaan']) ?></td>
                                            <td>
                                                <?php if ($item['status'] === 'available'): ?>
                                                    <span class="badge bg-success">Tersedia</span>
                                                <?php elseif ($item['status'] === 'borrowed'): ?>
                                                    <span class="badge bg-warning text-dark">Dipinjam</span>
                                                <?php elseif ($item['status'] === 'lost'): ?>
                                                    <span class="badge bg-danger">Hilang</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= esc($item['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <?php if ($item['status'] === 'available'): ?>
                                                    <form action="<?= site_url('books/items/delete/'.$item['id']) ?>" 
                                                          method="post" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus salinan ini? Stok buku katalog akan otomatis disesuaikan.')">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Hanya salinan fisik yang tersedia yang dapat dihapus">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
