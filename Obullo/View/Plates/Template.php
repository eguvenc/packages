<?php

namespace Obullo\View\Plates;

use League\Plates\Template\Template as AbstractTemplate;
use League\Container\ImmutableContainerAwareTrait;
use League\Container\ImmutableContainerAwareInterface;
use LogicException;

/**
 * Container which holds template data and provides access to template functions.
 */
class Template extends AbstractTemplate implements ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * Make available controller variables in view files
     * 
     * @param string $key Controller variable name
     * 
     * @return void
     */
    public function __get($key)
    {
        return $this->getContainer()->get($key);
    }
}
