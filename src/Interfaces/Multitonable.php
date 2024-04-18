<?php

namespace Safronik\CodePatterns\Interfaces;

interface Multitonable
{
    public static function getInstance( ...$params ): mixed;
    public static function isInitialized(): bool;
}