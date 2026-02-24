<?php

namespace Safronik\CodePatterns\Interfaces;

interface Finalizable
{
    public function setFinalizer( callable $finalizer ): void;
}
