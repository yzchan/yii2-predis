<?php

namespace Yzchan\Yii2Predis;

use yii\di\Instance;

class Session extends \yii\web\Session
{
    public $redis = 'redis';

    public $keyPrefix;

    public function init()
    {
        $this->redis = Instance::ensure($this->redis, Connection::className());
        if ($this->keyPrefix === null) {
            $this->keyPrefix = substr(md5(\Yii::$app->id), 0, 5);
        }
        parent::init();
    }

    public function getUseCustomStorage()
    {
        return true;
    }

    public function readSession($id)
    {
        $data = $this->redis->get($this->calculateKey($id));

        return $data === false || $data === null ? '' : $data;
    }

    public function writeSession($id, $data)
    {
        return (bool)$this->redis->set($this->calculateKey($id), $data, 'EX', $this->getTimeout());
    }

    public function destroySession($id)
    {
        $this->redis->del($this->calculateKey($id));
        return true;
    }

    protected function calculateKey($id)
    {
        return $this->keyPrefix . md5(serialize($id));
    }
}