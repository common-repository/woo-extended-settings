<?php
/**
 * Post type Admin API file.
 *
 * @package WordPress Plugin Template/Includes
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin API class.
 */
class WooCommerce_ExtendedSettings_Frontend
{

    /**
     * Constructor function
     */
    public function __construct()
    {


        $this->check_settings();
    }


    function check_settings()
    {
        /**
         * ---------------------------------------------- PRODUCTS SETTINGS
         */

        //Remove product summary
        $remove_product_summary = get_option(_WES_PREFIX_SETTINGS_ . 'remove_product_summary');

        if (is_array($remove_product_summary) && count($remove_product_summary) > 0) {
            add_action('template_redirect', function () use ($remove_product_summary) {
                foreach ($remove_product_summary as $field) {
                     switch ($field) {
                        case 'woocommerce_template_single_price':
                            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
                            break;
                        case 'woocommerce_template_single_title':
                            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
                            break;
                        case 'woocommerce_template_single_rating':
                            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
                            break;
                        case 'woocommerce_template_single_excerpt':
                            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
                            break;
                        case 'woocommerce_template_single_meta':
                            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
                            break;
                        case 'woocommerce_template_single_sharing':
                            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
                            break;
                    }
                }
            }, 20);
        }

        //Remove related products

        if (WooCommerce_Extended_Settings_Helpers::is_checked(get_option(_WES_PREFIX_SETTINGS_ . 'remove_related_products'))) {
            add_action('template_redirect', function () {
                remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
            }, 20);
        }

        // Remove add to cart button from single page
        if (WooCommerce_Extended_Settings_Helpers::is_checked(get_option(_WES_PREFIX_SETTINGS_ . 'woocommerce_template_single_add_to_cart'))) {

            add_action('template_redirect', function () {
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            }, 10);
        }

        // Remove add to cart button from loop single page
        if (WooCommerce_Extended_Settings_Helpers::is_checked(get_option(_WES_PREFIX_SETTINGS_ . 'woocommerce_template_loop_add_to_cart'))) {

            add_action('template_redirect', function () {
                remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            }, 10);
        }

        // Remove tabs.
        add_filter('woocommerce_product_tabs', array($this, 'remove_product_tabs'), 98);


        /**
         * ---------------------------------------------- CART SETTINGS
         */
        // Remove 'Product added to cart' html message.
        if (WooCommerce_Extended_Settings_Helpers::is_checked(get_option(_WES_PREFIX_SETTINGS_ . 'wc_add_to_cart_message_html'))) {
            add_filter('wc_add_to_cart_message_html', '__return_null');
        }

        if (WooCommerce_Extended_Settings_Helpers::is_checked(get_option(_WES_PREFIX_SETTINGS_ . 'redirect_if_empty_cart'))) {
            add_filter('woocommerce_return_to_shop_redirect', function () {
                $shop_page_id = wc_get_page_id('shop');
                $shop_page_url = $shop_page_id ? get_permalink($shop_page_id) : '';
                wp_safe_redirect($shop_page_url);
            }, 20);

        }
        /**
         * ---------------------------------------------- CHECKOUT SETTINGS
         */
        //Check to remove billing fields
        add_filter('woocommerce_checkout_fields', array($this, 'check_checkout_fields'));

        /**
         * ---------------------------------------------- SINGLE CHECKOUT PAGE
         *
         */

        if (WooCommerce_Extended_Settings_Helpers::is_checked(get_option(_WES_PREFIX_SETTINGS_ . 'single_checkout_page'))) {
            new WooCommerce_Extended_Settings_Single_Page_Checkout();
        }

    }

    function check_checkout_fields($fields)
    {
        $billing_fields = get_option(_WES_PREFIX_SETTINGS_ . 'remove_checkout_billing_fields');
        $shipping_fields = get_option(_WES_PREFIX_SETTINGS_ . 'remove_checkout_shipping_fields');

        if (is_array($billing_fields) && count($billing_fields) > 0) {
            foreach ($billing_fields as $billing_field_to_hide) {
                unset($fields['billing'][$billing_field_to_hide]);
            }
        }

        if (is_array($shipping_fields) && count($shipping_fields) > 0) {
            foreach ($shipping_fields as $shipping_field_to_hide) {
                unset($fields['shipping'][$shipping_field_to_hide]);
            }
        }

        if (WooCommerce_Extended_Settings_Helpers::is_checked(get_option(_WES_PREFIX_SETTINGS_ . 'remove_order_comments'))) {
            unset($fields['order']['order_comments']);
        }

        return $fields;
    }

    function remove_product_tabs($tabs)
    {
        $product_tabs_to_remove = get_option(_WES_PREFIX_SETTINGS_ . 'remove_product_tabs');

        if (is_array($product_tabs_to_remove) && count($product_tabs_to_remove) > 0) {
            foreach ($product_tabs_to_remove as $tab_to_remove) {
                unset($tabs[$tab_to_remove]);
            }
        }


        return $tabs;
    }

}

