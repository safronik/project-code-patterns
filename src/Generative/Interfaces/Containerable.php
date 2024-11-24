<?php

namespace Safronik\CodePatterns\Generative\Interfaces;

interface Containerable
{
    public static function get( string $alias ): mixed;
    public static function has( string $service ): bool;
}