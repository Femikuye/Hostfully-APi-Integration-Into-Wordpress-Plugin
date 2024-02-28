<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Base\Controllers;

use \User\EsPropertyListings\Base\BaseController;
use User\EsPropertyListings\Api\TableApi;
use User\EsPropertyListings\Api\CurlRequest;

class PropertyPostTypeController extends BaseController
{
    public $apiRequest;
    public function register()
    {
        $this->apiRequest = new CurlRequest();
        add_action("init", array($this, 'registerPropertyPostType'));
        add_action('add_meta_boxes', array($this, 'add_custom_property_fields_meta_box'));
        add_action('save_post', array($this, 'save_custom_property_fields'));
        add_shortcode($this->property_list_shortcode, array($this, 'esplPropertiesListings'));
        add_action('wp_ajax_espl_property_search', array($this, 'handle_espl_property_search'));
        add_action('wp_ajax_nopriv_espl_property_search', array($this, 'handle_espl_property_search'));
    }

    public function registerPropertyPostType()
    {
        $post = array(
            'single_name' => 'Esproperty',
            'name' => 'Esproperties',
            'public_status' => true,
            'has_archive' => true,
        );
        register_post_type(
            $this->single_property_url_path,
            array(
                'labels' => array(
                    'name' =>  $post['name'],
                    'singular_name' =>  $post['single_name']
                ),
                'public' =>  $post['public_status'],
                'has_archive' =>  $post['has_archive'],
                // 'rewrite' => array('slug' => $this->single_property_url_path),
                'menu_name'             => $post['single_name'] . ' Menu',
                'name_admin_bar'        => $post['single_name'] . ' Admin',
                'archives'              => $post['single_name'] . ' Archives',
                'attributes'            => $post['single_name'] . ' Attributes',
                'parent_item_colon'     => 'Parent ' . $post['single_name'],
                'all_items'             => 'All ' . $post['single_name'],
                'add_new_item'          => 'Add New ' . $post['single_name'],
                'add_new'               => 'Add New',
                'label'                 => $post['single_name'],
                'description'           => $post['single_name'],
                'supports'              => array('title', 'editor', 'custom-fields'), //
                'taxonomies'            => array('category', 'tags'),
                'hierarchical'          => false,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 5,
                'show_in_admin_bar'     => true,
                'can_export'            => true,
                'publicly_queryable'    => true,
                'capability_type'       => 'page'
            )
        );
    }
    function add_custom_property_fields_meta_box()
    {
        add_meta_box(
            'custom_property_fields_meta_box',
            'Property Details',
            array($this, 'render_custom_property_fields_meta_box'),
            $this->single_property_url_path, //'property',
            'normal',
            'high'
        );
    }

    function render_custom_property_fields_meta_box($post)
    {
        // Add your custom fields here
        $table = new TableApi();
        if ($externalId = $table->getPropertyExternalId($post->ID)) {
            echo '<p>Property External ID: ' . $externalId . '</p>';
        }
        echo 'Property Reviews ID: <input type="text" name="property_review_id" value="' . get_post_meta($post->ID, 'property_review_id', true) . '"><br>';
        echo 'Property Map ID: <input type="text" name="property_map_id" value="' . get_post_meta($post->ID, 'property_map_id', true) . '"><br>';
    }

    function save_custom_property_fields($post_id)
    {
        if (array_key_exists('property_review_id', $_POST)) {
            update_post_meta(
                $post_id,
                'property_review_id',
                sanitize_text_field($_POST['property_review_id'])
            );
        }
        if (array_key_exists('property_map_id', $_POST)) {
            update_post_meta(
                $post_id,
                'property_map_id',
                sanitize_text_field($_POST['property_map_id'])
            );
        }
    }

