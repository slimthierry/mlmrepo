<?php

namespace Drewlabs\Core\Validator\Traits;

trait ArrayValidator
{

    /**
     * Apply validation rules to an array input based on rules defines in an associative array
     *
     * @param array $values
     * @param array $rules
     * @param array $messages
     * @return void
     */
    public function validateArray(array $values, array $rules, array $messages = [])
    {
        $dirtyInputs = array();
        // Reset errors bag on each validations
        $this->setErrors([]);
        // Load the model view pre-defined rules and build the validatable inputs
        foreach ($rules as $key => $value) {
            # code...
            if (\array_key_exists($key, $values)) {
                $dirtyInputs[$key] = $values[$key];
            }
        }
        $validator = $this->validator->make($dirtyInputs, $rules, $messages);
        // Validation fails
        if ($validator->fails()) {
            $this->setErrors($validator->errors()->messages());
        }
        return $this;
    }
}
