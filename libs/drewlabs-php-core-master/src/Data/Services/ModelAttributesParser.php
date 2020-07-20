<?php

namespace Drewlabs\Core\Data\Services;

use Drewlabs\Contracts\Data\DataRepository\Services\IModelAttributesParser;
use Drewlabs\Contracts\Data\IParsable;
use Drewlabs\Contracts\Hasher\IHasher;

class ModelAttributesParser implements IModelAttributesParser
{

    /**
     * Possible password entry that can be sent in a request
     * It a protected variable that can be extended
     * @var array
     */

    protected $possible_password_entries = ['password', 'secret', 'mot_de_passe'];

    /**
     * @var IParsable
     */
    protected $model;

    /**
     * Dictionnary of key value pairs of the data to be inserted
     * @var array
     */
    protected $mappings;

    /**
     * Password hashind algorithm provided
     * @var IHasher
     */
    private $hasher;

    public function __construct(IHasher $hasher)
    {
        $this->hasher = clone $hasher;
    }

    /**
     * Set the current model to work on
     *
     * @param IParsable $model
     * @return static
     */
    public function setModel(IParsable $model)
    {
        $this->model = clone $model;
        return $this;
    }

    /**
     * Returns the model bein configured or create_new_folder
     *
     * @return IParsable
     */
    public function getModel()
    {
        return clone $this->model;
    }

    /**
     * Parse model state with the matching field defined in the model fillable array
     * @param array $inputs
     * @return static
     */
    public function setModelInputState(array $inputs)
    {
        $this->mappings = $this->buildModelFillableColumns($inputs, $this->model->getModelStateMap());
        return $this;
    }

    /**
     * Return a dictionnary of model fillable columns along with the insertion values
     *
     * @return array|null
     */
    public function getModelInputState()
    {
        return $this->mappings;
    }

    /**
     * Build current model fillable columns insertion key value pair based on matching field in the request input
     *
     * @param array $request_input_map
     * @param array $model_state_map
     * @return array
     */
    private function buildModelFillableColumns(array $request_input_map, array $model_state_map)
    {
        $list = [];
        // Generate a list of the request input keys
        $request_input_keys = array_keys($request_input_map);
        // if the model_state+map is empty, filter the request body by removing all nullable values
        if (empty($model_state_map) || is_null($model_state_map)) {
            // Get the value of the model fillable property
            $fillables = $this->model->getFillables();
            if (!is_null($fillables) && !empty($fillables)) {
                foreach ($fillables as $value) {
                    # code...
                    if (array_key_exists($value, $request_input_map)) {
                        $list[$value] = $request_input_map[$value];
                    }
                }
            }
            return $list;
        } else {
            // Loop through the variable and find fillable keys that match items in the
            // If there is a match fill the list variable with the request input value along with the fillable key
            foreach ($model_state_map as $key => $value) {
                // Checks if the value of the current key is set in the request_input_map
                if (\in_array($key, $request_input_keys)) {
                    // Get the fillable key that matches the current $key
                    $current_fillable_key = $value;
                    // Create an entry with the fillable key along with either a hash of the value or the normal state
                    // of the value
                    $list[$current_fillable_key] = \in_array($current_fillable_key, $this->possible_password_entries) ? $this->hasher->make($request_input_map[$key]) : $request_input_map[$key];
                }
            }
            return $list;
        }
    }

    public function __destruct()
    {
        unset($this->hasher);
        unset($this->model);
        unset($this->columns_map);
    }
}
