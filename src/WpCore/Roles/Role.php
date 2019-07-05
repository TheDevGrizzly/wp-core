<?php

namespace WpCore\Roles;

class Role
{
    public $name = '';

    public $label = '';

    public $capabilities = [];

    public $role;

    public function __construct()
    {
        remove_role($this->name);

        $this->role = add_role($this->name, $this->label);

        foreach ($this->capabilities as $cap) {
            $this->role->add_cap($cap);
        }
    }
}
