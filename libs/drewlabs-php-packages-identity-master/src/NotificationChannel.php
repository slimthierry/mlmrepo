<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;

class NotificationChannel extends Model implements Validatable
{
    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'notification_channels';

    /**
     * User id column name
     *
     * @var string
     */

    protected $fillable = [
        'identifier',
        'secret',
        'extras'
    ];

    protected $hidden = [
        'identifier',
        'secret',
    ];

    // View model interface implementations

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array(
            'identifier' => 'required|max:100',
            'secret' => 'nullable|max:255',
            'extras' => 'nullable|json',
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            'identifier' => 'sometimes|max:100',
            'secret' => 'nullable|max:255',
            'extras' => 'nullable|json',
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
