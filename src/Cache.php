<?php

namespace Yzchan\Yii2Predis;

use Predis\Client;
use yii\di\Instance;

class Cache extends \yii\caching\Cache
{
    /**
     * @var string|Connection|Client
     */
    public $redis = 'redis';
    public $database = 0;

    public function init()
    {
        parent::init();
        $this->redis = Instance::ensure($this->redis, Connection::className());
        $this->redis->select($this->database);
    }

    public function exists($key)
    {
        return (bool)$this->redis->exists($this->buildKey($key));
    }

    public function offsetExists($key)
    {
        return $this->exists($key);
    }

    /**
     * @inheritdoc
     */
    protected function getValue($key)
    {
        $data = $this->redis->get($key);
        return $data === null ? false : $data;
    }

    /**
     * @inheritdoc
     */
    protected function getValues($keys)
    {
        $response = $this->redis->mget($keys);
        $result = [];
        $i = 0;
        foreach ($keys as $key) {
            $result[$key] = $response[$i++];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function setValue($key, $value, $duration)
    {
        if ($duration == 0) {
            return (bool)$this->redis->set($key, $value);
        } else {
            return (bool)$this->redis->set($key, $value, 'PX', (int)($duration * 1000));
        }
    }

    /**
     * @inheritdoc
     */
    protected function addValue($key, $value, $duration)
    {
        if ($duration == 0) {
            return (bool)$this->redis->set($key, $value, 'NX');
        } else {
            return (bool)$this->redis->set($key, $value, 'PX', (int)($duration * 1000), 'NX');
        }
    }

    /**
     * @inheritdoc
     */
    protected function deleteValue($key)
    {
        return (bool)$this->redis->del($key);
    }

    /**
     * @inheritdoc
     */
    protected function flushValues()
    {
        return $this->redis->flushdb();
    }
}