<?php
/**
 * Detect environment
 * 
 * @return array
 */
$env = null;
$environments = include ROOT .'app/environments.php';
foreach (array_keys($environments) as $current) {
    if (in_array(gethostname(), $environments[$current])) {
        $env = $current;
        break;
    }
}
if ($env == null) {
    die('We could not detect your application environment, please correct your app/environments.php file.');
}
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