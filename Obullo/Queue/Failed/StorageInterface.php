<?php

namespace Obullo\Queue\Failed;

/**
 * Storage Handler Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface StorageInterface
{
    /**
     * Insert failed event data to storage
     * 
     * @param array $event key value data
     * 
     * @return void
     */
    public function save(array $event);

    /**
     * Check same error is daily exists
     *
     * @param string  $file error file
     * @param integer $line error line
      * 
     * @return void
     */
    public function exists($file, $line);

    /**
     * Update attempts
     * 
     * @param integer $id    queue failure id
     * @param integer $event data
     * 
     * @return void
     */
    public function update($id, array $event);
}