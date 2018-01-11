<?php

namespace WpCore\Models;

use Inflect\Inflect;

/**
 * Class Model, basic model
 */
class Model
{
    /**
     * Name of the custom post type
     * @var string
     */
    protected $post_type;

    /**
     * Custom post type arguments for register_post_type function
     * @see https://codex.wordpress.org/Function_Reference/register_post_type
     * for more informations on arguments
     * @var array
     */
    protected $cpt_args;

    /**
     * Contains Array of Custom Taxonomies params
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
     * for more informations
     * @var array
     */
    protected $taxonomies;

    /**
     * Main display name for the custom post type. It tries to generate all the
     * other labels from this one.
     * @var string
     */
    protected $label;

    /**
     * List of labels for the custom post type.
     * @see https://codex.wordpress.org/Function_Reference/register_post_type
     * for more information on labels
     * @var string[]
     */
    protected $labels;

    /**
     * List of field groups for the custom post type or the taxonomies
     * @var array
     */
    protected $fields_groups = [];

    /**
     * List of columns for the custom post type
     * @var array
     */
    protected $columns = [];

    /**
     * List of sortable columns for the custom post type
     * @var array
     */
    protected $sortable_columns = [];

    /**
     * List of actions for the columns of the custom post type
     * @var array
     */
    protected $columns_show = [];

    /**
     * List of the filters for the custom post type
     * @var array
     */
    protected $filters = [];

    /**
     * Init custom post type
     */
    public function __construct()
    {
        $this->cpt_args['labels'] = $this->generateLabels();

        // CPT
        add_action('init', [$this, 'init']);
        add_action('current_screen', function ($current_screen) {
            if ($current_screen->post_type == $this->post_type) {
                $this->register();
                $this->registerFieds();
            }
        });

        // Columns
        add_filter('manage_edit-' . $this->post_type . '_columns', [$this, 'customColumns']);
        add_filter('manage_edit-' . $this->post_type . '_sortable_columns', [$this, 'customColumnsSort']);
        add_action('manage_posts_custom_column', [$this, 'customColumnsShow']);

        // Filters
        add_action('restrict_manage_posts', [$this, 'customFilters']);
        add_filter('parse_query', [$this, 'actionFilters']);
    }

    /**
     * Register custom post type and taxonomies
     */
    public function init()
    {
        register_post_type($this->post_type, $this->cpt_args);

        if (!empty($this->taxonomies)) {
            foreach ($this->taxonomies as $tax_id => $tax_args) {
                register_taxonomy($tax_id, $this->post_type, $tax_args);
            }
        }
    }

    /**
     * Use custom columns for the custom post type
     * @param  array $columns Current columns
     * @return array          New comumns
     */
    public function customColumns($columns)
    {
        if (!empty($this->columns)) {
            return $this->columns;
        }
        return $columns;
    }

    /**
     * Use custom columns for the custom post type
     * @param  array $columns Current columns
     * @return array          New comumns
     */
    public function customColumnsSort($columns)
    {
        if (!empty($this->sortable_columns)) {
            return $this->sortable_columns;
        }
        return $columns;
    }

    /**
     * Actions for the custom columns
     * @param  string $name Name of the column
     * @return string       Content of the custom columns
     */
    public function customColumnsShow($name)
    {
        global $post;
        if ($post->post_type == $this->post_type) {
            $content = '-';
            $key = array_search($name, array_column($this->columns_show, 'id'));
            if ($key === false) {
                echo $content;
                return;
            }
            $column = $this->columns_show[$key];
            if ($column['type'] == 'meta') {
                $content = get_post_meta($post->ID, $column['id'], true);
            }

            if ($column['type'] == 'term') {
                $terms = get_the_term_list($post->ID, $column['id'], '', ',', '');
                if (is_string($terms)) {
                    $content = $terms;
                }
            }

            if ($column['type'] == 'thumbnail') {
                $content = get_the_post_thumbnail($post->ID, 'thumbnail');
            }

            if ($column['type'] == 'custom') {
                if (is_callable($column['custom'])) {
                    $column['custom']();
                } else if (is_string($column['custom'])) {
                    $content = $column['custom'];
                }
            }

            echo $content;
        }
    }

    /**
     * Generate selectors for filters
     */
    public function customFilters()
    {
        if (get_post_type() == $this->post_type) {
            foreach ($this->filters as $filter) {
                ?>
                <select name="<?php echo $filter['name']; ?>" id="<?php echo $filter['name']; ?>">
                    <?php foreach ($filter['values'] as $key => $value) : ?>
                        <?php $selected = (isset($_GET[$filter['name']]) and $_GET[$filter['name']] == $key) ? 'selected="select"' : ''; ?>
                        <option value="<?php echo $key; ?>" <?php echo $selected ?>>
                            <?php echo $value; ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?php
            }
        }
    }

