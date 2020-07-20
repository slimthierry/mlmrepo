<?php

namespace Drewlabs\Core\Validator;

use Drewlabs\Core\Validator\Contracts\IValidator;
use Illuminate\Contracts\Validation\Factory as FactoryContract;


class InputsValidator implements IValidator
{

    use \Drewlabs\Core\Validator\Traits\ArrayValidator;
    use \Drewlabs\Core\Validator\Traits\ModelValidator;
    /**
     * Model validation errors generated after validation
     *
     * @var array
     */
    protected $validation_errors = [];

    /**
     * Boolean property set to true preforming an update operation on the model
     *
     * @var boolean
     */
    protected $is_update = false;

    /**
     * Illuminate validator contract instance provider
     *
     * @var FactoryContract
     */
    private $validator;

    public function __construct(FactoryContract $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @inheritDoc
     */
    public function validate($validatable, array $input, $messages = array())
    {
        // Reset errors bag on each validations
        $this->setErrors([]);
        if ($validatable instanceof \Drewlabs\Core\Validator\Contracts\Validatable) {
            $result = $this->validateModel($validatable, $input);
            return $result;
        }

        if (is_array($validatable)) {
            return $this->validateArray($input, $validatable, $messages);
        }
        throw new \RuntimeException(
            "Error Processing Request, invalidate validation rule type. Please specify an instance of \\Drewlabs\\Core\\Validator\\Contracts\\Validatable or an array as first parameter to the function",
            500
        );
    }

    /**
     * @inheritDoc
     */
    public function fails()
    {
        if (empty($this->validation_errors)) {
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function errors()
    {
        return $this->validation_errors;
    }

    /**
     * Validation errors setter
     *
     * @param array $errors
     * @return void
     */
    protected function setErrors(array $errors)
    {
        $this->validation_errors = $errors;
    }

    /**
     * @inheritDoc
     */
    public function setUpdate(bool $update)
    {
        $this->is_update = $update;
        return $this;
    }
}
