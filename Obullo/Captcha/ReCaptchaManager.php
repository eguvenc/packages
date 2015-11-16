<?php

namespace Obullo\Captcha;

use Obullo\Captcha\Provider\ReCaptcha;
use Obullo\Container\ContainerInterface;

/**
 * ReCaptchaManager Class
 * 
 * @author    Obullo Framework <obulloframework@gmail.com>
 * @copyright 2009-2015 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class RecaptchaManager implements ServiceInterface
{
    /**
     * Container class
     * 
     * @var object
     */
    protected $c;

    /**
     * Service parameters
     * 
     * @var array
     */
    protected $params;

    /**
     * Constructor
     * 
     * @param ContainerInterface $c      container
     * @param array              $params service parameters
     */
    public function __construct(ContainerInterface $c, array $params)
    {
        $this->c = $c;
        $this->params = $params;
    }

    /**
     * Register
     * 
     * @return object logger
     */
    public function register()
    {
        $this->c['recaptcha'] = function () {

            $this->c['recaptcha.params'] = array_merge($this->params, $this->c['config']->load('captcha/recaptcha'));

            return new ReCaptcha(
                $this->c,
                $this->c['request'],
                $this->c['translator'],
                $this->c['logger'],
                $this->params
            );
        };

    }

}