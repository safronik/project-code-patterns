<?php

namespace Safronik\CodePatterns\Structural;

/**
 * Decorator
 *
 * Could be decorated anything via magic __call method
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait Decorator
{
    private object $decorated_object;
    private array  $callbacks = [];
    
    public function __construct( object $decorated_object )
    {
        $this->decorated_object = $decorated_object;
    }
    
    public function setArgumentFilter( string $method, callable $callback ): void
    {
        $this->setCallback( $method, $callback, 'before' );
    }

    public function setResultFilter( string $method, callable $callback ): void
    {
        $this->setCallback( $method, $callback, 'after' );
    }
    
    public function setCallback( string $method, callable $callback, string $order = 'before' ): void
    {
        in_array( $order, ['before', 'after'], true )
            || throw new \Exception("Order $order is not allowed");
            
        $this->callbacks[ $method ][ $order ] = $callback;
    }
    
    public function __call( string $name, array $arguments )
    {
        if( isset( $this->callbacks[ $name ][ 'before' ] ) ){
            $this->callbacks[ $name ][ 'before' ]( $arguments );
        }
        
        $method_result = $this->decorated_object->$name( $arguments );
        
        if( isset( $this->callbacks[ $name ][ 'after' ] ) ){
            $method_result = $this->callbacks[ $name ][ 'after' ]( $method_result, $arguments );
        }
        
        
        return $method_result;
    }
}
