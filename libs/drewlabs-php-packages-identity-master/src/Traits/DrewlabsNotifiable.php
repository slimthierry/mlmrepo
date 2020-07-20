<?php

namespace Drewlabs\Packages\Identity\Traits;

trait DrewlabsNotifiable
{

    /**
     * @inheritDoc
     */
    public function getChannels()
    {
        return method_exists($this, 'channels') ? $this->channels : [];
    }
}
