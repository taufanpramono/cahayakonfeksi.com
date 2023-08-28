<?php

if ( isset( $_POST['wptwa_woocommerce_button'] ) ) {
	$legit = true;
	
	/* Check if our nonce is set. */
	if ( ! isset( $_POST['wptwa_woocommerce_button_form_nonce'] ) ) {
		$legit = false;
	}
	
	$nonce = $_POST['wptwa_woocommerce_button_form_nonce'];
	
	/* Verify that the nonce is valid. */
	if ( ! wp_verify_nonce( $nonce, 'wptwa_woocommerce_button_form' ) ) {
		$legit = false;
	}
	
	/* 	Something is wrong with the nonce. Redirect it to the 
		settings page without processing any data.
		*/
	if ( ! $legit ) {
		wp_redirect( add_query_arg() );
		exit();
	}
	
	$wc_button_position = isset( $_POST['wc_button_position'] ) ? sanitize_text_field( trim( $_POST['wc_button_position'] ) ) : '';
	$wc_randomize_accounts_order = isset( $_POST['wc_randomize_accounts_order'] ) ? 'on' : 'off';
	$wc_total_accounts_shown = isset( $_POST['wc_total_accounts_shown'] ) ? ( int ) sanitize_text_field( trim( $_POST['wc_total_accounts_shown'] ) ) : 0;
	
	WPTWA_Utils::updateSetting( 'wc_button_position', $wc_button_position );
	WPTWA_Utils::updateSetting( 'wc_randomize_accounts_order', $wc_randomize_accounts_order );
	WPTWA_Utils::updateSetting( 'wc_total_accounts_shown', $wc_total_accounts_shown );
	
	$ids = array();
	$the_posts = isset( $_POST['wptwa_selected_account'] ) ? array_values( array_unique( $_POST['wptwa_selected_account'] ) ) : array();
	foreach ( $the_posts as $k => $v ) {
		$ids[] = ( int ) $v;
	}
	
	WPTWA_Utils::updateSetting( 'selected_accounts_for_woocommerce', json_encode( $ids ) );
	
	add_settings_error( 'wptwa-settings', 'wptwa-settings', __( 'WooCommerce button saved', 'wptwa' ), 'updated' );
	
}

WPTWA_Utils::setView( 'woocommerce_button' );

?>