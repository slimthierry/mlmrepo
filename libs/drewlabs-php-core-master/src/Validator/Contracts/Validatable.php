<?php

namespace Drewlabs\Core\Validator\Contracts;

interface Validatable
{
    /**
     * Build a dictionary of validation rules
     *
     * @return array
     */
    public function rules();

    /**
     * Build a dictionary of validation rules to apply when updating the view model
     *
     * @return array
     */
    public function updateRules();

    /**
     * Build a dictionary of validation errors messages
     *
     * @return array
     */
    public function messages();
}
