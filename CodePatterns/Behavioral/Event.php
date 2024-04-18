<?php

namespace Safronik\CodePatterns\Behavioral;

trait Event
{
    public function __call( string $name, array $arguments )
    {
        $event = $name;
        $name  = '_' . $name;
        
                        EventManager::triggerBefore( __CLASS__ . ":$event" );
        $arguments    = EventManager::triggerFilterInput( __CLASS__ . ":$event", $arguments );
        $return_value = $this->$name( ...$arguments );
        $return_value = EventManager::triggerFilterOutput( __CLASS__ . ":$event", $return_value );
                        EventManager::triggerAfter( __CLASS__ . ":$event" );
        
        return $return_value;
    }
}