    public function esplPropertiesListings($atts)
    {
        ob_start();

        // Define shortcode attributes and default values
        $atts = shortcode_atts(
            array(
                'rows_per_page' => 3,
            ),
            $atts,
            $this->property_list_shortcode
        );
        $limit = isset($atts['rows_per_page']) ? $atts['rows_per_page'] : 24;
        $nonce = wp_create_nonce('espl_search_property_nonce');
        $table = new TableApi();
        // Execute the query
        $properties_result = $table->searchProperties();
        $espl_option = get_option($this->settings_opt_name);
        if (is_array($espl_option) && isset($espl_option['property_cities'])) {
            $cities_array = $espl_option['property_cities'];
        } else {
            $cities_array = array(
                'Chester', 'Crewe', 'New Quay', 'Shrewsbury', 'Shropshire', 'Stafford',
                'Uttoxeter', 'Wrexham'
            );
        }
        $cities_options = '';
        foreach ($cities_array as $city) {
            $cities_options .= '<option value="' . $city . '">' . $city . '</option>';
        }
        $html_content = "
        <style>
        body {
            background-color: #f2f2f2 !important;
        }
        .qodef-widget-holder {
            display: none !important;
        }
    
        #qodef-page-inner {
            padding: 20px 0 20px !important;
        }
    </style>
        ";

        $search_wrapper = '
            <!--<form id="espl-property-search-form">-->
            <div class="espl-property-search-form">
                <select class="espl-property-city-input espl-form-input-style espl-form-input-control" name="espl-search-input-city">
                    <option value="">All Cities</option>
                    ' . $cities_options . '
                </select>
                <input type="hidden" value="' . $nonce . '" class="espl-property-nonce">
                <input type="text" name="espl-search-input-dates" placeholder="Dates" class="espl-form-input-style espl-form-input-control">
                <div class="espl-form-input-style espl-form-input-control espl-guests-counter-container">
                    <div class="espl-guest-count-input-text">Guests</div>
                    <div class="espl-guest-count-input-container">
                        <button type="button" class="espl-input-decrement"><i class="fa fa-solid fa-minus"></i></button>
                        <p class="espl-guests-counter-text">1</p>
                        <input style="display:none;" type="number" name="espl-search-input-guests" value="1" class="espl-input-guests-count" min="1" max="18">
                        <button type="button" class="espl-input-increment"><i class="fa fa-solid fa-plus"></i></button>
                    </div>
                </div>
                <button type="submit" class="espl-search-property-button">Search</button>
            </div>
            <div class="espl-loading-effect-wrapper"></div>
            <!--</form>-->
        ';
        $properties_items_html = '';
        if ($properties_result) {
            foreach ($properties_result as $row) {
                $url_path = home_url() . '/' . $this->single_property_url_path . '/' . $row->post_name;
                $properties_items_html .= '
                    <div class="espl-property-single-item">
                        <div class="espl-property-list-item-image">
                            <a href="' . $url_path . '"> 
                                <img alt="' . esc_html($row->post_title) . '" src="' . $row->espl_property_image_link . '">
                            </a>
                        </div>
                        <div class="espl-property-single-item-info">
                            <div class="espl-property-title">
                                <h5 class="espl-list-item-title-text">' . esc_html($row->post_title) . '</h5>
                                <span></span>
                            </div>
                            <div class="espl-property-title">
                            <p class="espl-list-item-address-text"> ' . $row->espl_property_city . '</p>
                            <span></span>
                            </div>
                            <div class="espl-property-list-item-features">
                                <div class="espl-item-feature-icon-wrapper">
                                    <span class="espl-property-item-icon-size">
                                        <i aria-hidden="true" class="fas fa-people-arrows"></i>
                                    </span> ' . $row->espl_property_max_guests . ' Max Guest' . ($row->espl_property_max_guests > 1 ? 's' : '') . '
                                </div>
                                <div class="espl-item-feature-icon-wrapper">
                                    <span class="espl-property-item-icon-size">
                                        <i aria-hidden="true" class="fas fa-shower"></i> 
                                    </span> ' . $row->espl_property_bathrooms . ' Bathroom' . ($row->espl_property_bathrooms > 1 ? 's' : '') . '
                                </div>
                                <div class="espl-item-feature-icon-wrapper">
                                    <span class="espl-property-item-icon-size">
                                        <i aria-hidden="true" class="fas fa-bed"></i>
                                    </span>' . $row->espl_property_bedrooms . ' Bedroom' . ($row->espl_property_bedrooms > 1 ? 's' : '') . '
                                </div>
                            </div>
                            <div class="espl-property-price">
                                <span class="espl-text-uppercase ">From</span>
                                <h3 class="espl-list-item-price-text ">£' . $row->espl_property_daily_rate . '</h3>
                                <span class="espl-text-uppercase ">Per Night</span>
                                <p class="espl-property-result-date"></p>
                            </div>
                            <a class="espl-search-property-button espl-text-uppercase" href="' . $url_path . '"> More Details</a>
                        </div>
                    </div>
                ';
            }
        } else {
            $properties_items_html = '<h3> No Property To Display</h3>';
        }
        $list_wrapper = '
            <div class="espl-property-list-main-wrapper">
                <div class="espl-property-items-wrapper">
                    ' . $properties_items_html . '
                </div>
            </div>
        ';

