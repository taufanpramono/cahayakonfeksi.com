<?php

class WPTWA_WooCommerce {
	
	public function __construct () {
		
		if ( is_admin() ) {
			add_action( 'add_meta_boxes', array( $this, 'addMetaBoxes' ) );
			add_action( 'save_post', array( $this, 'saveMetaBoxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueueScripts' ) );
		}
		else {
			add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'showBeforeATC' ) );
			add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'showAfterATC' ) );
			
			if ( 'after_long_description' === WPTWA_Utils::getSetting( 'wc_button_position' ) ) {
				add_filter( 'the_content', array( $this, 'showAfterLongDescription' ) );
			}
			if ( 'after_short_description' === WPTWA_Utils::getSetting( 'wc_button_position' ) ) {
				add_filter( 'woocommerce_short_description', array( $this, 'showAfterShortDescription' ), 10, 1 );
			}
		}
		
	}
	
	public function showBeforeATC () {
		
		if ( 'before_atc' !== WPTWA_Utils::getSetting( 'wc_button_position' ) || 'on' == get_post_meta( get_the_ID(), 'wptwa_remove_button', true ) ) {
			return;
		}
		echo $this->setContainer();
	}
	
	public function showAfterATC () {
		
		if ( 'after_atc' !== WPTWA_Utils::getSetting( 'wc_button_position' ) || 'on' == get_post_meta( get_the_ID(), 'wptwa_remove_button', true ) ) {
			return;
		}
		echo $this->setContainer();
	}
	
	public function showAfterLongDescription ( $content ) {
		if ( 'product' !== get_post_type() 
				|| ! is_single() 
				|| 'on' === get_post_meta( get_the_ID(), 'wptwa_remove_button', true ) 
			) {
			return $content;
		}
		
		return $content . $this->setContainer();
	}
	
	public function showAfterShortDescription ( $post_excerpt ) {
		
		if ( 'after_short_description' !== WPTWA_Utils::getSetting( 'wc_button_position' ) 
				|| 'on' === get_post_meta( get_the_ID(), 'wptwa_remove_button', true ) 
				|| ! is_single()
			) {
			return $post_excerpt;
		}
		return $post_excerpt . $this->setContainer();
	}
	
	private function setContainer () {
		
		$selected_accounts = json_decode( WPTWA_Utils::getSetting( 'selected_accounts_for_woocommerce', '[]' ), true );
		$selected_accounts = is_array( $selected_accounts ) ? $selected_accounts : array();
		
		$custom_accounts = json_decode( get_post_meta( get_the_ID(), 'wptwa_selected_accounts', true ) );
		$custom_accounts = is_array( $custom_accounts ) ? $custom_accounts : array();
		if ( count( $custom_accounts ) > 0 ) {
			$selected_accounts = $custom_accounts;
		}
		
		/*
		$result = array();
		
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
				$result[] = do_shortcode( '[whatsapp_button id="' . $post->ID . '"]' );
			}
			wp_reset_postdata();
		}
		*/
		$page_title = get_the_title();
		$page_url = get_permalink();
		
		return '<div class="wptwa-wc-buttons-container" data-ids="' . implode( ',', $selected_accounts ) . '" data-page-title="' . $page_title . '" data-page-url="' . $page_url . '"></div>';
		
		//return implode( '', $result );
		
	}
	
	public function addMetaBoxes () {
		
		add_meta_box(
			'wptwa_wc_button',
			esc_html__( 'WhatsApp Contact Button', 'wptwa' ),
			array( $this, 'showMetaBox' ),
			array( 'product' )
		);
		
	}
	
	public function showMetaBox ( $post ) {
		
		?>
		<p class="description"><?php esc_html_e( 'You can set a custom WhatsApp button for this product. Leave the following fields blank if you wish to use the default values.', 'wptwa' ); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Remove Button', 'wptwa' ); ?></th>
					<td>
						<input type="checkbox" name="wptwa_remove_button" id="wptwa_remove_button" value="on" <?php echo 'on' === strtolower( get_post_meta( $post->ID, 'wptwa_remove_button', true ) ) ? 'checked' : ''; ?> /> <label for="wptwa_remove_button"><?php esc_html_e( 'Remove WhatsApp button for this product', 'wptwa' ); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<table class="form-table" id="wptwa-custom-wc-button-settings">
			<tbody>
				<tr>
					<th><label for="wptwa_account_number"><?php esc_html_e( 'Selected Accounts', 'wptwa' ); ?></label></th>
					<td><?php WPTWA_Templates::displaySelectedAccounts( 'selected_accounts_for_product', get_the_ID() ); ?></td>
				</tr>
			</tbody>
		</table>
		
		<?php
		
		wp_nonce_field( 'wptwa_wc_meta_box', 'wptwa_wc_meta_box_nonce' );
		
	}
	
	public function saveMetaBoxes ( $post_id ) {
		
		/* Check if our nonce is set. */
		if ( ! isset( $_POST['wptwa_wc_meta_box_nonce'] ) ) {
			return;
		}
		
		$nonce = $_POST['wptwa_wc_meta_box_nonce'];
		
		/* Verify that the nonce is valid. */
		if ( ! wp_verify_nonce( $nonce, 'wptwa_wc_meta_box' ) ) {
			return;
		}
		
		$remove_button = isset( $_POST['wptwa_remove_button'] ) ? 'on' : 'off';
		$ids = array();
		$the_posts = isset( $_POST['wptwa_selected_account'] ) ? array_values( array_unique( $_POST['wptwa_selected_account'] ) ) : array();
		foreach ( $the_posts as $k => $v ) {
			$ids[] = ( int ) $v;
		}
		
		update_post_meta( $post_id, 'wptwa_selected_accounts', json_encode( $ids ));
		update_post_meta( $post_id, 'wptwa_remove_button', $remove_button);
		
	}
	
	public function adminEnqueueScripts ( $hook ) {
		
		if ( 'post.php' != $hook || 'product' != get_current_screen()->post_type ) {
			return;
		}
		wp_enqueue_script( 'wptwa-public', WPTWA_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), false, true );
		wp_enqueue_style( 'wptwa-admin', WPTWA_PLUGIN_URL . 'assets/css/admin.css' );
	}
	
}

?>