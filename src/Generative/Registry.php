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
    
    public function fillRegistry( $storage ): void
    {
        $this->storage = $storage;
    }
    
    public function isRegistryKeyExists( $key ): bool
    {
        return isset( $this->storage[ $key ] );
    }
    
    public function pushToRegistry( $key, $item, $allow_replace = true ): void
    {
        ! $allow_replace
            || $this->isRegistryKeyExists( $key )
            || throw new RegistryException("No $key found");

        $this->storage[ $key ] = $item;
    }
    
    public function getFromRegistry( $key ): mixed
    {
        $this->isRegistryKeyExists( $key )
            || throw new RegistryException("No $key found");
        
        return $this->storage[ $key ];
    }
    
    public function deleteFromRegistry( $key ): void
    {
        unset( $this->storage[ $key ] );
    }
}
