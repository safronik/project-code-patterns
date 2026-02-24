<?php

namespace Safronik\CodePatterns\Behavioral;

use Closure;

/**
 * Finalizer
 *
 * Allows registering a custom callable to be invoked
 * when the object is destroyed.
 *
 * @author  Roman Safronov
 * @version 1.0.0
 */
trait Finalizer
{
    private ?Closure $finalizer = null;

    public function setFinalizer( callable $finalizer ): void
    {
        $this->finalizer = Closure::fromCallable( $finalizer );
    }

    public function __destruct()
    {
        $this->finalizer && ($this->finalizer)();
    }
}
