<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Api;

use User\EsPropertyListings\Base\BaseController;

class TableApi extends BaseController
{

    public function createTables()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->properties_tbl_name;
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            espl_property_uid VARCHAR(60) NOT NULL,
            espl_property_id VARCHAR(30),
            espl_post_id INT(9),
            espl_property_address VARCHAR(70) NOT NULL,
            espl_property_state VARCHAR(25) NOT NULL,
            espl_property_city VARCHAR(25) NOT NULL,
            espl_property_image_link VARCHAR(225) NOT NULL,
            espl_property_bedrooms INT(2) DEFAULT 0,
            espl_property_bathrooms INT(2) DEFAULT 0,
            espl_property_beds INT(2) DEFAULT 0,
            espl_property_max_guests INT(2) DEFAULT 0,
            espl_property_min_stay INT(2) DEFAULT 0,
            espl_property_max_stay INT(2) DEFAULT 0,
            espl_property_booking_above_max_stay_alowed BOOLEAN DEFAULT true,
            espl_property_daily_rate FLOAT(6) DEFAULT 0
            )";
        $wpdb->query($sql);
        /**
         * espl_property_id = Hostfully item "externalId"
         * espl_property_uid = Hostfully item "uid"
         * espl_property_address = Hostfully item "address->address"
         * espl_property_city = Hostfully item "address->city"
         * espl_property_state = Hostfully item "address->state"
         * espl_property_image_link = Hostfully item "pictureLink"
         * espl_property_bedrooms = Hostfully item "bedrooms"
         * espl_property_bathrooms = Hostfully item "bathrooms"
         * espl_property_beds = Hostfully item "beds"
         * espl_property_max_guests = Hostfully item "availability->maxGuests"
         * espl_property_min_stay = Hostfully item "availability->minimumStay"
         * espl_property_max_stay = Hostfully item "availability->maximumStay"
         * espl_property_booking_above_max_stay_alowed = Hostfully item "availability->allowBookingRequestsAboveMaximumStay"
         * espl_property_daily_rate = Hostfully item "pricing->dailyRate"
         */
    }
    public function dropTables()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->properties_tbl_name;
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name}";
        $wpdb->query($sql);
    }
    public function searchProperties(array $params = [], $limit = null, $start = null)
    {
        global $wpdb;
        $query_values = [];
        $table_name = $wpdb->prefix . $this->properties_tbl_name;
        $sql = "SELECT * FROM $table_name JOIN wp_posts ON wp_posts.ID = {$table_name}.espl_post_id";
        $extended_sql = "";
        if (isset($params['city'])) {
            $extended_sql .= " {$table_name}.espl_property_city = %s";
            $query_values[] = $params['city'];
            unset($params['city']);
            if (count($params) > 0) {
                $extended_sql .= " AND";
            }
        }
        if (isset($params['price'])) {
            $extended_sql .= " {$table_name}.espl_property_daily_rate >= %d";
            $query_values[] = $params['price'];
            unset($params['price']);
            if (count($params) > 0) {
                $extended_sql .= " AND";
            }
        }
        if (isset($params['guests'])) {
            $extended_sql .= " {$table_name}.espl_property_max_guests >= %d";
            $query_values[] = $params['guests'];
            unset($params['guests']);
            if (count($params) > 0) {
                $extended_sql .= " AND";
            }
        }
        if (isset($params['bedrooms'])) {
            $extended_sql .= " {$table_name}.espl_property_bedrooms <= %d";
            $query_values[] = $params['bedrooms'];
            unset($params['bedrooms']);
            if (count($params) > 0) {
                $extended_sql .= " AND";
            }
        }
        if ($extended_sql !== "") {
            $extended_sql = " WHERE" . $extended_sql;
        }
        if ($limit  &&  $start) {
            $extended_sql .= " LIMIT %d , %d";
            $query_values[] = $start;
            $query_values[] = $limit;
        }
        $sql .= $extended_sql;
        $query = $wpdb->prepare($sql, $query_values);
        $rows = $wpdb->get_results($query);
        if ($wpdb->last_error === '') {
            return $rows;
        } else {
            return false;
        }
    }
    public function getPropertyExternalId($post_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->properties_tbl_name;
        $sql = "SELECT espl_property_id FROM $table_name WHERE espl_post_id = %s";
        $query_values = [$post_id];
        $query = $wpdb->prepare($sql, $query_values);
        $rows = $wpdb->get_results($query);
        if ($wpdb->last_error === '') {
            return $rows[0]->espl_property_id;
        } else {
            return false;
        }
    }
}
