<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Impor anggota (siswa, guru, pegawai) dari Excel.
 *
 * Alur: baca file -> analisis tiap baris terhadap data yang sudah ada -> pratinjau -> simpan.
 * Semua logika validasi ada di sini dan tidak menyentuh database, supaya mudah dites.
 */
class MemberImport
{
    public const MAX_ROWS = 1000;

    public const MODE_SKIP   = 'lewati';
    public const MODE_UPDATE = 'perbarui';

    /** Kolom template: kunci => [judul kolom, wajib?, alias judul lain yang diterima]. */
    public const COLUMNS = [
        'name'               => ['Nama Lengkap', true, ['nama']],
        'jenis_pengguna'     => ['Jenis Pengguna', true, ['jenis']],
        'member_code'        => ['NIS / NIP', true, ['nis', 'nip', 'nis/nip', 'nomor induk']],
        'member_code_nation' => ['NISN / NUPTK', false, ['nisn', 'nuptk', 'nisn/nuptk']],
        'class'              => ['Kelas', false, []],
        'phone'              => ['Telepon', false, ['no telepon', 'nomor telepon', 'hp']],
        'whatsapp'           => ['WhatsApp', false, ['wa', 'no whatsapp', 'nomor whatsapp']],
        'username'           => ['Username', false, []],
    ];

    public const JENIS = ['siswa', 'guru', 'pegawai'];

    // ------------------------------------------------------------------
    // Membaca file
    // ------------------------------------------------------------------

    /**
     * Baca sheet pertama. Baris pertama adalah judul kolom.
     *
     * @return array{rows: list<array{row: int, values: array<string, string>}>, error: ?string}
     */
    public static function readFile(string $path): array
    {
        try {
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(false); // format sel dipakai agar NIS/telepon terbaca apa adanya
            $sheet = $reader->load($path)->getSheet(0);
        } catch (\Throwable $e) {
            return ['rows' => [], 'error' => 'File tidak bisa dibaca. Pastikan file berformat .xlsx dari template.'];
        }

        $data = [];
        foreach ($sheet->getRowIterator() as $row) {
            $cells = [];
            foreach ($row->getCellIterator('A', 'J') as $cell) {
                $cells[] = trim((string) $cell->getFormattedValue());
            }
            $data[$row->getRowIndex()] = $cells;
        }

        return self::rowsFromTable($data);
    }

    /**
     * Ubah tabel mentah (nomor baris => daftar sel, baris pertama judul) menjadi baris bernama kolom.
     *
     * @param array<int, list<string>> $table
     */
    public static function rowsFromTable(array $table): array
    {
        if ($table === []) {
            return ['rows' => [], 'error' => 'File kosong.'];
        }

        $headerRow = array_key_first($table);
        $map       = self::mapHeaders($table[$headerRow]);
        if (is_string($map)) {
            return ['rows' => [], 'error' => $map];
        }

        $rows = [];
        foreach ($table as $num => $cells) {
            if ($num === $headerRow) {
                continue;
            }
            $values = [];
            foreach ($map as $key => $index) {
                $values[$key] = trim((string) ($cells[$index] ?? ''));
            }
            if (implode('', $values) === '') {
                continue; // baris kosong
            }
            $rows[] = ['row' => $num, 'values' => $values];
        }

        if ($rows === []) {
            return ['rows' => [], 'error' => 'Tidak ada data anggota di file (selain judul kolom).'];
        }
        if (count($rows) > self::MAX_ROWS) {
            return ['rows' => [], 'error' => 'Maksimal ' . self::MAX_ROWS . ' anggota per file. Bagi file menjadi beberapa bagian.'];
        }

        return ['rows' => $rows, 'error' => null];
    }

