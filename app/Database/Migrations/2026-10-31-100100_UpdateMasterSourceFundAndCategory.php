<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateMasterSourceFundAndCategory extends Migration
{
    /**
     * Kode singkat untuk sumber dana bawaan seeder (dipakai di kode buku).
     */
    private array $kodeSumberDana = [
        'BOS'               => 'BOS',
        'APBD'              => 'APBD',
        'APBN'              => 'APBN',
        'Hibah / Donasi'    => 'HIBAH',
        'Pembelian Pribadi' => 'PRIBADI',
    ];

    public function up()
    {
        // 1. Kolom baru
        $this->forge->addColumn('source_fund', [
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'id',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);

        $this->forge->addColumn('category', [
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);

        // 2. Hapus data ganda akibat seeder dijalankan berulang (simpan id terkecil)
        $this->db->query('DELETE c FROM category c JOIN category k ON c.code_ddc = k.code_ddc AND c.id > k.id');
        $this->db->query('DELETE s FROM source_fund s JOIN source_fund k ON s.source_name = k.source_name AND s.id > k.id');

        // 3. Isi kode sumber dana
        foreach ($this->db->table('source_fund')->get()->getResultArray() as $row) {
            $kode = $this->kodeSumberDana[$row['source_name']] ?? $this->toKode($row['source_name']);
            $this->db->table('source_fund')->where('id', $row['id'])->update(['kode' => $kode]);
        }

        // 4. Sumber dana yang sudah dipakai salinan tapi belum ada di master (dari form lama)
        $kodeMaster = array_column($this->db->table('source_fund')->select('kode')->get()->getResultArray(), 'kode');
        $dipakai    = $this->db->table('book_items')->distinct()->select('sumber_dana')->get()->getResultArray();

        foreach (array_column($dipakai, 'sumber_dana') as $sumber) {
            $kode = $this->toKode((string) $sumber);
            if ($kode === '' || in_array($kode, $kodeMaster, true)) {
                continue;
            }
            $this->db->table('source_fund')->insert(['kode' => $kode, 'source_name' => $sumber, 'is_active' => 1]);
            $kodeMaster[] = $kode;
        }

        // 5. Kunci keunikan
        $this->db->query('ALTER TABLE source_fund MODIFY kode VARCHAR(20) NOT NULL');
        $this->db->query('ALTER TABLE source_fund ADD UNIQUE KEY uq_source_fund_kode (kode)');
        $this->db->query('ALTER TABLE category ADD UNIQUE KEY uq_category_code_ddc (code_ddc)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE category DROP INDEX uq_category_code_ddc');
        $this->db->query('ALTER TABLE source_fund DROP INDEX uq_source_fund_kode');

        $this->forge->dropColumn('category', 'is_active');
        $this->forge->dropColumn('source_fund', ['kode', 'is_active']);
        // Catatan: data ganda yang dihapus di up() tidak dikembalikan.
    }

    private function toKode(string $nama): string
    {
        $kode = strtoupper(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $nama), '-'));

        return substr($kode, 0, 20);
    }
}
