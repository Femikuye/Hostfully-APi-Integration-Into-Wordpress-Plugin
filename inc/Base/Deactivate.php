<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Base;

class Deactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
