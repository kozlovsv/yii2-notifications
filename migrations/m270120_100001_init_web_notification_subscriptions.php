<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;

/**
 * Class m270120_100001_init_web_notification_subscriptions
 */
class m270120_100001_init_web_notification_subscriptions extends Migration
{
    /**
     * Create table `web_push_subscription`
     */
    public function up()
    {
        /** @var /webzop/notifications/Module $module */
        $module = Yii::$app->getModule('notifications');
        if(is_null($module)) return true;
        // If not set channel do nothing
        if (!isset($module->channels['web'])) return true;

        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // notifications
        $this->createTable('{{%web_push_subscription}}', [
            'id' => $this->primaryKey(),
            'subscription' => $this->text()->notNull(),
            'endpoint' => $this->string(500)->notNull(),
            'user_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);
        $this->createIndex('index_2', '{{%web_push_subscription}}', ['user_id']);
        $this->createIndex('index_4', '{{%web_push_subscription}}', ['endpoint']);
        return true;
    }

    /**
     * Drop table `web_push_subscription`
     */
    public function down()
    {
        $this->dropTable('{{%web_push_subscription}}');
        return true;
    }
}
