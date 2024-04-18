<?php

namespace Safronik\CodePatterns\Structural;

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
             || throw new \Exception("Method $method is not available for collection $collection");
        
        $method_results = [];
        foreach( $this->$collection as $item ){
            $method_results[] = $item->$method( ...$method_args );
        }
        
        if( $callback ){
            return $callback( $method_results );
        }
    }
}
