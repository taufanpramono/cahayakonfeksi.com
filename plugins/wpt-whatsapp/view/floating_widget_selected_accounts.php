<div class="wrap">
	
	<?php include_once( 'floating_widget_header.php' ); ?>
	
	<form action="" method="post" novalidate="novalidate">
		
		<p><?php esc_html_e( 'Select one or more accounts to display on the floating widget.', 'wptwa' ); ?></p>
		
		<?php WPTWA_Templates::displaySelectedAccounts( 'selected_accounts_for_widget' ); ?>
		
		<?php wp_nonce_field( 'wptwa_selected_accounts_form', 'wptwa_selected_accounts_form_nonce' ); ?>
		<input type="hidden" name="wptwa_selected_accounts" value="submit" />
		<input type="hidden" name="submit" value="submit" />
		<p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Selected Accounts', 'wptwa' ); ?>"></p>
		
	</form>
</div>