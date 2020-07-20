<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Illuminate\Auth\Notifications\VerifyEmail;
use Drewlabs\Contracts\Auth\IUserModel;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;

trait IlluminateMustVerifyEmail
{
    /**
     * Load user model form the auth configuration defined in the config folder at the root of the application
     *
     * @return IUserModel|Model
     */
    protected function loadUserModelModel()
    {
        $userModel = \config('drewlabs_identity.models.user.class');
        if (is_null($userModel)) {
            throw new \RuntimeException("User model Not defined. Please defined your user model in the auth.php file in the configs");
        }
        return new $userModel;
    }
    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        $userModel = $this->loadUserModelModel();
        return ! is_null($userModel->fromAuthenticatable($this)->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        $userModel = $this->loadUserModelModel();
        $user = $userModel->fromAuthenticatable($this);
        return $user->forceFill([
            'email_verified_at' => $user->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->loadUserModelModel()->fromAuthenticatable($this)->notify(new VerifyEmail);
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->loadUserModelModel()->fromAuthenticatable($this)->email;
    }
}
