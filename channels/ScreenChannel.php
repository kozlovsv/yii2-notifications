<?php

namespace kozlovsv\notifications\channels;

use Yii;
use kozlovsv\notifications\Channel;
use kozlovsv\notifications\Notification;

class ScreenChannel extends Channel
{
    public function send(Notification $notification)
    {
        $db = Yii::$app->getDb();
        $className = $notification->className();
        $currTime = time();
        $db->createCommand()->insert('{{%notifications}}', [
            'class' => strtolower(substr($className, strrpos($className, '\\')+1, -12)),
            'key' => $notification->key,
            'message' => (string)$notification->getTitle(),
            'route' => serialize($notification->getRoute()),
            'user_id' => $notification->userId,
            'created_at' => $currTime,
        ])->execute();
    }

}
