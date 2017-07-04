<?php

namespace App\Commands;

use App\Classes\Helper;
use App\Ethereum\JsonRPCException;
use App\Exceptions\StopBotException;
use Exception;

class CheckBalance extends Command
{
    public $name = '/check_balance';

    public function fire()
    {
        $user = $this->user;

        try {
            $eth = $user->getBalance('ETH');
        } catch (JsonRPCException $exception) {
            $user->sendMessage(__('bot.balance_error') . ' ' . $exception->getMessage());
            throw new StopBotException();
        }

        $user->sendTyping();

        $response = '<b>Ethereum</b>: ' . Helper::niceNumPrint($eth) . ' ETH' . PHP_EOL;

        foreach ($user->ERC20Coins as $coin) {
            try {
                $bal = $user->getBalance($coin);
                $response .= '<b>' . $coin->name . '</b>: ' . Helper::niceNumPrint($bal) . ' ' . $coin->ticker . PHP_EOL;
            } catch (Exception $e) {
                $user->sendMessage($e->getMessage());
            }
        }

        $user->sendMessage($response);


        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();

        return $text === __('bot.check_balance');
    }
}