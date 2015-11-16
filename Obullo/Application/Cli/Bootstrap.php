<?php

use Obullo\Container\Loader;
use Obullo\Container\Container;
use Obullo\Container\Dependency;
use Obullo\Container\ContainerInterface;

use Obullo\Config\Config;
use Obullo\Application\Cli;
use Obullo\Cli\NullRequest;
use Obullo\Cli\NullResponse;

/**
 * Detect environment
 * 
 * @return array
 */
$detectEnvironment = function () {
    static $env = null;
    if ($env != null) {
        return $env;
    }
    $hostname = gethostname();
    $envArray = include ROOT .'app/environments.php';
    foreach (array_keys($envArray) as $current) {
        if (in_array($hostname, $envArray[$current])) {
            $env = $current;
            break;
        }
    }
    if ($env == null) {
        die('We could not detect your application environment, please correct your app/environments.php hostnames.');
    }
    return $env;
};
$env = $detectEnvironment();

/**
 * Container
 * 
 * @var object
 */
$c = new Container(new Loader(ROOT ."app/".$env."/service", LOADER)); // Bind services to container
$c['app.env'] = $env;

/**
 * Include application
 */
require OBULLO .'Application/Cli.php';

/**
 * Dependency
 */
$c['dependency'] = function () use ($c) {
    return new Dependency($c);
};

/**
 * Config
 */
$c['config'] = function () use ($c) {
    return new Config($c);
};

/**
 * Application
 */
$c['app'] = function () use ($c) {
    return new Cli($c);
};

/**
 * Http request
 */
$c['request'] = function () {
    return new NullRequest;
};

/**
 * Http reponse
 */
$c['response'] = function () {
    return new NullResponse;
};

include APP .'components.php';