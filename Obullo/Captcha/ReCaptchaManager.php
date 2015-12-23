<?php

namespace Obullo\Captcha;

use Obullo\Captcha\Provider\ReCaptcha;

use Obullo\Container\ServiceInterface;
use Obullo\Container\ContainerInterface as Container;

/**
 * ReCaptchaManager Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ReCaptchaManager implements ServiceInterface
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
        $this->c['recaptcha.params'] = $params;
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        $this->c['recaptcha'] = function () {

            return new ReCaptcha(
                $this->c,
                $this->c['request'],
                $this->c['translator'],
                $this->c['logger'],
                $this->c['recaptcha.params']
            );
        };

    }

}