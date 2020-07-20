<?php //

namespace Drewlabs\Core\Data;

use Drewlabs\Contracts\Data\IDataProviderHandlerParams;

class DataProviderUpdateHandlerParams extends \Drewlabs\Contracts\EntityObject\AbstractEntityObject implements IDataProviderHandlerParams
{
    /**
     * List of keys that will be bind to parameters
     */
    const keys = [
        'method',
        'upsert',
        'should_mass_update'
    ];

    protected $guarded = [];

    /**
     * @inheritDoc
     */
    protected function getJsonableAttributes()
    {
        return static::keys;
    }

    /**
     * Returns the list of parameters to apply to the repository handler
     *
     * @return array
     */
    public function getParams()
    {
        return $this->attributesToArray();
    }

    /**
     * Returns list of bindable parameters keys
     *
     * @return array
     */
    public static function getParamsKeys()
    {
        return static::keys;
    }
}
