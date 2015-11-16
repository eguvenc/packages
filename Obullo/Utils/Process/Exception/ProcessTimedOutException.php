<?php

namespace Obullo\Utils\Process\Exception;

use LogicException;
use RuntimeException;
use Obullo\Utils\Process\Process;

/**
 * Exception that is thrown when a process times out.
 * 
 * This file is borrowed from Symfony process package 
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ProcessTimedOutException extends RuntimeException
{
    const TYPE_GENERAL = 1;
    const TYPE_IDLE = 2;

    protected $process;
    protected $timeoutType;

    public function __construct(Process $process, $timeoutType)
    {
        $this->process = $process;
        $this->timeoutType = $timeoutType;
        parent::__construct(
            sprintf(
            'The process "%s" exceeded the timeout of %s seconds.',
            $process->getCommandLine(),
            $this->getExceededTimeout()
            )
        );
    }

    public function getProcess()
    {
        return $this->process;
    }

    public function isGeneralTimeout()
    {
        return $this->timeoutType === self::TYPE_GENERAL;
    }

    public function isIdleTimeout()
    {
        return $this->timeoutType === self::TYPE_IDLE;
    }

    public function getExceededTimeout()
    {
        switch ($this->timeoutType) {
            case self::TYPE_GENERAL:
                return $this->process->getTimeout();

            case self::TYPE_IDLE:
                return $this->process->getIdleTimeout();

            default:
                throw new LogicException(sprintf('Unknown timeout type "%d".', $this->timeoutType));
        }
    }
}
