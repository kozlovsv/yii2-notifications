<?php

namespace kozlovsv\notifications\channels;


interface ITelegramNotification
{
    /**
     * @return string
     */
    public function getTelegramChatId();
}