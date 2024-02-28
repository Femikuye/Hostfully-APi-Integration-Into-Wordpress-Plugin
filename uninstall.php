<?php

/**
 * Trigger this file on Plugin uninstall
 * @package EsPropertyListings
 */

defined('WP_UNINSTALL_PLUGIN') or die;

// use User\EsPropertyListings\Api\TableApi;
// Clear Database stored data
// $books = get_posts(array('post_type' => 'book', 'numberposts' => -1));
// foreach ($books as $book) {
//     wp_delete_post($book->ID, true);
// }
// $table = new TableApi();
// $table->dropTables();
delete_option("espl_settings");
// Access the database via SQL
global $wpdb;
$table_name =  $wpdb->prefix . "espl_property_details";
$sql = "DROP TABLE $table_name";
$wpdb->query($sql);


$post_type_name = "property_listings";
$wpdb->query("DELETE FROM wp_posts WHERE post_type = '$post_type_name' ");
$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN(SELECT id FROM wp_posts)");
$wpdb->query("DELETE FROM wp_term_relationships WHERE object_id NOT IN(SELECT id FROM wp_posts)");
