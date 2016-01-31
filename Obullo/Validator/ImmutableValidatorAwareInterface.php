<?php

namespace Obullo\Validator;

use Obullo\Validator\ValidatorInterface as Validator;

interface ImmutableValidatorAwareInterface
{
    /**
     * Set validator object
     * 
     * @param object $validator validator
     *
     * @return void
     */
    public function setValidator(Validator $validator);

    /**
     * Get validator object
     *
     * @return object
     */
    public function getValidator();
}