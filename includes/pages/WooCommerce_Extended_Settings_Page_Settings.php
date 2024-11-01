<?php
/**
 * Settings class file.
 *
 * @package WordPress Plugin Template/Settings
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings class.
 */
class WooCommerce_Extended_Settings_Page_Settings
{

    /**
     * The single instance of woocommerce_extended_settings_Settings.
     *
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static $_instance = null;

    /**
     * The main plugin object.
     *
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $parent = null;

    /**
     * Prefix for plugin settings.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $base = '';

    /**
     * Available settings for plugin.
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $settings = array();

    /**
     * Constructor function.
     *
     * @param object $parent Parent object.
     */
    public function __construct($parent)
    {
        $this->parent = $parent;

        $this->base = _WES_PREFIX_SETTINGS_;

        // Initialise settings.
        add_action('init', array($this, 'init_settings'), 11);

        // Register plugin settings.
        add_action('admin_init', array($this, 'register_settings'));

        // Add settings page to menu.
        add_action('admin_menu', array($this, 'add_menu_item'));

        // Add settings link to plugins page.
        add_filter(
            'plugin_action_links_' . plugin_basename($this->parent->file),
            array(
                $this,
                'add_settings_link',
            )
        );

        // Configure placement of plugin settings page. See readme for implementation.
        add_filter($this->base . 'menu_settings', array($this, 'configure_settings'));
    }

    /**
     * Initialise settings
     *
     * @return void
     */
    public function init_settings()
    {
        $this->settings = $this->settings_fields();
    }

