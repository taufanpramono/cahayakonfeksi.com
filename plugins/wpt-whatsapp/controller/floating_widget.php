<?php

$tab = isset( $_GET['tab'] ) ? strtolower( $_GET['tab'] ) : '';

switch ( $tab ) {
	case 'display_settings':
		include_once( 'floating_widget_display_settings.php' );
		break;
	case 'auto_display':
		include_once( 'floating_widget_auto_display.php' );
		break;
	case 'consent_confirmation':
		include_once( 'floating_widget_consent_confirmation.php' );
		break;
	default :
		include_once( 'floating_widget_selected_accounts.php' );
		break;
}

?>