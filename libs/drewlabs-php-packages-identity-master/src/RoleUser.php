<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleUser extends Model
{

    use SoftDeletes;
    use AsPivot;

    /**
     *
     * @var string
     */
    protected $table = 'role_users';

    /**
     * User id column name
     *
     * @var string
     */
    //  public $user_id = 'user_id';

    protected $fillable = [
        'user_id', 'role_id',
    ];

    /**
     * User model relation definition
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Role model relation definition
     *
     * @return mixed
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user_id field name in the role_user collection
     *
     * @return string
     */
    public static function getUserIdFieldName()
    {
        return "user_id";
    }

    /**
     * Get the role_id field name in the role_user collection
     *
     * @return string
     */
    public static function roleIdFieldName()
    {
        return "role_id";
    }
}
