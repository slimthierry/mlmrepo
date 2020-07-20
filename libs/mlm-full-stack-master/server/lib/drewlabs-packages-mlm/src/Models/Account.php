<?php


namespace Drewlabs\Packages\MLM\Models;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Money\Money;
use Money\Currency;
use Carbon\Carbon;

/**
 * @property    Money $balance
 *
 * @property    Carbon $updated_at
 * @property    Carbon $created_at
 * @property    Carbon $created_at
 */
class Account extends Model
{
    /**
     * @var string
     */
    protected $table = 'Accounts';

     /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['id','client_membership_id', 'payment_mode_id', 'balance', 'enabled'];

      /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['id','enabled'];

        /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['balance'];


    public function transaction(): HasMany
    {
        return $this->hasMany('App\Models\Transaction');
    }
    // public function credit(
    //     $value,
    //     Carbon $created_at = null
    // ): Transaction {
    //     $value = is_a($value, Money::class)
    //         ? $value
    //         : new Money($value, new Currency($this->currency));
    //     return $this->postTrans($value, null, $created_at);
    // }

    // public function debit(
    //     $value,
    //     Carbon $created_at = null
    // ): Transaction {
    //     $value = is_a($value, Money::class)
    //         ? $value
    //         : new Money($value, new Currency($this->currency));
    //     return $this->postTrans(null, $value, $created_at);
    // }
    // public function enable(Account $account)
    // {
    //     $response = $this->jsonDispatch(new UpdateAccount($account, request()->merge(['enabled' => 1])));

    //     if ($response['success']) {
    //         $response['message'] = trans('messages.success.enabled', ['type' => $account->id]);
    //     }

    //     return response()->json($response);
    // }

    // public function disable(Account $account)
    // {
    //     $response = $this->jsonDispatch(new UpdateAccount($account, request()->merge(['enabled' => 0])));

    //     if ($response['success']) {
    //         $response['message'] = trans('messages.success.disabled', ['type' => $account->id]);
    //     }

    //     return response()->json($response);
    // }


    // public function resetCurrentBalances()
    // {
    //     $this->balance = $this->getBalance();
    //     $this->save();
    //     return $this->balance;
    // }

    /**
     * @param Money|float $value
     */
    protected function getBalanceAttribute($value)
    {
        // return new Money($value, new Currency($this->currency));
    }

    }
