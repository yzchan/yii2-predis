<?php

namespace Yzchan\Yii2Predis;

use Predis\Client;
use yii\base\Component;

class Connection extends Component
{
    public string $hostname = '127.0.0.1';
    public int $port = 6379;
    public int $database = 0;
    private Client $client;

    public function init()
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => $this->hostname,
            'port' => $this->port,
        ]);
        if ($this->database > 0) {
            $this->client->select($this->database);
        }
        parent::init();
    }

    public function __call($name, $params)
    {
        return call_user_func_array([$this->client, $name], $params);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}