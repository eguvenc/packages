<?php

namespace Obullo\Tests;

/**
 * Http test interface
 */
interface HttpTestInterface
{
    /**
     * Generate test results, this function used for
     * generating test result templates.
     * 
     * We run it in Obullo/Application/Http.php file.
     * 
     * @return void
     */
    public function generateTestResults();
}
