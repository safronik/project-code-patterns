<?php

namespace Safronik\CodePatterns\Interfaces;

interface Containerable
{
    
    public static function get( string $alias ): mixed;
    public static function has( string $service ): bool;
}