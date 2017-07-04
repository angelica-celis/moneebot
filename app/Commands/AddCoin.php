<?php

namespace App\Commands;

use App\ERC20Coin;
use App\Exceptions\StopBotException;
use Ethereum;
use Exception;
use Telegram;

class AddCoin extends Command
{
    public $name = '/add_coin';

    /**
     * Process adding coin functional
     *
     * @throws StopBotException
     */
    public function fire()
    {
        $user = $this->user;

        $state = $user->getState();

        if (isset($state['action']) && $state['action'] === 'addCoin') {
            $coinAddress = $this->updateParser->getMessageText();

            try {
                $info = Ethereum::getERC20CoinInfo($coinAddress);

            } catch (Exception $e) {
                $user->sendMessage(__('bot.error_adding_erc20') . ': ' . $e->getMessage(), [
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
                ]);
                throw new StopBotException();
            }

            /** @var ERC20Coin $erc20coin */
            $erc20coin = ERC20Coin::firstOrNew([
                'address' => $coinAddress,
                'user_id' => $user->id
            ]);

            $erc20coin->decimals = $info['decimals'];
            $erc20coin->name = $info['name'];
            $erc20coin->ticker = $info['symbol'];
            $erc20coin->save();

            $user->sendMessage($info['name'] . ' (' . $info['symbol'] . ')' . PHP_EOL .
                __('bot.balance') . ': ' . $user->getBalance($erc20coin) . ' ' . $info['symbol']);

            $user->setState(null);

            throw new StopBotException();
        }

        $user->setState([
            'action' => 'addCoin'
        ]);

        $user->sendMessage(__('bot.provide_erc20_address'), [
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
        ]);

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();
        $user = $this->user;

        $state = $user->getState();

        return $text === __('bot.addCoin') || (isset($state['action']) && $state['action'] === 'addCoin');
    }
}