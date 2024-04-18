<?php

namespace Safronik\CodePatterns\Generative;

/**
 * Builder
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait Builder
{
    protected object $builder;
    
    public function setBuilder( object $builder ): void
    {
        $this->builder = $builder;
    }
    
    public function build(): object
    {
        return $this->builder->build();
    }
}
