<?php

namespace Obullo\Validator;

use Obullo\Validator\ValidatorInterface as Validator;

trait ValidatorAwareTrait
{
    /**
     * Validator
     * 
     * @var object
     */
    protected $validator;

    /**
     * Set validator object
     * 
     * @param object $validator validator
     *
     * @return void
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Get validator object
     *
     * @return object
     */
    public function getValidator()
    {
        return $this->validator;
    }
}