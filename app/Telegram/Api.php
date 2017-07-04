<?php

namespace App\Telegram;

use Telegram\Bot\Objects\Message;

class Api extends \Telegram\Bot\Api
{
    public function editMessageText(array $params)
    {
        $response = $this->post('editMessageText', $params);

        return new Message($response->getDecodedBody());
    }

    public function answerInlineQuery(array $params)
    {
        $response = $this->post('answerInlineQuery', $params);

        return $response->getBody();
    }
}