<?php

namespace Safronik\CodePatterns\Interfaces;

interface Installable
{
    public static function getNamespace( $string ): ?string;
    public static function getScheme(): ?array;
    public static function getSlug(): ?string;
    public static function getOptions(): ?array;
}