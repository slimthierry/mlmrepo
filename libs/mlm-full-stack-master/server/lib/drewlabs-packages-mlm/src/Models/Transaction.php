<?php

namespace Drewlabs\Packages\MLM\Models;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;
use Money\Money;
use Money\Currency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    //
        /**
     * @var string
     */
    protected $table = 'Transactions';

       /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */

    protected $fillable = ['id', 'account_id', 'client_membership_id', 'debit', 'credit', 'currency', 'balance_before', 'balance_after', 'label', 'trans_status', 'type', 'payment_method'];

    /**
     * Currency.
     *
     * @var string $currency
     */
    protected $currency;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $guarded=['id'];

    /**
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'tags' => 'array',
    ];

    /**
     * Account relation.
     */
    public function Account(): BelongsTo
    {
        return $this->belongsTo('App\Models\Account');
    }

    // public function getCurrentBalance(string $currency): Money
    // {
    //     if ($this->type == 'mobile' || $this->type == 'web') {
    //         $balance = $this->transactions->sum('debit') - $this->transactions->sum('credit');
    //     } else {
    //         $balance = $this->transactions->sum('credit') - $this->transactions->sum('debit');
    //     }

    //     return new Money($balance, new Currency($currency));
    // }


    /**
     * Set currency.
     *
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

}