    /**
     * Cocokkan judul kolom dengan COLUMNS (tidak peka huruf besar/kecil & spasi).
     *
     * @return array<string, int>|string indeks kolom per kunci, atau pesan error
     */
    public static function mapHeaders(array $headers): array|string
    {
        $norm = static fn ($s) => preg_replace('/\s+/', ' ', strtolower(trim(str_replace('*', '', (string) $s))));

        $map = [];
        foreach ($headers as $i => $header) {
            $h = $norm($header);
            foreach (self::COLUMNS as $key => [$label, , $aliases]) {
                if (!isset($map[$key]) && in_array($h, array_merge([$norm($label)], $aliases), true)) {
                    $map[$key] = $i;
                }
            }
        }

        $missing = [];
        foreach (self::COLUMNS as $key => [$label, $required]) {
            if ($required && !isset($map[$key])) {
                $missing[] = $label;
            }
        }
        if ($missing) {
            return 'Kolom wajib tidak ditemukan: ' . implode(', ', $missing) . '. Gunakan template dari halaman ini.';
        }

        return $map;
    }

    // ------------------------------------------------------------------
    // Analisis
    // ------------------------------------------------------------------

    /**
     * Analisis setiap baris.
     *
     * @param list<array{row: int, values: array<string, string>}> $rows
     * @param array{
     *     by_code: array<string, array{id: int, role: string, jenis_pengguna: string, name: string}>,
     *     usernames: array<string, int>,
     *     nations: array<string, int>,
     *     classes: list<string>
     * } $ctx data yang sudah ada di database (kunci NIS/username/NISN dalam huruf kecil)
     *
     * @return list<array{row: int, status: string, errors: list<string>, notes: list<string>, data: array, user_id: ?int}>
     */
    public static function analyze(array $rows, array $ctx, string $mode): array
    {
        $classes = [];
        foreach ($ctx['classes'] as $c) {
            $classes[self::classKey($c)] = $c;
        }

        $seenCode = $seenNation = $seenUser = [];
        $result   = [];

        foreach ($rows as $r) {
            $v      = $r['values'];
            $errors = $notes = [];

            $name   = preg_replace('/\s+/', ' ', trim($v['name'] ?? ''));
            $jenis  = strtolower(trim($v['jenis_pengguna'] ?? ''));
            $code   = trim($v['member_code'] ?? '');
            $nation = trim($v['member_code_nation'] ?? '');
            $class  = trim($v['class'] ?? '');
            $phone  = self::normalizePhone($v['phone'] ?? '');
            $wa     = self::normalizePhone($v['whatsapp'] ?? '');
            $user   = trim($v['username'] ?? '');

            // --- isian dasar
            if (mb_strlen($name) < 3 || mb_strlen($name) > 100) {
                $errors[] = 'Nama wajib diisi, 3–100 karakter.';
            }
            if (!in_array($jenis, self::JENIS, true)) {
                $errors[] = 'Jenis pengguna harus siswa, guru, atau pegawai.';
            }
            if ($code === '' || mb_strlen($code) > 50) {
                $errors[] = 'NIS / NIP wajib diisi, maksimal 50 karakter.';
            }
            if (mb_strlen($nation) > 50) {
                $errors[] = 'NISN / NUPTK maksimal 50 karakter.';
            }
            foreach (['Telepon' => $phone, 'WhatsApp' => $wa] as $label => $num) {
                if ($num !== '' && !preg_match('/^[0-9+\-\s]{9,20}$/', $num)) {
                    $errors[] = "Nomor {$label} 9–20 karakter, hanya angka, spasi, + atau -.";
                }
            }

            // --- kelas (khusus siswa, harus ada di data kelas)
            if ($jenis !== 'siswa') {
                if ($class !== '') {
                    $notes[] = 'Kelas diabaikan karena bukan siswa.';
                }
                $class = '';
            } elseif ($class !== '') {
                if (isset($classes[self::classKey($class)])) {
                    $class = $classes[self::classKey($class)];
                } else {
                    $errors[] = "Kelas \"{$class}\" tidak ada di data kelas.";
                }
            }

            // --- duplikat di dalam file
            $codeKey = strtolower($code);
            if ($code !== '' && isset($seenCode[$codeKey])) {
                $errors[] = "NIS / NIP sama dengan baris {$seenCode[$codeKey]}.";
            }
            $nationKey = strtolower($nation);
            if ($nation !== '' && isset($seenNation[$nationKey])) {
                $errors[] = "NISN / NUPTK sama dengan baris {$seenNation[$nationKey]}.";
            }

            $existing = $code !== '' ? ($ctx['by_code'][$codeKey] ?? null) : null;
            $status   = 'baru';
            $userId   = null;

            if ($existing) {
                $userId = (int) $existing['id'];
                if ($mode !== self::MODE_UPDATE) {
                    $status  = 'lewati';
                    $notes[] = 'NIS / NIP sudah terdaftar atas nama ' . $existing['name'] . '.';
                } elseif ($existing['role'] !== 'member') {
                    $errors[] = 'NIS / NIP ini milik akun staf/admin, tidak diubah lewat impor.';
                } elseif ($jenis !== '' && $jenis !== $existing['jenis_pengguna']) {
                    $errors[] = 'Jenis pengguna berbeda dengan data tersimpan (' . $existing['jenis_pengguna'] . '). Ubah lewat menu Edit User.';
                } else {
                    $status = 'perbarui';
                    if ($user !== '') {
                        $notes[] = 'Username tidak diubah pada akun yang sudah ada.';
                    }
                }
            }

            // NISN/NUPTK tidak boleh milik pengguna lain
            if ($nation !== '' && isset($ctx['nations'][$nationKey]) && $ctx['nations'][$nationKey] !== $userId) {
                $errors[] = 'NISN / NUPTK sudah dipakai pengguna lain.';
            }

            // --- username (akun baru saja)
            if ($status === 'baru' && !$errors) {
                if ($user !== '') {
                    $userKey = strtolower($user);
                    if (strlen($user) < 4 || strlen($user) > 50 || !preg_match('/^[A-Za-z0-9_.\-]+$/', $user)) {
                        $errors[] = 'Username 4–50 karakter: huruf, angka, titik, garis bawah, atau minus.';
                    } elseif (isset($ctx['usernames'][$userKey]) || isset($seenUser[$userKey])) {
                        $errors[] = 'Username sudah dipakai.';
                    }
                } else {
                    $user = self::suggestUsername($code, $ctx['usernames'], $seenUser);
                    $notes[] = 'Username dibuat otomatis: ' . $user . '.';
                }
            }

            if ($status === 'lewati') {
                $errors = []; // baris yang dilewati tidak diproses, jadi isiannya tidak perlu dinilai
            } elseif ($errors) {
                $status = 'error';
            }

            // catat yang sudah terlihat (hanya bila baris dipakai)
            if ($code !== '' && !isset($seenCode[$codeKey])) {
                $seenCode[$codeKey] = $r['row'];
            }
            if ($nation !== '' && !isset($seenNation[$nationKey])) {
                $seenNation[$nationKey] = $r['row'];
            }
            if ($status === 'baru') {
                $seenUser[strtolower($user)] = true;
            }

            $result[] = [
                'row'     => $r['row'],
                'status'  => $status,
                'errors'  => $errors,
                'notes'   => $notes,
                'user_id' => $userId,
                'data'    => [
                    'name'               => $name,
                    'jenis_pengguna'     => $jenis,
                    'member_code'        => $code,
                    'member_code_nation' => $nation,
                    'class'              => $class,
                    'phone'              => $phone,
                    'whatsapp'           => $wa,
                    'username'           => $status === 'baru' ? $user : '',
                ],
            ];
        }

        return $result;
    }

