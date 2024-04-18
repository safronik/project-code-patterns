<?php

namespace Safronik\CodePatterns\Generative;

/**
 * Singleton
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait Singleton
{
    /**
     * @var mixed
     */
    private static self $instance;
    
    // Constructor is not allowed
    public function __clone() {}
    public function __wakeup() {}
    
    /**
     * Constructor
     *
     * @param array $params Additional parameters to pass in the method initialize()
     *
     * @return mixed|\static
     */
    public static function getInstance( ...$params ): mixed
    {
        return self::$instance ?? self::$instance = new static( ...$params );
    }
    
    /**
     * Alternative constructor
     * Doesn't return anything just initiate object
     *
     * Could be useful in case we don't need the object right now
     *
     * @param ...$params
     *
     * @return void
     */
    public static function initialize( ...$params ): void
    {
        self::getInstance( ...$params );
    }
    
    /**
     * Checks if the object is initialized
     *
     * @return bool
     */
    public static function isInitialized(): bool
    {
        return isset( static::$instance );
    }
    
}
