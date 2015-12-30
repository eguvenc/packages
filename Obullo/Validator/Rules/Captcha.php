<?php

namespace Obullo\Validator;

/**
 * Captcha
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Captcha
{
    protected $field;
    protected $request;
    protected $captcha;
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
        $this->captcha = $container['captcha'];
        $this->translator = $container['translator'];
    }

    /**
     * Check captcha
     * 
     * @param string $value string
     * 
     * @return bool
     */    
    public function isValid($value)
    {   
        if ($this->request->isPost()) {

            if (false == $this->captcha->result($value)->isValid()) {

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