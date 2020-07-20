<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentUser extends Model implements Validatable
{
    use SoftDeletes;

    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'department_users';
    /**
     * Related table "application unique identifier"
     *
     * @var string
     */
    protected $table_identifier = 'department_users';
    /**
     * User id column name
     *
     * @var string
     */

    protected $fillable = [
        'department_id',
        'is_manager',
        'agence_id',
        'user_id'
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo("Drewlabs\\Packages\\Identity\\UserInfo", 'user_id', 'id');
    }

    // View model interface implementations

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array(
            "agence_id" => "nullable|exists:agences,id",
            "department_id" => "required|exits:departments,id|unique:$this->table,department_id",
            "is_manager" => "required|boolean",
            "user_id" => "required|exists:user_infos,id",
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            "agence_id" => "nullable|exists:agences,id",
            "department_id" => "sometimes|exits:departments,id|unique:$this->table,department_id",
            "is_manager" => "sometimes|boolean",
            "user_id" => "sometimes|exists:user_infos,id",
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
