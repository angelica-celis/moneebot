<?php

return [
    'choose_language' => 'Choose language',
    'language_set' => 'Language changed',

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

    'welcome1' => 'Send your contact using keyboard button',
    'welcome2' => 'Great!' . PHP_EOL . 'Now we will create your Ethereum wallet. ' .
        'Make a password for it.' . PHP_EOL . PHP_EOL . '<b>Attention!</b> ' .
        'We strongly recommend deleting password after each enter for security reasons',

    'send_contact' => 'Send contact',
    'creating_wallet' => 'Crafting wallet...',
    'wallet_creating_error' => 'Error in wallet creation:',
    'wallet_created' => 'Wallet successfully created',

    'balance_error' => 'Error while getting balance:',

    'send_instructions' => 'To send ETH to users just enter <b>@username AMOUNT</b>' .
        PHP_EOL . PHP_EOL .
        'Also you can use phone +7999999999 or Ethereum address as recipient.' .
        PHP_EOL . PHP_EOL .
        'To send ERC20 token enter its symbol after transaction amount. ' .
        'i.e.: <b>@username 1 BNT</b>',

    'provide_erc20_address' => 'Enter address of ERC20 token',

    'cancel' => 'Back',
    'exit' => 'Exit',
    'error' => 'Error',

    'settings' => 'Settings',

    'change_password' => 'Change password',
    'download_json_utc' => 'Download JSON/UTC file',

    'provide_new_password' => 'Enter new password',
    'settings_updated' => 'Settings updated',

    'operation_canceled' => 'Operation cancelled',
    'sending_transaction' => 'Sending transaction...',

    'unknown_address' => 'Error: unknown address',
    'transaction_sent' => 'Transaction successful',

    'error_adding_erc20' => 'Error while adding token',
    'balance' => 'My balance',
    'password_changed' => 'Password changed',
    'erc20error' => 'You cannot send ERC20 tokens to users without account in @Moneebot',

    'send_placeholder' => 'Send <b>:value :coin</b> to <b>:rec</b>?' .
        PHP_EOL . 'Max tx price: :max_price ETH',

    'send_placeholder_commission' => 'User is not registered in @Moneebot, ' .
        'we will add commission to your transaction: ',

    'confirm' => 'Confirm',

    'unknown_command' => 'Unknown command',

    'incoming_transactions' => 'You have incoming transactions of :value ETH. You will receive them in a couple minutes.',

    'check_balance' => 'My balance',

    'get_address' => 'My address',
    'send' => 'Send',
    'addCoin' => 'Add ERC20 token',

    'set_phone' => 'Set phone number',

    'phone_saved' => 'Phone saved',

    'gas_settings' => 'Gas price',

    'choose_gas_price' => 'Choose gas price',

    'expert_mode' => 'Expert Mode',
    'expert_step1' => 'To send ETH to users just enter <b>@username AMOUNT</b>',
    'expert_step2' => 'Set Gas Limit',
    'expert_step3' => 'Set Gas Price в Gwei (from 1 to 100)',

    'help_text' => '<b>Is Monee wallet a full-featured Ethereum-wallet?</b>
Yes. We can provide you with JSON/UTC files, so you can manage your fund anywhere. Wallet hold ether and any ERC20 tokens.

<b>How do you store my Ethereum password?</b>
Monee is a zero knowledge system, therefore you or any other person can\'t recover passwords. But as long as you have access to your Telegram account, you can change it to new one.

<b>If I send funds to someone, who’s not in Monee yet, what happens?</b>
Our escrow smart contract holds it till the person shows up either with username or mobile phone number you’ve used.

<b>How do I send ether and tokens?</b>
Type Telegram username, mobile phone number or ether address then sum and name of token. By default we use ETH. Token names can be written in UPPERCASE or lowercase, it’s up to you!

<b>Can I participate in ICO?</b>
Sure. And as soon as the token is credited to your ether address you’ll see it on your balance.

<b>Why do I have add tokens manually?</b>
There are hundreds of tokens on Ethereum network, so we really need your help choosing, which one you want to see in Monee. And we’re working on simplifying of the process.

<b>Are you sure Telegram bot is secure?</b>
There were no breaches of Telegram or bots in the history of this messenger. But we recommend you not to store Monee password in chats, just to make it more secure.

<b>If I lose my Telegram account what happens?</b>
As long as you have access to your mobile phone number, you can restore your Monee wallet. Usernames are only used for simple send/receive features, but wallet is registered with verified by Telegram mobile phone number.'
];
