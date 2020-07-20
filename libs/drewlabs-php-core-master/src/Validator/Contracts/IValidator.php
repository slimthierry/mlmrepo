<?php

namespace Drewlabs\Core\Validator\Contracts;

interface IValidator extends IValidationErrorProvider
{

    /**
     * Validate provided dirty input against a set of rules and messages
     *
     * @param Validatable|array $validatable
     * @param array $input
     * @param array|null $input
     * @return static
     */
    public function validate($validatable, array $input, $messages = array());

    /**
     * Model {is_update} property setter
     *
     * @param boolean $update
     * @return static
     */
    public function setUpdate(bool $update);
}
