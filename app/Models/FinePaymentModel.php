<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Kwitansi pembayaran denda. Satu kwitansi melunasi satu atau beberapa denda
 * (fines.payment_id) milik anggota yang sama.
 */
class FinePaymentModel extends Model
{
    protected $table = 'fine_payments';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'nomor_kwitansi',
        'tahun',
        'urutan',
        'user_id',
        'total',
        'dibayar_at',
        'diterima_by',
        'catatan',
        'jumlah_cetak',
    ];

    /**
     * Data lengkap kwitansi: pembayaran, anggota, petugas penerima, dan rincian denda per buku.
     */
    public function receiptData(int $paymentId): ?array
    {
        $payment = $this->db->table('fine_payments p')
            ->select('p.*, u.name AS member_name, u.member_code, u.class, u.jenis_pengguna, s.name AS petugas_name')
            ->join('users u', 'u.id = p.user_id')
            ->join('users s', 's.id = p.diterima_by')
            ->where('p.id', $paymentId)
            ->get()
            ->getRowArray();

        if (!$payment) {
            return null;
        }

        $payment['items'] = $this->db->table('fines f')
            ->select('f.*, t.kode_transaksi, t.jatuh_tempo, td.is_lost, td.lost_reported_at, b.judul, bi.kode_inventaris')
            ->join('transactions t', 't.id = f.transaction_id')
            ->join('transaction_details td', 'td.id = f.transaction_detail_id', 'left')
            ->join('books b', 'b.id = td.book_id', 'left')
            ->join('book_items bi', 'bi.id = td.book_item_id', 'left')
            ->where('f.payment_id', $paymentId)
            ->orderBy('t.kode_transaksi')
            ->orderBy('f.id')
            ->get()
            ->getResultArray();

        return $payment;
    }
}
