<?php

namespace App\Commands;

use App\Exceptions\StopBotException;
use Telegram;

class ExpertMode extends Command
{
    public $name = '/expert_mode';

    public function fire()
    {
        $user = $this->user;
        $text = $this->updateParser->getMessageText();

        $sendCommand = new Send($this->updateParser, $this->dataStorage);

        $state = $user->getState();

        $cancel = [
            'reply_markup' => Telegram::replyKeyboardMarkup([
                'inline_keyboard' => [
                    [
                        [
                            'text' => __('bot.cancel'),
                            'callback_data' => json_encode([
                                'a' => 'cancel'
                            ])
                        ]
                    ]
                ]
            ])
        ];
        
        if (!$state) {
            $user->setState([
                'action' => 'sendAsExpert'
            ]);
            
            $state = $user->getState();
        }

        if (isset($state['action']) && $state['action'] === 'sendAsExpert') {
            switch ($state['step'] ?? null) {

                case 'step1' : {
                    if (!$this->updateParser->isSendRequest($text)) {
                        $user->sendMessage(__('bot.expert_step1'), $cancel);

                        throw new StopBotException();
                    }

                    $user->setState([
                        'action' => 'sendAsExpert',
                        'step' => 'step2',
                        'send_text' => $text
                    ]);

                    $user->sendMessage(__('bot.expert_step2'), $cancel);

                    throw new StopBotException();

                }
                    break;

                case 'step2' : {
                    $state['gas']['value'] = (int)$text;
                    $state['step'] = 'step3';

                    $user->setState($state);

                    $user->sendMessage(__('bot.expert_step3'), $cancel);

                    throw new StopBotException();
                }
                    break;

                case 'step3' : {

                    $state['gas']['price'] = (int)$text;

                    list($rec, $recipient) = $this->updateParser->getSendRecipient($state['send_text']);
                    $value = $this->updateParser->getSendValue($state['send_text']);
                    $coin = $this->updateParser->getSendCoin($state['send_text']);

                    $sendCommand->prepareSend($rec, $recipient, $value, $coin, $state['gas']);
                    $user->setState(null);

                    throw new StopBotException();
                }
                    break;

                default: {
                    $user->sendMessage(__('bot.expert_step1'), $cancel);
                    $user->setState([
                        'action' => 'sendAsExpert',
                        'step' => 'step1'
                    ]);

                    throw new StopBotException();
                }
            }
        }

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();
        $user = $this->user;

        $state = $user->getState();

        return $text === __('bot.expert_mode') || (isset($state['action']) && $state['action'] === 'sendAsExpert');
    }
}