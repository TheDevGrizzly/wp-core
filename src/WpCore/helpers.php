<?php

use Illuminate\Config\Repository;

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        $config_path = TEMPLATEPATH . '/config/';
        $keys = explode('.', $key);
        $file = $config_path . $keys[0] . '.php';

        if (file_exists($file)) {
            $config = new Repository(require $file);

            unset($keys[0]);

            return $config->get(implode('.', $keys), $default);
        }

        return false;
    }
}

// Better errors
if (defined('WP_DEBUG') && WP_DEBUG) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}
