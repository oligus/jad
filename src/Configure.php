<?php

namespace Jad;

/**
 * Class Configure
 * @package Jad
 */
class Configure
{
    /**
     * @var Configure $instance
     */
    private static $instance;

    /**
     * @var array
     */
    private $config = [
        'testMode'  => false,
        'debug'     => false
    ];

    /**
     * @return Configure
     */
    public static function getInstance()
    {
        if(!self::$instance instanceof Configure) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setConfig($key, $value)
    {
        if(array_key_exists($key, $this->config)) {
            $this->config[$key] = $value;
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getConfig($key)
    {
        if(array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
    }
}