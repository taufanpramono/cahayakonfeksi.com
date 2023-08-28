<?php

/**
 * This class catches the admin_init hook and decide which controller 
 * file to load based on the query string.
 */

class WPTWA_Controller {
	
	public function __construct () {
		
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'getController' ) );
		}
	}
	
	public function getController () {
		$page = isset( $_GET['page'] ) ? strtolower( $_GET['page'] ) : '';
		$prefix = WPTWA_PREFIX . '_';
		$file_name = substr( $page, 0, strlen( $prefix ) ) === $prefix
			? substr( $page, strlen( $prefix ), strlen( $page ) ) 
			: $page
			;
		
		$path_to_controller = WPTWA_PLUGIN_DIR . 'controller/' . $file_name . '.php';
		
		if ( file_exists( $path_to_controller ) ) {
			include_once( $path_to_controller );
		}
		
	}
	
}

?>