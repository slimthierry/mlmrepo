<?php

namespace Drewlabs\Packages\Identity\ModelObservers;

use Drewlabs\Packages\Identity\Organisation;

class OrganisationObserver
{
    /**
     * Handle the Organisation model "created" event.
     *
     * @param  Organisation  $model
     * @return void
     */
    public function created(Organisation $model)
    {
        //
    }

    /**
     * Handle the Organisation model "updated" event.
     *
     * @param  Organisation  $model
     * @return void
     */
    public function updated(Organisation $model)
    {
        //
    }

    /**
     * Handle the Organisation model "deleted" event.
     *
     * @param  Organisation  $model
     * @return void
     */
    public function deleted(Organisation $model)
    {
        //
        $model->departments()->delete();
    }
    /**
     * Handle the Organisation model "force delete" event.
     *
     * @param  Organisation  $model
     * @return void
     */
    public function forceDelete(Organisation  $model)
    {
        // $model->departments()->delete();
    }

    /**
     * Handle Organisation Model deleting event
     *
     * @param Organisation $model
     * @return void
     */
    public function creating(Organisation $model)
    {
        if (!\is_null(\config('drewlabs_identity.models.ressources_wallet.class', null))) {
            $walletModel = \config('drewlabs_identity.models.oragnisation_bank.class');
            $wallet = (new $walletModel)->create([
                'balance' => 0.0,
                'status' => 1
            ]);
            $model->wallet_id = $wallet->getKey();
        }
    }

    /**
     * Handle Organisation Model updating event
     *
     * @param Organisation $model
     * @return void
     */
    public function updating(Organisation $model)
    {

    }
}
