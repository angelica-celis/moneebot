<?php

namespace App\Commands;

use App\Exceptions\StopBotException;
use Exception;
use Telegram;

class ChangePassword extends Command
{
    public $name = '/change_password';

    public function fire()
    {
        $user = $this->user;

        $state = $user->getState();

        if (isset($state['action']) && $state['action'] === 'changePassword') {
            $password = $this->updateParser->getMessageText();

            try {
                $user->changeEthPassword($password);
            } catch (Exception $e) {
                $user->sendMessage($e->getMessage());
                throw new StopBotException();
            }

            $user->sendMessage(__('bot.password_changed'));
            $user->setState(null);
            throw new StopBotException();
        }

        $this->user->setState([
            'action' => 'changePassword'
        ]);

        $this->user->sendMessage(__('bot.provide_new_password'), [
            'reply_markup' => Telegram::replyKeyboardHide()
        ]);

        throw new StopBotException();
    }

    public function shouldFire()
    {
        $updateParser = $this->updateParser;
        $text = $updateParser->getMessageText();

        $user = $this->user;

        $state = $user->getState();

        return $text === __('bot.change_password') || (isset($state['action']) && $state['action'] === 'changePassword');
    }
}