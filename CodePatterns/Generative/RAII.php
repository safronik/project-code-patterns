<?php

namespace Safronik\CodePatterns\Generative;

/**
 * Resource Acquisition Is Initialization
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait RAII
{
    private mixed $destruct_handler;
    
    public function setDestructHandler( callable $destruct_handler ): void
    {
        $this->destruct_handler = $destruct_handler;
    }
    
    public function __destruct()
    {
        isset( $this->destruct_handler )
            && call_user_func( $this->destruct_handler );
    }
}
