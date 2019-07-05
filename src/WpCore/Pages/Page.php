<?php

namespace WpCore\Pages;

/**
 * Class Page, basic option page
 */
class Page
{
    /**
     * List of field groups for the page
     * @var array
     */
    protected $fields_groups = [];

    /**
     * List args to register page
     * @var array
     */
    protected $args = [];

    /**
     * Slug to identify page
     * @var string
     */
    protected $menu_slug = '';

    /**
     * Init custom post type
     */
    public function __construct()
    {
    	if (function_exists('acf_add_options_page')) {
    		acf_add_options_page($this->args);
    	}
        add_action('acf/init', function(){
            $this->register();
            $this->registerFieds();
        });
    }

    /**
     * Register fields groups for ACF
     */
    protected function register()
    {
        $this->fields_groups = [];
    }

    /**
     * Add field groups to custom post type
     */
    protected function registerFieds()
    {
        if (function_exists('acf_add_local_field_group')) {
            foreach ($this->fields_groups as $group) {
                acf_add_local_field_group($group);
            }
        }
    }

    /**
     * Helper function to create ACF group
     * @param  string  $id       Id of the group
     * @param  string  $title    Title of the group
     * @param  array   $options  Custom options for the group
     */
    protected function group($id, $title, $options = [])
    {
        $location = [[[
            'param' => 'options_page',
            'operator' => '==',
            'value' => $this->menu_slug,
            'order_no' => 0,
            'group_no' => 0,
        ]]];

        // array merge
        if (empty($options)) {
            $options = [
                'position' => 'normal',
                'layout' => 'box',
                'hide_on_screen' => [],
            ];
        }

        $this->fields_groups[$id] = [
            'key' => $id,
            'title' => $title,
            'fields' => [],
            'location' => $location,
            'options' => $options,
            'menu_order' => 0,
        ];
    }

    /**
     * Add field to a group
     * @param  string $group Id of the group
     * @param  array $field  Arguments of the field
     */
    protected function field($group, $field)
    {
        if (!isset($field['key'])) {
            $field['key'] = $this->menu_slug . '_' . $group . '_' . $field['name'];
        }
        $this->fields_groups[$group]['fields'][] = $field;
    }
}