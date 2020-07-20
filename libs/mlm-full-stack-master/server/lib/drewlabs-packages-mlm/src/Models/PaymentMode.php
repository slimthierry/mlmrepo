<?php

namespace Drewlabs\Packages\MLM\Models;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\HasMany;

use function PHPSTORM_META\type;

class PaymentMode extends Model
{
    protected $table = 'payments_modes';


    protected $fillable = [
                    'id',
                    'type',
                    'amount',
                    'created_at',
                    'updated_at'
    ];

    protected $hidden =[];

    public function Transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}

