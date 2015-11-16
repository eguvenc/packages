<?php

namespace Http\Middlewares;

use Obullo\Application\Middleware;
use Obullo\Application\Middlewares\RewriteHttpsTrait;

class Https extends Middleware
{
    use RewriteHttpsTrait;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {   
        $this->rewrite();
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