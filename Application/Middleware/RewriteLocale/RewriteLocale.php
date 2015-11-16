<?php

namespace Http\Middlewares;

use Obullo\Application\Middleware;
use Obullo\Application\Middlewares\RewriteLocaleTrait;

class RewriteLocale extends Middleware
{
    use RewriteLocaleTrait;

    /**
     *  Call action
     * 
     * @return void
     */
    public function call()
    {
        $this->excludeMethods(['post']);  // Ignore http post methods
        
        $this->rewrite();

        $this->next->call();
    }

}