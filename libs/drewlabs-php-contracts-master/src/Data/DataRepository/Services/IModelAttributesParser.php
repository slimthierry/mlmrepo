<?php

namespace Drewlabs\Contracts\Data\DataRepository\Services;

use Drewlabs\Contracts\Data\IParsable;

interface IModelAttributesParser
{

    /**
  * Set the current model to work on
  *
  * @param IParsable $model
  * @return static
  */
    public function setModel(IParsable $model);

    /**
     * Returns the model bein configured or create_new_folder
     *
     * @return IParsable
     */
    public function getModel();

    /**
     * Parse model state with the matching field defined in the model fillable array
     * @param array $model_state_map
     * @return static
     */
    public function setModelInputState(array $model_state_map);

    /**
     * Return a dictionnary of model fillable columns along with the insertion values
     *
     * @return array|null
     */
    public function getModelInputState();
}
