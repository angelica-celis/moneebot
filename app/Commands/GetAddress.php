<?php

namespace App\Commands;

use App\Exceptions\StopBotException;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Telegram;

class GetAddress extends Command
{
    public $name = '/get_address';

    public function fire()
    {
        $user = $this->user;

        $user->sendMessage($user->eth_account);
        $user->sendMessage('<a href="https://etherscan.io/address/' . $user->eth_account . '">Etherscan.io/address/' . $user->eth_account . '</a>');

        // Create and send QR code
        $file = storage_path(str_random() . '-code.png');
        $qrCode = new QrCode($user->eth_account);
        $qrCode->setSize(300)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setWriterByName('png')
            ->setEncoding('UTF-8')
            ->writeFile($file);

        Telegram::sendPhoto([
            'photo' => $file,
            'chat_id' => $user->telegram_id
        ]);

        @unlink($file);

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();

        return $text === __('bot.get_address');
    }
}