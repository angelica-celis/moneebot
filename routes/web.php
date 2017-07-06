<?php

Route::post('/secret/telegram/webhook/' . md5(env('TELEGRAM_BOT_TOKEN')), 'BotController@webhook')->name('hook');
