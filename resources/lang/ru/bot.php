<?php

return [
    'choose_language' => 'Выбрать язык / Choose language',
    'language_set' => 'Язык установлен',

    'start_text' => '<b>Monee</b> is a full-featured Ethereum wallet that can store ether and any other ERC20 tokens, some of our features:
 
<b>Everywhere</b>
Monee is available anywhere your Telegram account is.
	
<b>Simple</b>
To start using your wallet takes only few seconds – choose your password and we’ll do the rest.
	
<b>Security</b>
Telegram is the most secure messenger in the world. Combine it with Ethereum blockchain and you get best solution to host your wallet.
 
<b>Sending</b>
You can send funds to any Telegram user, just type his @username or phone number (International format, +). And of course you can send to regular ether-address.
 
<b>Escrow</b>
When you send funds to the users who are not in Monee yet, our escrow smart contract holds them till people register. That’s the easiest way you can move ether and tokens on the market!

Got questions? Check out our FAQ here – /help',

    'welcome1' => 'Отправьте Ваш контакт используя кнопку на клавиатуре. ',
    'welcome2' => 'Сейчас мы создадим Вам Ethereum-кошелек. ' .
        'Придумайте для него пароль. Используйте сложный набор букв и цифр, но обязательно сохраните в надежном месте. ' . PHP_EOL . PHP_EOL . '<b>Внимание!</b> ' .
        'Рекомендуется удалять сообщение с паролем после каждого ввода.',

    'send_contact' => 'Отправить контакт',
    'creating_wallet' => 'Создаем кошелек...',
    'wallet_creating_error' => 'Ошибка при создании кошелька:',
    'wallet_created' => 'Кошелек успешно создан',

    'balance_error' => 'Ошибка при запросе баланса:',

    'send_instructions' => 'Чтобы отправить ETH другому пользователю, введите <b>@username СУММА</b>' .
        PHP_EOL . PHP_EOL .
        'Так же вы можете использовать телефон в формате +7999999999 или адрес Ethereum в качестве получателя.' .
        PHP_EOL . PHP_EOL .
        'Чтобы отправить ERC20 токен другому пользователю, укажите его символ после суммы перевода. ' .
        'Например: <b>@username 1 BNT</b>',

    'provide_erc20_address' => 'Введите адрес ERC20 токена',

    'cancel' => 'Отмена',
    'exit' => 'Выход',
    'error' => 'Ошибка',

    'settings' => 'Настройки',

    'change_password' => 'Сменить пароль',
    'download_json_utc' => 'Скачать JSON/UTC файл',

    'provide_new_password' => 'Введите новый пароль',
    'settings_updated' => 'Настройки сохранены',

    'operation_canceled' => 'Операция отменена',
    'sending_transaction' => 'Отправляем перевод...',

    'unknown_address' => 'Ошибка: неизвестный адрес',
    'transaction_sent' => 'Перевод отправлен',

    'error_adding_erc20' => 'Ошибка добавления монеты',
    'balance' => 'Ваш баланс',
    'password_changed' => 'Новый пароль успешно установлен',
    'erc20error' => 'Нельзя отправить ERC20 токены пользователю без аккаунта в @Moneebot',

    'send_placeholder' => 'Отправить <b>:value :coin</b> на кошелек <b>:rec</b>?' .
        PHP_EOL . 'Максимальная стоимость транзакции: :max_price ETH',

    'send_placeholder_commission' => 'Так как пользователь не зарегистрирован в @Moneebot, ' .
        'к переводу добавлена комиссия в размере',

    'confirm' => 'Подтверждаю',

    'unknown_command' => 'Неизвестная команда',

    'incoming_transactions' => 'У вас есть входящие транзакции на сумму :value ETH, ожидайте зачисления.',


    'check_balance' => 'Мой баланс',

    'get_address' => 'Мой адрес',
    'send' => 'Отправить',
    'addCoin' => 'Добавить ERC20 монету',

    'set_phone' => 'Указать номер телефона',

    'phone_saved' => 'Телефон успешно сохранен',

    'gas_settings' => 'Настройки цены Gas',

    'choose_gas_price' => 'Выберите цену Gas',

    'expert_mode' => 'Expert Mode',
    'expert_step1' => 'Укажите получателя и количество через пробел (например: <b>@username СУММА</b>)',
    'expert_step2' => 'Укажите Gas Limit',
    'expert_step3' => 'Укажите Gas Price в Gwei (от 1 до 100)',

    'help_text' => '<b>Monee это полнофункциональный Ethereum-кошелёк?</b>
Да. Мы предоставляем JSON/UTC файлы, таким образом вы можете управлять своими средствами любым удобным способом. В кошельке могут храниться Ethereum и любые токены ERC20.

<b>Каким образом хранится мой Ethereum пароль?</b>
Monee это система с нулевым разглашением, поэтому вы или кто-то посторонний не можете восстанавливать пароли. Но пока у вас есть доступ к вашему аккаунту в Telegram, вы всегда сможете установить новый пароль.

<b>Что произойдет, если я отправлю средства кому-то, кто ещё не зарегистрирован в Monee?</b>
Наш депозитный смарт-контракт будет удерживать средства до тех пор, пока в системе не появится пользователь с username или номером телефона, который вы указали.

<b>Как отправить эфир и другие токены?</b>
Введите username, номер телефона или Ethereum адрес, а затем сумму и имя токена, который хотите передать. По-умолчанию в боте используется ETH. Названия токенов могут быть написаны в любом регистре, всё зависит от вас!

<b>Могу ли я участвовать в ICO?</b>
Конечно, и как только токены будут зачислены на ваш адрес, вы увидете их на своём балансе.

<b>Почему токены нужно добавлять вручную?</b>
В сети Ethereum существуют сотни токенов, поэтому боту нужна ваша помощь, чтобы определить какой именно вы хотите добавить. Мы уже работаем над упрощением данного процесса.

<b>Бот в Telegram это безопасно?</b>
За всё время существования, работа ботов в Telegram никогда не вызывала нареканий. Но для большей безопасности, мы не рекомендуем хранить пароль от Monee в чатах.

<b>Что будет если я потеряю свой аккаунт Telegram?</b>
Вы можете восстановить доступ к своему кошельку Monee, пока у вас есть доступ к вашему номеру телефона. Ваш username используется только для функций отправки и получения, а кошелек регистрируется с помощью номера вашего телефона.'
];
