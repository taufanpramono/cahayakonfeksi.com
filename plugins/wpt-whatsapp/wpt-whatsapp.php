<?php

/**
 * Plugin Name: WhatsApp Click to Chat
 * Plugin URI:  https://codecanyon.net/item/whatsapp-click-to-chat-for-wordpress/20248537
 * Description: A simple tool to display WhatsApp accounts on your site for users to click and chat with.
 * Version:     2.2.5
 * Author:      Indie Plugins
 * Author URI:  https://codecanyon.net/user/indie_plugins/portfolio/?ref=indie_plugins
 * License:     GPLv2 or later
 * Text Domain: wptwa
 */

/* Stop immediately if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/* All constants should be defined in this file. */
if ( ! defined( 'WPTWA_PREFIX' ) ) {
	define( 'WPTWA_PREFIX', 'wptwa' );
}
if ( ! defined( 'WPTWA_PLUGIN_DIR' ) ) {
	define( 'WPTWA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WPTWA_PLUGIN_BASENAME' ) ) {
	define( 'WPTWA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'WPTWA_PLUGIN_URL' ) ) {
	define( 'WPTWA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'WPTWA_SETTINGS_NAME' ) ) {
	define( 'WPTWA_SETTINGS_NAME', 'wptwa_settings' );
}
if ( ! defined( 'WPTWA_PLUGIN_BOOTSTRAP_FILE' ) ) {
	define( 'WPTWA_PLUGIN_BOOTSTRAP_FILE', __FILE__ );
}

/* Auto-load all the necessary classes. */
if( ! function_exists( 'wptwa_class_auto_loader' ) ) {
	
	function wptwa_class_auto_loader( $class ) {
		
		$includes = WPTWA_PLUGIN_DIR . 'includes/' . $class . '.php';
		
		if( is_file( $includes ) && ! class_exists( $class ) ) {
			include_once( $includes );
			return;
		}
		
	}
}
spl_autoload_register('wptwa_class_auto_loader');

/* Initialize all modules now. */
new WPTWA_Display();
new WPTWA_Shortcode();
new WPTWA_Activation();
new WPTWA_Scripts_And_Styles();
new WPTWA_Menu_Link();
new WPTWA_Accounts();
new WPTWA_Controller();
new WPTWA_Ajax();
new WPTWA_WooCommerce();

?>