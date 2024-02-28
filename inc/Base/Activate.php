<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Base;

use User\EsPropertyListings\Api\TableApi;

class Activate
{
    private static $table;
    public static function activate()
    {
        flush_rewrite_rules();
        self::$table = new TableApi();
        self::$table->createTables();
        $default = array();
        if (!get_option('espl_settings')) {
            update_option('espl_settings', $default);
        }
    }
}
