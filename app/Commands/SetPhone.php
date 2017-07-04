<?php

namespace App\Commands;

use App\Exceptions\StopBotException;
use Telegram;

class SetPhone extends Command
{
    public $name = '/set_phone';

    public function fire()
    {
        $user = $this->user;

        $contact = $this->updateParser->getContact();
        if ($contact && $contact['user_id'] == $user->telegram_id) {
            $phone = $contact['phone_number'];

            if (!starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }

            $user->phone = $phone;
            $user->save();
            $user->redeemPendingTxs();
            $user->sendMessage(__('bot.phone_saved'));
        } else {
            $user->sendMessage(__('bot.welcome1'), [
                'reply_markup' => Telegram::replyKeyboardMarkup([
                    'keyboard' => [
                        [
                            [
                                'text' => __('bot.send_contact'),
                                'request_contact' => true
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
        }

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;

        $text = $updateParser->getMessageText();
        $user = $updateParser->getUser();
        $contact = $updateParser->getContact();

        return $text === __('bot.set_phone') || ($contact && $contact['user_id'] == $user->telegram_id);
    }
}