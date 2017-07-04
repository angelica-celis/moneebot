<?php

namespace App\Commands;

class Start extends Command
{
    public $name = '/start';

    public function fire()
    {
        $this->user->sendMessage(__('bot.start_text'));
    }
}