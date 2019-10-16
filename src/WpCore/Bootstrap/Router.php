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
     * @param $method
     */
    protected static function call_function($callback) {
        list($controller, $method) = Str::parseCallback($callback);
        call_user_func([new $controller, $method]);
        exit();
    }

    /**
     * Checks if the query has no results (404).
     * @param $method
     */
    public static function is_404($method) {
        if (function_exists("is_404") && is_404()) {
            self::call_function($method);
        }
    }

    /**
     * Checks if you are on the BLOG homepage, if you set a static page as the front page this conditional tag
     * will return true only if you are on the page you set as the "Posts page".
     * @param $method
     */
    public static function is_home($method) {
        if (function_exists("is_home") && is_home()) {
            self::call_function($method);
        }
    }

    /**
     * Checks if you are on the SITE front page, if you set a static page as the front page this conditional tag
     * returns true if you are the static page set as "Front page display".
     * @param $method
     */
    public static function is_front_page($method) {
        if (function_exists("is_front_page") && is_front_page()) {
            self::call_function($method);
        }
    }

    /**
     * Checks if the query is for an existing single PAGE.
     * @param $page
     * @param $method
     */
    public static function is_page($method, $page = '') {
        if (function_exists("is_page") && is_page($page)) {
            self::call_function($method);
        }
    }

    /**
     * Checks if the query is for an existing single POST of any of the given types.
     * @param $method
     * @param string $post_type
     */
    public static function is_singular($method, $post_type = '') {
        if(function_exists("is_singular") && is_singular($post_type)) {
            self::call_function($method);
        }
    }

    /**
     * Checks if the query is for an existing single POST.
     * @param $method
     * @param string $post
     */
    public static function is_single($method, $post = '') {
        if(function_exists("is_single") && is_single($post)) {
            self::call_function($method);
        }
    }

    /**
     * Checks if the query is for an existing post type archive page.
     * @param $method
     * @param string $post_type
     */
    public static function is_post_type_archive($method, $post_type = '') {
        if(function_exists("is_post_type_archive") && is_post_type_archive($post_type)) {
            self::call_function($method);
        }
    }

    /**
     * Checks whether the query is for an existing category archive page.
     * @param $method
     * @param $category
     */
    public static function is_category($method, $category) {
        if(function_exists("is_category") && is_category($category)) {
            self::call_function($method);
        }
    }


    /**
     * Default route.
     * @param $method
     */
    public static function default($method) {
        self::call_function($method);
    }

}