<?php

class WPTWA_Ajax {
	
	public function __construct () {
		
		add_action( 'wp_ajax_wptwa_search_posts', array( $this, 'searchPost' ) );
		add_action( 'wp_ajax_wptwa_search_accounts', array( $this, 'searchAccounts' ) );
		
	}
	
	public function searchPost(  ) {
		
		check_ajax_referer( 'wptwa-search-nonce', 'security' );
		$title = sanitize_text_field( $_POST['title'] );
		
		$html = '';
		
		if ( filter_var( $_POST['title'], FILTER_VALIDATE_URL ) !== FALSE ) {
			$the_url = esc_url( $_POST['title'] );
			$html.= '<li data-id="' . $the_url . '">
					<span class="wptwa-title">' . $the_url . '</span>
				</li>';
		}
		else {
			global $post;
			$args = array(
				'posts_per_page' => 50,
				's' => $title,
				'post_type' => 'any'
			);
			
			$result = get_posts( $args );
			
			foreach ( $result as $post ) {
				setup_postdata( $post );
				
				$post_title = '' !== get_the_title() ? get_the_title() : sprintf( esc_html__( '[No title with ID: %s]', 'wptwa' ), get_the_ID() );
				$html.= '<li data-id="' . get_the_ID() . '">
					<span class="wptwa-title">' . $post_title . '</span>
					<span class="wptwa-permalink">' . esc_url( get_the_permalink() ) . '</span>
				</li>';
			}
			wp_reset_postdata();
		}
		
		if ( '' === $html ) {
			$html.= '<li data-id="">' . esc_html__( 'No Result', 'wptwa' ) . '</li>';
		}
		
		echo $html;
		
		wp_die();
		
	}
	
	public function searchAccounts(  ) {
		
		check_ajax_referer( 'wptwa-search-nonce', 'security' );
		$title = sanitize_text_field( $_POST['title'] );
		
		global $post;
		$args = array(
			'posts_per_page' => 50,
			's' => $title,
			'post_type' => 'wptwa_accounts'
		);
				
		$result = get_posts( $args );
		$html = '';
		
		foreach ( $result as $post ) {
			setup_postdata( $post );
			
			$name = get_post_meta( $post->ID, 'wptwa_name', true );
			$account_title = get_post_meta( $post->ID, 'wptwa_title', true );
			$avatar = get_the_post_thumbnail_url( $post->ID )
				? get_the_post_thumbnail_url( $post->ID )
				: WPTWA_PLUGIN_URL . 'assets/images/logo-green-small.png';
				
			
			$post_title = '' !== get_the_title() ? get_the_title() : sprintf( esc_html__( '[No title with ID: %s]', 'wptwa' ), get_the_ID() );
			
			$html.= '<div class="wptwa-item wptwa-clearfix" data-id="' . get_the_ID() . '" data-name-title="' . esc_attr( $name . ' / ' . $account_title ) . '" data-remove-label="' . esc_attr__( 'Remove', 'wptwa' ) . '">
						<div class="wptwa-avatar"><img src="' . $avatar . '" alt=""/></div>
						<div class="wptwa-info wptwa-clearfix">
							<div class="wptwa-title">' . $post_title . '</div>
							<div class="wptwa-meta">
								' . $name . ' / ' . $account_title . '
							</div>
						</div>
					</div>';
		}
		wp_reset_postdata();
		
		if ( '' === $html ) {
			$html.= '<div class="wptwa-item wptwa-clearfix">' . esc_html__( 'No Result', 'wptwa' ) . '</div>';
		}
		
		echo $html;
		
		wp_die();
		
	}
	
}

?>