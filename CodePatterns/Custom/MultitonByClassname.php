<?php

namespace Safronik\CodePatterns\Custom;

/**
 * MultitonByClassname
 *
 * Main differences from classic multiton
 * - Uses classname as key
 * - Could use parameters to initialize certain instance
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait MultitonByClassname
{
    private static array $instances = [];
    
    public static function getInstance( ...$params ): static
    {
        if ( ! isset(static::$instances[ static::class ]) ) {
            static::$instances[ static::class ] = new static( ...$params );
        }

        return static::$instances[ static::class ];
    }
    
    public static function isInitialized(): bool
    {
        return isset( static::$instances[ static::class ] );
    }

}
