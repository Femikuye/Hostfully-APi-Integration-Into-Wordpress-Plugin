<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Base;

use \User\EsPropertyListings\Base\BaseController;

class Enqueue extends BaseController
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));
        add_action('wp_enqueue_scripts', array($this, 'ui_enqueue'));
    }
    public function admin_enqueue()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_media();
        wp_enqueue_style('mypluginstyle', $this->plugin_url . 'assets/admin/styles.css');
        wp_enqueue_script('mypluginscript', $this->plugin_url . 'assets/admin/script.js');
    }
    function ui_enqueue()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_media();
        wp_enqueue_style('mypluginstyle', $this->plugin_url . 'assets/front/styles.css');
        wp_enqueue_script('mypluginuiscript', $this->plugin_url . 'assets/front/script.js', array('jquery'), '1.0', true);

        if (is_singular() && has_shortcode(get_post()->post_content, $this->property_list_shortcode)) {
            wp_localize_script('mypluginuiscript', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
            wp_enqueue_script('moment-js', $this->plugin_url . 'assets/front/moment.min.js');
            wp_enqueue_script('date-picker-js', $this->plugin_url . 'assets/front/daterangepicker.min.js');
            wp_enqueue_style('date-picker-css', $this->plugin_url . 'assets/front/daterangepicker.css');
        }
    }
}
