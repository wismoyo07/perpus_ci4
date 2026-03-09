<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionDetails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'auto_increment'=>true
            ],
            'transaction_id'=>[
                'type'=>'INT'
            ],
            'book_id'=>[
                'type'=>'INT'
            ],
            'is_lost'=>[
                'type'=>'TINYINT',
                'constraint'=>1,
                'default'=>0
            ],
            'lost_settlement_status'=>[
                'type'=>'ENUM',
                'constraint'=>['pending','replaced'],
                'null'=>true
            ],
            'lost_note'=>[
                'type'=>'TEXT',
                'null'=>true
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id',true);

        $this->forge->addUniqueKey(['transaction_id','book_id']);

        $this->forge->addKey('transaction_id');
        $this->forge->addKey('book_id');
        $this->forge->addKey('is_lost');

        $this->forge->addForeignKey('transaction_id','transactions','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('book_id','books','id','CASCADE','RESTRICT');

        $this->forge->createTable('transaction_details');
    }

    public function down()
    {
        $this->forge->dropTable('transaction_details');
    }
}