<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Pages;

use User\EsPropertyListings\Base\BaseController;

class Frontend extends BaseController
{
    public function register()
    {
        // Add custom rewrite rules
        // add_action('init', array($this, 'single_property_url_rewrite_rules'));

        // Register query variable
        // add_filter('query_vars', array($this, 'single_property_register_query_var'));

        // Load custom template for the custom property page
        // add_filter('template_include', array($this, 'single_property_template_include'));

        add_filter('single_template', array($this, 'my_custom_template'), 999);
    }
    public function single_property_url_rewrite_rules()
    {
        add_rewrite_rule('^mytest/([^/]+)/?', 'index.php?mytest=$matches[1]', 'top');
    }

    public function single_property_register_query_var($vars)
    {
        $vars[] = 'mytest';
        return $vars;
    }

    public function single_property_template_include($template)
    {
        if (get_query_var('mytest')) {
            return "$this->plugin_path/templates/front/single-property-template.php";
        }
        return $template;
    }



    public function my_custom_template($single)
    {

        global $post;

        /* Checks for single template by post type */
        if ($post->post_type == $this->single_property_url_path) {
            if (file_exists("$this->plugin_path/templates/front/single-property-template.php")) {
                return "$this->plugin_path/templates/front/single-property-template.php";
            }
        }

        return $single;
    }
}
