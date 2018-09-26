<?php

use app\modules\user\migrations\Migration;
use yii\db\Schema;

class m140209_132017_init extends Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username'             => Schema::TYPE_STRING . '(25) NOT NULL',
            'email'                => Schema::TYPE_STRING . '(255) NOT NULL',
            'password_hash'        => Schema::TYPE_STRING . '(60) NOT NULL',
            'auth_key'             => Schema::TYPE_STRING . '(32) NOT NULL',
            'confirmation_token'   => Schema::TYPE_STRING . '(32)',
            'confirmation_sent_at' => Schema::TYPE_INTEGER,
            'confirmed_at'         => Schema::TYPE_INTEGER,
            'unconfirmed_email'    => Schema::TYPE_STRING . '(255)',
            'recovery_token'       => Schema::TYPE_STRING . '(32)',
            'recovery_sent_at'     => Schema::TYPE_INTEGER,
            'blocked_at'           => Schema::TYPE_INTEGER,
            'registered_from'      => Schema::TYPE_INTEGER,
            'logged_in_from'       => Schema::TYPE_INTEGER,
            'logged_in_at'         => Schema::TYPE_INTEGER,
            'created_at'           => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'           => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $this->tableOptions);

        $this->createIndex('user_unique_username', '{{%user}}', 'username', true);
        $this->createIndex('user_unique_email', '{{%user}}', 'email', true);
        $this->createIndex('user_confirmation', '{{%user}}', 'id, confirmation_token', true);
        $this->createIndex('user_recovery', '{{%user}}', 'id, recovery_token', true);

        $this->createTable('{{%profile}}', [
            'user_id'        => Schema::TYPE_INTEGER . ' PRIMARY KEY',
            'name'           => Schema::TYPE_STRING . '(255)',
            'public_email'   => Schema::TYPE_STRING . '(255)',
            'gravatar_email' => Schema::TYPE_STRING . '(255)',
            'gravatar_id'    => Schema::TYPE_STRING . '(32)',
            'location'       => Schema::TYPE_STRING . '(255)',
            'website'        => Schema::TYPE_STRING . '(255)',
            'bio'            => Schema::TYPE_TEXT,
        ], $this->tableOptions);

        $this->addForeignKey('fk_user_profile', '{{%profile}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');

        $this->insert('{{%user}}', [
            'username'      => 'admin',
            'email'         => 'admin@basic.local',
            'password_hash' => Yii::$app->security->generatePasswordHash('1234567890', 10),
            'auth_key'      => Yii::$app->security->generateRandomString(),
            'confirmed_at'  => time()
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => $this->getDb()->getLastInsertID(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%profile}}');
        $this->dropTable('{{%user}}');
    }
}
