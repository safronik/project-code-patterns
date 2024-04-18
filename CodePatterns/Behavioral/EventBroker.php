<?php

namespace Safronik\CodePatterns\Behavioral;

use SplObserver;

/**
 * Observer
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
class EventBroker
{
    private array $subscribers = [];
    private array $topics      = [];
    
    public function createTopic( string $topic )
    {
        $this->topics[] = $topic;
    }

    public function deleteTopic( string $topic )
    {
        $this->topics[] = $topic;
    }
    
    public function subscribe( SplObserver $observer, string $topic ): void
    {
        $observer_key = spl_object_hash( $observer );
        $this->subscribers[ $observer_key ] = $observer;
        
        $this->topics[ $topic ][ $observer_key ] = $observer;
    }
    
    public function unsubscribe( SplObserver $observer ): void
    {
    
    }
    
    public function notify(): void
    {
    
    }
}
