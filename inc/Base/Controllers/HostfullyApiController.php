<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Base\Controllers;

use \User\EsPropertyListings\Base\BaseController;
use User\EsPropertyListings\Api\SettingsApi;
use User\EsPropertyListings\Api\Callbacks\DashboardCallback;
use User\EsPropertyListings\Api\CurlRequest;

class HostfullyApiController extends BaseController
{
    public $settings;
    public $pages;
    public $callback;
    public $apiRequest;
    public function register()
    {
        // $this->settings = new SettingsApi();
        // $this->callback = new DashboardCallback();
        $this->apiRequest = new CurlRequest();
        add_action('wp_ajax_espl_property_update', array($this, 'handle_espl_property_update'));
        add_action('wp_ajax_nopriv_espl_property_update', array($this, 'handle_espl_property_update'));
    }
    public function handle_espl_property_update()
    {
        // return wp_send_json_success('Request successful');
        // Check if nonce is set and valid
        $nonce = $_POST['nonce'];
        if (!wp_verify_nonce($nonce, 'espl_update_property_nonce')) {
            wp_send_json_error('Invalid Request');
        }
        $espl_option = get_option($this->settings_opt_name);
        if (!is_array($espl_option) || !isset($espl_option['api_key'])) {
            wp_send_json_error(["msg" => "Please set the Hostfully API Key before attempting update"]);
        }
        if (!is_array($espl_option) || !isset($espl_option['agency_uid'])) {
            wp_send_json_error(["msg" => "Please set the Hostfully AgencyUid before attempting update"]);
        }
        $next_page_key = null;
        global $wpdb;
        $property_cities = [];
        do {
            $data = $this->getProperties(20, $next_page_key);
            $data_rows[] = $data['msg'];
            if ($data['success']) {
                $my_data = $this->ToObject($data['data']);
                $rows = $my_data->properties;
                foreach ($rows as $row) {
                    $row = $this->ToObject($row);
                    if (!$row->externalId) {
                        // continue;
                    }
                    $table_name = $wpdb->prefix . $this->properties_tbl_name;
                    $property_uid = $row->uid;
                    if (!in_array($row->address->city, $property_cities)) {
                        $property_cities[] = $row->address->city;
                    }
                    $query = $wpdb->prepare("SELECT espl_post_id FROM $table_name WHERE espl_property_uid = %s", $property_uid);
                    // Execute the query
                    $queryresults = $wpdb->get_results($query);
                    // Check if any rows exist
                    if ($queryresults) {
                        $update_res = $this->updateProperty($queryresults[0]->espl_post_id, $row);
                        // $data_rows[] = $update_res;
                    } else {
                        $this->insertProperty($row);
                    }
                }
                $next_page_key = $my_data->_paging->_nextCursor;
            } else {
                $next_page_key = null;
            }
        } while ($next_page_key !== null);
        if (count($property_cities) > 0) {
            $espl_option['property_cities'] = $property_cities;
            update_option($this->settings_opt_name, $espl_option);
        }
        // Send response back to client
        wp_send_json_success(["msg" => "Update Successful!"]);
    }
    public function getProperties($max_result = 20, $next_page_key = null)
    {
        $espl_option = get_option($this->settings_opt_name);
        $params_query = "?agencyUid=" . $espl_option['agency_uid'] . "&_limit=" . $max_result;
        if (!is_null($next_page_key)) {
            $params_query .= "&_cursor=" . $next_page_key;
        }
        $url = $this->hostfully_api_url . "properties" . $params_query;
        $headers = array(
            'X-HOSTFULLY-APIKEY: ' . $espl_option['api_key'],
            'Accept: */*',
        );
        $dataArray = array(
            'url' => $url,
            'headers' => $headers
        );
        $res = $this->apiRequest->makeRequest($dataArray, "get", true);
        return $res;
    }
    public function getPropertyDescription($propert_uid)
    {
        $espl_option = get_option($this->settings_opt_name);
        if (!is_array($espl_option) || !isset($espl_option['api_key'])) {
            return ['success' => false, 'msg' => 'No API Key'];
        }
        $params_query = "?propertyUid=" . $propert_uid;

        $url = $this->hostfully_api_url . "property-descriptions" . $params_query;
        $headers = array(
            'X-HOSTFULLY-APIKEY: ' . $espl_option['api_key'],
            'Accept: */*',
        );
        $dataArray = array(
            'url' => $url,
            'headers' => $headers
        );
        $res = $this->apiRequest->makeRequest($dataArray, "get", true);
        return $res;
    }
    public function insertProperty($property_obj)
    {
        // Prepare post data
        $property_des = $this->getPropertyDescription($property_obj->uid);
        if (!$property_des['success']) {
            return false;
        }
        $property_des = $this->ToObject($property_des['data']);
        $title = $property_des->propertyDescriptions->{0}->name;
        $content = $property_des->propertyDescriptions->{0}->summary;
        $content .= $property_des->propertyDescriptions->{0}->space;
        $post_data = array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => $this->single_property_url_path,
        );
        // Insert the post into the database
        $post_id = wp_insert_post($post_data);
        if (!$post_id) {
            return false;
        }
        global $wpdb;

