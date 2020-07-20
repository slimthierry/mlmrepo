<?php

namespace Drewlabs\Core\Validator\Contracts;

interface IValidationErrorProvider
{
    /**
     * Return an array of errors generated during validation
     *
     * @return array
     */
    public function errors();

    /**
     * Checks if the validation fails or not
     *
     * @return bool
     */
    public function fails();
}
