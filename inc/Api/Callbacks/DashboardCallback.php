<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Api\Callbacks;

use User\EsPropertyListings\Base\BaseController;

class DashboardCallback extends BaseController
{
    public function settings($input)
    {
        // var_dump($input);
        // die;
        return $input;
    }
    public function sectionSetting()
    {
        return "Set The banner Default Background";
    }
    public function fieldSettings($params)
    {
        $label = $params['label_for'];
        $option = get_option($this->settings_opt_name);
        echo '
            <input type="text" 
            value="' . (is_array($option) && isset($option[$label]) ? $option[$label] : "") . '" 
            name="' . $this->settings_opt_name . '[' . $label . ']" >';
    }
}
