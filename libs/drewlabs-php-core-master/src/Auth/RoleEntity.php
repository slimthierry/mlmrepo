<?php

namespace Drewlabs\Core\Auth;

class RoleEntity
{
    public $id;
    public $label;
    public $display_label;
    public $description;

    /**
     * RoleEntity object initializer
     *
     * @param int $id
     * @param string $label
     * @param string $display_label
     * @param string $description
     */
    public function __construct($id, $label, $display_label = null, $description = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->display_label = $display_label;
        $this->description = $description;
    }
}
