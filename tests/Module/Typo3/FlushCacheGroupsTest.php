<?php

namespace Portrino\Codeception\Tests\Module\Typo3;

/*
 * This file is part of the Codeception Helper Module project
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 *
 */

use Codeception\Lib\ModuleContainer;
use Codeception\Module\Asserts;
use Portrino\Codeception\Factory\ProcessFactory;
use Portrino\Codeception\Interfaces\Commands\Typo3Command;
use Portrino\Codeception\Module\Typo3;
use Portrino\Codeception\Tests\Module\Typo3Test;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Process;

/**
 * Class FlushCacheGroupsTest
 * @package Portrino\Codeception\Tests\Module\Typo3
 */
class FlushCacheGroupsTest extends Typo3Test
{
    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->container = $this->prophesize(ModuleContainer::class);
        $this->process = $this->prophesize(Process::class);
        $this->ProcessFactory = $this->prophesize(ProcessFactory::class);
        $this->builder = $this->prophesize(Process::class);
        $this->asserts = $this->prophesize(Asserts::class);

        $tmpBuilder = new Process();
        $cmd = $tmpBuilder
            ->setPrefix(self::$typo3cmsPath)
            ->setArguments(
                [
                    Typo3Command::CACHE_FLUSH_GROUPS,
                    'groups' => 'pages, system'
                ]
            )
            ->getProcess()
            ->getCommandLine();

        $this->process->getCommandLine()->willReturn($cmd);
        $this->process->setTimeout(3600)->shouldBeCalledTimes(1);
        $this->process->setIdleTimeout(60)->shouldBeCalledTimes(1);
        $this->process->run()->shouldBeCalledTimes(1);

        $this->builder->setPrefix(self::$typo3cmsPath)->shouldBeCalled();
        $this->builder
            ->setArguments([
                Typo3Command::CACHE_FLUSH_GROUPS,
                'groups' => 'pages, system'
            ])
            ->shouldBeCalled();

        $this->builder->getProcess()->willReturn($this->process);

        $this->process->isSuccessful()->willReturn(true);
        $this->process->getOutput()->willReturn(self::DEBUG_SUCCESS);
        $this->asserts->assertTrue(true)->shouldBeCalled();

        $this->ProcessFactory->getBuilder()->willReturn($this->builder);

        $this->typo3 = new Typo3($this->container->reveal());
        $this->typo3->setProcessFactory($this->ProcessFactory->reveal());
        $this->typo3->_inject($this->asserts->reveal());
    }

    /**
     * @test
     */
    public function flushCacheGroupsSuccesfully()
    {
        $this->typo3->flushCacheGroups('pages, system');
    }
}
