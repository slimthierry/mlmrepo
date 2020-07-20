<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Contracts\Auth\IAuthenticatableInstanciatable;
use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Drewlabs\Packages\Identity\Traits\IdentityUserInfo;

class UserInfo extends Model implements Validatable, IAuthenticatableInstanciatable
{

    use IdentityUserInfo;

    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'user_infos';

    /**
     * The entity primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    // protected $appends = array('is_manager', 'company', 'division', 'workspaces', 'emails');

    // protected $hidden = array('organisation', 'department_id', 'organisation_name', 'user_id', 'user_workspaces', 'email', 'other_email');

    // protected $workspace_authorizations = [];

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'address',
        'email',
        'other_email',
        'phone_number',
        'postal_code',
        'birthdate',
        'sex',
        'user_id',
        'organisation_name',
        'parent_id',
        'department_id',
        'agence_id',
        'profile_url',
        'app_lang'
    ];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relations = [
        'department',
        "company",
        "manager",
        "agence"
    ];

    /**
     * {@inheritDoc}
     * Build a dictionary or laravel validation rules that can be applied to the {UserInfo} model when it is created
     */
    public function rules()
    {
        return array(
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'nullable|max:255',
            'email' => "required|unique:$this->table,email|required|max:190",
            'other_email' => "nullable|unique:$this->table,other_email|required|max:190",
            'phone_number' => 'nullable|max:20|min:8',
            'postal_code' => 'nullable|max:255',
            'birthdate' => 'nullable|max:100|date',
            'sex' => "required|in:F,M",
            'user_id' => 'required|exists:' . \drewlabs_identity_configs('models.user.table') . "," . \drewlabs_identity_configs('models.user.primaryKey'),
            'organisation_name' => "nullable|exists:" . \drewlabs_identity_configs('models.organisation.table') . "," . \drewlabs_identity_configs('models.organisation.labelKey'), //
            "parent_id" => "nullable|exists:$this->table,id",
            "department_id" => "nullable|exists:departments,id",
            "agence_id" => "nullable|exists:agences,id",
            "profile_url" => 'nullable|url',
            'app_lang' => 'nullable|max:5'
        );
    }

    /**
     * {@inheritDoc}
     * Build a dictionary or laravel validation rules that can be applied to the {UserInfo} model when it is updated
     */
    public function updateRules()
    {
        return array(
            'firstname' => 'nullable|string|max:50',
            'lastname' => 'nullable|string|max:50',
            'address' => 'nullable|max:255',
            'email' => "nullable|unique:$this->table,email|max:190",
            'other_email' => "nullable|unique:$this->table,other_email|max:190",
            'phone_number' => 'nullable|max:20|min:8',
            'postal_code' => 'nullable|max:255',
            'birthdate' => 'nullable|max:100|date',
            'sex' => "nullable|in:F,M",
            'user_id' => 'sometimes|exists:' . \drewlabs_identity_configs('models.user.table') . "," . \drewlabs_identity_configs('models.user.primaryKey'),
            'organisation_name' => "nullable|exists:" . \drewlabs_identity_configs('models.organisation.table') . "," . \drewlabs_identity_configs('models.organisation.labelKey'), //
            "department_id" => "nullable|exists:departments,id",
            "agence_id" => "nullable|exists:agences,id",
            "profile_url" => 'nullable|url',
            'app_lang' => 'nullable|max:5',
            "parent_id" => "nullable|exists:$this->table,id"
        );
    }

    public function messages()
    {
        return array();
    }
}
