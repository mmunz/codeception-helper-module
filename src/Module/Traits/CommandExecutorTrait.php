<?php

namespace Portrino\Codeception\Module\Traits;

use Codeception\Module\Asserts;
use Portrino\Codeception\Factory\ProcessFactory;
use Symfony\Component\Process\Process;

/**
 * Trait CommandExecutorTrait
 * @package Portrino\Codeception\Module\Traits
 */
trait CommandExecutorTrait
{
    /**
     * @var string
     */
    protected $consolePath;

    /**
     * @var int
     */
    protected $processTimeout;

    /**
     * @var int
     */
    protected $processIdleTimeout;

    /**
     * @var Asserts
     */
    protected $asserts;

    /**
     * @var ProcessFactory
     */
    protected $ProcessFactory;

    /**
     * @param ProcessFactory $ProcessFactory
     */
    public function setProcessFactory($ProcessFactory)
    {
        $this->ProcessFactory = $ProcessFactory;
    }

    /**
     * @param string $command
     * @param array  $arguments
     * @param array  $environmentVariables
     */
    public function executeCommand($command, $arguments = [], $environmentVariables = [])
    {
        array_unshift($arguments, $command);
        array_unshift($arguments, $this->consolePath);
        $arguments = array_map('strval', $arguments);

        $builder = $this->ProcessFactory->getBuilder($arguments);
        if (count($environmentVariables) > 0) {
            $builder->addEnvironmentVariables($environmentVariables);
        }

        $this->debugSection('Execute', $builder->getCommandLine());

        $builder->setTimeout($this->processTimeout);
        $builder->setIdleTimeout($this->processIdleTimeout);

        $builder->run();

        if ($builder->isSuccessful()) {
            $this->debugSection('Success', $builder->getOutput());
        } else {
            $this->debugSection('Error', $builder->getErrorOutput());
        }

        $this->asserts->assertTrue($builder->isSuccessful());
    }
}
