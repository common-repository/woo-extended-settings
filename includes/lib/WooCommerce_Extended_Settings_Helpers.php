<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin API class.
 */
class WooCommerce_Extended_Settings_Helpers
{

    public static function is_checked($checkbox_settings)
    {
        if ('on' === $checkbox_settings) {
            return true;
        }

        return false;
    }
}
