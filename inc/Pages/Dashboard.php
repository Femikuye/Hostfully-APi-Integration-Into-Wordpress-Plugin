<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Pages;

use User\EsPropertyListings\Base\BaseController;
use User\EsPropertyListings\Api\SettingsApi;
use User\EsPropertyListings\Api\Callbacks\DashboardCallback;

class Dashboard extends BaseController
{
    public $settings;
    public $pages;
    public $callback;
    public function register()
    {
        $this->settings = new SettingsApi();
        $this->callback = new DashboardCallback();

        $this->setSettings();
        $this->setSection();
        $this->setFields();

        $this->setPages();
        $this->settings->addPages($this->pages)->withSubPage("Dashboard")->register();
    }
    public function setPages()
    {
        $this->pages = [
            [
                'page_title' => 'ES Property Listings',
                'menu_title' => 'Property Listings',
                'capability' => 'manage_options',
                'menu_slug' => $this->plugin_slug,
                'callback' => array($this, 'dashboardHtml'),
                'icon_url' => 'dashicons-store',
                'position' => 110
            ]
        ];
    }
    public function setSettings()
    {
        $settings_array = [
            [
                'option_name' => $this->settings_opt_name,
                'option_group' => 'espl_settings_option',
                'callback' => array($this->callback, 'settings')
            ]
        ];
        $this->settings->setSettings($settings_array);
    }
    public function setSection()
    {
        $section_params =
            [
                [
                    'id' => 'espl_settings_section',
                    'title' => ' ',
                    'callback' => array($this->callback, 'sectionSetting'),
                    'page' => $this->plugin_slug
                ]
            ];
        $this->settings->setSections($section_params);
    }
    public function setFields()
    {
        $fields = [
            [
                'id' => 'api_key',
                'title' => 'Hostfully API Key',
                'page' => $this->plugin_slug,
                'section' => 'espl_settings_section',
                'callback' => array($this->callback, 'fieldSettings'),
                'args' => [
                    'label_for' => 'api_key',
                ]
            ],
            [
                'id' => 'agency_uid',
                'title' => 'Hostfully Agency Uid',
                'page' => $this->plugin_slug,
                'section' => 'espl_settings_section',
                'callback' => array($this->callback, 'fieldSettings'),
                'args' => [
                    'label_for' => 'agency_uid',
                ]
            ]
        ];
        $this->settings->setFields($fields);
    }
    public function dashboardHtml()
    {
        return require_once("$this->plugin_path/templates/admin/admin.php");
    }
}
