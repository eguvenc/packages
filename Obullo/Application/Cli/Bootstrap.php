<?php

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
        die('We could not detect your application environment, please correct your app/environments.php file.');
    }
    return $env;
};
$env = $detectEnvironment();

/**
 * Container
 * 
 * @var object
 */
$container = new League\Container\Container;
$container->add('env', new League\Container\Argument\RawArgument($env));

/**
 * Include application
 */
require OBULLO .'Application/Cli.php';

/**
 * Register core components
 */
$container->share('config', 'Obullo\Config\Config')->withArgument($container);
$container->share('app', 'Obullo\Application\Cli')->withArgument($container);
$container->share('request', 'Obullo\Cli\NullRequest');
$container->share('response', 'Obullo\Cli\NullResponse');

/**
 * Initialize to application
 */
require APP .'components.php';