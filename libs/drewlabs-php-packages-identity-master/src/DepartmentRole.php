<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentRole extends Model
{

    use SoftDeletes;
    use AsPivot;

    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'department_roles';
    /**
     * Related table "application unique identifier"
     *
     * @var string
     */
    protected $table_identifier = 'department_roles';
    /**
     * User id column name
     *
     * @var string
     */

    protected $fillable = [
        'department_id', 'role_id',
    ];
}
