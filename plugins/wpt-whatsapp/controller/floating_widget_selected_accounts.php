<?php

/* Stop immediately if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( isset( $_POST['wptwa_selected_accounts'] ) ) {
	
	$legit = true;
	
	/* Check if our nonce is set. */
	if ( ! isset( $_POST['wptwa_selected_accounts_form_nonce'] ) ) {
		$legit = false;
	}
	
	$nonce = isset( $_POST['wptwa_selected_accounts_form_nonce'] ) ? $_POST['wptwa_selected_accounts_form_nonce'] : '';
	
	/* Verify that the nonce is valid. */
	if ( ! wp_verify_nonce( $nonce, 'wptwa_selected_accounts_form' ) ) {
		$legit = false;
	}
	
	/* 	Something is wrong with the nonce. Redirect it to the 
		settings page without processing any data.
		*/
	if ( ! $legit ) {
		wp_redirect( add_query_arg() );
		exit();
	}
		
	$ids = array();
	$the_posts = isset( $_POST['wptwa_selected_account'] ) ? array_values( array_unique( $_POST['wptwa_selected_account'] ) ) : array();
	foreach ( $the_posts as $k => $v ) {
		$ids[] = ( int ) $v;
	}
	
	WPTWA_Utils::updateSetting( 'selected_accounts_for_widget', json_encode( $ids ) );
	
	add_settings_error( 'wptwa-settings', 'wptwa-settings', __( 'Selected accounts saved', 'wptwa' ), 'updated' );
}

WPTWA_Utils::setView( 'floating_widget_selected_accounts' );

?>