<?php

/**
 * This class is loaded on the front-end since its main job is 
 * to display the WhatsApp box.
 */

class WPTWA_Display {
	
	public function __construct () {
		
		add_action( 'wp_ajax_wptwa_display_widget', array( $this, 'displayWidget' ) );
		add_action( 'wp_ajax_nopriv_wptwa_display_widget', array( $this, 'displayWidget' ) );
		
		add_action( 'wp_ajax_wptwa_display_buttons', array( $this, 'displayButtons' ) );
		add_action( 'wp_ajax_nopriv_wptwa_display_buttons', array( $this, 'displayButtons' ) );
		
		if ( is_admin() ) {
			return;
		}
		
		add_action( 'wp_footer', array( $this, 'outputHTML' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wpEnqueueScripts' ), 1 );
	}
	
	public static function isBetweenTime( $from, $till, $input ) {
		$f = DateTime::createFromFormat( '!H:i', $from );
		$t = DateTime::createFromFormat( '!H:i', $till );
		$i = DateTime::createFromFormat( '!H:i', $input );
		if ( $f > $t ) {
			$t->modify( '+1 day' );
		}
		return ( $f <= $i && $i <= $t ) || ( $f <= $i->modify( '+1 day' ) && $i <= $t );
	}
	
	public function displayButtons () {
		
		$ids = isset( $_POST['ids'] ) ? explode( ',', $_POST['ids'] ) : array();
		$page_title = isset( $_POST['page-title'] ) ? esc_attr( $_POST['page-title'] ) : '';
		$page_url = isset( $_POST['page-url'] ) ? esc_url( $_POST['page-url'] ) : '';
		$type = isset( $_POST['type'] ) ? esc_attr( $_POST['type'] ) : '';
		
		$account = get_posts( array(
			'posts_per_page' => -1,
			'post__in' => $ids,
			'post_type' => 'wptwa_accounts'
		) );
		
		if ( count( $account ) < 1 ) {
			echo 'none';
			wp_die();
		}
		
		$pinned_acc = array();
		$online_acc = array();
		$offline_acc = array();
		$item = array();
		$i = 0;
		foreach ( $account as $post ) {
			setup_postdata( $post );
			
			$classes = array( 'wptwa-button', 'wptwa-account', 'wptwa-clearfix' );
			
			$from = get_post_meta( $post->ID, 'wptwa_hour_start', true ) . ':' . get_post_meta( $post->ID, 'wptwa_minute_start', true );
			$till = get_post_meta( $post->ID, 'wptwa_hour_end', true ) . ':' . get_post_meta( $post->ID, 'wptwa_minute_end', true );
			
			$offline_text = get_post_meta( $post->ID, 'wptwa_offline_text', true );
			
			$current_day = strtolower( date( 'l' ) );
			$availability = json_decode( get_post_meta( $post->ID, 'wptwa_availability', true ), true );
			$availability = is_array( $availability ) ? $availability : array();
			
			/* Time and day availability */
			
			if ( 	isset( $availability[ $current_day ] ) && 
					isset( $availability[ $current_day ][ 'hour_start' ] ) && 
					isset( $availability[ $current_day ][ 'minute_start' ] ) && 
					isset( $availability[ $current_day ][ 'hour_end' ] ) && 
					isset( $availability[ $current_day ][ 'minute_end' ] )
				) {
				
				$from = $availability[ $current_day ][ 'hour_start' ] . ':' . $availability[ $current_day ][ 'minute_start' ];
				$till = $availability[ $current_day ][ 'hour_end' ] . ':' . $availability[ $current_day ][ 'minute_end' ];
				
				/* Ignore if time is unavailable */
				if ( ! self::isBetweenTime( $from, $till, current_time( 'H:i' ) ) ) {
					if ( '' === trim( $offline_text ) ) {
						continue;
					}
					else {
						$classes[] = 'wptwa-offline';
					}
				}
				
			}
			else {
				continue;
			}
			
			$item = '';
			
			$number = preg_replace( '/[^0-9]/', '', get_post_meta( $post->ID, 'wptwa_number', true ) );
			$name = get_post_meta( $post->ID, 'wptwa_name', true );
			$title = get_post_meta( $post->ID, 'wptwa_title', true );
			$title = '' !== $title ? ' &nbsp;/&nbsp; ' . $title : '';
			$button_label = get_post_meta( $post->ID, 'wptwa_button_label', true );
			$button_label = '' !== $button_label ? $button_label : WPTWA_Utils::getSetting( 'button_label', esc_html__( 'Need help? Chat via WhatsApp', 'wptwa' ) );
			$predefined_text = get_post_meta( $post->ID, 'wptwa_predefined_text', true );
			$predefined_text = str_ireplace( '[wptwa_page_title]', $page_title, $predefined_text );
			$predefined_text = str_ireplace( '[wptwa_page_url]', $page_url, $predefined_text );
			$predefined_text = str_ireplace( "\r\n", rawurlencode( "\r\n" ), $predefined_text );
			
			$post_title = get_the_title( $post );
			
			$avatar_url = '';
			if ( has_post_thumbnail( $post ) ) {
				$avatar = '<img src="' . get_the_post_thumbnail_url( $post ) . '" alt="' . $name . '"/>';
			}
			else {
				$avatar = '<svg class="WhatsApp" width="40px" height="40px" viewBox="0 0 92 92"><use xlink:href="#wptwa-logo"></svg>';
			}
			
			
			
			if ( 'on' === get_post_meta( $post->ID, 'wptwa_hide_on_large_screen', true ) ) {
				$classes[] = 'wptwa-hide-on-large-screen';
			}
			
			if ( 'on' === get_post_meta( $post->ID, 'wptwa_hide_on_small_screen', true ) ) {
				$classes[] = 'wptwa-hide-on-small-screen';
			}
			
			if ( 'round' === WPTWA_Utils::getSetting( 'button_style' ) ) {
				$classes['wptwa-round'] = 'wptwa-round';
			}
			
			if ( 'round' === strtolower( $button_style ) ) {
				$classes['wptwa-round'] = 'wptwa-round';
			}
			
			$href = 'https://api.whatsapp.com/send?phone=' . $number . ( '' !== $predefined_text ? '&text=' . $predefined_text : '' );
			if ( strpos( get_post_meta( $post->ID, 'wptwa_number', true ), 'chat.whatsapp.com' ) !== false ) {
				$number = '';
				$href = esc_url( get_post_meta( $post->ID, 'wptwa_number', true ) );
				$classes[] = 'wptwa-group';
			}
			
			$background_color = get_post_meta( $post->ID, 'wptwa_background_color', true );
			$background_color_on_hover = get_post_meta( $post->ID, 'wptwa_background_color_on_hover', true );
			$text_color = get_post_meta( $post->ID, 'wptwa_text_color', true );
			$text_color_on_hover = get_post_meta( $post->ID, 'wptwa_text_color_on_hover', true );
			
			if ( '' !== trim( $background_color ) 
					|| '' !== trim( $background_color_on_hover )
					|| '' !== trim( $text_color ) 
					|| '' !== trim( $text_color_on_hover )
				) {
				
				$item.= '<style type="text/css" scoped>';
				$item.= '#wptwa-button-' . $post->ID . ' > * {';
				$item.= ( '' !== trim( $background_color ) ) ? 'background-color:' . $background_color . ' !important;' : '';
				$item.= ( '' !== trim( $text_color ) ) ? 'color:' . $text_color . ' !important;' : '';
				$item.= '}';
				$item.= '#wptwa-button-' . $post->ID . ' > *:hover {';
				$item.= ( '' !== trim( $background_color_on_hover ) ) ? 'background-color:' . $background_color_on_hover . ' !important;' : '';
				$item.= ( '' !== trim( $text_color_on_hover ) ) ? 'color:' . $text_color_on_hover . ' !important;' : '';
				$item.= '}';
				$item.= '</style>';
				
			}
			
			if ( in_array( 'wptwa-offline', $classes ) ) {
				$item.= '<span class="' . implode( ' ', $classes) . '" >';
				$item.= '<span class="wptwa-avatar">' . $avatar . '</span><span class="wptwa-text"><span class="wptwa-profile">' . $name . $title . '</span><span class="wptwa-copy">' . $button_label . '</span><span class="wptwa-offline-text">' . $offline_text . '</span></span>';
				$item.= '</span>';
			}
			else {
				$item.= '<a href="' . $href . '" class="' . implode( ' ', $classes) . '" data-number="' . $number . '" data-auto-text="' . esc_attr( $predefined_text ) . '" data-ga-label="' . esc_attr( $post_title ) . '" target="_blank">';
				$item.= '<span class="wptwa-avatar">' . $avatar . '</span><span class="wptwa-text"><span class="wptwa-profile">' . $name . $title . '</span><span class="wptwa-copy">' . $button_label . '</span></span>';
				$item.= '</a>';
			}
			
			
			if ( in_array( 'wptwa-offline', $classes ) ) {
				$offline_acc[ $i ] = array(
					'id' => $post->ID,
					'content' => $item
				);
			}
			else {
				$is_pinned = get_post_meta( $post->ID, 'wptwa_pin_account', true ) == 'on' ? true : false;
				if ( $is_pinned ) {
					$pinned_acc[ $i ] = array(
						'id' => $post->ID,
						'content' => $item
					);
				}
				else {
					$online_acc[ $i ] = array(
						'id' => $post->ID,
						'content' => $item
					);
				}
			}
			
			
			$i++;
						
		}
		
		wp_reset_postdata();
		
		ksort( $pinned_acc );
		ksort( $online_acc );
		ksort( $offline_acc );
		
		if ( 'woocommerce_button' === $type && 'on' === WPTWA_Utils::getSetting( 'wc_randomize_accounts_order' ) ) {
			shuffle( $online_acc );
		}
		
		$html = array_merge( $pinned_acc, $online_acc, $offline_acc );
		
		/* Limit the items shown if limit parameter is set. */			
		$total_accounts_shown = ( int ) esc_html( WPTWA_Utils::getSetting( 'wc_total_accounts_shown' ) );;
		if ( 'woocommerce_button' === $type && $total_accounts_shown > 0 ) {
			$i = 1;
			foreach ( $html as $k => $v ) {
				if ( $i > $total_accounts_shown ) {
					unset( $html[ $k ] );
				}
				else {
					$html[ $k ] = $v;
				}
				$i++;
			}
		}
		
		echo json_encode( $html );
		
		wp_die();
		
	}
	
	public function displayWidget () {
		
		$ids = isset( $_POST['ids'] ) ? explode( '-', $_POST['ids'] ) : array();
		$page_title = isset( $_POST['page-title'] ) ? $_POST['page-title'] : '';
		$page_url = isset( $_POST['page-url'] ) ? $_POST['page-url'] : '';
		
		if ( count( $ids ) < 1 ) {
			wp_die();
		}
		
		$the_accounts = get_posts( array(
			'posts_per_page' => -1,
			'post__in' => $ids,
			'post_type' => 'wptwa_accounts',
			'orderby' => 'post__in'
		) );
		
		$pinned_acc = array();
		$online_acc = array();
		$offline_acc = array();
		$i = 0;
		$someone_is_online = false;
		foreach ( $the_accounts as $post ) {
			setup_postdata( $post );
			
			$classes = array( 'wptwa-account', 'wptwa-clearfix' );
			
			$from = get_post_meta( $post->ID, 'wptwa_hour_start', true ) . ':' . get_post_meta( $post->ID, 'wptwa_minute_start', true );
			$till = get_post_meta( $post->ID, 'wptwa_hour_end', true ) . ':' . get_post_meta( $post->ID, 'wptwa_minute_end', true );
			
			$offline_text = get_post_meta( $post->ID, 'wptwa_offline_text', true );
			
			$current_day = strtolower( date( 'l' ) );
			$availability = json_decode( get_post_meta( $post->ID, 'wptwa_availability', true ), true );
			$availability = is_array( $availability ) ? $availability : array();
			
			/* Time and day availability */
			
			if ( 	isset( $availability[ $current_day ] ) && 
					isset( $availability[ $current_day ][ 'hour_start' ] ) && 
					isset( $availability[ $current_day ][ 'minute_start' ] ) && 
					isset( $availability[ $current_day ][ 'hour_end' ] ) && 
					isset( $availability[ $current_day ][ 'minute_end' ] )
				) {
				
				$from = $availability[ $current_day ][ 'hour_start' ] . ':' . $availability[ $current_day ][ 'minute_start' ];
				$till = $availability[ $current_day ][ 'hour_end' ] . ':' . $availability[ $current_day ][ 'minute_end' ];
				
				/* Ignore if time is unavailable */
				if ( ! self::isBetweenTime( $from, $till, current_time( 'H:i' ) ) ) {
					if ( '' === trim( $offline_text ) ) {
						continue;
					}
					else {
						$classes[] = 'wptwa-offline';
					}
				}
				
			}
			else {
				continue;
			}
			
			$number = preg_replace( '/[^0-9]/', '', get_post_meta( $post->ID, 'wptwa_number', true ) );
			$name = get_post_meta( $post->ID, 'wptwa_name', true );
			$title = get_post_meta( $post->ID, 'wptwa_title', true );
			$predefined_text = get_post_meta( $post->ID, 'wptwa_predefined_text', true );
			$predefined_text = str_ireplace( '[wptwa_page_title]', $page_title, $predefined_text );
			$predefined_text = str_ireplace( '[wptwa_page_url]', $page_url, $predefined_text );
			$predefined_text = str_ireplace( "\r\n", rawurlencode( "\r\n" ), $predefined_text );
			
			$post_title = get_the_title( $post );
			
			
			/* Filter by WPML languages */
			$languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
			if ( isset( $_POST['current-language'] ) ) {
				$current_language = $_POST['current-language'];
				$compatible_languages = json_decode( get_post_meta( $post->ID, 'wptwa_target_languages', true ), true );
				$compatible_languages = is_array( $compatible_languages ) ? $compatible_languages : array();
				if ( count( $compatible_languages ) > 0 && ! in_array( strtolower( $current_language ), $compatible_languages ) ) {
					continue;
				}
			}
			
			$avatar_url = '';
			if ( has_post_thumbnail( $post ) ) {
				$avatar_url = get_the_post_thumbnail_url( $post );
			}
			else {
				$classes[] = 'wptwa-no-image';
			}
			
			if ( 'on' === get_post_meta( $post->ID, 'wptwa_hide_on_large_screen', true ) ) {
				$classes[] = 'wptwa-hide-on-large-screen';
			}
			
			if ( 'on' === get_post_meta( $post->ID, 'wptwa_hide_on_small_screen', true ) ) {
				$classes[] = 'wptwa-hide-on-small-screen';
			}
			
			$href = 'https://api.whatsapp.com/send?phone=' . $number . ( '' !== $predefined_text ? '&text=' . $predefined_text : '' );
			if ( strpos( get_post_meta( $post->ID, 'wptwa_number', true ), 'chat.whatsapp.com' ) !== false ) {
				$number = '';
				$href = esc_url( get_post_meta( $post->ID, 'wptwa_number', true ) );
				$classes[] = 'wptwa-group';
			}
			
			if ( in_array( 'wptwa-offline', $classes ) ) {
				$offline_acc[ ++$i ] = '	<span class="' . implode( ' ', $classes ) . '">
								<div class="wptwa-face"><img src="' . esc_url( $avatar_url ) . '" onerror="this.style.display=\'none\'"></div>
								<div class="wptwa-info">
									<span class="wptwa-title">' . esc_html( $title ) . '</span>
									<span class="wptwa-name">' . esc_html( $name ) . '</span>
									<span class="wptwa-offline-text">' . esc_html( $offline_text ) . '</span>
								</div>
							</span>';
			}
			else {
				$is_pinned = get_post_meta( $post->ID, 'wptwa_pin_account', true ) == 'on' ? true : false;
				if ( $is_pinned ) {
					$pinned_acc[ ++$i ] = '<a href="' . $href . '" data-number="' . $number . '" class="' . implode( ' ', $classes ) . '" data-auto-text="' . esc_attr( $predefined_text ) . '" data-ga-label="' . esc_attr( $post_title ) . '" target="_blank">
								<div class="wptwa-face"><img src="' . esc_url( $avatar_url ) . '" onerror="this.style.display=\'none\'"></div>
								<div class="wptwa-info">
									<span class="wptwa-title">' . esc_html( $title ) . '</span>
									<span class="wptwa-name">' . esc_html( $name ) . '</span>
								</div>
							</a>';
				}
				else {
					$online_acc[ ++$i ] = '<a href="' . $href . '" data-number="' . $number . '" class="' . implode( ' ', $classes ) . '" data-auto-text="' . esc_attr( $predefined_text ) . '" data-ga-label="' . esc_attr( $post_title ) . '" target="_blank">
								<div class="wptwa-face"><img src="' . esc_url( $avatar_url ) . '" onerror="this.style.display=\'none\'"></div>
								<div class="wptwa-info">
									<span class="wptwa-title">' . esc_html( $title ) . '</span>
									<span class="wptwa-name">' . esc_html( $name ) . '</span>
								</div>
							</a>';
				}
				
				$someone_is_online = true;
			}
			
		}
		wp_reset_postdata();
		
		if ( count( $pinned_acc ) > 0 
			|| count( $online_acc ) > 0
			|| count( $offline_acc ) > 0 ) {
			
			ksort( $pinned_acc );
			ksort( $online_acc );
			ksort( $offline_acc );
			
			if ( 'on' === WPTWA_Utils::getSetting( 'randomize_accounts_order' ) ) {
				shuffle( $online_acc );
			}
			
			$html = array_merge( $pinned_acc, $online_acc, $offline_acc );
			
			/* Limit the items shown if limit parameter is set. */			
			$total_accounts_shown = ( int ) esc_html( WPTWA_Utils::getSetting( 'total_accounts_shown' ) );;
			if ( $total_accounts_shown > 0 ) {
				$i = 1;
				foreach ( $html as $k => $v ) {
					if ( $i > $total_accounts_shown ) {
						$html[ $k ] = '';
					}
					else {
						$html[ $k ] = $v;
					}
					$i++;
				}
			}
			
			if ( isset( $_POST['current-language'] ) ) {
				do_action( 'wpml_switch_language', $_POST['current-language'] );
			}
			
			$toggle_text = esc_html( WPTWA_Utils::getSetting( 'toggle_text' ) );
			$description = wp_kses_post( WPTWA_Utils::getSetting( 'description' ) );
			
			if ( has_filter( 'wpml_translate_single_string' ) ) {
				$toggle_text = apply_filters('wpml_translate_single_string', $toggle_text, 'WhatsApp Click to Chat', 'Toggle Text' );
				$description = apply_filters('wpml_translate_single_string', $description, 'WhatsApp Click to Chat', 'Description' );
			}
			
			$delay_time = filter_var( WPTWA_Utils::getSetting( 'delay_time' ), FILTER_SANITIZE_NUMBER_INT );
			$inactivity_time = filter_var( WPTWA_Utils::getSetting( 'inactivity_time' ), FILTER_SANITIZE_NUMBER_INT );
			$scroll_length = filter_var( WPTWA_Utils::getSetting( 'scroll_length' ), FILTER_SANITIZE_NUMBER_INT );
			
			$classes = array( 'wptwa-container' );
			if ( 'left' === esc_attr( WPTWA_Utils::getSetting( 'box_position' ) ) ) {
				$classes[] = 'wptwa-left-side';
			}
			
			if ( '' === $toggle_text ) {
				$classes[] = 'circled-handler';
			}
			
			if ( 'on' === esc_attr( WPTWA_Utils::getSetting( 'toggle_round_on_desktop' ) ) ) {
				$classes[] = 'wptwa-round-toggle-on-desktop';
			}
			
			if ( 'on' === esc_attr( WPTWA_Utils::getSetting( 'toggle_round_on_mobile' ) ) ) {
				$classes[] = 'wptwa-round-toggle-on-mobile';
			}
			
			if ( 'on' === esc_attr( WPTWA_Utils::getSetting( 'toggle_center_on_mobile' ) ) ) {
				$classes[] = 'wptwa-mobile-center';
			}
			
			if ( 'on' === esc_attr( WPTWA_Utils::getSetting( 'disable_auto_display_on_small_screen' ) ) ) {
				$classes[] = 'wptwa-disable-auto-display-on-small-screen';
			}
			
			/* If we should disable auto-display when no one is online. */
			if ( ! $someone_is_online && 'on' === esc_attr( WPTWA_Utils::getSetting( 'disable_auto_display_when_no_one_online' ) ) ) {
				$delay_time = 0;
				$inactivity_time = 0;
				$scroll_length = 0;
			}
			
			/* GDPR HTML */
			$gdpr_html = '';
			$consent_description = '' !== trim( WPTWA_Utils::getSetting( 'consent_description' ) )
				? wpautop( trim( WPTWA_Utils::getSetting( 'consent_description' ) ) )
				: ''
				;
			$consent_checkbox_text_label = '' !== trim( WPTWA_Utils::getSetting( 'consent_checkbox_text_label' ) )
				? wpautop( trim( WPTWA_Utils::getSetting( 'consent_checkbox_text_label' ) ) )
				: ''
				;
			
			if ( has_filter( 'wpml_translate_single_string' ) ) {
				$consent_description = wpautop( apply_filters('wpml_translate_single_string', $consent_description, 'WhatsApp Click to Chat', 'Consent Description' ) );
				$consent_checkbox_text_label = wpautop( apply_filters('wpml_translate_single_string', $consent_checkbox_text_label, 'WhatsApp Click to Chat', 'Consent Checkbox Text Label' ) );
			}
			
			$consent_checkbox_text_label = '' !== $consent_checkbox_text_label
				? '<div class="wptwa-confirmation"><label><input type="checkbox" name="wptwa-consent" id="wptwa-consent" /></label><div>' . $consent_checkbox_text_label . '</div></div>'
				: ''
				;
			
			if ( '' !== $consent_description || '' !== $consent_checkbox_text_label ) {
				$gdpr_html = '<div class="wptwa-gdpr">' . $consent_description . $consent_checkbox_text_label . '</div>';
			}
			
			echo '<div class="' . implode( ' ', $classes ) . '" data-delay-time="' . $delay_time . '" data-inactive-time="' . $inactivity_time . '" data-scroll-length="' . $scroll_length . '">
					<div class="wptwa-box">
						<div class="wptwa-description">
							' . wpautop( $description ) . '
						</div>
						<span class="wptwa-close"></span>
						<div class="wptwa-people">
							' . $gdpr_html . implode( '', $html ) . '
						</div>
					</div>
					<div class="wptwa-toggle"><svg class="WhatsApp" width="20px" height="20px" viewBox="0 0 90 90"><use xlink:href="#wptwa-logo"></svg> <span class="wptwa-text">' . esc_html( $toggle_text ) . '</span></div>
					<div class="wptwa-mobile-close"><span>' . esc_html( WPTWA_Utils::getSetting( 'mobile_close_button_text', esc_html__( 'Go back to page', 'wptwa' ) ) ) . '</span></div>
				</div>';
			
		}
		
		wp_die();
	}
	
	public function outputHTML () {
		
		echo '
			<span class="wptwa-flag"></span>
			<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
				<symbol id="wptwa-logo">
					<path id="WhatsApp" d="M90,43.841c0,24.213-19.779,43.841-44.182,43.841c-7.747,0-15.025-1.98-21.357-5.455L0,90l7.975-23.522   c-4.023-6.606-6.34-14.354-6.34-22.637C1.635,19.628,21.416,0,45.818,0C70.223,0,90,19.628,90,43.841z M45.818,6.982   c-20.484,0-37.146,16.535-37.146,36.859c0,8.065,2.629,15.534,7.076,21.61L11.107,79.14l14.275-4.537   c5.865,3.851,12.891,6.097,20.437,6.097c20.481,0,37.146-16.533,37.146-36.857S66.301,6.982,45.818,6.982z M68.129,53.938   c-0.273-0.447-0.994-0.717-2.076-1.254c-1.084-0.537-6.41-3.138-7.4-3.495c-0.993-0.358-1.717-0.538-2.438,0.537   c-0.721,1.076-2.797,3.495-3.43,4.212c-0.632,0.719-1.263,0.809-2.347,0.271c-1.082-0.537-4.571-1.673-8.708-5.333   c-3.219-2.848-5.393-6.364-6.025-7.441c-0.631-1.075-0.066-1.656,0.475-2.191c0.488-0.482,1.084-1.255,1.625-1.882   c0.543-0.628,0.723-1.075,1.082-1.793c0.363-0.717,0.182-1.344-0.09-1.883c-0.27-0.537-2.438-5.825-3.34-7.977   c-0.902-2.15-1.803-1.792-2.436-1.792c-0.631,0-1.354-0.09-2.076-0.09c-0.722,0-1.896,0.269-2.889,1.344   c-0.992,1.076-3.789,3.676-3.789,8.963c0,5.288,3.879,10.397,4.422,11.113c0.541,0.716,7.49,11.92,18.5,16.223   C58.2,65.771,58.2,64.336,60.186,64.156c1.984-0.179,6.406-2.599,7.312-5.107C68.398,56.537,68.398,54.386,68.129,53.938z"/>
				</symbol>
			</svg>
			';
		
		global $post;
		
		$current_post_type = get_post_type();
		$current_post_id = get_the_ID();
		//$protocol = stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) === true ? 'https://' : 'http://';
		$current_url = $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
		
		$selected_accounts = json_decode( WPTWA_Utils::getSetting( 'selected_accounts_for_widget', '[]' ), true );
		$selected_accounts = count( $selected_accounts ) < 1 ? array( 0 ) : $selected_accounts;
		
		$the_accounts = get_posts( array(
			'posts_per_page' => -1,
			'post__in' => $selected_accounts,
			'post_type' => 'wptwa_accounts',
			'orderby' => 'post__in'
		) );
		
		$displayedIds = array();
		
		foreach ( $the_accounts as $post ) {
			setup_postdata( $post );
			
			$_target = json_decode( get_post_meta( $post->ID, 'wptwa_target', true ) );
			$target = is_array( $_target ) ? $_target : array();
			
			$_included_ids = json_decode( get_post_meta( $post->ID, 'wptwa_included_ids', true ), true );
			$included_ids = is_array( $_included_ids ) ? $_included_ids : array();
			
			$_excluded_ids = json_decode( get_post_meta( $post->ID, 'wptwa_excluded_ids', true ), true );
			$excluded_ids = is_array( $_excluded_ids ) ? $_excluded_ids : array();
			
			/* Page targeting. */
			
			/* Included Posts & URLs */
			$is_included = false;
			foreach ( $included_ids as $k => $v ) {
				if ( filter_var( $v, FILTER_VALIDATE_URL ) !== FALSE ) {
					$parsed = parse_url( $v );
					$_current_url = $parsed['scheme'] . '://' . $current_url;
					if ( $v == $_current_url ) {
						$is_included = true;
						break;
					}
				}
				if ( ! filter_var( $v, FILTER_VALIDATE_URL ) && is_singular() && $current_post_id == $v ) {
					$is_included = true;
					break;
				}
			}
			
			if ( $is_included ) {
				$displayedIds[] = $post->ID;
				continue;
			}
			
			
			/* Excluded Posts & URLs */
			$is_excluded = false;
			foreach ( $excluded_ids as $k => $v ) {
				if ( filter_var( $v, FILTER_VALIDATE_URL ) !== FALSE && $v == $current_url ) {
					$parsed = parse_url( $v );
					$_current_url = $parsed['scheme'] . '://' . $current_url;
					if ( $v == $_current_url ) {
						$is_excluded = true;
						break;
					}
				}
				if ( ! filter_var( $v, FILTER_VALIDATE_URL ) && is_singular() && $current_post_id == $v ) {
					$is_excluded = true;
					break;
				}
			}
			if ( $is_excluded ) {
				continue;
			}
			
			/* Default homepage */
			if ( ( is_front_page() && is_home() ) && ! in_array( 'home', $target ) ) {
				continue;
			}
			
			/* Static homepage */
			if ( is_front_page() && ! in_array( 'home', $target ) ) {
				continue;
			}
			
			/* Blog page */
			if ( is_home() && ! in_array( 'blog', $target )) {
				continue;
			}
			
			if ( ( is_search() || is_archive() ) && ! in_array( 'archive', $target ) ) {
				continue;
			}
			
			if ( ! ( is_front_page() && is_home() ) && ! is_front_page() && is_singular( 'page' ) && ! in_array( 'page', $target ) ) {
				continue;
			}
			
			/*
			if ( is_singular() && in_array( $current_post_id, $excluded_ids ) ) {
				continue;
			}
			*/
			
			if ( is_singular( 'post' ) && ! in_array( 'post', $target ) ) {
				continue;
			}
			
			$existing_post_types = get_post_types( array( '_builtin' => false ) );
			if ( in_array( $current_post_type, $existing_post_types ) ) {
				if ( ! in_array( $current_post_type, $target ) ) {
					continue;
				}
			}
			
			$displayedIds[] = $post->ID;
			
		}
		wp_reset_postdata();
		
		/* Get current WPML lang and attach the ids to show */
		if ( count( $displayedIds ) > 0 ) {
			$ids = implode( '-', $displayedIds );
			$current_lang = apply_filters( 'wpml_current_language', NULL );
			echo '<span id="wptwa-show-widget" data-current-language="' . $current_lang . '" data-ids="' . $ids . '" data-page-title="' . get_the_title() . '" data-page-url="' . get_permalink() . '"></span>';
		}
		
	}
	
	public function wpEnqueueScripts () {
		
		$plugin_data = get_file_data( WPTWA_PLUGIN_BOOTSTRAP_FILE, array( 'version' ) );
		$plugin_version = isset( $plugin_data[0] ) ? $plugin_data[0] : false;
		
		wp_enqueue_style( 'wptwa-public', WPTWA_PLUGIN_URL . 'assets/css/public.css', array(), $plugin_version );
		
		$css_file = WPTWA_PLUGIN_DIR . 'assets/css/auto-generated-wptwa.css';
		if ( file_exists( $css_file ) ) {
			$last_modified = filemtime( $css_file );
			wp_enqueue_style( 'wptwa-generated', WPTWA_PLUGIN_URL . 'assets/css/auto-generated-wptwa.css', array(), $last_modified );
		}
		
		wp_enqueue_script( 'wptwa-public', WPTWA_PLUGIN_URL . 'assets/js/public.js', array( 'jquery' ), $plugin_version, true );
		wp_localize_script( 'wptwa-public', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}
	
}

?>