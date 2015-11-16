<?php

namespace Http\Middlewares;

use Obullo\Application\Middleware;

class Csrf extends Middleware
{
    /**
     *  Call action
     * 
     * @return void
     */ 
    public function call()
    {
        $verify = $this->csrf->verify();

        if ($this->request->isAjax() && ! $verify) {      // Build your ajax errors
            
            echo $this->response->json(
                [
                    'success' => 0,
                    'message' => 'The action you have requested is not allowed.'
                ]
            );

        } elseif (! $verify) {     // Build your http errors

            $this->response->withStatus(401)->error(
                'The action you have requested is not allowed.', 
                'Access Denied'
            );
        }
        $this->next->call();
    }
    
}