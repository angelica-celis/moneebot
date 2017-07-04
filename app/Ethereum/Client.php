<?php

namespace App\Ethereum;

class Client extends JsonRPC
{
    private $master_account;

    function __construct($host = null, $port = null, $version = "2.0")
    {
        if (!$host) {
            $host = env('ETHEREUM_HOST', '127.0.0.1');
        }

        if (!$port) {
            $port = env('ETHEREUM_PORT', 8545);
        }

        parent::__construct($host, $port, $version);

        $this->host = $host;
        $this->port = $port;
        $this->version = $version;
        $this->master_account = env('ETHEREUM_MASTER_ACCOUNT');
    }

    /**
     * Wei to Eth converter
     *
     * @param $wei
     * @return float|int
     */
    public static function weiToEth($wei)
    {
        return hexdec($wei) / pow(10, 18);
    }

    /**
     * Eth to wei converter
     *
     * @param $value
     * @return string
     */
    public static function ethToWei($value)
    {
        return '0x' . dechex($value * pow(10, 18));
    }

    /**
     * Pad data with 0's for Solidity argument
     *
     * @param $data
     * @return string
     */
    public static function pad($data)
    {
        return str_repeat('0', 64 - mb_strlen($data)) . $data;;
    }

    /**
     * Getting ERC20 coin info
     *
     * @param $coinAddress
     * @return array
     * @throws \Exception
     */
    public function getERC20CoinInfo($coinAddress)
    {
        $name = hex2bin(substr($this->request('eth_call', [[
            'from' => $this->getMasterAccount(),
            'to' => $coinAddress,
            'data' => '0x06fdde03',
            'value' => '0x0'
        ], 'latest'])->result, 2));

        $symbol = hex2bin(substr($this->request('eth_call', [[
            'from' => $this->getMasterAccount(),
            'to' => $coinAddress,
            'data' => '0x95d89b41',
            'value' => '0x0'
        ], 'latest'])->result, 2));

        $decimals = hexdec(substr($this->request('eth_call', [[
            'from' => $this->getMasterAccount(),
            'to' => $coinAddress,
            'data' => '0x313ce567',
            'value' => '0x0'
        ], 'latest'])->result, 2));

        $name = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $name));
        $symbol = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $symbol));

        if (!$symbol) {
            throw new \Exception('This is not ERC20 coin');
        }

        if (!$name) {
            $name = $symbol;
        }

        return compact('name', 'symbol', 'decimals');
    }

    /**
     * Getting master account address
     *
     * @return mixed
     */
    public function getMasterAccount()
    {
        return $this->master_account;
    }
}