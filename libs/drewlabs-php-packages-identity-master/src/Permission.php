<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model implements Validatable
{
    use SoftDeletes;

    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The label field
     *
     * @var string
     */
    protected $permission_label = 'label';

    /**
     * Related table primary key
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'label', 'display_label', 'description',
    ];

    /**
     * Related models definitions
     *
     * @var array
     */
    protected $relations = [
        'permission_roles',
    ];

    /**
     * PermissionRoles model relation definition
     *
     * @return mixed
     */
    public function permission_roles()
    {
        return $this->hasMany(PermissionRole::class);
    }
    /**
     * Get the label field of the permission table
     *
     * @return string
     */
    public function getPermission()
    {
        return $this->permission_label;
    }

    /**
     * Returns the permission label of system administrators
     *
     * @return void
     */
    public static function getSysAdminPermission()
    {
        return "all";
    }

    // View model interface implementations

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array(
            "label" => ["required", "max:45", \Illuminate\Validation\Rule::unique($this->table, 'label')->whereNull('deleted_at'), "min:3"],
            "display_label" => "required|max:255",
            "description" => "nullable",
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            "label" => "sometimes|max:45|min:3",
            "display_label" => "sometimes|max:255",
            "description" => "sometimes",
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
