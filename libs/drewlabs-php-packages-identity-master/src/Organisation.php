<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisation extends Model implements Validatable
{

    use SoftDeletes;
    /**
     * Related table name
     *
     * @var string
     */
    protected $table = 'organisations';
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
        'phone_number',
        'address',
        'postal_code',
    ];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relations = [
        "user_info",
    ];

    /**
     * Return the user info associated model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user_info()
    {
        return $this->hasOne(UserInfo::class, "organisation_id", "id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function departments()
    {
        return $this->hasMany(Department::class, 'organisation_id', 'id');
    }

    /**
     * Return the wallet model associated to the current organisation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wallet()
    {
        if(!\is_null(\config('drewlabs_identity.models.ressources_wallet.class', null)) && !\is_null(\config('drewlabs_identity.models.ressources_wallet.primaryKey', null))) {
            return $this->belongsTo(\config('drewlabs_identity.models.ressources_wallet.class'), 'wallet_id', \config('drewlabs_identity.models.ressources_wallet.primaryKey'));
        }
        return null;
    }

    /**
     * Return the association table that relate the organisations and banks tables
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organisation_banks()
    {
        if(!\is_null(\config('drewlabs_identity.models.oragnisation_bank.class', null))) {
            return $this->hasMany(\config('drewlabs_identity.models.oragnisation_bank.class'));
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array(
            'name' => 'required|string|max:145',
            'phone_number' => 'required|max:20',
            'address' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:255',
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        return array(
            'name' => 'sometimes|string|max:145',
            'phone_number' => 'sometimes|max:20',
            'address' => 'sometimes|string|max:255',
            'postal_code' => 'sometimes|string|max:255',
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
