<?php

namespace App\Commands;

use App\Exceptions\StopBotException;
use Exception;
use Telegram;

class DownloadKey extends Command
{
    public $name = '/download_key';

    public function fire()
    {
        $user = $this->user;

        try {
            Telegram::sendDocument([
                'chat_id' => $user->telegram_id,
                'document' => glob('/home/forge/.ethereum/keystore/*--' . substr($user->eth_account, 2))[0]
            ]);
        } catch (Exception $e) {
            $user->sendMessage(__('bot.error'));
        }

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();

        return $text === __('bot.download_json_utc');
    }
}