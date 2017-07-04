<?php

namespace App\Commands;

use App\Exceptions\StopBotException;
use Illuminate\Support\Collection;
use Telegram;

class ChooseLang extends Command
{
    protected static $languages = [
        'English' => 'en',
        'Русский' => 'ru',
    ];

    public $name = '/choose_lang';

    public function fire()
    {
        $text = $this->updateParser->getMessageText();
        $user = $this->user;

        if ($this->updateParser->isLanguageText($text, self::$languages)) {
            $user->lang = self::$languages[$text];
            $user->save();

            \Lang::setLocale($user->lang);

            if ($user->eth_account) {
                $user->sendMessage(__('bot.language_set'));
            } else {
                $user->sendMessage(__('bot.welcome2'), ['reply_markup' => Telegram::replyKeyboardHide()]);
            }

        } else {
            $this->user->sendMessage('Choose language', [
                'reply_markup' => Telegram::replyKeyboardMarkup([
                    'keyboard' => collect(self::$languages)->map(function ($item, $key) {
                        return [
                            'text' => $key
                        ];
                    })->values()->chunk(2)->map(function (Collection $item) {
                        return $item->values();
                    })->toArray()
                ])
            ]);
        }

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $text = $this->updateParser->getMessageText();

        return $text === '/start' || $text === __('bot.choose_language') ||
            $this->updateParser->isLanguageText($text, self::$languages);
    }
}