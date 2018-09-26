<?php

use yii\db\Migration;

class m160714_010524_alter_profile_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('profile', 'name');
        $this->dropColumn('profile', 'public_email');
        $this->dropColumn('profile', 'gravatar_email');
        $this->dropColumn('profile', 'gravatar_id');
        $this->dropColumn('profile', 'location');
        $this->dropColumn('profile', 'website');
        $this->dropColumn('profile', 'bio');
        $this->addColumn('profile', 'first_name', $this->string()->notNull());
        $this->addColumn('profile', 'last_name',  $this->string()->notNull());
    }

    public function safeDown()
    {
        $this->addColumn('profile', 'name',           $this->string());
        $this->addColumn('profile', 'public_email',   $this->string());
        $this->addColumn('profile', 'gravatar_email', $this->string());
        $this->addColumn('profile', 'gravatar_id',    $this->string(32));
        $this->addColumn('profile', 'location',       $this->string());
        $this->addColumn('profile', 'website',        $this->string());
        $this->addColumn('profile', 'bio',            $this->string());
        $this->dropColumn('profile', 'first_name');
        $this->dropColumn('profile', 'last_name');
    }
}
