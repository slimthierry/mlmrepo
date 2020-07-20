<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;

class Agence extends Model implements Validatable
{

    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'agences';
    /**
     * The entity primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'label',
        'address',
        'country'
    ];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relations = [];

    // View model interface implementations

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array(
            'label' => 'bail|required|max:100',
            'address' => 'bail|nullable',
            'country' => 'bail|nullable|max:100',
            'id' => "bail|sometimes|unique:$this->table,id"
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            'label' => 'bail|sometimes|max:100',
            'id' => "bail|sometimes",
            'address' => 'bail|nullable',
            'country' => 'bail|nullable|max:100',
            "id" => "required|exists:$this->table,$this->primaryKey"
        );
    }

    /**
     * @inheritDoc
     */
    public function messages()
    {
        return array();
    }
}
