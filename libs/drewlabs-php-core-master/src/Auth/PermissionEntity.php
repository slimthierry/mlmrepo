<?php

namespace Drewlabs\Core\Auth;

class PermissionEntity
{
    public $id;
    public $label;
    public $display_label;
    public $description;

    /**
     * PermissionEntity object initializer
     *
     * @param int $id
     * @param string|null $label
     * @param string|null $display_label
     * @param string|null $description
     */
    public function __construct($id, $label, $display_label = null, $description = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->display_label = $display_label;
        $this->description = $description;
    }
}
