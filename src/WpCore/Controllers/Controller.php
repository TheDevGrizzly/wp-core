<?php

namespace WpCore\Controllers;

use WpCore\Bootstrap\Blade;

/**
 * Class Controller, basic controller
 */
class Controller
{
    /**
     * Helper function to tell which view to render
     * @param  string $view The view to render
     * @param  array  $with Data to pass to the view
     * @return string       Html rendered
     */
    protected function view($view, $with = [])
    {
        echo Blade::render($view, $with);
    }
}
