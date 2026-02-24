<?php

namespace Safronik\CodePatterns\Behavioral;

/**
 * Resource Acquisition Is Initialization
 *
 * Ensures that resource acquisition happens in the constructor
 * and release happens automatically in the destructor.
 *
 * @author  Roman Safronov
 * @version 2.0.0
 */
trait RAII
{
    abstract protected function acquireResource(): void;
    abstract protected function releaseResource(): void;

    public function __construct()
    {
        $this->acquireResource();
    }

    public function __destruct()
    {
        $this->releaseResource();
    }
}
