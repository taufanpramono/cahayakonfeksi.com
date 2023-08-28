<?php

/**
 * Controller: settings.php
 */

/* Stop immediately if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Settings', 'wptwa' ); ?></h1>
	
	<?php settings_errors(); ?>
	
	<form action="" method="post" novalidate="novalidate">
		<p><?php esc_html_e( 'Use this form to set default style for shortcode buttons. You can reset the style for individual button when creating/editing a WhatsApp account.', 'wptwa' ); ?></p>
		<table id="wptwa-default-settings" class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="button_label"><?php esc_html_e( 'Button Label', 'wptwa' ); ?></label></th>
					<td>
						<input name="button_label" type="text" id="button_label" class="regular-text" value="<?php echo esc_attr( WPTWA_Utils::getSetting( 'button_label' ) ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="button_style"><?php esc_html_e( 'Button Style', 'wptwa' ); ?></label></th>
					<td>
						<select name="button_style" id="button_style">
							<option value="boxed" <?php selected( 'boxed', WPTWA_Utils::getSetting( 'button_style' ), true); ?>><?php esc_html_e( 'Boxed', 'wptwa' );?></option>
							<option value="round" <?php selected( 'round', WPTWA_Utils::getSetting( 'button_style' ), true); ?>><?php esc_html_e( 'Round', 'wptwa' );?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="button_background_color"><?php esc_html_e( 'Button Background Color', 'wptwa' ); ?></label></th>
					<td>
						<input name="button_background_color" type="text" id="button_background_color" class="minicolors" value="<?php echo esc_attr( WPTWA_Utils::getSetting( 'button_background_color' ) ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="button_text_color"><?php esc_html_e( 'Button Text Color', 'wptwa' ); ?></label></th>
					<td>
						<input name="button_text_color" type="text" id="button_text_color" class="minicolors" value="<?php echo esc_attr( WPTWA_Utils::getSetting( 'button_text_color' ) ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="button_background_color_on_hover"><?php esc_html_e( 'Button Background Color on Hover', 'wptwa' ); ?></label></th>
					<td>
						<input name="button_background_color_on_hover" type="text" id="button_background_color_on_hover" class="minicolors" value="<?php echo esc_attr( WPTWA_Utils::getSetting( 'button_background_color_on_hover' ) ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="button_text_color_on_hover"><?php esc_html_e( 'Button Text Color on Hover', 'wptwa' ); ?></label></th>
					<td>
						<input name="button_text_color_on_hover" type="text" id="button_text_color_on_hover" class="minicolors" value="<?php echo esc_attr( WPTWA_Utils::getSetting( 'button_text_color_on_hover' ) ); ?>">
					</td>
				</tr>
				
				<tr>
					<th scope="row"><label for="button_background_color_offline"><?php esc_html_e( 'Button Background Color When Offline', 'wptwa' ); ?></label></th>
					<td>
						<input name="button_background_color_offline" type="text" id="button_background_color_offline" class="minicolors" value="<?php echo esc_attr( WPTWA_Utils::getSetting( 'button_background_color_offline' ) ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="button_text_color_offline"><?php esc_html_e( 'Button Text Color When Offline', 'wptwa' ); ?></label></th>
					<td>
						<input name="button_text_color_offline" type="text" id="button_text_color_offline" class="minicolors" value="<?php echo esc_attr( WPTWA_Utils::getSetting( 'button_text_color_offline' ) ); ?>">
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php wp_nonce_field( 'wptwa_settings_form', 'wptwa_settings_form_nonce' ); ?>
		<input type="hidden" name="wptwa_settings" value="submit" />
		<input type="hidden" name="submit" value="submit" />
		<p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'wptwa' ); ?>"></p>
		
	</form>
</div>