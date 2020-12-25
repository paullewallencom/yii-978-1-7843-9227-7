<?php

use yii\db\Schema;
use yii\db\Migration;

class m140920_120417_table_create_user extends Migration
{
    public function up()
    {
        $this->createTable('user', [
                'id' => 'pk',
                'username' => 'varchar(24) NOT NULL',
                'password' => 'varchar(128) NOT NULL',
                'authkey' => 'varchar(255) NOT NULL',
            ]);

        $this->insert('user', [
                'username' => 'admin',
                'password' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
                'authkey' => uniqid()
            ]);
        $this->insert('user', [
                'username' => 'demo',
                'password' => Yii::$app->getSecurity()->generatePasswordHash('demo'),
                'authkey' => uniqid()
            ]);
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
