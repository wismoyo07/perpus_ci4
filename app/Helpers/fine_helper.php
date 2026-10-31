<?php

/**
 * Status ringkas denda per transaksi, dari agregat tabel fines
 * (lihat FineModel::summaryByTransaction).
 */

if (!function_exists('fine_summary_status')) {
    /**
     * Menentukan status ringkas dari agregat denda satu transaksi.
     *
     * @param array|null $agg n_belum, n_tahan, n_lunas, n_bebas, rp_belum, rp_tahan, rp_lunas
     *
     * @return array{label: string, class: string, sisa: float}|null null jika transaksi tanpa denda
     */
    function fine_summary_status(?array $agg): ?array
    {
        if (!$agg) {
            return null;
        }

        $nBelum = (int) ($agg['n_belum'] ?? 0);
        $nTahan = (int) ($agg['n_tahan'] ?? 0);
        $nLunas = (int) ($agg['n_lunas'] ?? 0);
        $nBebas = (int) ($agg['n_bebas'] ?? 0);

        if ($nBelum + $nTahan + $nLunas + $nBebas === 0) {
            return null;
        }

        // Status yang masih menuntut tindakan didahulukan
        if ($nBelum > 0) {
            $campuran = ($nTahan + $nLunas + $nBebas) > 0;
            return ['label' => 'Belum Lunas', 'class' => 'bg-danger', 'sisa' => $campuran ? (float) $agg['rp_belum'] : 0.0];
        }
        if ($nTahan > 0) {
            $campuran = ($nLunas + $nBebas) > 0;
            return ['label' => 'Ditahan', 'class' => 'bg-warning text-dark', 'sisa' => $campuran ? (float) $agg['rp_tahan'] : 0.0];
        }
        if ($nLunas > 0) {
            return ['label' => 'Lunas', 'class' => 'bg-success', 'sisa' => 0.0];
        }

        return ['label' => 'Dibebaskan', 'class' => 'bg-secondary', 'sisa' => 0.0];
    }
}

if (!function_exists('fine_badge')) {
    /**
     * Badge status denda untuk ditampilkan di samping nominal total_denda.
     * $trxStatus 'terlambat' menandai denda buku yang belum kembali masih berjalan.
     */
    function fine_badge(?array $agg, ?string $trxStatus = null): string
    {
        $html   = '';
        $status = fine_summary_status($agg);

        if ($status) {
            $html .= '<span class="badge ' . $status['class'] . ' ms-1">' . $status['label'] . '</span>';
            if ($status['sisa'] > 0) {
                $html .= '<br><small class="text-muted">Sisa Rp ' . number_format($status['sisa'], 0, ',', '.') . '</small>';
            }
        }
        if ($trxStatus === 'terlambat') {
            $html .= '<br><small class="text-danger"><i class="fa-solid fa-hourglass-half"></i> denda masih berjalan</small>';
        }

        return $html;
    }
}

if (!function_exists('fine_status_badge')) {
    /** Badge untuk satu baris denda (per buku). */
    function fine_status_badge(string $status): string
    {
        $map = [
            'belum_lunas' => ['Belum Lunas', 'bg-danger'],
            'ditahan'     => ['Ditahan', 'bg-warning text-dark'],
            'lunas'       => ['Lunas', 'bg-success'],
            'dibebaskan'  => ['Dibebaskan', 'bg-secondary'],
        ];
        [$label, $class] = $map[$status] ?? [ucfirst($status), 'bg-light text-dark'];

        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }
}
