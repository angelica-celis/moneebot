<?php

Route::post('/secret/telegram/webhook', 'BotController@webhook')->name('hook');
