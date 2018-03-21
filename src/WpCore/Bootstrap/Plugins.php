<?php

namespace WpCore\Bootstrap;

class Plugins
{
    /**
     * Action to init tgmpa
     */
    public function __construct()
    {
        add_action('tgmpa_register', [$this, 'plugins']);
    }

    /**
     * Declare required and recomended plugins
     */
    public function plugins()
    {
        $plugins = config('plugins.plugins');
        $config = config('plugins.config');
        tgmpa($plugins, $config);
    }
}
