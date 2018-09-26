<?php

use yii\db\Migration;

class m160814_153306_alter_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user', 'role', $this->integer()->defaultValue(1)->after('email')
        );
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'role');
    }
}
