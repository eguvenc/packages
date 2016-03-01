<?php

namespace Obullo\Log\Pusher;

/**
 * Pusher Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface PusherInterface
{
    /**
     * Push the data
     *
     * @param array $data payload
     * 
     * @return void
     */
    public function push(array $data);
}