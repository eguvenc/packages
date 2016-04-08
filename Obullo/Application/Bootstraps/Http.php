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
require OBULLO .'Application/Http.php';

/**
 * Register core components
 */
$container->share('config', 'Obullo\Config\Config')->withArgument($container);
$container->share('app', 'Obullo\Application\Http')->withArgument($container);
$container->share('middleware', 'Obullo\Application\MiddlewareStack')->withArgument($container);

/**
 * Create test environment
 */
$testEnvironment = null;
if (defined('STDIN') && ! empty($_SERVER['argv'][0]) && $_SERVER['SCRIPT_FILENAME'] == 'public/index.php') {
    $testEnvironment = new Obullo\Tests\TestEnvironment;
    $testEnvironment->createServer();
}
/**
 * Create http server request
 */
$request = Obullo\Http\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);
/**
 * Register core components
 */
$container->share('request', $request);
$container->share('response', 'Obullo\Http\Response');
/**
 * Set container to test environment if its available
 */
if ($testEnvironment) {
    $testEnvironment->setContainer($container);
}
/**
 * Initialize to application
 */
$container->get('app')->init();