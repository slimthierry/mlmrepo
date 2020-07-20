<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionRole extends Model
{
    use SoftDeletes;
    use AsPivot;

    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'permission_roles';
    // /**
    //  * Related table "application unique identifier"
    //  *
    //  * @var string
    //  */
    // protected $table_identifier = 'perm_roles';

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'permission_id',
    ];

    /**
     * Permission model relation definition
     *
     * @return void
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Role model relation definition
     *
     * @return void
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
