<?php

namespace App;

use Ethereum;
use Exception;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Telegram;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $telegram_id
 * @property string|null $username
 * @property string|null $phone
 * @property string|null $eth_account
 * @property string|null $eth_password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $lang
 * @property int|null $gas_price
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ERC20Coin[] $ERC20Coins
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEthAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEthPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereGasPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'telegram_id'
    ];

    protected $eth;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setState($data)
    {
        \Cache::put('state-' . $this->id, $data, 1440);
    }

    public function sendETH($to, $amount, $gas = null)
    {
        $result = null;

        $this->unlockAccount();

        if (!$gas) {
            $gas['value'] = 21000;
            $gas['price'] = Ethereum::ethToWei($this->calcGasPrice());
        } else {
            $gas['price'] = Ethereum::ethToWei($gas['price'] / pow(10, 9));
        }

        try {
            $result = Ethereum::request('eth_sendTransaction', [
                [
                    'from' => $this->eth_account,
                    'to' => $to,
                    'value' => Ethereum::ethToWei($amount),
                    'gas' => '0x' . dechex($gas['value']),
                    'gasPrice' => $gas['price']
                ]
            ]);
        } catch (\Exception $e) {
            $this->lockAccount();
            throw $e;
        } finally {
            $this->lockAccount();
        }

        return $result;
    }

    public function unlockAccount()
    {
        Ethereum::request('personal_unlockAccount', [
            $this->eth_account,
            \Crypt::decrypt($this->eth_password)
        ]);
    }

    public function calcGasPrice()
    {
        if ($this->gas_price) {
            return (double)($this->gas_price / pow(10, 9));
        }

        return Ethereum::weiToEth(Ethereum::request('eth_gasPrice')->result);
    }

    public function lockAccount()
    {
        Ethereum::request('personal_lockAccount', [
            $this->eth_account
        ]);
    }

    public function sendCoin($to, $coin, $amount, $gas = null)
    {
        $result = null;

        /** @var ERC20Coin $ERC20Coin */
        $ERC20Coin = $this->ERC20Coins->where('ticker', mb_strtoupper($coin))->first();

        if (!$ERC20Coin) {
            throw new \Exception("Coin not found in your wallet");
        }

        $amount = $amount * pow(10, $ERC20Coin->decimals);

        $this->unlockAccount();

        if (!$gas) {
            $gas['value'] = 100000;
            $gas['price'] = Ethereum::ethToWei($this->calcGasPrice());
        } else {
            $gas['price'] = Ethereum::ethToWei($gas['price'] / pow(10, 9));
        }

        try {
            $data = '0xa9059cbb';

            $data .= Ethereum::pad(str_replace('0x', '', $to));
            $data .= Ethereum::pad(dechex($amount));

            $result = Ethereum::request('eth_sendTransaction', [
                [
                    'from' => $this->eth_account,
                    'to' => $ERC20Coin->address,
                    'value' => '0x0',
                    'gas' => '0x' . dechex($gas['value']),
                    'gasPrice' => $gas['price'],
                    'data' => $data
                ]
            ]);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->lockAccount();
        }

        return $result;
    }

    public function ERC20Coins()
    {
        return $this->hasMany(ERC20Coin::class, 'user_id', 'id');
    }

    public function getBalance($coin = 'ETH')
    {
        if ($coin === 'ETH') {
            $response = Ethereum::request('eth_getBalance', [$this->eth_account, 'latest']);

            return Ethereum::weiToEth($response->result);
        }

        if ($coin instanceof ERC20Coin) {

            $result = Ethereum::request('eth_call', [[
                'from' => $this->eth_account,
                'to' => $coin->address,
                'data' => '0x70a08231' . Ethereum::pad(substr($this->eth_account, 2)),
                'value' => '0x0'
            ], 'latest']);

            return hexdec($result->result) / pow(10, $coin->decimals);
        }

        throw new \Exception('Монета ' . $coin . ' не найдена');
    }

    public function redeemPendingTxs()
    {
        $pendingTxs = $this->getPendingTx()->where('coin', null);

        $totalValue = $pendingTxs->sum('value');

        if ($totalValue > 0) {
            $this->sendMessage(__('bot.incoming_transactions', ['value' => $totalValue]));
            Ethereum::request('personal_unlockAccount', [
                Ethereum::getMasterAccount(),
                env('ETHEREUM_MASTER_PASSWORD')
            ]);

            Ethereum::request('eth_sendTransaction', [
                [
                    'from' => Ethereum::getMasterAccount(),
                    'to' => $this->eth_account,
                    'value' => Ethereum::ethToWei($totalValue),
                    'gas' => '0x' . dechex(21000),
                    'gasPrice' => Ethereum::request('eth_gasPrice')->result
                ]
            ]);

            Ethereum::request('personal_lockAccount', [
                Ethereum::getMasterAccount()
            ]);

            PendingTransaction::whereIn('id', $pendingTxs->pluck('id'))->delete();
        }
    }

    /**
     * Get list of a pending transactions
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getPendingTx()
    {
        $q = PendingTransaction::query();

        if ($this->username) {
            $q->orWhere('username', '@' . $this->username);
        }

        if ($this->phone) {
            $q->orWhere('phone', $this->phone);
        }

        if ($this->telegram_id) {
            $q->orWhere('telegram_id', $this->telegram_id);
        }

        return $q->get();

    }

    /**
     * Send message to user's chat
     *
     * @param $text
     * @param array $params
     * @return Telegram\Bot\Message
     */
    public function sendMessage($text, $params = [])
    {
        $params = array_merge([
            'chat_id' => $this->telegram_id,
            'text' => $text,
            'parse_mode' => 'html',
            'reply_markup' => $this->getCurrentMenu(),
            'disable_web_page_preview' => true
        ], $params);

        return Telegram::sendMessage($params);
    }

    /**
     * Get user's current state menu
     *
     * @return string
     */
    private function getCurrentMenu()
    {
        if(!$this->eth_account) {
            return Telegram::replyKeyboardHide();
        }

        return Telegram::replyKeyboardMarkup([
            'keyboard' => [
                [
                    [
                        'text' => __('bot.check_balance')
                    ],
                    [
                        'text' => __('bot.get_address')
                    ]
                ],
                [
                    [
                        'text' => __('bot.send')
                    ],
                    [
                        'text' => __('bot.settings')
                    ]
                ]
            ]
        ]);
    }

    /**
     * Get user's current state
     *
     * @return mixed
     */
    public function getState()
    {
        return \Cache::get('state-' . $this->id);
    }

    /**
     * Create Ethereum wallet for user
     *
     * @param $password
     * @return mixed
     */
    public function createWallet($password)
    {
        $response = Ethereum::request('personal_newAccount', [$password]);

        return $response->result;
    }

    /**
     * Change user's password. Using geth's interactive method, because
     * non-interactive use is not available
     *
     *
     * @param $newPassword
     * @throws Exception
     */
    public function changeEthPassword($newPassword)
    {
        $newPassword = str_replace(PHP_EOL, '', $newPassword);
        $newPassword = escapeshellarg($newPassword);

        $process = proc_open('geth account update ' . $this->eth_account, [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["file", "/tmp/error-output.txt", "a"]
        ], $pipes);

        if (is_resource($process)) {

            fwrite($pipes[0], \Crypt::decrypt($this->eth_password) . PHP_EOL);

            fwrite($pipes[0], $newPassword . PHP_EOL);
            fwrite($pipes[0], $newPassword . PHP_EOL);

            fclose($pipes[0]);

            stream_get_contents($pipes[1]);

            fclose($pipes[1]);

            $return_value = proc_close($process);

            if ($return_value === 0) {
                $this->eth_password = \Crypt::encrypt($newPassword);
                $this->save();
                return;
            }
        }

        throw new \Exception(__('bot.error'));
    }

    /**
     * Edit message in user's chat
     *
     * @param $message_id
     * @param $text
     * @param array $params
     * @return Telegram\Bot\Objects\Message
     */
    public function editMessage($message_id, $text, $params = [])
    {
        $params = array_merge([
            'chat_id' => $this->telegram_id,
            'text' => $text,
            'message_id' => $message_id,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true
        ], $params);

        return Telegram::editMessageText($params);
    }

    /**
     * Send "typing" action to user
     *
     * @return null
     */
    public function sendTyping()
    {
        try {
            Telegram::sendChatAction([
                'chat_id' => $this->telegram_id,
                'action' => 'typing'
            ]);
        } catch (Exception $e) {
        }
    }
}
