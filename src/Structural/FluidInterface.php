<?php

namespace Safronik\CodePatterns\Structural;

trait FluidInterface {

	/**
	 * Implements fluid interface for methods starts from:
     *  - set
     *  - add
     *  - save
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return $this
	 */
	public function __call( $name, $arguments )
    {
        // Get
        if( str_starts_with( $name, 'get' ) ){
            
            $var = lcfirst( substr( $name, 3 ) );
            
            return $this->$var;
        }
        
        // Set
        if( str_starts_with( $name, 'set' ) ){
            
            $var        = lcfirst( substr( $name, 3 ) );
            $this->$var = $arguments[0];
            
            return $this;
        }
        
        // Add
        if( str_starts_with( $name, 'add' ) ){
            
            $namespace = preg_replace( '/^(.*)?(\\\\.*)$/', '$1', __CLASS__ );
            $class     = ucfirst( substr( $name, 3 ) );
            $class     = '\\' . $namespace . '\\' . $class;
            $child     = $arguments
                ? new $class( ...$arguments )
                : new $class();
            
            $this->$name = $child;
            
            return $child;
        }
        
        /** Example: saveSome() */
        if( str_starts_with( $name, 'save' ) ){
            
            $var = lcfirst( substr( $name, 4 ) );
            $this->save( $var );
            
            return $this;
        }
    }

}