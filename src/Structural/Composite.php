<?php

namespace Safronik\CodePatterns\Structural;

use Safronik\CodePatterns\Exceptions\CompositeException;

/**
 * Singleton
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait Composite
{
    public function composeBy( string $collection, string $method, array $method_args = [], callable $callback = null )
    {
        method_exists(
            current( $this->$collection ),
            $method
        )
             || throw new CompositeException("Method $method is not available for collection $collection");
        
        $method_results = [];
        foreach( $this->$collection as $item ){
            $method_results[] = $item->$method( ...$method_args );
        }
        
        if( $callback ){
            return $callback( $method_results );
        }
    }
}
