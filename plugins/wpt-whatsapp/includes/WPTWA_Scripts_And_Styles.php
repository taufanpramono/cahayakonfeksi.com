<?php

class WPTWA_Scripts_And_Styles {
	
	public function __construct () {
		
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueueScripts' ) );
		}
		
	}
	
	/**
	 * Enqueue scripts and styles only for our plugin.
	 */
	public function adminEnqueueScripts ( $hook ) {
		
		global $pagenow;
		
		$settings_pages = array(
			WPTWA_PREFIX . '_settings',
			WPTWA_PREFIX . '_floating_widget',
			WPTWA_PREFIX . '_woocommerce_button'
		);
		
		$plugin_data = get_file_data( WPTWA_PLUGIN_BOOTSTRAP_FILE, array( 'version' ) );
		$plugin_version = isset( $plugin_data[0] ) ? $plugin_data[0] : false;
		
		if ( ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && in_array( strtolower( $_GET['page'] ), $settings_pages ) ) || 
				'wptwa_accounts' === get_post_type() ) {
			
			wp_enqueue_media();
			
			wp_enqueue_style( 'jquery-minicolors', WPTWA_PLUGIN_URL . 'assets/css/jquery-minicolors.css', array(), $plugin_version );
			wp_enqueue_style( 'wptwa-admin', WPTWA_PLUGIN_URL . 'assets/css/admin.css', array(), $plugin_version );
			
			wp_enqueue_script( 'jquery-minicolors', WPTWA_PLUGIN_URL . 'assets/js/vendor/jquery.minicolors.min.js', array( 'jquery' ), $plugin_version, true );
			wp_enqueue_script( 'wptwa-admin', WPTWA_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), $plugin_version, true );
		}
		
	}
	
}

?>