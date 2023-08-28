<?php

class WPTWA_Menu_Link {
	
	private static $menus = array();
	
	public function __construct () {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'addMenuLink' ) );
			add_filter( 'plugin_action_links_' . WPTWA_PLUGIN_BASENAME, array( $this, 'addPluginActionLinks' ) );
			add_filter( 'plugin_row_meta', array( $this, 'pluginRowMeta' ), 10, 4 );
			add_filter( 'admin_footer_text', array( $this, 'adminFooterText' ) );
		}
	}
	
	public function addMenuLink () {
		
		$parent_slug = 'wptwa_parent';
		
		$this->addMenu(
			esc_html__( 'WhatsApp', 'wptwa' ),
			'',
			$parent_slug,
			'',
			'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAABP0lEQVQoz2XRO0gVcBTH8ZNDVzEpNFIicIpa06VBIQgSDFzcnAJpiCKCwEBuiEi6+KBwikiscNDu0NLQEgpNQctFe4C9aEgjscQX6OfvcB+gcobDOb8vvx+cE6JYTcbl/bbkowmtpX2hZQzju3E33TBiQTLpRAnIyNlxx7GyW8Y1a+bUFIBBSVtZLNUFW56LcN6ue0K7nIZ9SJfkUhiypFqlvKRnH3BE3ovwzpRw3A/JwwMxQxbDN/eF8ETScgDo9id81SeE035675RQoa4IXLccZr0sjhf9M++KrF+yaoRRn0LWX/VFpNkHSZJsqldh0ePQaENvObVKlxmvXRVuSZrDSStuHzpTaJOMidBh22Wd7jpTFmv12pVTKcIjyX/r1qx6ZdCAacu2PHC08Kxnnup2VqMeb3z2xVv9zpXc9gBr2VaI0t5EZgAAAABJRU5ErkJggg=='
		);
		
		$this->addMenu(
			esc_html__( 'Add New Account', 'wptwa' ),
			'',
			'post-new.php?post_type=wptwa_accounts',
			$parent_slug
		);
		
		$this->addMenu(
			esc_html__( 'Floating Widget', 'wptwa' ),
			array( $this, 'getView' ),
			'wptwa_floating_widget',
			$parent_slug
		);
		
		$this->addMenu(
			esc_html__( 'WooCommerce Button', 'wptwa' ),
			array( $this, 'getView' ),
			'wptwa_woocommerce_button',
			$parent_slug
		);
			
		$this->addMenu(
			esc_html__( 'Settings', 'wptwa' ),
			array( $this, 'getView' ),
			'wptwa_settings',
			$parent_slug
		);
	}
	
	private function addMenu ( $title, $callback, $slug, $parent_slug = '', $icon = '' ) {
		
		if ( '' === $parent_slug ) {
			add_menu_page(
				$title,
				$title,
				'manage_options',
				$slug,
				$callback,
				$icon
			);
		}
		else {
			add_submenu_page(
				$parent_slug,
				$title,
				$title,
				'manage_options',
				$slug,
				$callback,
				$icon
			);
			
			self::$menus[$title] = $slug;
		}
		
	}
	
	public function getView () {
		WPTWA_Utils::getView();
	}
	
	public static function getMenus () {
		return self::$menus;
	}
	
	/**
	 * Add 'Settings' link to the plugin page. 
	 * This link will only displayed if the plugin is active.
	 */
	public function addPluginActionLinks ( $links ) {
		$settings_link = sprintf( '<a href="admin.php?page=wptwa_settings">%1$s</a>', esc_html__( 'Settings', 'wptwa' ) );
		array_unshift( $links, $settings_link );
		return $links;
	}
	
	public function pluginRowMeta ( $links, $file ) {
		if ( WPTWA_PLUGIN_BASENAME == $file ) {
			$links[] = '<a href="http://docs.indieplugins.com/wptwa/" target="_blank">' . esc_html__( 'Read Documentation', 'wptwa' ) . '</a>';
			$links[] = '<a href="https://codecanyon.net/item/whatsapp-click-to-chat-for-wordpress/20248537/support" target="_blank">' . esc_html__( 'Get Support', 'wptwa' ) . '</a>';
		}
		return $links;
	}
	
	/**
	 * Ask for some stars at the bottom of admin page
	 */
	public function adminFooterText ( $default ) {
		global $pagenow;
		
		$setting_pages = array(
			WPTWA_PREFIX . '_settings',
			WPTWA_PREFIX . '_floating_widget',
			WPTWA_PREFIX . '_woocommerce_button'
		);
		
		
		$post_type = filter_input( INPUT_GET, 'post_type' );
		if ( ! $post_type ) {
			$post_type = get_post_type( filter_input( INPUT_GET, 'post' ) );
		}
		
		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && in_array( $_GET['page'], $setting_pages ) ||
				'wptwa_accounts' === $post_type ) {
			
			$plugin_data = get_plugin_data( WPTWA_PLUGIN_BOOTSTRAP_FILE, false, true );
			echo 'WhatsApp Click to Chat ' . esc_html__( 'Version', 'wptwa') . ' ' . $plugin_data['Version'];
			echo ' ' . esc_html__( 'by', 'wptwa' ) . ' <a href="https://codecanyon.net/user/indie_plugins/portfolio" target="_blank">Indie Plugins</a>' ;
			//echo ' | <a href="http://doc.indieplugins.com/wptwa/" target="_blank">' . esc_html__( 'Read Docs', 'wptwa' ) . '</a>';
			//echo ' | <a href="https://codecanyon.net/item/whatsapp-click-to-chat-for-wordpress/20248537/support" target="_blank">' . esc_html__( 'Get Support', 'wptwa' ) . '</a>';
			//echo ' | <a href="https://codecanyon.net/item/whatsapp-click-to-chat-for-wordpress/20248537" target="_blank">' . esc_html__( 'Give a ', 'wptwa' ) . '&#9733;&#9733;&#9733;&#9733;&#9733;</a>';
			//printf( '<span>%1$s <b><a href="https://codecanyon.net/item/whatsapp-click-to-chat-for-wordpress/20248537" target="_blank">%2$s</a></b> %3$s</span>', esc_html__( 'Thank you for using this plugin. Support the developer by leaving a ', 'wptwa'), esc_html__( '&#9733;&#9733;&#9733;&#9733;&#9733;', 'wptwa'), esc_html__( 'rating. Huge thanks in advance.', 'wptwa' ) );
		}
		else {
			echo $default;
		}
	}
	
}

?>