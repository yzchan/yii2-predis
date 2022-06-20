<?php

namespace Yzchan\Yii2Predis;

use Predis\Client;
use yii\base\Component;

class Connection extends Component
{
    public $hostname = '127.0.0.1';
    public $port = 6379;
    public $database = 0;
    private $client;

    public function init()
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => $this->hostname,
            'port' => $this->port,
        ]);
        \Yii::debug('Opening redis DB connection', __METHOD__);
        if ($this->database > 0) {
            $this->client->select($this->database);
        }
        parent::init();
    }

    public function __call($name, $params)
    {
        \Yii::debug("Executing Redis Command: {$name}", __METHOD__);
        return call_user_func_array([$this->client, $name], $params);
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}