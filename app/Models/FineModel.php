<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Denda keterlambatan per buku (satu baris per transaction_detail).
 *
 * Status: ditahan (buku dilaporkan hilang, menunggu penyelesaian),
 * belum_lunas, lunas, dibebaskan.
 */
class FineModel extends Model
{
    protected $table = 'fines';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'transaction_id',
        'transaction_detail_id',
        'user_id',
        'hari_terlambat',
        'hari_dikenakan',
        'tarif',
        'jumlah_awal',
        'jumlah',
        'status',
        'catatan',
        'diproses_by',
        'diproses_at',
        'payment_id',
    ];

    /**
     * Agregat denda per transaksi (jumlah baris & nominal per status), diindeks transaction_id.
     *
     * @param int[] $transactionIds
     */
    public function summaryByTransaction(array $transactionIds): array
    {
        $transactionIds = array_values(array_unique(array_map('intval', $transactionIds)));
        if ($transactionIds === []) {
            return [];
        }

        $rows = $this->db->table('fines')
            ->select("transaction_id,
                SUM(status = 'belum_lunas') AS n_belum, COALESCE(SUM(CASE WHEN status = 'belum_lunas' THEN jumlah END), 0) AS rp_belum,
                SUM(status = 'ditahan') AS n_tahan,     COALESCE(SUM(CASE WHEN status = 'ditahan' THEN jumlah END), 0) AS rp_tahan,
                SUM(status = 'lunas') AS n_lunas,       COALESCE(SUM(CASE WHEN status = 'lunas' THEN jumlah END), 0) AS rp_lunas,
                SUM(status = 'dibebaskan') AS n_bebas", false)
            ->whereIn('transaction_id', $transactionIds)
            ->groupBy('transaction_id')
            ->get()
            ->getResultArray();

        return array_column($rows, null, 'transaction_id');
    }

    /** Tambahkan kunci 'fine' (agregat denda) ke setiap baris transaksi. */
    public function attachSummary(array $transactions, string $idKey = 'id'): array
    {
        $summary = $this->summaryByTransaction(array_column($transactions, $idKey));
        foreach ($transactions as &$trx) {
            $trx['fine'] = $summary[$trx[$idKey]] ?? null;
        }
        unset($trx);

        return $transactions;
    }

    /** Ringkasan jumlah & nominal denda per status (opsional untuk satu anggota). */
    public function totals(?int $userId = null): array
    {
        $builder = $this->db->table('fines')
            ->select("
                SUM(status = 'belum_lunas') AS jml_belum, COALESCE(SUM(CASE WHEN status = 'belum_lunas' THEN jumlah END), 0) AS rp_belum,
                SUM(status = 'ditahan') AS jml_tahan,     COALESCE(SUM(CASE WHEN status = 'ditahan' THEN jumlah END), 0) AS rp_tahan,
                SUM(status = 'lunas') AS jml_lunas,       COALESCE(SUM(CASE WHEN status = 'lunas' THEN jumlah END), 0) AS rp_lunas,
                SUM(status = 'dibebaskan') AS jml_bebas,  COALESCE(SUM(CASE WHEN status = 'dibebaskan' THEN jumlah_awal END), 0) AS rp_bebas
            ", false);
        if ($userId !== null) {
            $builder->where('user_id', $userId);
        }

        return $builder->get()->getRowArray();
    }
}
