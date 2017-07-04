<?php

namespace App\Commands;

use App\Exceptions\StopBotException;
use Telegram;

class Settings extends Command
{
    public $name = '/settings';

    public function fire()
    {
        $user = $this->user;

        $user->sendMessage(__('bot.settings'), [
            'reply_markup' => Telegram::replyKeyboardMarkup([
                'keyboard' => [
                    [
                        [
                            'text' => __('bot.change_password')
                        ]
                    ],
                    [
                        [
                            'text' => __('bot.choose_language')
                        ]
                    ],
                    [
                        [
                            'text' => __('bot.gas_settings')
                        ]
                    ],
                    [
                        [
                            'text' => __('bot.download_json_utc')
                        ]
                    ],
                    [
                        [
                            'text' => __('bot.addCoin')
                        ]
                    ],
                    [
                        [
                            'text' => __('bot.expert_mode')
                        ]
                    ],
                    [
                        [
                            'text' => __('bot.set_phone')
                        ]
                    ],
                    [
                        [
                            'text' => __('bot.cancel')
                        ]
                    ]
                ]
            ])
        ]);

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();

        return $text === __('bot.settings');
    }
}