<?php

namespace App\Commands;

use App\Classes\Helper;
use App\Exceptions\StopBotException;
use App\PendingTransaction;
use App\User;
use Ethereum;
use Exception;
use Telegram;

class Send extends Command
{
    public $name = '/send';

    public function fire()
    {
        $user = $this->user;

        $text = $this->updateParser->getMessageText();

        if ($this->updateParser->isSendRequest($text)) {

            list($rec, $recipient) = $this->updateParser->getSendRecipient($text);
            $value = $this->updateParser->getSendValue($text);
            $coin = $this->updateParser->getSendCoin($text);

            $this->prepareSend($rec, $recipient, $value, $coin);

            throw new StopBotException();
        }

        $callback_data = $this->updateParser->getCallbackData();

        if (!empty($callback_data['a']) && $callback_data['a'] === 'send') {

            $user->editMessage($this->updateParser->getMessageId(), __('bot.sending_transaction'));

            if ($callback_data['v'] < 0 || (float)$callback_data['v'] != $callback_data['v']) {
                $user->editMessage($this->updateParser->getMessageId(), __('bot.error'));
                throw new StopBotException();
            }

            $user->sendTyping();

            switch (mb_substr($callback_data['rec'], 0, 1)) {
                case '@' : {
                    /** @var User $recipient */
                    $recipient = User::where('username', str_replace('@', '', $callback_data['rec']))->first();

                    if (!$recipient || !$recipient->eth_account) {
                        $account = Ethereum::getMasterAccount();
                        $pendingTx = new PendingTransaction();
                        $pendingTx->username = $callback_data['rec'];
                        $pendingTx->value = $callback_data['v'];
                        $pendingTx->coin = $callback_data['coin'];
                    } else {
                        $account = $recipient->eth_account;
                    }
                }
                    break;

                case '+' : {
                    /** @var User $recipient */
                    $recipient = User::where('phone', $callback_data['rec'])->first();

                    if (!$recipient || !$recipient->eth_account) {
                        $account = Ethereum::getMasterAccount();
                        $pendingTx = new PendingTransaction();
                        $pendingTx->phone = $callback_data['rec'];
                        $pendingTx->value = $callback_data['v'];
                        $pendingTx->coin = $callback_data['coin'];
                    } else {
                        $account = $recipient->eth_account;
                    }
                }
                    break;

                case '0' : {
                    $account = $callback_data['rec'];
                }
                    break;

                default : {
                    $user->editMessage($this->updateParser->getMessageId(), __('bot.unknown_address'));
                    throw new StopBotException();
                }
            }

            $account = trim($account);

            try {
                if (!$callback_data['coin']) {
                    $result = $user->sendETH($account, $callback_data['v'] + $callback_data['commission'],
                        $callback_data['gas'] ?? null);
                } else {
                    $result = $user->sendCoin($account, $callback_data['coin'], $callback_data['v'],
                        $callback_data['gas'] ?? null);
                }
            } catch (Exception $e) {
                $user->editMessage($this->updateParser->getMessageId(), __('bot.error') . ': ' . $e->getMessage());

                throw new StopBotException();
            }

            if (isset($pendingTx)) {
                $pendingTx->tx = $result->result;
                $pendingTx->save();
            }

            $user->editMessage($this->updateParser->getMessageId(), __('bot.transaction_sent') . PHP_EOL . PHP_EOL .
                '<a href="https://etherscan.io/tx/' . $result->result . '">' . $result->result . '</a>');

            throw new StopBotException();

        }

        $this->user->sendMessage(__('bot.send_instructions'));

        throw new StopBotException();
    }

    /**
     * Prepare transaction for sending. Ask user's permission
     *
     * @param $rec
     * @param $recipient
     * @param $value
     * @param $coin
     * @param null $gas
     * @throws StopBotException
     */
    public function prepareSend($rec, $recipient, $value, $coin, $gas = null)
    {
        $user = $this->updateParser->getUser();

        $noUser = false;

        if ((!$recipient || !$recipient->eth_account) && !starts_with($rec, '0x')) {
            $noUser = true;
        }

        $commission = 0;
        if ($noUser) {
            $commission = Ethereum::weiToEth(Ethereum::request('eth_gasPrice')->result) * 21000;

            if ($coin) {
                $user->sendMessage(__('bot.erc20error'));
                throw new StopBotException();
            }
        }

        $max_price = (float)$user->calcGasPrice() * ($coin ? 100000 : 21000);

        if ($gas) {
            $max_price = $gas['value'] * $gas['price'] / pow(10, 9);
        }

        $messageText = __('bot.send_placeholder', [
            'value' => Helper::niceNumPrint($value),
            'coin' => $coin ? $coin : 'ETH',
            'rec' => $rec,
            'max_price' => Helper::niceNumPrint($max_price)
        ]);

        if ($noUser) {
            $messageText .= PHP_EOL . PHP_EOL . __('bot.send_placeholder_commission') . ' ' . $commission . ' ETH';
        }

        $user->sendMessage($messageText, [
            'reply_markup' => Telegram::replyKeyboardMarkup([
                'inline_keyboard' => [
                    [
                        [
                            'text' => __('bot.confirm'),
                            'callback_data' => $this->dataStorage->put([
                                'a' => 'send',
                                'rec' => $rec,
                                'v' => $value,
                                'coin' => $coin,
                                'commission' => $commission,
                                'gas' => $gas
                            ])
                        ],
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
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();

        $callback_data = $this->updateParser->getCallbackData();

        return $text === __('bot.send') ||
            (!empty($callback_data['a']) && $callback_data['a'] === 'send') ||
            $this->updateParser->isSendRequest($text);
    }
}