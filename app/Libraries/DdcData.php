<?php

namespace App\Libraries;

/**
 * Data acuan Dewey Decimal Classification (DDC) untuk tabel `category`.
 *
 * Tingkat ditentukan dari kode 3 digit:
 *   X00 = kelas utama, XY0 = divisi, lainnya = seksi.
 *
 * Dipakai bersama oleh DdcSeeder (instalasi baru) dan migrasi AddDdcDivisions
 * (database yang sudah berjalan).
 */
class DdcData
{
    /**
     * 10 kelas utama.
     */
    public const CLASSES = [
        '000' => 'Karya Umum, Ilmu Komputer, Informasi',
        '100' => 'Filsafat & Psikologi',
        '200' => 'Agama',
        '300' => 'Ilmu Sosial',
        '400' => 'Bahasa',
        '500' => 'Ilmu Pengetahuan Alam / Sains',
        '600' => 'Teknologi / Ilmu Terapan',
        '700' => 'Seni & Rekreasi',
        '800' => 'Kesusastraan',
        '900' => 'Sejarah & Geografi',
    ];

    /**
     * Divisi (XY0) dan seksi tambahan. 040 sengaja tidak ada (dikosongkan di DDC).
     */
    public const DIVISIONS = [
        // 000 - Karya Umum
        '010' => 'Bibliografi',
        '020' => 'Ilmu Perpustakaan dan Informasi',
        '030' => 'Ensiklopedi Umum',
        '050' => 'Terbitan Berseri Umum (Majalah, Jurnal)',
        '060' => 'Organisasi Umum dan Museum',
        '070' => 'Jurnalisme, Penerbitan, Surat Kabar',
        '080' => 'Kumpulan Karya Umum',
        '090' => 'Manuskrip dan Buku Langka',

        // 100 - Filsafat & Psikologi
        '110' => 'Metafisika',
        '120' => 'Epistemologi',
        '130' => 'Fenomena Paranormal & Psikologi Populer',
        '140' => 'Aliran Filsafat Tertentu',
        '150' => 'Psikologi',
        '160' => 'Logika',
        '170' => 'Etika (Filsafat Moral)',
        '180' => 'Filsafat Kuno, Abad Pertengahan, & Oriental',
        '190' => 'Filsafat Barat Modern',

        // 200 - Agama
        '210' => 'Agama Alamiah (Filsafat Agama)',
        '220' => 'Alkitab & Injil',
        '230' => 'Teologi Kristen',
        '240' => 'Teologi Moral & Devosional Kristen',
        '250' => 'Gereja Setempat & Tarekat Kristen',
        '260' => 'Teologi Sosial & Agama Kristen',
        '270' => 'Sejarah Gereja Kristen',
        '280' => 'Denominasi & Sekte Kristen',
        '290' => 'Agama Lainnya & Agama Perbandingan',
        '297' => 'Islam',

        // 300 - Ilmu Sosial
        '310' => 'Statistik Umum',
        '320' => 'Ilmu Politik',
        '330' => 'Ekonomi',
        '340' => 'Hukum',
        '350' => 'Administrasi Publik & Ilmu Militer',
        '360' => 'Masalah & Layanan Sosial; Asosiasi',
        '370' => 'Pendidikan',
        '380' => 'Perdagangan, Komunikasi, & Transportasi',
        '390' => 'Adat Istiadat, Etiket, & Folklore',

        // 400 - Bahasa
        '410' => 'Linguistik',
        '420' => 'Bahasa Inggris & Anglo-Saxon',
        '430' => 'Bahasa Jerman',
        '440' => 'Bahasa Prancis',
        '450' => 'Bahasa Italia',
        '460' => 'Bahasa Spanyol & Portugis',
        '470' => 'Bahasa Latin',
        '480' => 'Bahasa Yunani Klasik',
        '490' => 'Bahasa-bahasa Lainnya (termasuk Bahasa Indonesia)',

        // 500 - Ilmu Alam & Matematika
        '510' => 'Matematika',
        '520' => 'Astronomi',
        '530' => 'Fisika',
        '540' => 'Kimia & Ilmu Pengetahuan Terkait',
        '550' => 'Ilmu Bumi (Geologi, Meteorologi)',
        '560' => 'Paleontologi (Fosil)',
        '570' => 'Biologi & Ilmu Hayati',
        '580' => 'Botani (Tumbuhan)',
        '590' => 'Zoologi (Hewan)',

        // 600 - Teknologi (Ilmu Terapan)
        '610' => 'Ilmu Kedokteran & Kesehatan',
        '620' => 'Teknik (Engineering) & Sekitarnya',
        '630' => 'Pertanian & Industri Terkait',
        '640' => 'Ekonomi Rumah Tangga & Keluarga',
        '650' => 'Manajemen & Bisnis',
        '660' => 'Teknik Kimia',
        '670' => 'Manufaktur',
        '680' => 'Pabrikasi Produk Khusus',
        '690' => 'Teknik Bangunan & Konstruksi',

        // 700 - Seni & Rekreasi
        '710' => 'Perencanaan Kota & Seni Pertamanan',
        '720' => 'Arsitektur',
        '730' => 'Seni Plastik; Patung',
        '740' => 'Menggambar & Seni Dekorasi',
        '750' => 'Seni Lukis & Lukisan',
        '760' => 'Seni Grafis',
        '770' => 'Fotografi & Foto',
        '780' => 'Musik',
        '790' => 'Seni Rekreasi, Permainan, & Olahraga',

        // 800 - Kesusastraan
        '810' => 'Sastra Amerika dalam Bahasa Inggris',
        '820' => 'Sastra Inggris & Anglo-Saxon',
        '830' => 'Sastra Jerman',
        '840' => 'Sastra Prancis',
        '850' => 'Sastra Italia',
        '860' => 'Sastra Spanyol & Portugis',
        '870' => 'Sastra Latin',
        '880' => 'Sastra Yunani',
        '890' => 'Sastra Bahasa Lainnya (termasuk Sastra Indonesia)',

        // 900 - Sejarah & Geografi
        '910' => 'Geografi & Perjalanan',
        '920' => 'Biografi, Genealogi, Insignia',
        '930' => 'Sejarah Dunia Kuno',
        '940' => 'Sejarah Umum Eropa',
        '950' => 'Sejarah Umum Asia (termasuk Sejarah Indonesia)',
        '960' => 'Sejarah Umum Afrika',
        '970' => 'Sejarah Umum Amerika Utara',
        '980' => 'Sejarah Umum Amerika Selatan',
        '990' => 'Sejarah Umum Wilayah Lainnya',
    ];

    /**
     * Tingkat sebuah kode DDC 3 digit: 'kelas', 'divisi', atau 'seksi'.
     */
    public static function level(string $code): string
    {
        if (substr($code, 1) === '00') {
            return 'kelas';
        }

        return substr($code, 2) === '0' ? 'divisi' : 'seksi';
    }
}
