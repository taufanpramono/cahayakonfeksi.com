<?php

class WPTWA_Activation {
	
	public function __construct () {
		
		if ( is_admin() ) {
			register_activation_hook( WPTWA_PLUGIN_BOOTSTRAP_FILE, array( $this, 'activation' ) );
		}
		
		add_action( 'plugins_loaded', array( $this, 'loadTextDomain' ) );
		
	}
	
	public function activation () {
		
		/* Add options to WordPress specific for WPTWA */
		if ( ! get_option( WPTWA_SETTINGS_NAME ) ) {
			WPTWA_Utils::prepeareSettings();
			WPTWA_Utils::updateSetting( 'toggle_text', esc_html__( 'Chat with us on WhatsApp', 'wptwa' ) );
			WPTWA_Utils::updateSetting( 'toggle_text_color', 'rgba(255, 255, 255, 1)' );
			WPTWA_Utils::updateSetting( 'toggle_background_color', '#0DC152' );
			WPTWA_Utils::updateSetting( 'description', esc_html__( 'Hi there! Click one of our representatives below and we will get back to you as soon as possible.', 'wptwa' ) );
			WPTWA_Utils::updateSetting( 'mobile_close_button_text', esc_html__( 'Close and go back to page', 'wptwa' ) );
			WPTWA_Utils::updateSetting( 'container_text_color', 'rgba(85, 85, 85, 1)' );
			WPTWA_Utils::updateSetting( 'container_background_color', 'rgba(255, 255, 255, 1)' );
			WPTWA_Utils::updateSetting( 'account_hover_background_color', 'rgba(245, 245, 245, 1)' );
			WPTWA_Utils::updateSetting( 'account_hover_text_color', 'rgba(85, 85, 85, 1)' );
			WPTWA_Utils::updateSetting( 'border_color_between_accounts', '#f5f5f5' );
			WPTWA_Utils::updateSetting( 'box_position', 'right' );
			
			WPTWA_Utils::updateSetting( 'consent_alert_background_color', 'rgba(255, 0, 0, 1)' );
			
			WPTWA_Utils::updateSetting( 'button_label', 'Need help? Chat via WhatsApp' );
			WPTWA_Utils::updateSetting( 'button_background_color', '#0DC152' );
			WPTWA_Utils::updateSetting( 'button_text_color', '#ffffff' );
			WPTWA_Utils::updateSetting( 'button_background_color_on_hover', '#0DC152' );
			WPTWA_Utils::updateSetting( 'button_text_color_on_hover', '#ffffff' );
			
			WPTWA_Utils::updateSetting( 'button_background_color_offline', '#a0a0a0' );
			WPTWA_Utils::updateSetting( 'button_text_color_offline', '#ffffff' );
			
			WPTWA_Utils::updateSetting( 'hide_on_large_screen', 'off' );
			WPTWA_Utils::updateSetting( 'hide_on_small_screen', 'off' );
			
			WPTWA_Utils::updateSetting( 'delay_time', '0' );
			WPTWA_Utils::updateSetting( 'inactivity_time', '0' );
			WPTWA_Utils::updateSetting( 'scroll_length', '0' );
			
			WPTWA_Utils::updateSetting( 'total_accounts_shown', '0' );
			
			WPTWA_Utils::generateCustomCSS();
		}
		else {
			WPTWA_Utils::generateCustomCSS();
		}
		
	}
	
	public function loadTextDomain () {
		load_plugin_textdomain( 'wptwa', false, plugin_basename( WPTWA_PLUGIN_DIR ) . '/languages' );
	}
	
}

?>