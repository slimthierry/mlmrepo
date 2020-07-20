<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;

class Department extends Model implements Validatable
{
    // use SoftDeletes;
    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'departments';
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
        'name',
        'description',
        'organisation_id'
    ];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relations = [
        'roles',
        'company'
    ];

    /**
     * Get the list of roles model related to this model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function department_roles()
    {
        return $this->hasMany(DepartmentRole::class, 'department_id', 'id');
    }

    /**
     * Get the list of roles model related to this model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, "department_roles")
            ->using(DepartmentRole::class)
            ->wherePivot('deleted_at', '=', null)
            ->withPivot([]);
    }

    /**
     * Permission role model relation definition
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function department_user()
    {
        return $this->hasOne(DepartmentUser::class, 'department_id', 'id');
    }

    /**
     * Return a belongsTo relationship between the current model and the organisation model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Organisation::class, "organisation_id", "id");
    }


    // View model interface implementations

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array(
            "name" => "required|max:255|unique:$this->table,name",
            "description" => "nullable",
            "organisation_id" => "required|exists:organisations,id",
            "roles.*" => "exists:roles,id",
            "roles" => "required",
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            "name" => "sometimes|max:255",
            "description" => "nullable",
            "roles.*" => "exists:roles,id",
            "organisation_id" => "sometimes|exists:organisations,id",
            "roles" => "nullable",
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
