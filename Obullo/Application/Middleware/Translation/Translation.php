<?php

namespace Http\Middlewares;

use Obullo\Application\Middleware;
use Obullo\Application\Middlewares\SetDefaultLocaleTrait;

class Translation extends Middleware
{
    use SetDefaultLocaleTrait;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {   
        $this->setLocale();
    }

    /**
     *  Call action
     * 
     * @return void
     */
    public function call()
    {
        $this->next->call();
    }
    
}