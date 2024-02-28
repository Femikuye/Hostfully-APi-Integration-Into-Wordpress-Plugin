<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Base;

class BaseController
{
    public $settings_opt_name;
    public $plugin_path;
    public $plugin_name;
    public $plugin_url;
    public $plugin_slug;
    public $single_property_url_path;
    public $properties_tbl_name;
    public $property_list_shortcode;
    public $hostfully_api_url;
    function __construct()
    {
        $this->settings_opt_name = "espl_settings";
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_name =  plugin_basename(dirname(__FILE__, 3)) . '/es_property_listings.php';
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin_slug = "es_property_listings";
        $this->single_property_url_path = "property_listings";
        $this->properties_tbl_name = "espl_property_details";
        $this->property_list_shortcode = "espl_property_list";
        $this->hostfully_api_url = "https://api.hostfully.com/api/v3/";
    }
    public function ToObject($Array)
    {
        // Create new stdClass object
        $object = new \stdClass();

        // Use loop to convert array into
        // stdClass object
        foreach ($Array as $key => $value) {
            if (is_array($value)) {
                $value = $this->ToObject($value);
            }
            $object->$key = $value;
        }
        return $object;
    }
    public function trimAmenityText($amenity_text)
    {
        $text_arr = explode("_", $amenity_text);
        if ($text_arr[0] === "HAS") {
            unset($text_arr[0]);
        }
        return implode(" ", $text_arr);
    }
    public function getPolicies($post_id)
    {
        $custom_fields = get_post_custom($post_id);
        $policies = [];
        foreach ($custom_fields as $key => $value) {
            if (strpos($key, "rule_") !== false) {
                $policies[] = $value[0];
            }
        }
        if (count($policies) > 0) {
            return $policies;
        }
        return false;
    }
    public function getSingleAmenityIcon($key)
    {
        $amenities = [
            "HAS_TV" => [
                "icon" => '<i class="fa-solid fa-tv"></i>'
            ],
            "HAS_CABLE_TV" => [
                "icon" => '<i class="fa-solid fa-tv"></i>'
            ],
            "HAS_SMART_TV" => [
                "icon" => '<i class="fa-solid fa-tv"></i>'
            ],
            "HAS_AIR_CONDITIONING" => [
                "icon" => '<i class="fa-solid fa-fan"></i>'
            ],
            "HAS_HEATING" => [
                "icon" => '<i class="fa-solid fa-temperature-arrow-down"></i>'
            ],
            "HAS_KITCHEN" => [
                "icon" => '<i class="fa-solid fa-kitchen-set"></i>'
            ],
            "HAS_KITCHENETTE" => [
                "icon" => '<i class="fa-solid fa-kitchen-set"></i>'
            ],
            "HAS_INTERNET_WIFI" => [
                "icon" => '<i class="fa-solid fa-wifi"></i>'
            ],
            "HAS_PAID_WIFI" => [
                "icon" => '<i class="fa-solid fa-wifi"></i>'
            ],
            "HAS_WIFI_SPEED_25" => [
                "icon" => '<i class="fa-solid fa-wifi"></i>'
            ],
            "HAS_WIFI_SPEED_50" => [
                "icon" => '<i class="fa-solid fa-wifi"></i>'
            ],
            "HAS_WIFI_SPEED_100" => [
                "icon" => '<i class="fa-solid fa-wifi"></i>'
            ],
            "HAS_WIFI_SPEED_250" => [
                "icon" => '<i class="fa-solid fa-wifi"></i>'
            ],
            "HAS_WIFI_SPEED_500" => [
                "icon" => '<i class="fa-solid fa-wifi"></i>'
            ],
            "HAS_WASHER" => [
                "icon" => '<i class="fa-thin fa-washing-machine"></i>'
            ],
            "HAS_DRYER" => [
                "icon" => '<i class="fa-sharp fa-thin fa-dryer"></i>'
            ],
            "HAS_SHARED_WASHER" => [
                "icon" => '<i class="fa-sharp fa-regular fa-washing-machine"></i>'
            ],
            "HAS_SHARED_DRYER" => [
                "icon" => '<i class="fa-regular fa-dryer"></i>'
            ],
            "HAS_POOL" => [
                "icon" => '<i class="fa-solid fa-person-swimming"></i>'
            ],
            "HAS_POOL_ALL_YEAR" => [
                "icon" => '<i class="fa-solid fa-person-swimming"></i>'
            ],
            "HAS_POOL_SEASONAL" => [
                "icon" => '<i class="fa-solid fa-person-swimming"></i>'
            ],
            "HAS_COMMUNAL_POOL" => [
                "icon" => '<i class="fa-solid fa-person-swimming"></i>'
            ],
            "HAS_HEATED_POOL" => [
                "icon" => '<i class="fa-solid fa-person-swimming"></i>'
            ],
            "HAS_INDOOR_POOL_ALL_YEAR" => [
                "icon" => '<i class="fa-solid fa-person-swimming"></i>'
            ],
            "HAS_INDOOR_POOL_SEASONAL" => [
                "icon" => '<i class="fa-solid fa-person-swimming"></i>'
            ],
            "HAS_FENCED_YARD" => [
                "icon" => '<i class="fa-solid fa-campground"></i>'
            ],
            "HAS_HOT_TUB" => [
                "icon" => '<i class="fa-solid fa-bath"></i>'
            ],
            "HAS_FREE_PARKING" => [
                "icon" => '<i class="fa-solid fa-square-parking"></i>'
            ],
            "HAS_FREE_STREET_PARKING" => [
                "icon" => '<i class="fa-solid fa-square-parking"></i>'
            ],
            "HAS_PAID_OFF_PREMISES_PARKING" => [
                "icon" => '<i class="fa-solid fa-square-parking"></i>'
            ],
            "HAS_PAID_ON_PREMISES_PARKING" => [
                "icon" => '<i class="fa-solid fa-square-parking"></i>'
            ],
            "HAS_EV_CAR_CHARGER" => [
                "icon" => '<i class="fa-solid fa-plug"></i>'
            ],
            "HAS_INDOOR_FIREPLACE" => [
                "icon" => '<i class="fa-solid fa-fire-burner"></i>'
            ],
            "HAS_SMOKE_DETECTOR" => [
                "icon" => '<i class="fa-solid fa-smoking"></i>'
            ],
            "HAS_FIRE_EXTINGUISHER" => [
                "icon" => '<i class="fa-solid fa-fire-extinguisher"></i>'
            ],
            "HAS_EMERGENCY_EXIT" => [
                "icon" => '<i class="fa-solid fa-door-open"></i>'
            ],
            "HAS_DEADBOLT_LOCK" => [
                "icon" => '<i class="fa-solid fa-bolt-lightning"></i>'
            ],
            "HAS_OUTDOOR_LIGHTING" => [
                "icon" => '<i class="fa-solid fa-bolt-lightning"></i>'
            ],
            "HAS_ESSENTIALS" => [
                "icon" => '<i class="fa-solid fa-toolbox"></i>'
            ],
            "HAS_BALCONY_TERRASSE" => [
                "icon" => '<i class="fa-thin fa-house"></i>'
            ],
            "HAS_BABY_HIGH_CHAIR" => [
                "icon" => '<i class="fa-sharp fa-thin fa-chair-office"></i>'
            ],
            "HAS_BABY_TRAVEL_BED" => [
                "icon" => '<i class="fa-solid fa-bed-pulse"></i>'
            ],
            "HAS_CDDVD_PLAYER" => [
                "icon" => '<i class="fa-solid fa-compact-disc"></i>'
            ],
            "HAS_BOARD_GAMES" => [
                "icon" => '<i class="fa-solid fa-gamepad"></i>'
            ],
            "HAS_BARBECUE" => [
                "icon" => '<i class="fa-solid fa-grill-hot"></i>'
            ],
            "HAS_ELEVATOR" => [
                "icon" => '<i class="fa-solid fa-elevator"></i>'
            ],
            "HAS_DEHUMIDIFIER" => [
                "icon" => '<i class="fa-thin fa-refrigerator"></i>'
            ],
            "HAS_CEILING_FAN" => [
                "icon" => '<i class="fa-solid fa-fan"></i>'
            ],
            "HAS_VENTILATION_FAN" => [
                "icon" => '<i class="fa-solid fa-mask-ventilator"></i>'
            ],
            "HAS_HAIR_DRYER" => [
                "icon" => '<i class="fa-duotone fa-user-hair"></i>'
            ],
            "HAS_CROCKERY_CUTLERY" => [
                "icon" => '<i class="fa-solid fa-bag-shopping"></i>'
            ],
            "HAS_POTS_PANS" => [
                "icon" => '<i class="fa-solid fa-bag-shopping"></i>'
            ],
        ];
        //                  HAS_OVEN HAS_MICROWAVE_OVEN HAS_WATER_KETTLE HAS_COFFEE_MAKER HAS_DISHWASHER HAS_TOASTER HAS_FRIDGE HAS_KITCHEN_ISLAND HAS_DINING_TABLE HAS_ALARM_SYSTEM HAS_BASKETBALL_COURT HAS_CINEMA_ROOM HAS_GATED_PROPERTY HAS_HELIPAD HAS_GYM HAS_IPOD_STATION HAS_JACUZZI HAS_STEAM_ROOM HAS_LIBRARY HAS_MASSAGE_ROOM HAS_OFFICE HAS_DESK HAS_DESK_CHAIR HAS_COMPUTER_MONITOR HAS_PRINTER HAS_POOL_TABLE HAS_PIANO HAS_SAFE_BOX HAS_CABINET_LOCKS HAS_SEA_VIEW HAS_SMART_HOME HAS_SOCCER_FIELD HAS_TENNIS HAS_TOILETRIES HAS_WINE_CELLAR HAS_WARDROBE HAS_IRON HAS_IRONING_FACILITIES HAS_SHAMPOO HAS_BREAKFAST HAS_MEAL_DELIVERY HAS_BUZZER HAS_DOORMAN HAS_CARBON_MONOXIDE_DETECTOR HAS_FIRST_AID_KIT HAS_CAT HAS_DOG HAS_OTHER_PET HAS_24_CHECKIN HAS_SAFETY_CARD HAS_HANGERS IS_LAPTOP_FRIENDLY HAS_LOCK_ON_BEDROOM HAS_PRIVATE_ENTRANCE HAS_BABY_BATH HAS_BABY_MONITOR HAS_BABYSITTER_RECOMMENDATIONS HAS_BATHTUB HAS_CHANGING_TABLE HAS_CHILDRENS_BOOKS_AND_TOYS HAS_OUTDOOR_PLAY_AREA HAS_CHILDRENS_DINNERWARE HAS_CHILDCARE HAS_FIREPLACE_GUARDS HAS_GAME_CONSOLE HAS_OUTLET_COVERS HAS_PACK_N_PLAY_TRAVEL_CRIB HAS_ROOM_DARKENING_SHADES HAS_STAIR_GATES HAS_TABLE_CORNER_GUARDS HAS_WINDOW_GUARDS HAS_LAKE_ACCESS HAS_BEACH_FRONT HAS_WATER_FRONT HAS_SKI_IN_SKI_OUT HAS_STOVE CHECK_IN_OPTION CHECK_IN_OPTION_INSTRUCTION HAS_LINENS HAS_TOWELS HAS_HOT_WATER HAS_COOKING_BASICS HAS_SURVEILLANCE HAS_GARDEN HAS_DECK_PATIO HAS_AIR_FILTER HAS_ENHANCED_CLEANING HAS_CLEANING_WITH_DISINFECTANTS HAS_HIGH_TOUCH_SURFACES_CLEANING_WITH_DISINFECTANTS HAS_IN_PERSON_CHECKIN HAS_CONTACTLESS_CHECKIN HAS_CONCIERGE HAS_OCEAN_FRONT IS_RESORT IS_RURAL HAS_TOWN HAS_WATER_VIEW IS_DOWNTOWN HAS_GOLF_COURSE_VIEW IS_LAKEFRONT HAS_MOUNTAIN IS_NEAR_OCEAN HAS_RIVER HAS_VILLAGE HAS_BEACH HAS_BEACH_VIEW IS_GOLF_COURSE_FRONT HAS_LAKE HAS_LAKE_VIEW HAS_MOUNTAIN_VIEW HAS_BAKING_SHEET HAS_BARBEQUE_UTENSILS HAS_BEACH_ESSENTIALS HAS_BIDET HAS_BIKES_FOR_RENT HAS_BLENDER HAS_BOAT_SLIP HAS_BODY_SOAP HAS_BREAD_MAKER HAS_CLEANING_PRODUCTS HAS_CLOTHES_DRYING_RACK HAS_COFFEE HAS_CONDITIONER HAS_ETHERNET_CONNECTION HAS_EXERCISE_EQUIPMENT HAS_EXTRA_PILLOWS_AND_BLANKETS HAS_FIRE_PIT HAS_FREEZER HAS_HAMMOCK HAS_LAUNDROMAT_NEARBY HAS_KAYAK HAS_MINI_FRIDGE HAS_MOSQUITO_NET HAS_OUTDOOR_KITCHEN HAS_OUTDOOR_SEATING HAS_PING_PONG_TABLE HAS_POCKET_WIFI HAS_PORTABLE_FANS HAS_RAIN_SHOWER HAS_RECORD_PLAYER HAS_RESORT_ACCESS HAS_RICE_MAKER HAS_SAFE HAS_SHOWER_GEL HAS_SOUND_SYSTEM HAS_TRASH_COMPACTER HAS_WINE_GLASSES INVOICE_PROVIDED
    }
}
