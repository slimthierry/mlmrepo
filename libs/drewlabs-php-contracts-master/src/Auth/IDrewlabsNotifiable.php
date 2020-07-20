<?php

namespace Drewlabs\Contracts\Auth;

interface IDrewlabsNotifiable
{
    /**
     * Fetch list of channels associated with this instance that can be use for notifications
     *
     * @return array
     */
    public function getChannels();

    // Define other notification methods here
}
