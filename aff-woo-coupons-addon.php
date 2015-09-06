<?php

/**
 * Plugin Name: Affiliate WooCommerce Coupons Addon
 * Plugin URI: http://www.tipsandtricks-hq.com
 * Description: Addon for using WooCommerce Coupons with the affiliate platform plugin
 * Version: 1.3
 * Author: Tips and Tricks HQ
 * Author URI: http://www.tipsandtricks-hq.com/
 * Requires at least: 3.0
 */
if (!defined('ABSPATH'))
    exit;

if (!class_exists('AFF_WOO_COUPON_ADDON')) {

    class AFF_WOO_COUPON_ADDON {

        var $version = '1.3';
        var $db_version = '1.0';
        var $plugin_url;
        var $plugin_path;

        function __construct() {
            $this->define_constants();
            $this->includes();
            $this->loader_operations();
            //Handle any db install and upgrade task
            add_filter('aff_woo_before_awarding_commission_filter', array(&$this, 'aff_woo_check_coupons'), 10, 2);
            //add_filter('wpap_below_your_affiliate_link', array(&$this, 'show_affiliates_coupon'));
            add_shortcode('wpap_woo_show_coupon_code', array(&$this, 'show_affiliates_coupon'));

            add_action('init', array(&$this, 'plugin_init'), 0);
        }

        function define_constants() {
            define('AFF_WOO_COUPON_ADDON_VERSION', $this->version);
            define('AFF_WOO_COUPON_ADDON_URL', $this->plugin_url());
            define('AFF_WOO_COUPON_ADDON_PATH', $this->plugin_path());
        }

        function includes() {
            include_once('class-aff-woo-coupons-association.php');
        }

        function loader_operations() {
            add_action('plugins_loaded', array(&$this, 'plugins_loaded_handler')); //plugins loaded hook		
        }

        function plugins_loaded_handler() {//Runs when plugins_loaded action gets fired
            $this->do_db_upgrade_check();
        }

        function do_db_upgrade_check() {
            //NOP
        }

        function plugin_url() {
            if ($this->plugin_url)
                return $this->plugin_url;
            return $this->plugin_url = plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__));
        }

        function plugin_path() {
            if ($this->plugin_path)
                return $this->plugin_path;
            return $this->plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
        }

        function plugin_init() {//Gets run with WP Init is fired
            //wp_enqueue_style('aff.wc.addon.style', AFF_WOO_COUPON_ADDON_URL.'/...-addon-style.css');
            //add_action('action_name_goes_here', array(&$this,'custom_action_function_handler'));
            add_action('admin_menu', array(&$this, 'add_admin_menus'));
        }

        function add_admin_menus() {
            //Add the menu
            include_once('aff-woo-coupons-settings.php');
            $parent_slug = WP_AFF_PLATFORM_PATH . 'wp_affiliate_platform1.php';
            $page_title = 'WooCommerce Coupons';
            $menu_title = 'Woo Coupons';
            add_submenu_page($parent_slug, $page_title, $menu_title, AFFILIATE_MANAGEMENT_PERMISSION, 'wp-aff-woo-coupons', 'wp_aff_woo_coupons_settings_menu');
        }

        function aff_woo_check_coupons($referrer, $order) {
            wp_affiliate_log_debug("Handling the aff_woo_before_awarding_commission_filter.", true);
            $txn_coupons = $order->get_used_coupons();
            if (sizeof($txn_coupons) > 0) {
                foreach ($txn_coupons as $code) {
                    if (!$code) {
                        continue;
                    }
                    wp_affiliate_log_debug("Found a coupon code for this transaction. Coupon Code: " . $code, true);
                    //$coupon = new WC_Coupon( $code );
                    $collection_obj = AFF_WOO_COUPONS_ASSOC::get_instance();
                    $item = $collection_obj->find_item_by_code($code);
                    $aff_id = $item->aff_id;
                    wp_affiliate_log_debug("Affiliate ID value for this coupon code: " . $aff_id, true);
                    if (!empty($aff_id)) {
                        $referrer = $aff_id;
                        return $referrer;
                    }
                }
            } else {
                wp_affiliate_log_debug("No coupons used for this woocommerce transaction.", true);
                return $referrer;
            }
            return $referrer;
        }

        function show_affiliates_coupon($args) {

            $ap_id = $_SESSION['user_id'];
            $collection_obj = AFF_WOO_COUPONS_ASSOC::get_instance();
            $item = $collection_obj->find_item_by_ap_id($ap_id);
            $c_code = $item->coupon_code;
            
            $content = $c_code;
            return $content;
        }

    }

    //End of plugin class
}//End of class not exists check

$GLOBALS['AFF_WOO_COUPON_ADDON'] = new AFF_WOO_COUPON_ADDON();
