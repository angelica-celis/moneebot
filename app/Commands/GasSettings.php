<?php

namespace App\Commands;

use App\Exceptions\StopBotException;
use Ethereum;
use Telegram;

class GasSettings extends Command
{
    public $name = '/gas_settings';

    public function fire()
    {
        $user = $this->user;

        $callback_data = $this->updateParser->getCallbackData();

        if (!empty($callback_data['a']) && $callback_data['a'] === 'setGasPrice') {
            $user->editMessage($this->updateParser->getMessageId(), 'Ok');

            $user->gas_price = $callback_data['price'];
            $user->save();

            throw new StopBotException();
        }

        $price = Ethereum::weiToEth(Ethereum::request('eth_gasPrice')->result) * pow(10, 9);

        $user->sendMessage(__('bot.choose_gas_price'), [
            'reply_markup' => Telegram::replyKeyboardMarkup([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Cheapest - 2 Gwei',
                            'callback_data' => json_encode([
                                'a' => 'setGasPrice',
                                'price' => 2
                            ])
                        ]
                    ],
                    [
                        [
                            'text' => 'Cheap - 10 Gwei',
                            'callback_data' => json_encode([
                                'a' => 'setGasPrice',
                                'price' => 10
                            ])
                        ]
                    ],
                    [
                        [
                            'text' => 'Recommended - ' . $price . ' Gwei',
                            'callback_data' => json_encode([
                                'a' => 'setGasPrice',
                                'price' => $price
                            ])
                        ]
                    ],
                    [
                        [
                            'text' => 'Fast - 30 Gwei',
                            'callback_data' => json_encode([
                                'a' => 'setGasPrice',
                                'price' => 30
                            ])
                        ]
                    ],
                    [
                        [
                            'text' => 'Fastest - 60 Gwei',
                            'callback_data' => json_encode([
                                'a' => 'setGasPrice',
                                'price' => 60
                            ])
                        ]
                    ],
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
        ]);

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();

        $callback_data = $this->updateParser->getCallbackData();

        return $text === __('bot.gas_settings') || (!empty($callback_data['a']) && $callback_data['a'] === 'setGasPrice');
    }
}