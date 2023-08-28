<?php

/**
 * This class adds a shortcode to display the affilaite links.
 */

class WPTWA_Shortcode {
	
	private static $styles = '';
	private static $button_id = 0;
	
	public function __construct () {
		
		add_shortcode( 'whatsapp', array( $this, 'shortcodeWhatsApp' ) );
		add_shortcode( 'whatsapp_button', array( $this, 'whatsappButton' ) );
		
		add_action( 'wp_footer', array( 'WPTWA_Shortcode', 'addStyles' ) );
		
	}
	
	public function whatsappButton ( $atts, $content = null ) {
		
		extract( shortcode_atts( array(
			'id' => '',
			'button_style' => ''
		), $atts ) );
		
		wp_reset_postdata();
		
		$page_title = get_the_title();
		$page_url = get_permalink();
		
		$account = get_posts( array(
			'posts_per_page' => -1,
			'post__in' => array( $id ),
			'post_type' => 'wptwa_accounts'
		) );
		
		if ( count( $account ) < 1 ) {
			return '';
		}
		
		$item = '';
		foreach ( $account as $post ) {
			setup_postdata( $post );
			$item.= '<p id="wptwa-button-' . $post->ID . '" class="wptwa-button-container" data-target-id="' . $post->ID . '" data-page-title="' . $page_title . '" data-page-url="' . $page_url . '"><span></span></p>';
		}
		
		wp_reset_postdata();
		
		return wpautop( $item );
	}
	
	/* Legacy code. Delete it and it will break the old shortcode. */
	public function shortcodeWhatsApp ( $atts, $content = null ) {
		
		extract( shortcode_atts( array(
			'number' => '',
			'group_invite_url' => '',
			'auto_text' => '',
			'text_color' => '',
			'background_color' => '',
			'text_color_on_hover' => '',
			'background_color_on_hover' => '',
			'display' => 'inline-block',
			'icon' => 'yes',
			'align' => 'center'
		), $atts ) );
		
		if ( '' === trim( $number ) && ( '' === esc_url( $group_invite_url ) || strpos( $group_invite_url, 'chat.whatsapp.com' ) === false ) ) {
			return '';
		}
		
		$number = preg_replace( '/[^0-9]/', '', $number );
		
		$auto_text = '' !== trim( esc_attr( $auto_text ) ) ? esc_attr( $auto_text ) : '';
		$href = 'https://api.whatsapp.com/send?phone=' . $number . ( '' !== $auto_text ? '&text=' . $auto_text : '' );
		
		$button_id = 'whatsapp-button-' . self::$button_id++;
		$fa = '';
		$class = array( 'wptwa-account' );
		
		if ( '' !== esc_url( $group_invite_url ) && strpos( $group_invite_url, 'chat.whatsapp.com' ) !== false ) {
			$href = esc_url( $group_invite_url );
			$class[] = 'wptwa-group';
		}
		
		if ( '' !== $text_color ||
				'' !== $background_color ||
				'' !== $text_color_on_hover ||
				'' !== $background_color_on_hover ) {
			
			self::$styles .= '<style type="text/css">
				
				#' . $button_id . ' {
					color: ' . $text_color . ';
					background: ' . $background_color . ';
					
				}
				#' . $button_id . ' svg {
					fill: ' . $text_color . ';
				}
				#' . $button_id . ':hover {
					color: ' . $text_color_on_hover . ';
					background: ' . $background_color_on_hover . ';
					text-decoration: none;
				}
				#' . $button_id . ':hover svg {
					fill: ' . $text_color_on_hover . ';
				}
				
				</style>';
			
			$fa = '<svg class="WhatsApp" width="15px" height="15px" viewBox="0 0 90 90"><use xlink:href="#wptwa-logo"></svg>';
				
			$class[] = 'whatsapp-custom-styled';
			
			if ( 'block' === $display ) {
				$class[] = 'block-level';
			}
			if ( 'yes' !== $icon ) {
				$class[] = 'no-icon';
			}
			if ( 'left' === $align ) {
				$class[] = 'align-left';
			}
		}
		
		if ( 'block' === $display ) {
			return  wpautop( '<a id="' . $button_id . '" class="' . implode( ' ', $class ) . '" href="' . $href . '" data-number="' . $number . '" data-auto-text="' . $auto_text . '" target="_blank">' . $fa . ( '' !== $content ? '<span>' . $content . '</span>' : '' ) . '</a>' );
		}
		else {
			return  '<a id="' . $button_id . '" class="' . implode( ' ', $class ) . '" href="' . $href . '" data-number="' . $number . '" data-auto-text="' . $auto_text . '" target="_blank">' . $fa . ( '' !== $content ? '<span>' . $content . '</span>' : '' ) . '</a>';
		}
		
	}
	
	public static function addStyles () {
		
		echo self::$styles;
		
	}
	
}

?>