<?php

namespace Obullo\Validator;

/**
 * ReCaptcha
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ReCaptcha
{
    protected $field;
    protected $request;
    protected $recaptcha;
    protected $validator;
    protected $translator;

    /**
     * Constructor
     * 
     * @param Validator $validator object
     * @param string    $field     name
     * @param array     $params    rule parameters 
     */
    public function __construct(ValidatorInterface $validator, $field, $params = array())
    {
        $params = null;
        $container = $validator->getContainer();

        $this->field = $field;
        $this->validator = $validator;
        $this->request = $container['request'];
        $this->recaptcha = $container['recaptcha'];
        $this->translator = $container['translator'];
    }

    /**
     * Check reCaptcha
     * 
     * @return bool
     */    
    public function isValid()
    {   
        if ($this->request->isPost()) {

            $code = $this->request->post('g-recaptcha-response');

            if (false == $this->recaptcha->result($code)->isValid()) {

                $this->validator->setError(
                    $this->field,
                    $this->translator->get(
                        'OBULLO:VALIDATOR:CAPTCHA:VALIDATION',
                        $this->translator['OBULLO:VALIDATOR:CAPTCHA:LABEL']
                    )
                );
                return false;
            }
            return true;
        }
        return false;
    }
}