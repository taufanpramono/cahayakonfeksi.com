<?php

/* Stop immediately if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( isset( $_POST['wptwa_settings'] ) ) {
	
	$legit = true;
	
	/* Check if our nonce is set. */
	if ( ! isset( $_POST['wptwa_settings_form_nonce'] ) ) {
		$legit = false;
	}
	
	$nonce = $_POST['wptwa_settings_form_nonce'];
	
	/* Verify that the nonce is valid. */
	if ( ! wp_verify_nonce( $nonce, 'wptwa_settings_form' ) ) {
		$legit = false;
	}
	
	/* 	Something is wrong with the nonce. Redirect it to the 
		settings page without processing any data.
		*/
	if ( ! $legit ) {
		wp_redirect( add_query_arg() );
		exit();
	}
	
	$button_label = isset( $_POST['button_label'] ) ? sanitize_text_field( trim( $_POST['button_label'] ) ) : '';
	$button_style = isset( $_POST['button_style'] ) ? sanitize_text_field( trim( $_POST['button_style'] ) ) : '';
	$button_background_color = isset( $_POST['button_background_color'] ) ? sanitize_text_field( trim( $_POST['button_background_color'] ) ) : '';
	$button_text_color = isset( $_POST['button_text_color'] ) ? sanitize_text_field( trim( $_POST['button_text_color'] ) ) : '';
	$button_background_color_on_hover = isset( $_POST['button_background_color_on_hover'] ) ? sanitize_text_field( trim( $_POST['button_background_color_on_hover'] ) ) : '';
	$button_text_color_on_hover = isset( $_POST['button_text_color_on_hover'] ) ? sanitize_text_field( trim( $_POST['button_text_color_on_hover'] ) ) : '';
	
	$button_background_color_offline = isset( $_POST['button_background_color_offline'] ) ? sanitize_text_field( trim( $_POST['button_background_color_offline'] ) ) : '';
	$button_text_color_offline = isset( $_POST['button_text_color_offline'] ) ? sanitize_text_field( trim( $_POST['button_text_color_offline'] ) ) : '';
	
	WPTWA_Utils::updateSetting( 'button_label', $button_label );
	WPTWA_Utils::updateSetting( 'button_style', $button_style );
	WPTWA_Utils::updateSetting( 'button_background_color', $button_background_color );
	WPTWA_Utils::updateSetting( 'button_text_color', $button_text_color );
	WPTWA_Utils::updateSetting( 'button_background_color_on_hover', $button_background_color_on_hover );
	WPTWA_Utils::updateSetting( 'button_text_color_on_hover', $button_text_color_on_hover );
	
	WPTWA_Utils::updateSetting( 'button_background_color_offline', $button_background_color_offline );
	WPTWA_Utils::updateSetting( 'button_text_color_offline', $button_text_color_offline );
	
	/* Recreate CSS file */
	WPTWA_Utils::generateCustomCSS();
	
	add_settings_error( 'wptwa-settings', 'wptwa-settings', __( 'Settings saved', 'wptwa' ), 'updated' );
}

WPTWA_Utils::setView( 'settings' );

?>