<div class="wrap">
	
	<?php include_once( 'floating_widget_header.php' ); ?>
	
	<form action="" method="post" novalidate="novalidate">
		
		<p><?php esc_html_e( 'The fields below should have a numeric value of more than 0 for the feature to work.', 'wptwa' ); ?></p>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="delay_time"><?php esc_html_e( 'Delay Time', 'wptwa' ); ?></label></th>
					<td>
						<input name="delay_time" type="number" min="0" max="999" id="delay_time" value="<?php echo filter_var( WPTWA_Utils::getSetting( 'delay_time' ), FILTER_SANITIZE_NUMBER_INT ); ?>"> <?php esc_html_e( 'second(s)', 'wptwa' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="inactivity_time"><?php esc_html_e( 'Inactivity Time', 'wptwa' ); ?></label></th>
					<td>
						<input name="inactivity_time" type="number" min="0" max="999" id="inactivity_time" value="<?php echo filter_var( WPTWA_Utils::getSetting( 'inactivity_time' ), FILTER_SANITIZE_NUMBER_INT ); ?>"> <?php esc_html_e( 'second(s)', 'wptwa' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="scroll_length"><?php esc_html_e( 'Scroll Length', 'wptwa' ); ?></label></th>
					<td>
						<input name="scroll_length" type="number" min="0" max="100" id="scroll_length" value="<?php echo filter_var( WPTWA_Utils::getSetting( 'scroll_length' ), FILTER_SANITIZE_NUMBER_INT ); ?>">  <?php esc_html_e( '%', 'wptwa' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="disable_auto_display_on_small_screen"><?php esc_html_e( 'Disable on mobile', 'wptwa' ); ?></label></th>
					<td>
						<input name="disable_auto_display_on_small_screen" type="checkbox" id="disable_auto_display_on_small_screen" value="on" <?php echo 'on' === WPTWA_Utils::getSetting( 'disable_auto_display_on_small_screen' ) ? 'checked' : ''; ?>>  <label for="disable_auto_display_on_small_screen"><?php esc_html_e( 'Yes, disable auto display on small screen.', 'wptwa' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="disable_auto_display_when_no_one_online"><?php esc_html_e( 'Disable when no one is online', 'wptwa' ); ?></label></th>
					<td>
						<input name="disable_auto_display_when_no_one_online" type="checkbox" id="disable_auto_display_when_no_one_online" value="on" <?php echo 'on' === WPTWA_Utils::getSetting( 'disable_auto_display_when_no_one_online' ) ? 'checked' : ''; ?>>  <label for="disable_auto_display_when_no_one_online"><?php esc_html_e( 'Yes, disable auto display when no one is online.', 'wptwa' ); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php wp_nonce_field( 'wptwa_auto_display_form', 'wptwa_auto_display_form_nonce' ); ?>
		<input type="hidden" name="wptwa_auto_display" value="submit" />
		<input type="hidden" name="submit" value="submit" />
		<p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Auto Display', 'wptwa' ); ?>"></p>
		
	</form>
</div>