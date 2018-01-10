<?php

namespace Ayctor\Models;

use WpCore\Models\Model;

class Blueprint extends Model
{
    protected $post_type = 'blueprint';

    protected $label = 'Blueprint';

    protected $cpt_args = [];

    protected $taxonomies = [];

    protected function register()
    {
        // register fields for cpt and taxonomies
    }
}
