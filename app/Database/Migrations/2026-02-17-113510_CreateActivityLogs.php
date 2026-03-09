<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'auto_increment'=>true
            ],
            'user_id'=>[
                'type'=>'INT',
                'null'=>true
            ],
            'aktivitas'=>[
                'type'=>'VARCHAR',
                'constraint'=>255
            ],
            'target_user_id'=>[
                'type'=>'INT',
                'null'=>true
            ],
            'ip_address'=>[
                'type'=>'VARCHAR',
                'constraint'=>45,
                'null'=>true
            ],
            'user_agent'=>[
                'type'=>'TEXT',
                'null'=>true
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id',true);
        $this->forge->addKey('user_id');

        $this->forge->addForeignKey('user_id','users','id','CASCADE','SET NULL');

        $this->forge->createTable('activity_logs');
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs');
    }
}