        // Property Detail table
        $table_name = $wpdb->prefix . $this->properties_tbl_name;
        if ($image_rows = $this->getPropertyImages($property_obj->uid, 1)) {
            $property_obj->pictureLink = $image_rows[0]["url"];
        }
        $tbl_data = $this->getPropertytableData($post_id, $property_obj);
        // Insert data into the custom table using prepared statements
        $wpdb->insert($table_name, $tbl_data['data_array'], $tbl_data['data_format']);

        // Check if the insert was successful
        if ($wpdb->last_error === '') {
            return true;
        } else {
            return $wpdb->last_error;
        }
    }
    public function updateProperty($post_id, $property_obj)
    {
        global $wpdb;
        $property_des = $this->getPropertyDescription($property_obj->uid);
        if (!$property_des['success']) {
            return false;
        }
        $property_des = $this->ToObject($property_des['data']);
        $title = $property_des->propertyDescriptions->{0}->name;
        $content = $property_des->propertyDescriptions->{0}->summary;
        $content .=  $property_des->propertyDescriptions->{0}->space;
        $post_data = array(
            'ID'           => $post_id,
            'post_title'    => $title,
            'post_content'  => $content,
        );
        // Update the post
        $updated_post_id = wp_update_post($post_data);
        // Your custom table name
        $table_name = $wpdb->prefix . $this->properties_tbl_name;
        if ($image_rows = $this->getPropertyImages($property_obj->uid, 1)) {
            $property_obj->pictureLink = $image_rows[0]["url"];
        }
        $tbl_data = $this->getPropertytableData($post_id, $property_obj);
        $where_clause = array(
            'espl_post_id' => $post_id,
        );
        // Format the where clause
        $where_formats = array(
            '%s', // for integer
        );

        // Update data in the custom table using prepared statements
        $wpdb->update($table_name, $tbl_data['data_array'], $where_clause, $tbl_data['data_format'], $where_formats);

        // Check if the update was successful
        if ($wpdb->last_error === '') {
            return true;
        } else {
            return $wpdb->last_error;
        }
    }
    public function getPropertytableData($post_id, $property_obj)
    {
        // Data to be inserted
        $data_to_insert = array(
            'espl_property_uid' => $property_obj->uid,
            'espl_property_id' => $property_obj->externalId,
            'espl_post_id' => $post_id,
            'espl_property_address' => $property_obj->address->address,
            'espl_property_state' => $property_obj->address->state,
            'espl_property_city' => $property_obj->address->city,
            'espl_property_image_link' => $property_obj->pictureLink,
            'espl_property_bedrooms' => $property_obj->bedrooms,
            'espl_property_bathrooms' => $property_obj->bathrooms,
            'espl_property_beds' => $property_obj->beds,
            'espl_property_max_guests' =>  $property_obj->availability->maxGuests,
            'espl_property_min_stay' =>  $property_obj->availability->minimumStay,
            'espl_property_max_stay' =>  $property_obj->availability->maximumStay,
            'espl_property_booking_above_max_stay_alowed' =>  $property_obj->availability->allowBookingRequestsAboveMaximumStay,
            'espl_property_daily_rate' => $property_obj->pricing->dailyRate,
        );
        // Format the data for the insert query
        $data_formats = array(
            '%s', // for espl_property_uid
            '%s', // for espl_property_id
            '%d', // for espl_post_id
            '%s', // for espl_property_address
            '%s', // for espl_property_state
            '%s', // for espl_property_city
            '%s', // for espl_property_image_link
            '%d', // for espl_property_bedrooms
            '%d', // for espl_property_bathrooms
            '%d', // for espl_property_beds
            '%d', // for espl_property_max_guests
            '%d', // for espl_property_min_stay
            '%d', // for espl_property_max_stay
            '%d', // for espl_property_booking_above_max_stay_alowed
            '%f', // for espl_property_daily_rate
        );
        return ['data_array' => $data_to_insert,  'data_format' => $data_formats];
    }
    public function getSinglePropert($slug)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->properties_tbl_name;
        $query = $wpdb->prepare("SELECT * FROM wp_posts JOIN $table_name ON wp_posts.ID = {$table_name}.espl_post_id WHERE 
        post_name = %s", $slug);
        // Execute the query
        $queryresults = $wpdb->get_results($query);
        // Check if any rows exist
        return $queryresults;
    }
    public function getPropertyImages($property_uid, $max_images = 6)
    {
        $espl_option = get_option($this->settings_opt_name);
        if (!is_array($espl_option) || !isset($espl_option['api_key'])) {
            return ['success' => false, 'msg' => 'No API Key'];
        }
        $params_query = "?propertyUid=" . $property_uid;

        $url = $this->hostfully_api_url . "photos" . $params_query;
        $headers = array(
            'X-HOSTFULLY-APIKEY: ' . $espl_option['api_key'],
            'Accept: */*',
        );
        $dataArray = array(
            'url' => $url,
            'headers' => $headers
        );
        $this->apiRequest = new CurlRequest();
        $res = $this->apiRequest->makeRequest($dataArray, "get", true);
        $images = [];
        if ($res['success']) {
            $image_rows = $this->ToObject($res['data']['photos']);
            foreach ($image_rows as $image_row) {
                if (count($images) >= $max_images) {
                    return $images;
                }
                $images[] = ["url" => $image_row->largeScaleImageUrl, "des" => $image_row->description];
            }
            return $images;
        }
        return false;
    }
    public function getPropertyAmenities($property_uid)
    {
        $espl_option = get_option($this->settings_opt_name);
        if (!is_array($espl_option) || !isset($espl_option['api_key'])) {
            return false;
        }
        $params_query = "?propertyUid=" . $property_uid;

        $url = $this->hostfully_api_url . "amenities" . $params_query;
        $headers = array(
            'X-HOSTFULLY-APIKEY: ' . $espl_option['api_key'],
            'Accept: */*',
        );
        $dataArray = array(
            'url' => $url,
            'headers' => $headers
        );
        $this->apiRequest = new CurlRequest();
        $res = $this->apiRequest->makeRequest($dataArray, "get", true);
        if ($res['success']) {
            $amenities = $this->ToObject($res['data']['amenities']);
            return $amenities;
        }
        return false;
    }
    public function getPropertyRules($property_uid)
    {
        $espl_option = get_option($this->settings_opt_name);
        if (!is_array($espl_option) || !isset($espl_option['api_key'])) {
            return false;
        }
        $params_query = "?propertyUid=" . $property_uid;

        $url = $this->hostfully_api_url . "property-rules" . $params_query;
        $headers = array(
            'X-HOSTFULLY-APIKEY: ' . $espl_option['api_key'],
            'Accept: */*',
        );
        $dataArray = array(
            'url' => $url,
            'headers' => $headers
        );
        $this->apiRequest = new CurlRequest();
        $res = $this->apiRequest->makeRequest($dataArray, "get", true);
        if ($res['success']) {
            // $rules = $this->ToObject($res['data']['propertyRules']);
            if ($res['data']['_metadata']['count'] > 0) {
                return $this->ToObject($res['data']['propertyRules']);;
            }
        }
        return false;
    }
}
