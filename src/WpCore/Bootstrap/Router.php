<?php

namespace WpCore\Bootstrap;
use Illuminate\Support\Str;

/**
 * Router class for WP Core boilerplate.
 * @package WpCore\Bootstrap
 */

class Router
{
    /**
     * Creates a list populated by the callback's controller and method.
     * Calls the function in the list passed as a parameter.
     *
     * @param string|array $callback
     *
     * @return void
     */
    protected static function call_function($callback): void
    {
        if (!is_array($callback)) {
            list($controller, $method) = Str::parseCallback($callback);
            $callback = [new $controller, $method];
        }

        call_user_func($callback);
        exit();
    }

    /**
     * Determines whether the query has resulted in a 404 (returns no results).
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_404($method): void
    {
        if (function_exists('is_404') && is_404()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for the blog homepage.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_home($method): void
    {
        if (function_exists('is_home') && is_home()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for the front page of the site.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_front_page($method): void
    {
        if (function_exists('is_front_page') && is_front_page()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for the Privacy Policy page.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_privacy_policy($method): void
    {
        if (function_exists('is_privacy_policy') && is_privacy_policy()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for a search.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_search($method): void
    {
        if (function_exists('is_search') && is_search()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing attachment page.
     *
     * @param string|array              $method
     * @param int|string|int[]|string[] $attachment
     *
     * @return void
     */
    public static function is_attachment($method, $attachment = ''): void
    {
        if (function_exists('is_attachment') && is_attachment($attachment)) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing single page.
     *
     * @param string|array              $method
     * @param int|string|int[]|string[] $page
     *
     * @return void
     */
    public static function is_page($method, $page = ''): void
    {
        if (function_exists('is_page') && is_page($page)) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing archive page.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_archive($method): void
    {
        if (function_exists('is_archive') && is_archive()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing date archive.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_date($method): void
    {
        if (function_exists('is_date') && is_date()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing year archive.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_year($method): void
    {
        if (function_exists('is_year') && is_year()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing month archive.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_month($method): void
    {
        if (function_exists('is_month') && is_month()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing day archive.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_day($method): void
    {
        if (function_exists('is_day') && is_day()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for a specific time.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function is_time($method): void
    {
        if (function_exists('is_time') && is_time()) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing author archive page.
     *
     * @param string|array              $method
     * @param int|string|int[]|string[] $author
     *
     * @return void
     */
    public static function is_author($method, $author = ''): void
    {
        if(function_exists('is_author') && is_author($author)) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing single post of any post type (post, attachment, page, custom post types).
     *
     * @param string|array    $method
     * @param string|string[] $post_type
     *
     * @return void
     */
    public static function is_singular($method, $post_type = ''): void
    {
        if(function_exists('is_singular') && is_singular($post_type)) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing single post.
     *
     * @param string|array              $method
     * @param int|string|int[]|string[] $post
     *
     * @return void
     */
    public static function is_single($method, $post = ''): void
    {
        if(function_exists('is_single') && is_single($post)) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing post type archive page.
     *
     * @param string|array    $method
     * @param string|string[] $post_type
     *
     * @return void
     */
    public static function is_post_type_archive($method, $post_type = ''): void
    {
        if(function_exists('is_post_type_archive') && is_post_type_archive($post_type)) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing custom taxonomy archive page.
     *
     * @param string|array              $method
     * @param string|string[]           $taxonomy
     * @param int|string|int[]|string[] $term
     *
     * @return void
     */
    public static function is_tax($method, $taxonomy = '', $term = ''): void
    {
        if(function_exists('is_tax') && is_tax($taxonomy, $term)) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing category archive page.
     *
     * @param string|array              $method
     * @param int|string|int[]|string[] $category
     *
     * @return void
     */
    public static function is_category($method, $category = ''): void
    {
        if(function_exists('is_category') && is_category($category)) {
            self::call_function($method);
        }
    }

    /**
     * Determines whether the query is for an existing tag archive page.
     *
     * @param string|array              $method
     * @param int|string|int[]|string[] $tag
     *
     * @return void
     */
    public static function is_tag($method, $tag = ''): void
    {
        if(function_exists('is_tag') && is_tag($tag)) {
            self::call_function($method);
        }
    }


    /**
     * Default route.
     *
     * @param string|array $method
     *
     * @return void
     */
    public static function default($method): void
    {
        self::call_function($method);
    }
}