<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class UserNotificationChannel extends Model implements Validatable
{
    use AsPivot;
    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'user_notification_channels';

    /**
     * User id column name
     *
     * @var string
     */

    protected $fillable = [
        'user_id',
        'channel_id',
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
            'user_id' => 'required|exists:users,user_id',
            'channel_id' => 'required|exists:notification_channels,id',
            'identifier' => 'required|max:100',
            'secret' => 'nullable|max:100',
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            'user_id' => 'sometimes|exists:users,user_id',
            'channel_id' => 'sometimes|exists:notification_channels,id',
            'identifier' => 'sometimes|max:100',
            'secret' => 'nullable|max:100',
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
