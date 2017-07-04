<?php

namespace App\Commands;

use App\Exceptions\StopBotException;

class Cancel extends Command
{
    public $name = '/cancel';

    public function fire()
    {
        $user = $this->user;

        $user->setState(null);

        $callback_data = $this->updateParser->getCallbackData();

        if (!empty($callback_data['a']) && $callback_data['a'] === 'cancel') {

            $user->editMessage($this->updateParser->getMessageId(), __('bot.operation_canceled'));

            throw new StopBotException();
        }

        $user->sendMessage('Ok');

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();

        $callback_data = $this->updateParser->getCallbackData();

        return $text === __('bot.cancel') || (!empty($callback_data['a']) && $callback_data['a'] === 'cancel');
    }
}