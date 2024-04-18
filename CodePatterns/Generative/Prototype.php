<?php

namespace Safronik\CodePatterns\Generative;

/**
 * Prototype
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait Prototype
{
    private object $prototype;
    
    public function setPrototype( object $prototype ): void
    {
        $this->prototype = $prototype;
    }
    
    public function getPrototype(): object
    {
        return clone $this->prototype;
    }
}