    /**
     * Add settings page to admin menu
     *
     * @return void
     */
    public function add_menu_item()
    {

        $args = $this->menu_settings();

        // Do nothing if wrong location key is set.
        if (is_array($args) && isset($args['location']) && function_exists('add_' . $args['location'] . '_page')) {
            switch ($args['location']) {
                case 'options':
                case 'submenu':
                    $page = add_submenu_page($args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function']);
                    break;
                case 'menu':
                    $page = add_menu_page($args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position']);
                    break;
                default:
                    return;
            }
            add_action('admin_print_styles-' . $page, array($this, 'settings_assets'));
        }
    }

    /**
     * Prepare default settings page arguments
     *
     * @return mixed|void
     */
    private function menu_settings()
    {
        return apply_filters(
            $this->base . 'menu_settings',
            array(
                'location' => 'submenu', // Possible settings: options, menu, submenu.
                'parent_slug' => 'woocommerce',
                'page_title' => __('Extended Settings', 'woocommerce-extended-settings'),
                'menu_title' => __('Extended Settings', 'woocommerce-extended-settings'),
                'capability' => 'manage_options',
                'menu_slug' => $this->parent->_token . '_settings',
                'function' => array($this, 'settings_page'),
                'icon_url' => '',
                'position' => 1,
            )
        );
    }

    /**
     * Container for settings page arguments
     *
     * @param array $settings Settings array.
     *
     * @return array
     */
    public function configure_settings($settings = array())
    {
        return $settings;
    }

    /**
     * Load settings JS & CSS
     *
     * @return void
     */
    public function settings_assets()
    {
        wp_register_script($this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array('farbtastic', 'jquery'), '1.0.0', true);
        wp_enqueue_script($this->parent->_token . '-settings-js');
    }

    /**
     * Add settings link to plugin list table
     *
     * @param  array $links Existing links.
     * @return array        Modified links.
     */
    public function add_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __('Settings', 'woocommerce-extended-settings') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * Build settings fields
     *
     * @return array Fields to be displayed on settings page
     */
    private function settings_fields()
    {
        $settings['product'] = array(
            'title' => __('Product', 'woocommerce-extended-settings'),
            'description' => __('Extended Settings for WooCommerce for the products.', 'woocommerce-extended-settings'),
            'fields' => array(
                array(
                    'id' => 'remove_related_products',
                    'label' => __("Remove related products:", 'woocommerce-extended-settings'),
                    'description' => __("Do you want to remove the related products section from Single Product Page?", 'woocommerce-extended-settings'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
                array(
                    'id' => 'woocommerce_template_single_add_to_cart',
                    'label' => __("'Add to cart' button on single page:", 'woocommerce-extended-settings'),
                    'description' => __("Do you want to remove the 'Add to cart' button from Single Product Page?", 'woocommerce-extended-settings'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
                array(
                    'id' => 'woocommerce_template_loop_add_to_cart',
                    'label' => __("'Add to cart' button on products page:", 'woocommerce-extended-settings'),
                    'description' => __("Do you want to remove the 'Add to cart' button from Products Page?", 'woocommerce-extended-settings'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
                array(
                    'id' => 'remove_product_summary',
                    'label' => __('Product summary:', 'woocommerce-extended-settings'),
                    'description' => __("Remove the product summary that you don't want in the product page.", 'woocommerce-extended-settings'),
                    'type' => 'checkbox_multi',
                    'options' => array(
                        'woocommerce_template_single_price' => __('Product price', 'woocommerce-extended-settings'),
                        'woocommerce_template_single_title' => __('Product title', 'woocommerce-extended-settings'),
                        'woocommerce_template_single_rating' => __('Product rating', 'woocommerce-extended-settings'),
                        'woocommerce_template_single_excerpt' => __('Product excerpt', 'woocommerce-extended-settings'),
                        'woocommerce_template_single_meta' => __('Product category', 'woocommerce-extended-settings'),
                        'woocommerce_template_single_sharing' => __('Product sharing', 'woocommerce-extended-settings'),
                    )
                ),
                array(
                    'id' => 'remove_product_tabs',
                    'label' => __('Tabs from product page:', 'woocommerce-extended-settings'),
                    'description' => __("Remove the tabs that you don't want in the product page.", 'woocommerce-extended-settings'),
                    'type' => 'checkbox_multi',
                    'options' => array(
                        'additional_information' => __('Additional Information', 'woocommerce-extended-settings'),
                        'reviews' => __('Reviews', 'woocommerce-extended-settings'),
                    )
                ),
            ),
        );

        $settings['cart'] = array(
            'title' => __('Cart', 'woocommerce-extended-settings'),
            'description' => __('Extended Settings for WooCommerce for the Cart page.', 'woocommerce-extended-settings'),
            'fields' => array(
                array(
                    'id' => 'wc_add_to_cart_message_html',
                    'label' => __("'Added to cart' message:", 'woocommerce-extended-settings'),
                    'description' => __("Do you want to remove the 'Product has been added to your cart.' message?", 'woocommerce-extended-settings'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
                array(
                    'id' => 'redirect_if_empty_cart',
                    'label' => __("Empty cart:", 'woocommerce-extended-settings'),
                    'description' => __("If the cart is empty, redirect the user to the shop page.", 'woocommerce-extended-settings'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
            ),
        );

        $settings['checkout'] = array(
            'title' => __('Checkout', 'woocommerce-extended-settings'),
            'description' => __('Extended Settings for WooCommerce for the Checkout page', 'woocommerce-extended-settings'),
            'fields' => array(
                array(
                    'id' => 'remove_checkout_billing_fields',
                    'label' => __('Remove individual Billing fields:', 'woocommerce-extended-settings'),
                    'description' => __('', 'woocommerce-extended-settings'),
                    'type' => 'checkbox_multi',
                    'options' => array(
                        'billing_first_name' => __('First Name', 'woocommerce-extended-settings'),
                        'billing_last_name' => __('Last Name', 'woocommerce-extended-settings'),
                        'billing_company' => __('Company', 'woocommerce-extended-settings'),
                        'billing_address_1' => __('Address 1', 'woocommerce-extended-settings'),
                        'billing_address_2' => __('Address 2', 'woocommerce-extended-settings'),
                        'billing_country' => __('Country', 'woocommerce-extended-settings'),
                        'billing_city' => __('City', 'woocommerce-extended-settings'),
                        'billing_postcode' => __('Postcode', 'woocommerce-extended-settings'),
                        'billing_email' => __('Email', 'woocommerce-extended-settings'),
                        'billing_phone' => __('Phone', 'woocommerce-extended-settings'),
                    )
                ),
                array(
                    'id' => 'remove_checkout_shipping_fields',
                    'label' => __('Remove individual Shipping fields:', 'woocommerce-extended-settings'),
                    'description' => __('', 'woocommerce-extended-settings'),
                    'type' => 'checkbox_multi',
                    'options' => array(
                        'shipping_first_name' => __('First Name', 'woocommerce-extended-settings'),
                        'shipping_last_name' => __('Last Name', 'woocommerce-extended-settings'),
                        'shipping_company' => __('Company', 'woocommerce-extended-settings'),
                        'shipping_address_1' => __('Address 1', 'woocommerce-extended-settings'),
                        'shipping_address_2' => __('Address 2', 'woocommerce-extended-settings'),
                        'shipping_city' => __('City', 'woocommerce-extended-settings'),
                        'shipping_postcode' => __('Postcode', 'woocommerce-extended-settings'),
                        'shipping_country' => __('Country', 'woocommerce-extended-settings'),
                        'shipping_email' => __('Email', 'woocommerce-extended-settings'),
                        'shipping_phone' => __('Phone', 'woocommerce-extended-settings'),
                    )
                ),
                array(
                    'id' => 'remove_order_comments',
                    'label' => __("Remove order notes:", 'woocommerce-extended-settings'),
                    'description' => __("Do you want to remove the order notes field?", 'woocommerce-extended-settings'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
            ),
        );
;
        $settings['single_page_checkout'] = array(
            'title' => __('Single Page Checkout', 'woocommerce-extended-settings'),
            'description' => __('Merge the Cart page and the Checkout page into a single one.', 'woocommerce-extended-settings'),
            'fields' => array(
                array(
                    'id' => 'single_checkout_page',
                    'label' => __("Activate:", 'woocommerce-extended-settings'),
                    'description' => __("Do you want to have a single checkout page?", 'woocommerce-extended-settings'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
                array(
                    'description' => __("In order to work, you need to add this shortcode [woocommerce_extended_settings_single_page_checkout] in the Checkout page.", 'woocommerce-extended-settings'),
                    'type' => 'info_box',
                ),
                array(
                    'id' => 'add_last_product_to_single_page',
                    'label' => __("Last added product:", 'woocommerce-extended-settings'),
                    'description' => __("Do you want the last product added to the cart to be displayed on the checkout page?", 'woocommerce-extended-settings'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
                array(
                    'id' => 'remove_related_products_from_single_page',
                    'label' => __("Related products section:", 'woocommerce-extended-settings'),
                    'description' => __("Do you want to remove the related products suggestions?", 'woocommerce-extended-settings'),
                    'type' => 'checkbox',
                    'default' => '',
                ),
                array(
                    'id' => 'checkout_page_layout',
                    'label' => __('Checkout page layout:', 'woocommerce-extended-settings'),
                    'description' => '',
                    'type' => 'select',
                    'options' => array(
                        '1' => 'Full Width',
                        '2' => 'Half width',
                    ),
                    'default' => '1',
                ),
                array(
                    'id' => 'billing_checkout_page_layout',
                    'label' => __('Billing details section layout:', 'woocommerce-extended-settings'),
                    'description' => '',
                    'type' => 'select',
                    'options' => array(
                        '1' => 'Full Width',
                        '2' => 'Half width',
                    ),
                    'default' => '1',
                ),
            ),
        );

        $settings = apply_filters($this->parent->_token . '_settings_fields', $settings);

        return $settings;
    }

    /**
     * Register plugin settings
     *
     * @return void
     */
    public function register_settings()
    {
        if (is_array($this->settings)) {

            // Check posted/selected tab.
            $current_section = '';
            if (isset($_POST['tab']) && $_POST['tab']) {
                $current_section = sanitize_text_field($_POST['tab']);
            } else {
                if (isset($_GET['tab']) && $_GET['tab']) {
                    $current_section = sanitize_text_field($_GET['tab']);
                }
            }
            foreach ($this->settings as $section => $data) {

                if ($current_section && $current_section !== $section) {
                    continue;
                }

                // Add section to page.
                add_settings_section($section, $data['title'], array($this, 'settings_section'), $this->parent->_token . '_settings');

                foreach ($data['fields'] as $field) {

                    // Validation callback for field.
                    $validation = '';
                    if (isset($field['callback'])) {
                        $validation = $field['callback'];
                    }

                    // Register field.
                    $option_name = $this->base . $field['id'];
                    register_setting($this->parent->_token . '_settings', $option_name, $validation);

                    // Add field to page.
                    add_settings_field(
                        $field['id'],
                        $field['label'],
                        array($this->parent->admin, 'display_field'),
                        $this->parent->_token . '_settings',
                        $section,
                        array(
                            'field' => $field,
                            'prefix' => $this->base,
                        )
                    );
                }

                if (!$current_section) {
                    break;
                }
            }
        }
    }

    /**
     * Settings section.
     *
     * @param array $section Array of section ids.
     * @return void
     */
    public function settings_section($section)
    {
        $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
        echo $html;
    }

    /**
     * Load settings page content.
     *
     * @return void
     */
    public function settings_page()
    {

        // Build page HTML.
        $html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
        $html .= '<h2>' . __('Extended Settings for WooCommerce', 'woocommerce-extended-settings') . '</h2>' . "\n";

        $tab = '';

        if (isset($_GET['tab']) && $_GET['tab']) {
            $tab .= sanitize_text_field($_GET['tab']);
        }

        // Show page tabs.
        if (is_array($this->settings) && 1 < count($this->settings)) {

            $html .= '<h2 class="nav-tab-wrapper">' . "\n";

            $c = 0;
            foreach ($this->settings as $section => $data) {

                // Set tab class.
                $class = 'nav-tab';
                if (!isset($_GET['tab'])) {
                    if (0 === $c) {
                        $class .= ' nav-tab-active';
                    }
                } else {
                    if (isset($_GET['tab']) && $section == sanitize_text_field($_GET['tab'])) {
                        $class .= ' nav-tab-active';
                    }
                }

                // Set tab link.
                $tab_link = add_query_arg(array('tab' => $section));
                if (isset($_GET['settings-updated'])) {
                    $tab_link = remove_query_arg('settings-updated', $tab_link);
                }

                // Output tab.
                $html .= '<a href="' . $tab_link . '" class="' . esc_attr($class) . '">' . esc_html($data['title']) . '</a>' . "\n";

                ++$c;
            }

            $html .= '</h2>' . "\n";
        }

        $html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

        // Get settings fields.
        ob_start();
        settings_fields($this->parent->_token . '_settings');
        do_settings_sections($this->parent->_token . '_settings');
        $html .= ob_get_clean();

        $html .= '<p class="submit">' . "\n";
        $html .= '<input type="hidden" name="tab" value="' . esc_attr($tab) . '" />' . "\n";
        $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'woocommerce-extended-settings')) . '" />' . "\n";
        $html .= '</p>' . "\n";
        $html .= '</form>' . "\n";
        $html .= '</div>' . "\n";

        echo $html;
    }

    /**
     * Main woocommerce_extended_settings_Settings Instance
     *
     * Ensures only one instance of woocommerce_extended_settings_Settings is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see woocommerce_extended_settings()
     * @param object $parent Object instance.
     * @return object woocommerce_extended_settings_Settings instance
     */
    public static function instance($parent)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($parent);
        }
        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, esc_html(__('Cloning of WooCommerce Extended Settings is forbidden.')), esc_attr($this->parent->_version));
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, esc_html(__('Unserializing instances of WooCommerce Extended Settings is forbidden.')), esc_attr($this->parent->_version));
    }

}