    /**
     * Action for custom filters
     * @param  WP_Query $query Current query
     */
    public function actionFilters($query)
    {
        if (is_admin() and isset($query->query['post_type']) and $query->query['post_type'] == $this->post_type) {
            $qv = &$query->query_vars;
            if (!isset($qv['meta_query'])) {
                $qv['meta_query'] = [];
            }
            foreach ($this->filters as $filter) {
                if (!$filter['action']) {
                    if (isset($_GET[$filter['name']]) and $_GET[$filter['name']] != '') {
                        $qv['meta_query'][] = [
                            'key' => $filter['name'],
                            'value' => urldecode($_GET[$filter['name']])
                        ];
                    }
                } else {
                    foreach ($filter['action'] as $key => $action) {
                        if ($_GET[$filter['name']] == $key) {
                            $qv['meta_query'][] = $action;
                        }
                    }
                }
            }
        }
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
        if (function_exists('register_field_group')) {
            foreach ($this->fields_groups as $group) {
                register_field_group($group);
            }
        }
    }

    /**
     * Helper function to create ACF group
     * @param  string  $id       Id of the group
     * @param  string  $title    Title of the group
     * @param  boolean $taxonomy If the group is for a taxonomy
     * @param  array   $options  Custom options for the group
     */
    protected function group($id, $title, $taxonomy = false, $options = [])
    {
        $location = [[[
            'param' => 'post_type',
            'operator' => '==',
            'value' => $this->post_type,
            'order_no' => 0,
            'group_no' => 0,
        ]]];

        if ($taxonomy) {
            $location = [[[
                'param' => 'ef_taxonomy',
                'operator' => '==',
                'value' => $taxonomy,
                'order_no' => 0,
                'group_no' => 0,
            ]]];
        }

        // array merge
        if (empty($options)) {
            $options = [
                'position' => 'normal',
                'layout' => 'box',
                'hide_on_screen' => [],
            ];
        }

        $this->fields_groups[$id] = [
            'id' => $id,
            'title' => $title,
            'fields' => [],
            'location' => $location,
            'options' => $options,
            'menu_order' => 0,
        ];
    }

    /**
     * Helper function to add group for the custom post type
     * @param  strong $id      Id of the group
     * @param  string $title   Title of the group
     * @param  array  $options Custom options
     */
    protected function groupCpt($id, $title, $options = [])
    {
        $this->group($id, $title, false, $options);
    }

    /**
     * Helper function to add group for the custom taxonomies
     * @param  strong $id         Id of the group
     * @param  string $title      Title of the group
     * @param  string $taxonomy   Name of the taxonomy
     * @param  array  $options    Custom options
     */
    protected function groupTax($id, $title, $taxonomy, $options = [])
    {
        $this->group($id, $title, $taxonomy, $options);
    }

    /**
     * Add field to a group
     * @param  string $group Id of the group
     * @param  array $field  Arguments of the field
     */
    protected function field($group, $field)
    {
        if (!isset($field['key'])) {
            $field['key'] = $this->post_type . '_' . $group . '_' . $field['name'];
        }
        $this->fields_groups[$group]['fields'][] = $field;
    }

    /**
     * Add column for the custom post type
     * @param  string  $id     Id of the column
     * @param  string  $label  Label of the column
     * @param  boolean $sort   If the column is sortable
     * @param  boolean|string $type   Type of the column
     * @param  boolean $custom Custom value for the column
     */
    protected function column($id, $label, $sort = false, $type = false, $custom = false)
    {
        $this->columns[$id] = __($label);
        if ($sort) {
            $this->sortable_columns[$id] = $id;
        }
        if ($type) {
            $this->columns_show[] = compact('id', 'type', 'custom');
        }
    }

    /**
     * Add filter for the custom post type
     * @param  string  $name   Name of the filter
     * @param  array  $values Values of the filter
     * @param  array $action Custom action for the filter
     */
    protected function filter($name, $values, $action = false)
    {
        $this->filters[] = compact('name', 'values', 'action');
    }

    /**
     * Helper function to generate labels for a custom post type
     * @return array List of all labels
     */
    protected function generateLabels()
    {
        if (isset($this->cpt_args['labels']) && !empty($this->cpt_args['labels'])) {
            return $this->cpt_args['labels'];
        }

        if (!empty($this->labels)) {
            return $this->labels;
        }

        if (!empty($this->label)) {
            $label = $this->label;
        } else {
            $label = $this->post_type;
        }

        $singular = Inflect::singularize($label);
        $plural = Inflect::pluralize($label);
        $labels = [
            'add_new_item' => 'Ajouter ' . $singular,
            'all_items' => $plural,
            'name' => $plural,
            'archives' => 'Archives des ' . $plural,
            'attributes' => 'Attributs des ' . $plural,
            'edit_item' => 'Editer ' . $singular,
            'filter_items_list' => 'Filtrer la liste des ' . $plural,
            'insert_into_item' => 'Insérer dans ' . $singular,
            'items_list' => 'Liste des ' . $plural,
            'items_list_navigation' => 'Navigation de la liste des ' . $plural,
            'name_admin_bar' => $singular,
            'new_item' => '',
            'not_found' => $singular . ' introuvable',
            'not_found_in_trash' => $singular . ' introuvable dans la corbeille',
            'search_items' => 'Chercher ' . $plural,
            'singular_name' => $singular,
            'view_item' => 'Voir ' . $singular,
            'view_items' => 'Voir ' . $plural,
            'uploaded_to_this_item' => 'Uploader dans ' . $singular,
        ];
        return $labels;
    }
}
