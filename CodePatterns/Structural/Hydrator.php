<?php

namespace Safronik\CodePatterns\Structural;

/**
 * Class Hydrator
 *
 * Checks if the property exists and cast it to its default type
 *
 * @author Roman safronov
 * @version 1.0.0
 */
trait Hydrator
{
    /**
     * Set passed params to object properties and cast parameter type
     *
     * @param object|array $params
     *
     * @return void
     */
    public function hydrate( object|array $params = array() ): void
    {
        foreach ( $params as $param_name => $param ) {
            
            if ( property_exists(static::class, $param_name) ) {
                
                $type = isset( $this->$param_name )
                    ? strtolower( gettype($this->$param_name) )
                    : 'null';
                
                $this->$param_name = $param;
				
				// Skip type casting for default undefined properties
				if( $type === 'null' ){
					continue;
				}
				
                settype($this->$param_name, $type);
            }
        }
    }
}