        $html_content .= $search_wrapper;
        $html_content .= $list_wrapper;
        return $html_content;

        return ob_get_clean();
    }
    public function handle_espl_property_search()
    {
        // return wp_send_json_success('Request successful');
        // Check if nonce is set and valid
        $nonce = $_POST['nonce'];
        if (!wp_verify_nonce($nonce, 'espl_search_property_nonce')) {
            wp_send_json_error('Invalid Request');
        }
        $espl_option = get_option($this->settings_opt_name);
        if (!is_array($espl_option) || !isset($espl_option['api_key'])) {
            wp_send_json_error(["msg" => "Please set the Hostfully API Key before attempting update"]);
        }
        if (!is_array($espl_option) || !isset($espl_option['agency_uid'])) {
            wp_send_json_error(["msg" => "Please set the Hostfully AgencyUid before attempting update"]);
        }
        $table = new TableApi();
        // city price guests bedrooms limit start
        $query_params = [];
        if (isset($_POST['city']) && !empty($_POST['city'])) {
            $query_params['city'] = $_POST['city'];
        }
        if (isset($_POST['price']) && $_POST['price'] > 0) {
            $query_params['price'] = $_POST['price'];
        }
        if (isset($_POST['guests']) && $_POST['guests'] > 0) {
            $query_params['guests'] = $_POST['guests'];
        }
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 0;
        $single_page_url = home_url() . '/' . $this->single_property_url_path . '/';
        if ($properties_result = $table->searchProperties($query_params)) {
            if ((isset($_POST['startDate']) && !empty($_POST['startDate'])) && (isset($_POST['endDate']) && !empty($_POST['endDate']))) {
                $available_properties = [];
                foreach ($properties_result as $row) {
                    if ($this->isPropertyAvailable($row->espl_property_uid, $_POST['startDate'], $_POST['endDate'])) {
                        $available_properties[] = $row;
                    }
                }
                return wp_send_json_success(["rows" =>  $available_properties, "page" => $page, "url" => $single_page_url]);
            }
            return wp_send_json_success(["rows" => $properties_result, "page" => $page, "url" => $single_page_url]);
        }
        return  wp_send_json_error('No Properties To Display');
    }
    public function isPropertyAvailable($propert_uid, $start_date, $end_date)
    {
        // $params_query = "?propertiesUids=" . $propert_uids . "&from=" . $start_date . "&to=" . $end_date;
        $params_query = "?from=" . $start_date . "&to=" . $end_date;
        $espl_option = get_option($this->settings_opt_name);
        $url = $this->hostfully_api_url . "property-calendar/" . $propert_uid . $params_query;
        $headers = array(
            'X-HOSTFULLY-APIKEY: ' . $espl_option['api_key'],
            'Accept: */*',
        );
        $dataArray = array(
            'url' => $url,
            'headers' => $headers
        );
        $res = $this->apiRequest->makeRequest($dataArray, "get", true);
        if (!$res['success']) {
            return false;
        }
        $returned_data = $res['data'];
        $returned_data = $this->ToObject($returned_data);
        foreach ($returned_data->calendar->entries as $row) {
            if ($row->availability->availableForCheckIn === false) {
                return false;
            }
        }
        return true;
        return $res;
    }
}
