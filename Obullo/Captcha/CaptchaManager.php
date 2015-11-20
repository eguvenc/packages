<?php

namespace Obullo\Captcha;

use Obullo\Captcha\Provider\Image;
use Obullo\Captcha\Provider\ReCaptcha;

use Obullo\Container\ContainerInterface as Container;
use Obullo\Container\ServiceInterface;

/**
 * CaptchaManager Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class CaptchaManager implements ServiceInterface
{
    /**
     * Container class
     * 
     * @var object
     */
    protected $c;

    /**
     * Constructor
     * 
     * @param Container $container container
     */
    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    /**
     * Set service parameters
     * 
     * @param array $params service configuration
     *
     * @return void
     */
    public function setParams(array $params)
    {
        $this->c['captcha.params'] = $params;
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        $this->c['captcha'] = function () {

            return new Image(
                $this->c,
                $this->c['url'],
                $this->c['request'],
                $this->c['session'],
                $this->c['translator'],
                $this->c['logger'],
                $this->c['captcha.params']
            );
        };

    }

}