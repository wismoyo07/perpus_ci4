<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterClass extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | TABLE: class
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'class_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('class');


        /*
        |--------------------------------------------------------------------------
        | TABLE: category (DDC)
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'code_ddc' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'desc_ddc' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('category');


        /*
        |--------------------------------------------------------------------------
        | TABLE: source_fund
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'source_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('source_fund');
    }

    public function down()
    {
        $this->forge->dropTable('class');
        $this->forge->dropTable('category');
        $this->forge->dropTable('source_fund');
    }
}
