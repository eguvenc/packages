<?php

use Obullo\Container\Loader;
use Obullo\Container\Container;
use Obullo\Container\Dependency;
use Obullo\Container\ContainerInterface;

use Obullo\Config\Config;
use Obullo\Application\Http;

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
require OBULLO .'Application/Http.php';

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
    return new Http($c);
};

/**
 * Components
 */
include APP .'components.php';

/**
 * Create Server Request
 */
$request = \Obullo\Http\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);
$c['request'] = function () use ($request, $c) {
    $request->setContainer($c);
    return $request;
};

/**
 * Initialize to application
 */
$c['app']->init();