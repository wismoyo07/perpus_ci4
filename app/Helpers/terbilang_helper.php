<?php

if (!function_exists('terbilang')) {
    /**
     * Angka ke kata dalam bahasa Indonesia (bilangan bulat), misalnya
     * 12500 menjadi "dua belas ribu lima ratus". Dipakai untuk kwitansi denda.
     */
    function terbilang(int $angka): string
    {
        if ($angka < 0) {
            return 'minus ' . terbilang(-$angka);
        }

        $satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($angka < 12) {
            return $angka === 0 ? 'nol' : $satuan[$angka];
        }
        if ($angka < 20) {
            return $satuan[$angka - 10] . ' belas';
        }
        if ($angka < 100) {
            return trim($satuan[intdiv($angka, 10)] . ' puluh ' . ($angka % 10 ? $satuan[$angka % 10] : ''));
        }

        $tingkat = [
            1_000_000_000_000 => 'triliun',
            1_000_000_000     => 'miliar',
            1_000_000         => 'juta',
            1_000             => 'ribu',
            100               => 'ratus',
        ];
        foreach ($tingkat as $nilai => $nama) {
            if ($angka >= $nilai) {
                $depan = intdiv($angka, $nilai);
                $sisa  = $angka % $nilai;
                // "seratus" dan "seribu", bukan "satu ratus" / "satu ribu"
                $kata = ($depan === 1 && in_array($nama, ['ratus', 'ribu'], true))
                    ? 'se' . $nama
                    : terbilang($depan) . ' ' . $nama;

                return $sisa ? $kata . ' ' . terbilang($sisa) : $kata;
            }
        }

        return '';
    }
}
