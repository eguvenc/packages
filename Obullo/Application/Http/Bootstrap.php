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

$container = new League\Container\Container;
$container->add('env', new League\Container\Argument\RawArgument($env));

/**
 * Include application
 */
require OBULLO .'Application/Http.php';

/**
 * Register core components
 */
$container->share('config', 'Obullo\Config\Config')->withArgument($container);
$container->share('app', 'Obullo\Application\Http')->withArgument($container);
$container->share('middleware', 'Obullo\Application\MiddlewareStack')->withArgument($container);

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
$request->setContainer($container);

/**
 * Share Server Request
 */
$container->share(
    'request',
    $request
);

/**
 * Register app components
 */
require APP .'components.php';

/**
 * Register router
 */
$container->share('router', 'Obullo\Router\Router')
    ->withArgument($container)
    ->withArgument($container->get('request'))
    ->withArgument($container->get('logger'));


/**
 * Initialize to application
 */
$container->get('app')->init();