<?php
/**
 * @package              rent_angry_cat
 *
 * @wordpress-plugin
 * Plugin Name:          Rent angry cats
 * Plugin URI:
 * Description:          WooCommerce based rental plugin. Daily and hourly rent of cars, apartments, things, animals.
 * Version:              1.1.1
 * Author:               superpuperlesha
 * Author URI:           https://profiles.wordpress.org/superpuperlesha/#content-plugins
 * License:              GPL-2.0+
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          rent_angry_cat
 * WC requires at least: 3.0.0
 * WC tested up to:      latest
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/* TRANSLATE plugin */
load_plugin_textdomain(
    'rent_angry_cat',
    false,
    dirname( plugin_basename( __FILE__ ) ) . '/languages'
);

/* INCLUDE plugin */
require __DIR__ . '/class-rent_angry_cat.php';

/* Admin message */
function rent_angry_cat_admin_compatibility_message(){
    add_action( 'admin_notices', function (){
        echo'<div class="error">
                <h3>'.__('Plugin name compatibility error! (Rent angry cats)', 'rent_angry_cat').'</h3>
             </div>';
    } );
}

/* ACTIVATE plugin */
add_action( 'activate_'.plugin_basename( __FILE__ ), 'rent_angry_cat_activate' );
if( !function_exists('rent_angry_cat_activate') ) {
    function rent_angry_cat_activate( $network_wide ){
        if (\Rent_Angry_Cat_ns\Rent_Angry_Cat::getSearchPostPerPage() < 1) {
            update_option('dscc_admin_listitem_count', 5);
        }
        if (\Rent_Angry_Cat_ns\Rent_Angry_Cat::getSearchDefRadius() < 1) {
            update_option('dscc_admin_filter_radius', 5);
        }
        if (\Rent_Angry_Cat_ns\Rent_Angry_Cat::getSearchFilterType() == 0) {
            update_option('dscc_filter_type_show', 0);
        }
        register_uninstall_hook(__FILE__, 'rent_angry_cat_uninstall');
    }
}else{
    rent_angry_cat_admin_compatibility_message();
}

/* DELETE plugin */
if( !function_exists('rent_angry_cat_uninstall') ) {
    function rent_angry_cat_uninstall(){
        delete_option('dscc_admin_googleipikey');
        delete_option('dscc_admin_listitem_count');
        delete_option('dscc_admin_filter_radius');
        delete_option('dscc_filter_type_show');
    }
}else{
    rent_angry_cat_admin_compatibility_message();
}

if( !function_exists('rent_angry_cat_register_product_type') ) {
    function rent_angry_cat_register_product_type(){
        class WC_Product_Rentacat extends WC_Product_Simple{
            public function get_type(){
                return 'rentacat';
            }
        }
        new WC_Product_Rentacat();
    }
}else{
    rent_angry_cat_admin_compatibility_message();
}

/*
 * add link to setup plugin
 */
function WMufn_plugin_settings_link( $links ) {
	$settings_link = '<a href="'.admin_url( 'options-general.php?page=rent_angry_cat' ).'">'.__('Settings', 'wm_cf7_userto_hubspot').'</a>';
	array_unshift($links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'WMufn_plugin_settings_link' );

/* START plugin */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    $Rent_Angry_Cat = new \Rent_Angry_Cat_ns\Rent_Angry_Cat();
} else {
    /* Admin message */
    add_action( 'admin_notices', function (){
        echo'<div class="error">
                <h3>'.__('Woocommerce plugin is required to use the plugin [Rent angry cats] !', 'rent_angry_cat').'</h3>
             </div>';
    } );
}

















