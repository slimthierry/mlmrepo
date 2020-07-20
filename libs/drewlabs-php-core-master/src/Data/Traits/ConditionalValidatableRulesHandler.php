<?php

namespace Drewlabs\Core\Data\Traits;

trait ConditionalValidatableRulesHandler
{
    protected $multiple;

    /**
     * {@inheritDoc}
     */
    public function setApplyMultipleValidationRules($value)
    {
        $this->multiple = $value;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getApplyMultipleValidationRulesAttributes()
    {
        return $this->multiple;
    }

    /**
     * {@inheritDoc}
     */
    public function resetConditionnalAttributes()
    {
        $this->multiple = false;
        return $this;
    }
}
