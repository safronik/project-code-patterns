<?php

namespace Safronik\CodePatterns\Generative;

use Safronik\CodePatterns\Structural\DTO;

/**
 * Fabric
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait Fabric
{
    abstract public function fabricate( DTO|array $params ): object;
}
