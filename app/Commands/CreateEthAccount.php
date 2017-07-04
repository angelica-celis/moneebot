<?php

namespace App\Commands;

use App\Ethereum\JsonRPCException;
use App\Exceptions\StopBotException;

class CreateEthAccount extends Command
{
    public $name = '/createEthAccount';

    public function fire()
    {
        $user = $this->updateParser->getUser();

        $user->sendMessage(__('bot.creating_wallet'));
        $user->sendTyping();

        $password = $this->updateParser->getMessageText();

        try {
            $wallet = $user->createWallet($password);
        } catch (JsonRPCException $exception) {
            $user->sendMessage(__('bot.wallet_creating_error') . ' ' . $exception->getMessage());
            throw new StopBotException();
        }

        $user->eth_account = $wallet;
        $user->eth_password = \Crypt::encrypt($password);
        $user->save();

        $user->sendMessage(__('bot.wallet_created'));
        $user->sendMessage($wallet);

        $user->redeemPendingTxs();

        throw new StopBotException();
    }

    public function shouldFire()
    {
        return !$this->user->eth_account;
    }
}