<?php

namespace Obullo\Validator;

interface ValidatorAwareInterface
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