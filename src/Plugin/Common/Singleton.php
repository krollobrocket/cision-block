<?php

namespace CisionBlock\Plugin\Common;

abstract class Singleton
{
    /**
     * @var array $instance
     */
    protected static array $instance = [];

    /**
     * @return mixed
     */
    final public static function getInstance(): static
    {
        $class = get_called_class();
        if (!isset(self::$instance[$class]) || !self::$instance[$class] instanceof $class) {
            self::$instance[$class] = new static();
        }
        return static::$instance[$class];
    }

    /**
     * Singleton constructor.
     */
    final private function __construct()
    {
        $this->init();
    }

    /**
     * Prevent instantiation.
     */
    private function __clone()
    {
    }

    /**
     * Prevent instantiation.
     *
     * @throws \Exception
     */
    final public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton');
    }

    protected function init()
    {
    }
}
