<?php

// @todo Should be a code template, not a service
/** @todo implement priority groups
 * 'first' - special phrase to set hook before all
 *  0 - 100 ('crutch')   - crutches and reserve
 *  100 - 199 ('security') - security
 *  200 - 299 ('regular') - base logic (default)
 * 'last' - special phrase to set hook after all
 */

namespace Safronik\CodePatterns\Behavioral;

use Safronik\CodePatterns\Exceptions\EventManagerException;
use Safronik\CodePatterns\Generative\Singleton;

/**
 * Event manager
 *
 *
 *
 * @author  Roman safronov
 * @version 1.0.0
 */

class EventManager
{
    use Singleton;
    
    private array $events = [];
    private array $allowed_types = [
        'before',
        'after',
        'filter_input',
        'filter_output',
    ];
    
    public static function hook( string $class, string $event, $type, callable $callback ): void
    {
        self::getInstance()->create( $type, $event, $callback );
    }
    
    public static function filterInput( string $class, string $event, callable $callback ): void
    {
        self::getInstance()->create( 'filter_input', "$class:$event", $callback );
    }
    
    public static function before( string $class, string $event, callable $callback ): void
    {
        self::getInstance()->create( 'before', "$class:$event", $callback );
    }
    
    public static function after( string $class, string $event, callable $callback ): void
    {
        self::getInstance()->create( 'after', "$class:$event", $callback );
    }
    
    public static function filterOutput( string $class, string $event, callable $callback ): void
    {
        self::getInstance()->create( 'filter_output', "$class:$event", $callback );
    }
    
    private function create( string $type, string $event, callable $callback, int $priority = 200 )
    {
        ! in_array( $type, $this->allowed_types, true)
            && throw new EventManagerException( 'EventManager type is not valid: ' . $type );
        
        $this->events[ $event ][ $type ][ $priority ] = $callback;
    }
    
    public static function triggerFilterInput( string $event, array $arguments ): mixed
    {
        return self::getInstance()->exists( 'filter_input', $event )
            ? self::getInstance()->trigger( 'filter_input', $event, ...$arguments )
            : $arguments;
    }
    
    public static function triggerBefore( string $event ): void
    {
        if( self::getInstance()->exists( 'before', $event ) ){
            self::getInstance()->trigger( 'before', $event );
        }
    }
    
    public static function triggerAfter( string $event ): void
    {
        if( self::getInstance()->exists( 'after', $event ) ){
            self::getInstance()->trigger( 'after', $event );
        }
    }
    
    public static function triggerFilterOutput( string $event, mixed $return_value ): mixed
    {
        return self::getInstance()->exists( 'filter_output', $event )
            ? self::getInstance()->trigger( 'filter_output', $event, $return_value )
            : $return_value;
    }
    
    public static function triggerCustom( string $event ): void
    {
        if( self::getInstance()->exists( 'custom', $event ) ){
            self::getInstance()->trigger( 'custom', $event );
        }
    }
    
    private function exists( string $type, string $event ): bool
    {
        return isset( $this->events[ $event ][ $type ] );
    }
    
    private function trigger( string $type, string $event, ...$params ): mixed
    {
        return $this->events[ $event ][ $type ]( ...$params );
    }
    
}