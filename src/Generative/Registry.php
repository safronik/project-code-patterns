<?php

namespace Safronik\CodePatterns\Generative;

use Safronik\CodePatterns\Exceptions\RegistryException;

/**
 * Registry
 *
 * Allowed operations:
 * - isExists
 * - fill
 * - push
 * - get
 * - delete
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait Registry
{
    private array $storage = [];
    
    public function fill( $storage ): void
    {
        $this->storage = $storage;
    }
    
    public function isExists( $key ): bool
    {
        return isset( $this->storage[ $key ] );
    }
    
    public function push( $key, $item, $replace = true ): void
    {
        ! $replace
            || $this->isExists( $key )
            || throw new RegistryException("No $key found");

        $this->storage[ $key ] = $item;
    }
    
    public function get( $key ): mixed
    {
        $this->isExists( $key )
            || throw new RegistryException("No $key found");
        
        return $this->storage[ $key ];
    }
    
    public function delete( $key ): void
    {
        unset( $this->storage[ $key ] );
    }
}
