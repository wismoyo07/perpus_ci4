<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-plus-circle-fill text-primary"></i> Transaksi Peminjaman Baru</h4>
        <a href="/transactions" class="btn btn-outline-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form method="post" action="/transactions/store">
                <?= csrf_field() ?>

                <div class="row">
                    <!-- PILIH MEMBER -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Pilih Member *</label>
                        <select name="member_id" class="form-select form-select-lg" required id="member-select">
                            <option value="">-- Pilih Member --</option>
                            <?php foreach ($members as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= esc($m['name']) ?> 
                                    (<?= esc($m['jenis_pengguna']) ?>) - Limit: <?= in_array($m['jenis_pengguna'], ['guru','pegawai']) ? '5' : '3' ?> Buku
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- JENIS PEMINJAMAN -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Jenis Peminjaman *</label>
                        <select name="jenis_peminjaman" class="form-select form-select-lg" required id="jenis-peminjaman-select">
                            <option value="pendek" selected>Jangka Pendek (Buku Umum - Max 30 Hari)</option>
                            <option value="panjang">Jangka Panjang (Buku Pelajaran - Semester / Tahun Ajaran)</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- TANGGAL PINJAM -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Pinjam</label>
                        <input type="date" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
                    </div>

                    <!-- TANGGAL JATUH TEMPO -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Jatuh Tempo *</label>
                        <input type="date" name="jatuh_tempo" id="jatuh-tempo-picker" class="form-control" required value="<?= date('Y-m-d', strtotime('+10 days')) ?>">
                        <small class="text-muted" id="due-date-hint">
                            Buku umum default: 10 hari (dapat disesuaikan hingga maks 30 hari).
                        </small>
                    </div>
                </div>

                <hr class="my-4">

                <!-- PILIH BUKU & COPIES -->
                <div class="mb-4">
                    <label class="form-label fw-bold d-block mb-3 fs-5">Pilih Salinan Buku yang Dipinjam</label>

                    <div class="row g-3" style="max-height: 450px; overflow-y: auto;">
                        <?php foreach ($books as $b): ?>
                            <!-- Group book copies -->
                            <?php 
                                // Filter available copies for this book
                                $copies = array_filter($book_items, function($item) use ($b) {
                                    return $item['book_id'] == $b['id'];
                                });
                            ?>
                            <?php if (!empty($copies)): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border p-3 rounded shadow-xs book-card" id="book-card-<?= $b['id'] ?>">
                                        <div class="form-check">
                                            <input class="form-check-input book-checkbox"
                                                   type="checkbox"
                                                   id="chk-<?= $b['id'] ?>"
                                                   data-book-id="<?= $b['id'] ?>">
                                            
                                            <label class="form-check-label fw-bold text-dark" for="chk-<?= $b['id'] ?>">
                                                <?= esc($b['judul']) ?>
                                            </label>
                                        </div>
                                        <div class="mt-2 text-muted small">
                                            Stok: <?= $b['stok_tersedia'] ?> tersedia
                                        </div>

                                        <!-- Copy Selection Dropdown -->
                                        <div class="mt-2 copy-select-container" style="display:none;">
                                            <label class="form-label small fw-medium mb-1 text-secondary">Pilih Kode Inventaris *</label>
                                            <select name="book_item_ids[]" 
                                                    class="form-select form-select-sm item-select" 
                                                    id="item-select-<?= $b['id'] ?>" 
                                                    disabled>
                                                <option value="">-- Pilih Salinan --</option>
                                                <?php foreach ($copies as $copy): ?>
                                                    <option value="<?= $copy['id'] ?>">
                                                        <?= esc($copy['kode_inventaris']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-4 pt-2 border-top">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="submit-btn" disabled>
                        <i class="bi bi-check2-circle"></i> Proses Peminjaman
                    </button>
                    <a href="/transactions" class="btn btn-light btn-lg">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<!-- INTERACTIVE DATES AND SELECTION SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const memberSelect = document.getElementById('member-select');
    const jenisSelect = document.getElementById('jenis-peminjaman-select');
    const duePicker = document.getElementById('jatuh-tempo-picker');
    const dueHint = document.getElementById('due-date-hint');
    const checkboxes = document.querySelectorAll('.book-checkbox');
    const submitBtn = document.getElementById('submit-btn');

    // Helper to calculate target date
    function getDateDaysAhead(days) {
        const d = new Date();
        d.setDate(d.getDate() + days);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    // Function to calculate maximum limit based on member type
    function getLimit() {
        const selectedText = memberSelect.options[memberSelect.selectedIndex]?.text || '';
        if (selectedText.includes('guru') || selectedText.includes('pegawai')) {
            return 5;
        }
        return 3;
    }

    // Handle due date changes based on Jenis Peminjaman
    function updateDueDefaults() {
        const val = jenisSelect.value;
        if (val === 'panjang') {
            duePicker.value = getDateDaysAhead(180);
            dueHint.innerHTML = 'Buku pelajaran default: 180 hari (1 semester). Silakan sesuaikan tanggal untuk pengembalian dinamis sesuai arahan sekolah.';
            duePicker.removeAttribute('max');
        } else {
            duePicker.value = getDateDaysAhead(10);
            dueHint.innerHTML = 'Buku umum default: 10 hari. <strong>Maksimal 30 hari</strong> dari hari ini.';
            duePicker.setAttribute('max', getDateDaysAhead(30));
        }
    }

    jenisSelect.addEventListener('change', updateDueDefaults);

    // Validate due date custom picker
    duePicker.addEventListener('change', function() {
        const today = new Date();
        today.setHours(0,0,0,0);
        const selectedDate = new Date(this.value);
        selectedDate.setHours(0,0,0,0);

        if (selectedDate < today) {
            alert('Tanggal jatuh tempo tidak boleh di masa lalu.');
            updateDueDefaults();
            return;
        }

        if (jenisSelect.value === 'pendek') {
            const maxDate = new Date();
            maxDate.setDate(maxDate.getDate() + 30);
            maxDate.setHours(0,0,0,0);

            if (selectedDate > maxDate) {
                alert('Peminjaman buku umum (jangka pendek) maksimal 30 hari.');
                this.value = getDateDaysAhead(30);
            }
        }
    });

    // Handle checkboxes and item dropdown visibility
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const bookId = this.dataset.bookId;
            const card = document.getElementById('book-card-' + bookId);
            const selectContainer = card.querySelector('.copy-select-container');
            const selectEl = card.querySelector('.item-select');

            const checked = document.querySelectorAll('.book-checkbox:checked');
            const limit = getLimit();

            if (memberSelect.value === '') {
                alert('Silakan pilih member terlebih dahulu.');
                this.checked = false;
                return;
            }

            if (checked.length > limit) {
                alert('Maksimal peminjaman untuk member ini adalah ' + limit + ' buku.');
                this.checked = false;
                return;
            }

            if (this.checked) {
                card.classList.add('border-primary', 'bg-light');
                selectContainer.style.display = 'block';
                selectEl.removeAttribute('disabled');
                selectEl.setAttribute('required', 'required');
            } else {
                card.classList.remove('border-primary', 'bg-light');
                selectContainer.style.display = 'none';
                selectEl.setAttribute('disabled', 'disabled');
                selectEl.removeAttribute('required');
                selectEl.value = '';
            }

            // Enable or disable submit button
            const activeChecked = document.querySelectorAll('.book-checkbox:checked');
            if (activeChecked.length > 0) {
                submitBtn.removeAttribute('disabled');
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
            }
        });
    });

    // Reset books selection when member changes
    memberSelect.addEventListener('change', function() {
        checkboxes.forEach(cb => {
            if (cb.checked) {
                cb.checked = false;
                const event = new Event('change');
                cb.dispatchEvent(event);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>