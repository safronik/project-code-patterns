<?php

namespace Safronik\CodePatterns\Interfaces;

interface Singletonable
{
    public static function getInstance( ...$params ): mixed;
    public static function isInitialized(): bool;
}