    /** Ringkasan jumlah per status. */
    public static function summarize(array $analysis): array
    {
        $sum = ['baru' => 0, 'perbarui' => 0, 'lewati' => 0, 'error' => 0];
        foreach ($analysis as $a) {
            $sum[$a['status']]++;
        }

        return $sum;
    }

    /**
     * Telepon yang diketik sebagai angka di Excel kehilangan angka 0 di depan
     * (0812... menjadi 812...). Kembalikan angka 0 itu.
     */
    public static function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        if (preg_match('/^8\d{8,12}$/', $phone)) {
            return '0' . $phone;
        }

        return $phone;
    }

    /** Username otomatis dari NIS/NIP: huruf kecil, karakter tak valid jadi "_", unik. */
    public static function suggestUsername(string $code, array $taken, array $seen = []): string
    {
        $base = strtolower(preg_replace('/[^A-Za-z0-9_.\-]+/', '_', $code));
        $base = trim($base, '_.-');
        if (strlen($base) < 4) {
            $base = 'agt_' . $base;
        }
        $base = substr($base, 0, 45);

        $candidate = $base;
        for ($i = 2; isset($taken[$candidate]) || isset($seen[$candidate]); $i++) {
            $candidate = $base . '_' . $i;
        }

        return $candidate;
    }

    private static function classKey(string $class): string
    {
        return strtolower(preg_replace('/\s+/', '', $class));
    }

    // ------------------------------------------------------------------
    // Template
    // ------------------------------------------------------------------

    /**
     * Template unduhan: judul kolom, contoh baris, kolom teks (agar 0 di depan NIS/telepon tidak hilang),
     * pilihan jenis pengguna & kelas, dan sheet petunjuk.
     */
    public static function buildTemplate(array $classes): Spreadsheet
    {
        $book  = new Spreadsheet();
        $sheet = $book->getActiveSheet()->setTitle('Anggota');

        $col = 'A';
        foreach (self::COLUMNS as [$label, $required]) {
            $sheet->setCellValue($col . '1', $label . ($required ? ' *' : ''));
            $sheet->getColumnDimension($col)->setWidth(20);
            $sheet->getStyle($col . '2:' . $col . (self::MAX_ROWS + 1))
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $col++;
        }
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType('solid')->getStartColor()->setRGB('D1F2EB');
        $sheet->freezePane('A2');

        $examples = [
            ['Siti Aminah', 'siswa', '2026001', '0098765432', $classes[0] ?? '', '081234567890', '081234567890', ''],
            ['Dra. Endang Lestari', 'guru', '197001012000032001', '', '', '081298765432', '081298765432', 'endang_l'],
        ];
        foreach ($examples as $i => $values) {
            $c = 'A';
            foreach ($values as $value) {
                $sheet->setCellValueExplicit($c . ($i + 2), $value, 's');
                $c++;
            }
        }

        // Pilihan isian
        $jenis = new DataValidation();
        $jenis->setType(DataValidation::TYPE_LIST)->setAllowBlank(false)->setShowDropDown(true)
            ->setShowErrorMessage(true)->setErrorTitle('Jenis pengguna')
            ->setError('Pilih siswa, guru, atau pegawai.')->setFormula1('"siswa,guru,pegawai"');
        $sheet->setDataValidation('B2:B' . (self::MAX_ROWS + 1), $jenis);

        $info = $book->createSheet()->setTitle('Petunjuk');
        $lines = [
            'PETUNJUK IMPOR ANGGOTA',
            '',
            '1. Isi data di sheet "Anggota" mulai baris 2. Contoh di baris 2–3 boleh dihapus atau ditimpa.',
            '2. Kolom bertanda * wajib diisi. Maksimal ' . self::MAX_ROWS . ' anggota per file.',
            '3. Jenis Pengguna: siswa, guru, atau pegawai.',
            '4. Kelas hanya untuk siswa dan harus sama dengan daftar kelas di bawah.',
            '5. Username boleh dikosongkan; akan dibuat otomatis dari NIS / NIP.',
            '6. Password awal semua akun baru adalah 123456 dan wajib diganti saat login pertama.',
            '7. NIS / NIP yang sudah terdaftar akan dilewati, atau diperbarui (nama, NISN/NUPTK, kelas, kontak) bila Anda memilih "perbarui" saat impor.',
            '',
            'Daftar kelas:',
        ];
        foreach (array_merge($lines, $classes) as $i => $line) {
            $info->setCellValue('A' . ($i + 1), $line);
        }
        $info->getStyle('A1')->getFont()->setBold(true);
        $info->getColumnDimension('A')->setWidth(110);

        $book->setActiveSheetIndex(0);

        return $book;
    }
}
