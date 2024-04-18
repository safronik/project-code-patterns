<?php

namespace Safronik\CodePatterns\Interfaces;

interface Serviceable
{
    public static function getAlias(): ?string;
    public static function getGatewayAlias(): ?string;
}