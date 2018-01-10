<?php

namespace WpCore\Shortcodes;

use WpCore\Bootstrap\Blade;

/**
 * Class shortcode, basic shortcode
 */
class Shortcode
{
    /**
     * Name of the shortcode to use in wp admin
     * @var string
     */
    protected $name = '';

    /**
     * Defaults values for arguments
     * @var array
     */
    protected $defaults = [];

    /**
     * Blade template of the shortcode
     * @var string
     */
    protected $template = '';

    /**
     * Init shortcode
     */
    public function __construct()
    {
        add_shortcode($this->name, [$this, 'register']);
    }

    /**
     * Init attributes and declare view for the shortcode
     * @param  array $attributes Attributes of the shortcode
     * @return string            Html rendered of the shortcode
     */
    public function register($attributes)
    {
        $attributes = shortcode_atts($this->defaults, $attributes);
        return $this->view($this->template, $attributes);
    }

    /**
     * Render view for the mail
     * @param  string $view View to render
     * @param  array  $with Data to pass to the view
     * @return string Html rendered
     */
    protected function view($view, $with = [])
    {
        return Blade::render($view, $with);
    }
}
