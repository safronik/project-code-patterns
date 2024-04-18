<?php

namespace Safronik\CodePatterns\Generative;

/**
 * Multiton classic
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait Multiton
{
    private static array $instances = [];
    
    public static function getInstance( $key ): static
    {
        if( ! isset( static::$instances[ $key ] ) ){
            static::$instances[ $key ] = new static;
        }

        return static::$instances[ $key ];
    }
    
    public static function isInitialized( $key ): bool
    {
        return isset( static::$instances[ $key ] );
    }

}
