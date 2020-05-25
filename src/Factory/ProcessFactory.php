<?php

namespace Portrino\Codeception\Factory;

use Symfony\Component\Process\Process;

/**
 * Class ProcessFactory
 * @package Portrino\Codeception\Factory
 */
class ProcessFactory
{
    /**
     * @return Process
     */
    public function getBuilder($command)
    {
        return new Process($command);
    }
}
