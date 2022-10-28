<?php

namespace kozlovsv\notifications\channels;

use Exception;
use TelegramBot\Api\BotApi;
use Yii;
use yii\base\InvalidConfigException;
use kozlovsv\notifications\Channel;
use kozlovsv\notifications\Notification;
use yii\helpers\Html;
use yii\helpers\Url;

class TelegramChannel extends Channel
{
    /**
     * @var BotApi
     */
    public $telegram;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->telegram = Yii::$app->get('telegram');
        if (!$this->telegram) throw new InvalidConfigException('Not find module telegram');
    }

    /**
     * Sends a notification in this channel.
     */
    public function send(Notification $notification)
    {
        $chatId = '';
        try {
            if ($notification instanceof ITelegramNotification) {
                $chatId = $notification->getTelegramChatId();
            }
            if (!$chatId) return;
            $message = $this->composeMessage($notification);
            $this->telegram->sendMessage($chatId, $message, 'HTML');
        } catch (Exception $e) {
            Yii::error("Ошибка при отправке сообщения Telegram. Chat {$chatId}. " . $e->getMessage());
        }
    }

    /**
     * Composes message with
     * @param \kozlovsv\notifications\Notification $notification the body content
     * @return string $message
     */
    protected function composeMessage($notification)
    {
        return $notification->getTitle() . "\r\n\r\n" . Html::a('Перейти', Url::to($notification->getRoute(), true));
    }
}
