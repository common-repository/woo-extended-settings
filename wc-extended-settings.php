<?php
/**
 * Plugin Name: Extended Settings for WooCommerce
 * Version: 1.0.0
 * Plugin URI:
 * Description: Sleek plugin designed to add versatility to WooCommerce. Supports One Page Shopping, redirect if cart is empty, remove additional product information, remove selected Billing and Shipping fields.
 * Author: Nagy Paul Sorel - Inventiff.Agency
 * Contributors: Pantea David, Condor Daria
 * Author URI: https://inventiff.agency
 * Requires at least: 4.0
 * Tested up to: 5.2.2
 *
 * @package WordPress
 * @author Nagy Paul Sorel - Inventiff.Agency
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

//Define globals
define('_WES_PREFIX_SETTINGS_', 'wes_inv_');
define('_WES_ROOT_DIR_', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('_WES_VERSION_', '1.0.0');

// Load plugin class files.
require_once 'includes/WooCommerce_Extended_Settings.php';

//Load Pages
require_once 'includes/pages/WooCommerce_Extended_Settings_Page_Settings.php';

// Load plugin libraries.
require_once 'includes/lib/WooCommerce_Extended_Settings_Admin.php';
require_once 'includes/lib/WooCommerce_Extended_Settings_Frontend.php';
require_once 'includes/lib/WooCommerce_Extended_Settings_Helpers.php';
require_once 'includes/lib/WooCommerce_Extended_Settings_Single_Page_Checkout.php';


/**
 * Returns the main instance of woocommerce_extended_settings to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object woocommerce_extended_settings
 */

if (PHP_VERSION_ID >= 5100) {
    if (!function_exists('is_woocommerce_active')) {
        function is_woocommerce_active()
        {
            $active_plugins = (array)get_option('active_plugins', array());
            if (is_multisite()) {
                $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
            }
            return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
        }
    }

    if (is_woocommerce_active()) {
        function woocommerce_extended_settings()
        {
            $instance = WooCommerce_Extended_Settings::instance(__FILE__, _WES_VERSION_);

            if (is_null($instance->settings)) {
                $instance->settings = WooCommerce_Extended_Settings_Page_Settings::instance($instance);
            }

            return $instance;
        }

        woocommerce_extended_settings();
    }
} else {
    echo 'PHP Version error.';
}

