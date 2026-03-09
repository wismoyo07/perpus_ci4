<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class UpdateLateTransactions extends BaseCommand
{
    protected $group       = 'Library';
    protected $name        = 'transactions:updateLate';
    protected $description = 'Update status transaksi menjadi terlambat dan cek H-2 jatuh tempo';

    public function run(array $params)
    {
        $db = Database::connect();

        CLI::write('Memulai pengecekan transaksi...', 'yellow');

        // =========================
        // 1️⃣ Update menjadi terlambat
        // =========================

        $affected = $db->table('transactions')
            ->where('status', 'dipinjam')
            ->where('jatuh_tempo <', date('Y-m-d'))
            ->update(['status' => 'terlambat']);

        CLI::write('Update terlambat selesai.', 'green');

        // =========================
        // 2️⃣ Cek H-2 jatuh tempo
        // =========================

        $dueSoon = $db->table('transactions')
            ->where('status', 'dipinjam')
            ->where('DATEDIFF(jatuh_tempo, CURDATE()) =', 2)
            ->get()
            ->getResultArray();

        foreach ($dueSoon as $trx) {
            CLI::write(
                "Reminder H-2: {$trx['kode_transaksi']} - User ID {$trx['user_id']}",
                'light_blue'
            );
        }

        CLI::write('Proses selesai.', 'yellow');
    }
}