<?php

/* Stop immediately if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( isset( $_POST['wptwa_auto_display'] ) ) {
	
	$legit = true;
	
	if ( ! isset( $_POST['wptwa_auto_display_form_nonce'] ) ) {
		$legit = false;
	}
	
	$nonce = isset( $_POST['wptwa_auto_display_form_nonce'] ) ? $_POST['wptwa_auto_display_form_nonce'] : '';
	
	if ( ! wp_verify_nonce( $nonce, 'wptwa_auto_display_form' ) ) {
		$legit = false;
	}
	
	if ( ! $legit ) {
		wp_redirect( add_query_arg() );
		exit();
	}
	
	$delay_time = isset( $_POST['delay_time'] ) ? sanitize_text_field( trim( $_POST['delay_time'] ) ) : '';
	$inactivity_time = isset( $_POST['inactivity_time'] ) ? sanitize_text_field( trim( $_POST['inactivity_time'] ) ) : '';
	$scroll_length = isset( $_POST['scroll_length'] ) ? sanitize_text_field( trim( $_POST['scroll_length'] ) ) : '';
	$disable_auto_display_on_small_screen = isset( $_POST['disable_auto_display_on_small_screen'] ) ? 'on' : 'off';
	$disable_auto_display_when_no_one_online = isset( $_POST['disable_auto_display_when_no_one_online'] ) ? 'on' : 'off';
	
	WPTWA_Utils::updateSetting( 'delay_time', $delay_time );
	WPTWA_Utils::updateSetting( 'inactivity_time', $inactivity_time );
	WPTWA_Utils::updateSetting( 'scroll_length', $scroll_length );
	WPTWA_Utils::updateSetting( 'disable_auto_display_on_small_screen', $disable_auto_display_on_small_screen );
	WPTWA_Utils::updateSetting( 'disable_auto_display_when_no_one_online', $disable_auto_display_when_no_one_online );
	
	add_settings_error( 'wptwa-settings', 'wptwa-settings', __( 'Auto display saved', 'wptwa' ), 'updated' );
}

WPTWA_Utils::setView( 'floating_widget_auto_display' );

?>