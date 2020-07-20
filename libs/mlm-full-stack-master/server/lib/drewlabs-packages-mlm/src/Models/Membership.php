<?php

namespace Drewlabs\Packages\MLM\Models;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use App\Models\Account;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{

    protected $table = 'clients_memberships';

    /**
  * The attributes that are mass assignable.
  *
  * @var array
  */

    protected $fillable = ['id','parrain_id', 'username', 'phone_number', 'email', 'code', 'member_level'];

 /**
  * The attributes that should be hidden for arrays.
  *
  * @var array
  */

    protected $hidden = [];

    public function Account(): HasMany
    {
        return $this->hasMany(Account::class);
    }


    public function ref () {
        return $this->where('id', $this->parrain)->first();
    }

    public function parent () {
        return $this->where('id', $this->parent_id)->first();
    }

    public function children () {
        return $this->where('parrain_id', $this->id)->get();
    }
}
