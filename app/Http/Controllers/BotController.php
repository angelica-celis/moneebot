<?php

namespace App\Http\Controllers;

use App\Classes\{
    DataStorage, UpdateParser
};
use App\Commands\{
    AddCoin, Cancel, ChangePassword, CheckBalance, ChooseLang, Command, CreateEthAccount, DownloadKey,
    ExpertMode, GasSettings, GetAddress, Help, Send, SetPhone, Settings, Start
};
use App\Exceptions\StopBotException;
use Exception;
use Log;
use Telegram;

class BotController extends Controller
{
    protected $dataStorage;
    protected $updateParser;
    protected $commands = [

        Cancel::class,
        Start::class,
        ChooseLang::class,
        CreateEthAccount::class,
        Help::class,
        SetPhone::class,
        CheckBalance::class,
        GetAddress::class,
        ExpertMode::class,
        Send::class,
        AddCoin::class,
        GasSettings::class,
        Settings::class,
        ChangePassword::class,
        DownloadKey::class
    ];

    public function __construct(DataStorage $dataStorage, UpdateParser $updateParser)
    {
        $this->dataStorage = $dataStorage;
        $this->updateParser = $updateParser;
    }

    /**
     * Process Telegram webhook
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function webhook()
    {
        $this->updateParser->setUpdate(Telegram::getWebhookUpdates());

        // set user's locale
        $user = $this->updateParser->getUser();
        if ($user->lang) {
            \Lang::setLocale($user->lang);
        }

        try {

            // check and run each command
            foreach ($this->commands as $command) {
                /** @var Command $commandInstance */
                $commandInstance = new $command($this->updateParser, $this->dataStorage);

                if ($commandInstance->shouldFire()) {
                    $commandInstance->fire();
                }
            }

            $this->updateParser->getUser()->sendMessage(__('bot.unknown_command'));

        } catch (StopBotException $stopBotException) {
            // stop bot exception. just stop interacting with user's chat
        } catch (Exception $exception) {
            if (env('APP_ENV') !== 'production') {
                throw $exception;
            }

            Log::error($exception);
        }

        return response(['status' => 'ok'], 200);
    }
}
