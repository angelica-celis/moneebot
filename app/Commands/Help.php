<?php

namespace App\Commands;

use App\Exceptions\StopBotException;

class Help extends Command
{
    public $name = '/help';

    public function fire()
    {
        $this->user->sendMessage(__('bot.help_text'));

        throw new StopBotException();
    }
}