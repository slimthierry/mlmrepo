<?php

namespace Drewlabs\Core\Validator\Traits;

trait ModelValidator
{
    /**
     * Apply validation rules on array input using rules defines in a validatable model instance
     *
     * @param \Drewlabs\Core\Validator\Contracts\Validatable $validatable
     * @param array $input
     * @return static
     */
    public function validateModel(\Drewlabs\Core\Validator\Contracts\Validatable $validatable, array $input)
    {
        $validator_inputs = $input;
        // Load the validation rules from the view model
        $rules = $this->is_update ? (!is_null($validatable->updateRules()) ? $validatable->updateRules() : array()) : $validatable->rules();
        $validator = $this->validator->make(
            $validator_inputs,
            $rules,
            is_array($validatable->messages()) ? $validatable->messages() : array()
        );
        // Validation fails
        if ($validator->fails()) {
            $this->setErrors($validator->errors()->messages());
        }
        // Reset the update property when validation completes in order to not apply the same property value to the next call on the validtor
        $this->setUpdate(false);
        // Return the object for methods chaining
        return $this;
    }
}
