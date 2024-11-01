<?php
if (!defined('ABSPATH')) {
    exit;
}

class WooCommerce_Extended_Settings_Single_Page_Checkout
{

    private $current_product_id = null;

    public function __construct()
    {
        add_filter('woocommerce_add_to_cart_redirect', array($this, 'woocommerce_extended_settings_checkout_woo_redirect'));
        add_shortcode('woocommerce_extended_settings_single_page_checkout', array($this, 'woocommerce_single_page_checkout'));
        add_action('wp_head', array($this, 'woocommerce_billing_single_page_checkout'));

        if (WooCommerce_Extended_Settings_Helpers::is_checked(get_option(_WES_PREFIX_SETTINGS_ . 'remove_related_products_from_single_page'))) {
            add_action('template_redirect', function () {
                remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
            }, 20);
        }

        add_action('wp', function () {
            $this->current_product_id = end(WC()->cart->cart_contents)['product_id'];
        });
    }

    function woocommerce_extended_settings_checkout_woo_redirect()
    {
        global $woocommerce;
        $checkout_url = wc_get_checkout_url();

        return $checkout_url;
    }


    function woocommerce_single_page_checkout($atts)
    {
        $column_option = get_option(_WES_PREFIX_SETTINGS_ . 'checkout_page_layout');

        switch ($column_option) {
            case 1:
                $column_option_class = 'col-md-12';
                break;
            case 2:
                $column_option_class = 'col-md-6';
                break;
            default:
                $column_option_class = 'col-md-12';
                break;
        }

        echo $this->get_checkout_page_html($column_option_class);
    }

    function get_checkout_page_html($column_option_class)
    {
        $html = '';
        $html .= '<div class="row">';

        if (WooCommerce_Extended_Settings_Helpers::is_checked(get_option(_WES_PREFIX_SETTINGS_ . 'add_last_product_to_single_page'))) {
            $html .= '<div class="col-md-12">';
            $html .= do_shortcode('[product_page id="' . $this->current_product_id . '"]');
            $html .= '</div>';
        }

        $html .= '<div class="' . $column_option_class . '">';
        $html .= do_shortcode('[woocommerce_cart]');
        $html .= '</div>';

        $html .= '<div class="' . $column_option_class . '">';
        $html .= do_shortcode('[woocommerce_checkout]');
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    function get_billing_checkout_page_html($column_option_class)
    {
        $html = '';

        $html .= '<style type="text/css" media="screen">';

        $html .= '.woocommerce #customer_details .col-1, .woocommerce #customer_details .col-2{';
        $html .= 'width:' . $column_option_class . ';';
        $html .= '}';

        $html .= '.cart-collaterals .cross-sells {';
        $html .= 'display:none';
        $html .= '}';
        $html .= '@media(max-width: 640px){';
        $html .= '.woocommerce #customer_details .col-1, .woocommerce #customer_details .col-2{';
        $html .= 'width: 100% !important;';
        $html .= '}';
        $html .= '}';
        $html .= '</style>';

        return $html;
    }

    function woocommerce_billing_single_page_checkout()
    {
        $column_billing_option = get_option(_WES_PREFIX_SETTINGS_ . 'billing_checkout_page_layout');

        switch ($column_billing_option) {
            case 1:
                $column_option_value = '98%';
                break;
            case 2:
                $column_option_value = '48%';
                break;
            default:
                $column_option_value = '48%';
                break;
        }

        echo $this->get_billing_checkout_page_html($column_option_value);
    }
}
