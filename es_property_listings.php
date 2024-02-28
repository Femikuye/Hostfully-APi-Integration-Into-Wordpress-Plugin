<?php

/**
 * @package EsPropertyListings
 */
/*
 Plugin Name: Effective Stays Property Listings
 Plugin URI: https://phemrise.com
 Description: This plugin is designed to connect to the Hostfully API for property listings
 Author: Phemrise
 Version: 1.0.0
 Author URI: https://phemrise.com
 License: GPLv2 or later
 Text Domain: Effective Stays Property Listings
 */
/*

*/
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

defined('ABSPATH') or die("No direct access allowed");

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

//  Activation
function activate_es_property_listings()
{
    User\EsPropertyListings\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_es_property_listings');

// Deactivation
function deactivate_es_property_listings()
{
    User\EsPropertyListings\Base\Deactivate::deactivate();
}
register_activation_hook(__FILE__, 'deactivate_es_property_listings');

if (class_exists('User\\EsPropertyListings\\Init')) {
    User\EsPropertyListings\Init::register_services();
}
