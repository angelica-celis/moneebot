<?php

namespace App\Ethereum;

class JsonRPC
{
    protected $host, $port, $version;
    protected $id = 0;

    function __construct($host, $port, $version = "2.0")
    {
        $this->host = $host;
        $this->port = $port;
        $this->version = $version;
    }

    /**
     * Make a request to JSON RPC server
     *
     * @param $method
     * @param array $params
     * @return mixed
     * @throws JsonRPCException
     */
    function request($method, $params = array())
    {
        $data = array();
        $data['jsonrpc'] = $this->version;
        $data['id'] = $this->id++;
        $data['method'] = $method;
        $data['params'] = $params;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->host);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $ret = curl_exec($ch);

        if ($ret !== FALSE) {
            $formatted = $this->format_response($ret);

            if (isset($formatted->error)) {
                throw new JsonRPCException($formatted->error->message, $formatted->error->code);
            }

            return $formatted;
        }

        throw new JsonRPCException("Server did not respond");
    }

    /**
     * Format JSON RPC response
     *
     * @param $response
     * @return mixed
     */
    function format_response($response)
    {
        return json_decode($response);
    }

}