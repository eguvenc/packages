<?php

namespace Obullo\Container\ServiceProvider;

class Filter extends AbstractServiceProvider
{
    /**
     * The provides array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        'filter',
        'filter.is',
        'filter.clean',
    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     *
     * @return void
     */
    public function register()
    {
        $container = $this->getContainer();

        $container->share('filter', 'Obullo\Filters\Filter')->withArgument($container);
        $container->share('filter.is', 'Obullo\Filters\Is');
        $container->share('filter.clean', 'Obullo\Filters\Clean');
    }
}