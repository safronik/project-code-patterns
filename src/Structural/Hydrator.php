<?php

namespace Safronik\CodePatterns\Structural;

use Exception;

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
    public function hydrate( DTO|array $input ): void
    {
        if( $input instanceof DTO ){
            $this->hydrateFromDTO( $input );
        }elseif( is_object( $input ) ){
            $this->hydrateFromObject( $input );
        }else{
            $this->hydrateFromArray( $input );
        }
    }

    /**
     * @param DTO $input
     * @return void
     * @throws Exception
     * @todo implement
     * Hydrate object properties from DTO
     *
     */
    public function hydrateFromDTO( DTO $input ): void
    {
        throw new Exception( 'NOT implemented' );
    }

    /**
     * Hydrate object properties from object
     *
     * @param object $input
     * @return void
     */
    private function hydrateFromObject( object $input ): void
    {
        $this->hydrateFromArray( (array)$input );
    }

    /**
     * Set passed params to object properties and cast parameter type
     *
     * @param array $params
     *
     * @return void
     */
    public function hydrateFromArray( array $params ): void
    {
        foreach( $params as $param_name => $param ){

            if( property_exists( static::class, $param_name ) ){

                $type = isset( $this->$param_name )
                    ? strtolower( gettype( $this->$param_name ) )
                    : 'null';

                $this->$param_name = $param;

                // Skip type casting for default undefined properties
                if( $type === 'null' ){
                    continue;
                }

                settype( $this->$param_name, $type );
            }
        }
    }
}