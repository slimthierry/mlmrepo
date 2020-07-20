<?php

namespace Drewlabs\Packages\Identity\Http\Requests;

use Illuminate\Validation\Rule;

class UserRequest implements \Drewlabs\Core\Validator\Contracts\Validatable
{
    /**
     * {@inheritDoc}
     * Validate an incoming Login Request inputs
     */
    public function rules()
    {
        return array(
            "username" => "required|max:60|unique:users,user_name",
            "password" => "sometimes|min:6|max:100",
            "password_confirmation" => "required_with:password|same:password",
            "is_active" => "sometimes|boolean",
            "is_verified" => 'sometimes|boolean',
            "roles" => "required|array",
            "roles.*" => Rule::exists("roles", "id")->where(function ($query) {
                return $query->whereNull('deleted_at');
            }),
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'organization' => 'nullable|max:60',
            'address' => 'nullable|max:255',
            'email' => 'required|max:190|unique:user_infos,email',
            'other_email' => 'nullable|max:190|unique:user_infos,other_email',
            'phone_number' => 'nullable|max:20|min:8',
            'postal_code' => 'nullable|max:255',
            'birthdate' => ['sometimes', 'max:255', 'date', 'regex:/^\d\d\d\d-(0?[1-9]|1[0-2])-(0?[1-9]|[12][0-9]|3[01])$/'],
            'sex' => 'sometimes|in:F,M',
            'organisation_name' => "nullable|exists:" . \drewlabs_identity_configs('models.organisation.table') . "," . \drewlabs_identity_configs('models.organisation.labelKey'), //
            'department_id' => "nullable|required_with:organisation_name|exists:departments,id",
            'is_department_manager' => 'nullable|boolean'
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            "username" => "sometimes|max:60",
            "password" => "nullable|min:6|max:100",
            "password_confirmation" => "required_with:password|same:password",
            "is_active" => "sometimes|boolean",
            "double_auth_active" => "sometimes|boolean",
            'firstname' => 'sometimes|string|max:50',
            'lastname' => 'sometimes|string|max:50',
            'address' => 'nullable|max:255',
            'email' => "sometimes|max:190",
            'other_email' => "nullable|max:190",
            'phone_number' => 'sometimes|max:20|min:8',
            'postal_code' => 'nullable|max:255',
            'birthdate' => 'sometimes|max:100|date',
            'sex' => "sometimes|in:F,M",
            'organisation_name' => "nullable|exists:" . \drewlabs_identity_configs('models.organisation.table') . "," . \drewlabs_identity_configs('models.organisation.labelKey'), //
            'department_id' => 'nullable|required_with:organisation_name|exists:departments,id',
            'is_department_manager' => 'nullable|boolean'
        );
    }

    /**
     * {@inheritDoc}
     * Returns validation error when login request validation fails
     */
    public function messages()
    {
        return array();
    }
}
