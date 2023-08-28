<?php

function wptwa_is_active ( $tab ) {
	$get = isset( $_GET['tab'] ) ? strtolower( $_GET['tab'] ) : '';
	
	if ( $get === $tab || ( '' === $get && 'selected_accounts' === $tab ) ) {
		echo ' nav-tab-active ';
	}
}

?>

<h1><?php esc_html_e( 'Floating Widget', 'wptwa' ); ?></h1>

<?php settings_errors(); ?>

<h2 class="nav-tab-wrapper">
	<a href="?page=wptwa_floating_widget&tab=selected_accounts" class="nav-tab <?php wptwa_is_active( 'selected_accounts' ); ?>"><?php esc_html_e( 'Selected Accounts', 'wptwa' ); ?></a>
	<a href="?page=wptwa_floating_widget&tab=display_settings" class="nav-tab <?php wptwa_is_active( 'display_settings' ); ?>"><?php esc_html_e( 'Display Settings', 'wptwa' ); ?></a>
	<a href="?page=wptwa_floating_widget&tab=auto_display" class="nav-tab <?php wptwa_is_active( 'auto_display' ); ?>"><?php esc_html_e( 'Auto Display', 'wptwa' ); ?></a>
	<a href="?page=wptwa_floating_widget&tab=consent_confirmation" class="nav-tab <?php wptwa_is_active( 'consent_confirmation' ); ?>"><?php esc_html_e( 'Consent Confirmation', 'wptwa' ); ?></a>
</h2>