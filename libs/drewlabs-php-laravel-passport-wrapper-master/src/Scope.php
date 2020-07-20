<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;

class Scope extends Model implements Validatable
{

    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'scopes';
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
        'label',
        'description_fr',
        'description_en',
    ];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relations = [];
    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array(
            'label' => 'required|string|max:100',
            'description_fr' => 'required|max:255',
            'description_en' => 'required|max:255',
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            'label' => 'sometimes|string|max:100',
            'description_fr' => 'sometimes|max:255',
            'description_en' => 'sometimes|max:255',
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
