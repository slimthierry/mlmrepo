<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model implements Validatable
{

    use SoftDeletes;
    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'roles';
    /**
     * The entity primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @inheritDoc
     */
    protected $indexRoute = 'get_roles';


    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'display_label',
        'description',
        'is_admin_user_role'
    ];
    /**
     * Related models definitions
     *
     * @var array
     */
    public $relations = [
        // 'permission_roles.permission',
        'permissions'
    ];

    /**
     * RoleUser relation definition
     *
     * @return mixed
     */
    public function role_users()
    {
        return $this->hasMany(RoleUser::class);
    }

    /**
     * Permission role model relation definition
     *
     * @return mixed
     */
    public function permission_roles()
    {
        return $this->hasMany(PermissionRole::class);
    }

    /**
     * Get the permissions associated with this role
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_roles')
            ->using(PermissionRole::class);
    }

    /**
     * Get the list of roles model related to this model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function department_roles()
    {
        return $this->hasMany(DepartmentRole::class, 'role_id', 'id');
    }

    /**
     * Return the label property of the role model
     *
     * @return string
     */
    public static function getRole()
    {
        return "label";
    }

    /**
     * Verifies if the current object contains a list of permission labels
     *
     * @param array $permissions
     * @return bool
     */
    public function contains(array $permissions)
    {
        $permission_labels = array_map(function ($permission_role) {
            return $permission_role->permission->label;
        }, $this->permission_roles->all());
        return !empty(array_intersect($permission_labels, $permissions));
    }

    // View model interface implementations

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array(
            "label" => ["required", "max:190",  \Illuminate\Validation\Rule::unique($this->table, 'label')->whereNull('deleted_at')],
            "display_label" => "required|max:255",
            "description" => "nullable",
            "is_admin_user_role" => "nullable|boolean",
            "permissions" => "required",
            "permissions.*" => "exists:permissions,id"
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            "label" => "sometimes|max:190",
            "display_label" => "sometimes|max:255",
            "description" => "nullable",
            "is_admin_user_role" => "nullable|boolean",
            "permissions" => "sometimes",
            "permissions.*" => "exists:permissions,id",
            "deletable_permissions" => "nullable",
            "deletable_permissions.*" => "exists:permissions,id",
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
