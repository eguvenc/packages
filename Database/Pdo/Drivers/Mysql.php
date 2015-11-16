<?php

namespace Obullo\Database\Pdo\Drivers;

use PDO;
use Obullo\Database\Pdo\Adapter;
use Obullo\Container\ServiceProviderInterface;

/**
 * Pdo Mysql Database Driver
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Mysql extends Adapter
{
    /**
     * Column identifier symbol
     * 
     * @var string
     */
    public $escapeIdentifier = '`';

    /**
     * Connect to PDO
     * 
     * @return void
     */
    public function createConnection()
    {
        $this->conn = new PDO($this->params['dsn'], $this->params['username'], $this->params['password'], $this->params['options']);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Aways show the pdo exceptions errors. // PDO::ERRMODE_SILENT 
    }
}