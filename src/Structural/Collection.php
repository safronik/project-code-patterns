<?php

namespace Safronik\CodePatterns\Structural;

class Collection extends \ArrayObject
{
    public function __construct( object|array $array = [] )
    {
        parent::__construct( $array );
    }
}