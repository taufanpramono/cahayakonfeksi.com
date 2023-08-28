<div class="wrap">
	<h1><?php esc_html_e( 'WooCommerce Button', 'wptwa' ); ?></h1>
	<?php settings_errors(); ?>
	
	<form action="" method="post" novalidate="novalidate">
		
		<p><?php esc_html_e( 'Use the form below to automatically display buttons on WooCommerce product page.', 'wptwa' ); ?></p>
		
		<table class="form-table wptwa-account-item">
			<tbody>
				<tr>
					<th scope="row"><label for="wc_button_position"><?php esc_html_e( 'Button position', 'wptwa' ); ?></label></th>
					<td>
						<select name="wc_button_position" id="wc_button_position">
							<option value="after_short_description" <?php selected( 'after_short_description', WPTWA_Utils::getSetting( 'wc_button_position' ), true); ?>><?php esc_html_e( 'After short description', 'wptwa' ); ?></option>
							<option value="after_long_description" <?php selected( 'after_long_description', WPTWA_Utils::getSetting( 'wc_button_position' ), true); ?>><?php esc_html_e( 'After long description', 'wptwa' ); ?></option>
							<option value="before_atc" <?php selected( 'before_atc', WPTWA_Utils::getSetting( 'wc_button_position' ), true); ?>><?php esc_html_e( 'Before Add to Cart button', 'wptwa' ); ?></option>
							<option value="after_atc" <?php selected( 'after_atc', WPTWA_Utils::getSetting( 'wc_button_position' ), true); ?>><?php esc_html_e( 'After Add to Cart button', 'wptwa' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wc_randomize_accounts_order"><?php esc_html_e( 'Randomize Accounts Order', 'wptwa' ); ?></label></th>
					<td>
						<p><input type="checkbox" name="wc_randomize_accounts_order" value="on" id="wc_randomize_accounts_order" <?php checked( 'on', WPTWA_Utils::getSetting( 'wc_randomize_accounts_order' ), true ); ?> /> <label for="wc_randomize_accounts_order"><?php esc_html_e( 'Yes, randomize the order of accounts', 'wptwa' ); ?></label></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wc_total_accounts_shown"><?php esc_html_e( 'Total accounts shown', 'wptwa' ); ?></label></th>
					<td>
						<p><input type="number" min="0" max="100" name="wc_total_accounts_shown" value="<?php echo filter_var( WPTWA_Utils::getSetting( 'wc_total_accounts_shown' ), FILTER_SANITIZE_NUMBER_INT ); ?>" id="wc_total_accounts_shown" /> </p>
						<p class="description"><?php esc_html_e( "If the value is zero (0), then all the selected accounts will be displayed.", "wptwa" );?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="selected_accounts"><?php esc_html_e( 'Select accounts to display', 'wptwa' ); ?></label></th>
					<td><?php WPTWA_Templates::displaySelectedAccounts( 'selected_accounts_for_woocommerce' ); ?></td>
				</tr>
			</tbody>
		</table>
		
		<?php wp_nonce_field( 'wptwa_woocommerce_button_form', 'wptwa_woocommerce_button_form_nonce' ); ?>
		<input type="hidden" name="wptwa_woocommerce_button" value="submit" />
		<input type="hidden" name="submit" value="submit" />
		<p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save WooCommerce Button', 'wptwa' ); ?>"></p>
		
	</form>
	
</div>