<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EthereumServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('ethereum', '\App\Ethereum\Client');
    }
}
