<?php

namespace Ayctor\Roles;

use WpCore\Roles\Role;

class Blueprint extends Role
{
    public $name = '';

    public $label = '';

    public $capabilities = [];
}
