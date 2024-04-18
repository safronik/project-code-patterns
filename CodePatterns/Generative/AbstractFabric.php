<?php

namespace Safronik\CodePatterns\Generative;

/**
 * Abstract Fabric
 *
 * @author  Roman safronov
 * @version 1.0.0
 */
trait AbstractFabric
{
    abstract public function getFabric( string $fabric ): Fabric;
}
