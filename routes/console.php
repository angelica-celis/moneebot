<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('run:local', function () {

    $url = exec('cd ' . base_path() . ' && valet fetch-share-url');

    Telegram::setWebhook([
        'url' => str_replace('http:', 'https:', $url . '/secret/telegram/webhook')
    ])->getBody();

    $this->info('Success!');
})->describe('Run the bot locally');


Artisan::command('run:global', function () {

    $url = str_replace('http:', 'https:', route('hook'));

    $this->info($url);

    Telegram::setWebhook([
        'url' => $url
    ])->getBody();

    $this->info('Success!');
})->describe('Run the bot globally');
