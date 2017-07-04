<?php

namespace App\Classes;

use Cache;

class DataStorage
{
    /**
     * @param $data
     * @return string
     */
    public function put($data)
    {
        Cache::put('data_' . ($hash = str_random()), $data, 1440);

        return $hash;
    }

    /**
     * @param $hash
     * @return mixed
     */
    public function get($hash)
    {
        return Cache::get('data_' . $hash);
    }
}