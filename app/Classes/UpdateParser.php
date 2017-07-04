<?php

namespace App\Classes;

use App\User;

class UpdateParser
{

    private $update;
    private $user;

    public function __construct(DataStorage $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    public function setUpdate($update)
    {
        $this->update = $update;
    }

    public function getUser()
    {
        if ($this->user) {
            return $this->user;
        }

        $from = $this->getFrom();

        if (empty($from['id'])) {
            throw new \Exception("No user found in update");
        }

        /** @var User $user */
        $user = User::firstOrNew([
            'telegram_id' => $from['id']
        ]);

        if (!$user->exists) {
            $user->name = $from['first_name'] . (isset($from['last_name']) ? ' ' . $from['last_name'] : '');
            $user->telegram_id = $from['id'];
            $user->username = $from['username'] ?? null;
            $user->save();
        }

        $this->user = $user;

        return $this->user;
    }

    public function getFrom()
    {
        $update = $this->update;

        if (isset($update['message'])) {
            return $update['message']['from'];
        }

        if (isset($update['callback_query'])) {
            return $update['callback_query']['from'];
        }

        if (isset($update['inline_query'])) {
            return $update['inline_query']['from'];
        }

        return null;
    }

    public function getMessageText()
    {
        return $this->update['message']['text'] ?? null;
    }

    public function getContact()
    {
        return $this->update['message']['contact'] ?? null;
    }

    public function getSendRecipient($text)
    {
        $data = explode(' ', $text);

        $rec = $data[0];
        $recipient = null;

        if (!starts_with($rec, '0')) {
            switch (mb_substr($rec, 0, 1)) {
                case '@' : {
                    /** @var User $recipient */
                    $recipient = User::where('username', str_replace('@', '', $rec))->first();
                }
                    break;

                case '+' : {
                    /** @var User $recipient */
                    $recipient = User::where('phone', $rec)->first();
                }
                    break;
            }
        }

        return [$rec, $recipient];
    }

    public function getSendValue($text)
    {
        $data = explode(' ', $text);
        $value = (float)str_replace(',', '.', $data[1]);

        return $value;
    }

    public function getSendCoin($text)
    {
        $data = explode(' ', $text);
        $coin = $data[2] ?? null;

        return $coin;
    }

    public function isSendRequest($text)
    {
        $data = explode(' ', $text);
        $count = count($data);

        if (!in_array($count, [2, 3])) {
            return false;
        }

        if (starts_with($text, '+')) {
            $phone = substr(explode(' ', $text)[0], 1);

            return preg_match('/^([0-9])+$/', $phone);
        }

        return starts_with($text, '@') || starts_with($text, '0x');
    }

    public function getMessageId()
    {
        $update = $this->update;

        if (isset($update['message'])) {
            return $update['message']['id'];
        }

        if (isset($update['callback_query'])) {
            return $update['callback_query']['message']['message_id'];
        }

        return null;
    }

    public function getCallbackData()
    {
        if (isset($this->update['callback_query']['data'])) {
            $data = json_decode($this->update['callback_query']['data'], true);

            if ($data) {
                return $data;
            }

            $data = $this->dataStorage->get($this->update['callback_query']['data']);

            if ($data) {
                return $data;
            }

            return $this->update['callback_query']['data'];
        }

        return [];
    }

    public function isLanguageText($text, $languages)
    {
        return in_array($text, array_keys($languages));
    }
}