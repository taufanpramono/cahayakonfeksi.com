<?php

class WPTWA_Templates {
	
	static public function displaySelectedAccounts ( $category, $product_id = 0 ) {
		$selected_accounts = json_decode( WPTWA_Utils::getSetting( $category, '' ), true );
		
		if ( 'selected_accounts_for_product' === $category ) {
			$selected_accounts = json_decode( get_post_meta( $product_id, 'wptwa_selected_accounts', true ) );
		}
		
		$selected_accounts_html = '';
		
		$selected_accounts = is_array( $selected_accounts ) ? $selected_accounts : array();
		
		if ( count( $selected_accounts ) > 0 ) {
			global $post;
			$the_accounts = get_posts( array(
				'posts_per_page' => -1,
				'post__in' => $selected_accounts,
				'post_type' => 'wptwa_accounts',
				'orderby' => 'post__in'
			) );

			foreach ( $the_accounts as $post ) {
				setup_postdata( $post );
				
				$name = get_post_meta( $post->ID, 'wptwa_name', true );
				$account_title = get_post_meta( $post->ID, 'wptwa_title', true );
				$avatar = get_the_post_thumbnail_url( $post->ID )
					? get_the_post_thumbnail_url( $post->ID )
					: WPTWA_PLUGIN_URL . 'assets/images/logo-green-small.png';
					
				
				$post_title = '' !== get_the_title() ? get_the_title() : sprintf( esc_html__( '[No title with ID: %s]', 'wptwa' ), get_the_ID() );
				
				$selected_accounts_html.= '<div class="wptwa-item wptwa-clearfix" data-id="' . get_the_ID() . '" data-name-title="' . esc_attr( $name . ' / ' . $account_title ) . '" >
								<div class="wptwa-avatar"><img src="' . $avatar . '" alt=""/></div>
								<div class="wptwa-info wptwa-clearfix">
									<a href="post.php?post=' . get_the_ID() . '&action=edit" target="_blank" class="wptwa-title">' . $post_title . '</a>
									<div class="wptwa-meta">
										' . $name . ' / ' . $account_title . ' <br/>
										<span class="wptwa-remove-account">' . esc_html__( 'Remove', 'wptwa' ) . '</span>
									</div>
								</div>
								<div class="wptwa-updown"><span class="wptwa-up dashicons dashicons-arrow-up-alt2"></span><span class="wptwa-down dashicons dashicons-arrow-down-alt2"></span></div>
								<input type="hidden" name="wptwa_selected_account[]" value="' . get_the_ID() . '"/>
							</div>';
				
			}
			wp_reset_postdata();
		}
		?>
		<div class="wptwa-account-search">
			<div class="wptwa-search-box">
				<input type="text" class="widefat" placeholder="<?php esc_attr_e( 'Type the title of the accounts you want to display', 'wptwa' ); ?>"  data-nonce="<?php echo wp_create_nonce( 'wptwa-search-nonce' ); ?>" />
			</div>
			<div class="wptwa-account-list"></div>
		</div>

		<div class="wptwa-account-result">
			<h4><?php esc_html_e( 'Selected Accounts:', 'wptwa' ); ?></h4>
			<div class="wptwa-account-list"><?php echo $selected_accounts_html; ?></div>
		</div>
		<?php
	}
	
}

?>