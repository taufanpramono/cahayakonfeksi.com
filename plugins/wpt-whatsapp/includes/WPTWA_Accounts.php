<?php

class WPTWA_Accounts {
	
	public function __construct () {
		
		add_action( 'init', array( $this, 'accountsPostType' ) );
		
		if ( ! is_admin() ) {
			return;
		}
		
		add_filter( 'manage_wptwa_accounts_posts_columns', array( $this, 'accountTabulationHeader' ) );
		add_action( 'manage_wptwa_accounts_posts_custom_column', array( $this, 'accountTabulationData' ), 10, 2 );
		
		add_filter( 'manage_edit-wptwa_accounts_sortable_columns', array( $this, 'accountTabulationSorting' ) );
		
		add_action( 'add_meta_boxes', array( $this, 'addMetaBoxes' ) );
		add_action( 'save_post', array( $this, 'saveMetaBoxes' ) );
		
	}
	
	public function accountsPostType () {
		$labels = array(
			'name'               => _x( 'WhatsApp Accounts', 'post type general name', 'wptwa' ),
			'singular_name'      => _x( 'WhatsApp Account', 'post type singular name', 'wptwa' ),
			'menu_name'          => _x( 'Accounts', 'admin menu', 'wptwa' ),
			'name_admin_bar'     => _x( 'Account', 'add new on admin bar', 'wptwa' ),
			'add_new'            => _x( 'Add New', 'book', 'wptwa' ),
			'add_new_item'       => __( 'Add New Account', 'wptwa' ),
			'new_item'           => __( 'New Account', 'wptwa' ),
			'edit_item'          => __( 'Edit Account', 'wptwa' ),
			'view_item'          => __( 'View Account', 'wptwa' ),
			'all_items'          => __( 'All Accounts', 'wptwa' ),
			'search_items'       => __( 'Search Accounts', 'wptwa' ),
			'parent_item_colon'  => __( 'Parent Accounts:', 'wptwa' ),
			'not_found'          => __( 'No accounts found.', 'wptwa' ),
			'not_found_in_trash' => __( 'No accounts found in Trash.', 'wptwa' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'WhatsApp Accounts', 'wptwa' ),
			'public'             => false,
			'exclude_from_search'=> true,
			'show_ui'            => true,
			'show_in_menu'       => 'wptwa_parent',
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'menu_position'      => null,
			'supports'           => array( 'title', 'thumbnail' )
		);
	
		register_post_type( 'wptwa_accounts', $args );
	}
	
	public function accountTabulationHeader ( $defaults ) {
		unset( $defaults['title'] );
		unset( $defaults['date'] );
		$defaults['title']  = esc_html__( 'Account Title', 'wptwa' );
		$defaults['picture']  = esc_html__( 'Picture', 'wptwa' );
		$defaults['number']  = esc_html__( 'Number', 'wptwa' );
		$defaults['role']  = esc_html__( 'Role Title', 'wptwa' );
		$defaults['pinned']  = esc_html__( 'Pin Account', 'wptwa' );
		return $defaults;
	}
	
	public function accountTabulationData ( $column_name, $post_id ) {
		if ( $column_name == 'picture' ) {
			if ( has_post_thumbnail( $post_id ) ) {
				echo '<img src="' . get_the_post_thumbnail_url() . '" style="max-width: 40px;"/>';
			}
		}
		if ( $column_name == 'number' ) {
			echo get_post_meta( $post_id, 'wptwa_number', true );
		}
		if ( $column_name == 'role' ) {
			echo get_post_meta( $post_id, 'wptwa_title', true );
		}
		if ( $column_name == 'pinned' ) {
			echo get_post_meta( $post_id, 'wptwa_pin_account', true ) === 'on' ? esc_html__( 'Yes', 'wptwa' ) : esc_html__( 'No', 'wptwa' );
		}
	}
	
	public function accountTabulationSorting ( $columns ) {
		$columns['number'] = 'number';
		$columns['time'] = 'time';
		return $columns;
	}
	
	public function addMetaBoxes () {
		
		$screen = get_current_screen();
		
		add_meta_box(
			'wptwa-links',
			esc_html__( 'Links', 'wptwa' ),
			array( $this, 'links' ),
			array( 'wptwa_accounts' ),
			'side'
		);
		
		if( 'add' !== $screen->action ) {
			add_meta_box(
				'wptwa-copy-shortcode',
				esc_html__( 'Shortcode for this account', 'wptwa' ),
				array( $this, 'copyShortcode' ),
				array( 'wptwa_accounts' ),
				'side'
			);
		}
		
		add_meta_box(
			'wptwa-account-information',
			esc_html__( 'WhatsApp Account Information', 'wptwa' ),
			array( $this, 'accountInformation' ),
			array( 'wptwa_accounts' ),
			'normal'
		);
		
		add_meta_box(
			'wptwa-page-targeting',
			esc_html__( 'Page Targeting', 'wptwa' ),
			array( $this, 'pageTargeting' ),
			array( 'wptwa_accounts' ),
			'normal'
		);
		
		add_meta_box(
			'wptwa-button-style',
			esc_html__( 'Button Style', 'wptwa' ),
			array( $this, 'buttonStyle' ),
			array( 'wptwa_accounts' ),
			'normal'
		);
		
	}
	
	public function accountInformation ( $post ) {
		
		global $pagenow;
		
		$new = 'post-new.php' === $pagenow ? true : false;
		
		$number = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_number', true ) );
		$name = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_name', true ) );
		$title = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_title', true ) );
		$predefined_text = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_predefined_text', true ) );
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			$predefined_text = sanitize_textarea_field( get_post_meta( $post->ID, 'wptwa_predefined_text', true ) );
		}
		
		$button_label = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_button_label', true ) );
		
		$hour_start = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_hour_start', true ) ) : '';
		$minute_start = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_minute_start', true ) ) : '';
		$hour_end = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_hour_end', true ) ) : '23';
		$minute_end = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_minute_end', true ) ) : '59';
		
		$sunday = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_sunday', true ) ) : 'on';
		$monday = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_monday', true ) ) : 'on';
		$tuesday = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_tuesday', true ) ) : 'on';
		$wednesday = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_wednesday', true ) ) : 'on';
		$thursday = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_thursday', true ) ) : 'on';
		$friday = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_friday', true ) ) : 'on';
		$saturday = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_saturday', true ) ) : 'on';
		
		$hide_on_large_screen = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_hide_on_large_screen', true ) ) : 'off';
		$hide_on_small_screen = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_hide_on_small_screen', true ) ) : 'off';
		
		$pin_account = ! $new ? sanitize_text_field( get_post_meta( $post->ID, 'wptwa_pin_account', true ) ) : 'off';
		
		$offline_text = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_offline_text', true ) );
		
		$availability = array(
			'sunday' => array(
				'label' => esc_html( 'Sunday', 'wptwa' ),
				'hour_start' => 0,
				'minute_start' => 0,
				'hour_end' => 23,
				'minute_end' => 59
			)
			,
			'monday' => array(
				'label' => esc_html( 'Monday', 'wptwa' ),
				'hour_start' => 0,
				'minute_start' => 0,
				'hour_end' => 23,
				'minute_end' => 59
			)
			,
			'tuesday' => array(
				'label' => esc_html( 'Tuesday', 'wptwa' ),
				'hour_start' => 0,
				'minute_start' => 0,
				'hour_end' => 23,
				'minute_end' => 59
			)
			,
			'wednesday' => array(
				'label' => esc_html( 'Wednesday', 'wptwa' ),
				'hour_start' => 0,
				'minute_start' => 0,
				'hour_end' => 23,
				'minute_end' => 59
			)
			,
			'thursday' => array(
				'label' => esc_html( 'Thursday', 'wptwa' ),
				'hour_start' => 0,
				'minute_start' => 0,
				'hour_end' => 23,
				'minute_end' => 59
			)
			,
			'friday' => array(
				'label' => esc_html( 'Friday', 'wptwa' ),
				'hour_start' => 0,
				'minute_start' => 0,
				'hour_end' => 23,
				'minute_end' => 59
			)
			,
			'saturday' => array(
				'label' => esc_html( 'Saturday', 'wptwa' ),
				'hour_start' => 0,
				'minute_start' => 0,
				'hour_end' => 23,
				'minute_end' => 59
			)
		);
		
		$existing_availability = json_decode( sanitize_text_field( get_post_meta( $post->ID, 'wptwa_availability', true ) ), true );
		$existing_availability = is_array( $existing_availability ) ? $existing_availability : array();
		foreach ( $existing_availability as $k => $v ) {
			if ( 	isset( $availability[ $k ] ) &&
					isset( $availability[ $k ][ 'hour_start' ] ) && 
					isset( $availability[ $k ][ 'minute_start' ] ) && 
					isset( $availability[ $k ][ 'hour_end' ] ) && 
					isset( $availability[ $k ][ 'minute_end' ] )
				) {
				
				$availability[ $k ][ 'hour_start' ] = $v[ 'hour_start' ];
				$availability[ $k ][ 'minute_start' ] = $v[ 'minute_start' ];
				$availability[ $k ][ 'hour_end' ] = $v[ 'hour_end' ];
				$availability[ $k ][ 'minute_end' ] = $v[ 'minute_end' ];
				
			}
		}
		
		?>
		
		<table class="form-table" id="wptwa-custom-wc-button-settings">
			<tbody>
				<tr>
					<th scope="row"><label for="wptwa_number"><?php esc_html_e( 'Account Number or group chat URL', 'wptwa' ); ?></label></th>
					<td>
						<p>
							<input type="text" class="widefat" id="wptwa_number" name="wptwa_number" value="<?php echo esc_attr( $number ); ?>" />
							<p class="description"><?php printf( esc_html__( 'Refer to %s for a detailed explanation.', 'wptwa' ), '<a href="https://faq.whatsapp.com/en/general/21016748" target="_blank">https://faq.whatsapp.com/en/general/21016748</a>' ); ?></p>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wptwa_name"><?php esc_html_e( 'Name', 'wptwa' ); ?></label></th>
					<td>
						<input type="text" id="wptwa_name" name="wptwa_name" value="<?php echo esc_attr( $name ); ?>" class="widefat" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wptwa_title"><?php esc_html_e( 'Title', 'wptwa' ); ?></label></th>
					<td>
						<input type="text" id="wptwa_title" name="wptwa_title" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wptwa_predefined_text"><?php esc_html_e( 'Predefined Text', 'wptwa' ); ?></label></th>
					<td>
						<textarea name="wptwa_predefined_text" id="wptwa_predefined_text" rows="3" class="widefat"><?php echo esc_textarea( $predefined_text ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Use [wptwa_page_title] and [wptwa_page_url] shortcodes to output the page\'s title and URL respectively. ', 'wptwa' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wptwa_button_label"><?php esc_html_e( 'Button Label', 'wptwa' ); ?></label></th>
					<td>
						<input type="text" id="wptwa_button_label" name="wptwa_button_label" value="<?php echo esc_attr( $button_label ); ?>" placeholder="<?php echo WPTWA_Utils::getSetting( 'button_label', esc_html__( 'Need help? Chat via WhatsApp', 'wptwa' ) ); ?>" class="widefat" />
						<p class="description"><?php esc_html_e( 'This text applies only on shortcode button. Leave empty to use the default label.', 'wptwa' ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th scope="row"><label for="wptwa_availability"><?php esc_html_e( 'Availability', 'wptwa' ); ?></label></th>
					<td>
						<?php foreach ( $availability as $k => $v ) : ?>
						
							<p>
								<strong><?php echo $v['label']; ?></strong><br/>
								<select name="wptwa_availability[<?php echo $k; ?>][hour_start]">
									<?php $this->displayAvailabilityOptions( 'hour', $v['hour_start'] ); ?>
								</select> :
								<select name="wptwa_availability[<?php echo $k; ?>][minute_start]">
									<?php $this->displayAvailabilityOptions( 'minute', $v['minute_start'] ); ?>
								</select> <?php esc_html_e( 'to', 'wptwa' ); ?>
								<select name="wptwa_availability[<?php echo $k; ?>][hour_end]">
									<?php $this->displayAvailabilityOptions( 'hour', $v['hour_end'] ); ?>
								</select> :
								<select name="wptwa_availability[<?php echo $k; ?>][minute_end]">
									<?php $this->displayAvailabilityOptions( 'minute', $v['minute_end'] ); ?>
								</select>
							</p><br/>
						
						<?php endforeach; ?>
						
						<?php if ( '' === trim( get_option( 'timezone_string' ) ) && '' === get_option( 'gmt_offset' ) ) : ?>
							
							<p><a href="options-general.php"><?php esc_html_e( 'Please set your time zone first so we can have an accurate time availability.', 'wptwa' ); ?></a></p>
							
						<?php else : ?>
							
							<p class="description"><?php printf( esc_html__( 'Note that the timezone currently in use is %s', 'wptwa' ), '<a href="options-general.php#timezone_string" target="_blank">' . ( '' !== get_option( 'timezone_string' ) ? get_option( 'timezone_string' ) : get_option( 'gmt_offset' ) ) . '</a>' ); ?></p>
							
						<?php endif; ?>
					</td>
				</tr>
				
				<tr>
					<th scope="row"><label for=""><?php esc_html_e( 'Pin this account', 'wptwa' ); ?></label></th>
					<td>
						<p><input type="checkbox" name="wptwa_pin_account" value="on" id="wptwa_pin_account" <?php checked( 'on', $pin_account ); ?> /> <label for="wptwa_pin_account"><?php esc_html_e( 'Yes, pin this account.', 'wptwa' ); ?></label></p>
						<p class="description"><?php esc_html_e( 'If checked, this account will always be placed on top even when the list is randomized.', 'wptwa' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for=""><?php esc_html_e( 'Display based on screen width', 'wptwa' ); ?></label></th>
					<td>
						<p><input type="checkbox" name="wptwa_hide_on_large_screen" value="on" id="wptwa_hide_on_large_screen" <?php checked( 'on', $hide_on_large_screen ); ?> /> <label for="wptwa_hide_on_large_screen"><?php esc_html_e( 'Hide on large screen (wider than 782px)', 'wptwa' ); ?></label></p>
						<p><input type="checkbox" name="wptwa_hide_on_small_screen" value="on" id="wptwa_hide_on_small_screen" <?php checked( 'on', $hide_on_small_screen ); ?> /> <label for="wptwa_hide_on_small_screen"><?php esc_html_e( 'Hide on small screen (narrower than 783px)', 'wptwa' ); ?></label></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wptwa_offline_text"><?php esc_html_e( 'Description text when offline', 'wptwa' ); ?></label></th>
					<td>
						<input type="text" id="wptwa_offline_text" name="wptwa_offline_text" value="<?php echo esc_attr( $offline_text ); ?>" class="widefat" />
						<p class="description"><?php esc_html_e( 'If this field is left blank, the account will be hidden when not available.', 'wptwa' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php
		
		wp_nonce_field( 'wptwa_account_meta_box', 'wptwa_account_meta_box_nonce' );
	}
	
	public function displayAvailabilityOptions ( $time, $value ) {
		$limit = 'hour' === $time ? 23 : 59;
		
		for ( $i = 0; $i <= $limit; $i++ ) {
			$text_number = strlen( $i ) < 2 ? '0' . $i : $i;
			$selected = intval( $value ) === $i ? 'selected' : '';
			echo '<option value="' . $text_number . '" ' . $selected . '>' . $text_number . '</option>';
		}
		
	}
	
	public function getInclusion ( $ids, $category ) {
		
		$ids = is_array( $ids ) ? $ids : array();
		$html = '';
		$category = 'included' === strtolower( $category ) ? 'included' : 'excluded';
		
		foreach ( $ids as $k => $v ) {
			if ( filter_var( $v, FILTER_VALIDATE_URL ) !== FALSE ) {
				$the_url = esc_url( $v );
				$html.= '
				<li id="wptwa-included-url-' . $k . '">
					<p class="wptwa-permalink"><a href="' . $the_url . '" target="_blank">' . $the_url . '</a></p>
					<span class="dashicons dashicons-no"></span>
					<input type="hidden" name="wptwa_' . $category . '[]" value="' . $the_url . '"/>
				</li>';
				unset( $ids[ $k ] );
			}
		}
		
		if ( count( $ids ) > 0 ) {
			global $post;
			$included_posts = get_posts( array(
				'posts_per_page' => -1,
				'post__in' => $ids,
				'post_type' => 'any'
			) );

			foreach ( $included_posts as $post ) {
				
				setup_postdata( $post );
				
				$html.= '
				<li id="wptwa-included-' . get_the_ID() . '">
					<p class="wptwa-title">' . get_the_title() . '</p>
					<p class="wptwa-permalink"><a href="' . esc_url( get_the_permalink() ) . '" target="_blank">' . esc_url( get_the_permalink() ) . '</a></p>
					<span class="dashicons dashicons-no"></span>
					<input type="hidden" name="wptwa_' . $category . '[]" value="' . get_the_ID() . '"/>
				</li>';
				
			}
			wp_reset_postdata();
		}
		return $html;
	}
	
	public function pageTargeting ( $post ) {
		
		global $pagenow;
		
		$new = 'post-new.php' === $pagenow ? true : false;
		
		if ( $new ) {
			$target = array( 'home', 'blog', 'archive', 'page', 'post' );
		}
		else {
			$target = json_decode( get_post_meta( $post->ID, 'wptwa_target', true ) );
			$target = is_array( $target ) ? $target : array();
		}
		
		/* Include and exclude ids */
		
		$included_html = $this->getInclusion ( json_decode( get_post_meta( $post->ID, 'wptwa_included_ids', true ) ), 'included' );
		$excluded_html = $this->getInclusion ( json_decode( get_post_meta( $post->ID, 'wptwa_excluded_ids', true ) ), 'excluded' );
		
		
		/* WPML languages */
		
		$current_target_languages = json_decode( get_post_meta( $post->ID, 'wptwa_target_languages', true ) );
		$current_target_languages = is_array( $current_target_languages ) ? $current_target_languages : array();
		
		$languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
		
		?>
		<p class="description"><?php esc_html_e( 'Page targeting applies only to accounts inside the floating widget. It will be ignored on shortcode buttons. Make sure to clear the cache after saving this post if you use a caching plugin.', 'wptwa' ); ?></p>
		
		<table class="form-table" id="wptwa-custom-wc-button-settings">
			<tbody>
				<tr>
					<th scope="row"><label for=""><?php esc_html_e( 'Show on these post types', 'wptwa' ); ?></label></th>
					<td>
						<p>
							<input type="checkbox" name="wptwa_target[home]" id="wptwa_target[home]" value="home" <?php echo in_array( 'home', $target ) ? 'checked' : '' ?> />
							<label for="wptwa_target[home]"><?php esc_html_e( 'Homepage', 'wptwa' ); ?></label>
						</p>
						<p>
							<input type="checkbox" name="wptwa_target[blog]" id="wptwa_target[blog]" value="blog" <?php echo in_array( 'blog', $target ) ? 'checked' : '' ?> />
							<label for="wptwa_target[blog]"><?php esc_html_e( 'Blog Index', 'wptwa' ); ?></label>
						</p>
						<p>
							<input type="checkbox" name="wptwa_target[archive]" id="wptwa_target[archive]" value="archive" <?php echo in_array( 'archive', $target ) ? 'checked' : '' ?> />
							<label for="wptwa_target[archive]"><?php esc_html_e( 'Archives', 'wptwa' ); ?></label>
						</p>
						<p>
							<input type="checkbox" name="wptwa_target[page]" id="wptwa_target[page]" value="page" <?php echo in_array( 'page', $target ) ? 'checked' : '' ?> />
							<label for="wptwa_target[page]"><?php esc_html_e( 'Pages', 'wptwa' ); ?></label>
						</p>
						<p>
							<input type="checkbox" name="wptwa_target[post]" id="wptwa_target[post]" value="post" <?php echo in_array( 'post', $target ) ? 'checked' : '' ?> />
							<label for="wptwa_target[post]"><?php esc_html_e( 'Blog posts', 'wptwa' ); ?></label>
						</p>
						<?php foreach ( get_post_types( array( '_builtin' => false ), 'objects' ) as $post_type ) : ?>
						<p>
							<input type="checkbox" name="wptwa_target[<?php echo $post_type->name; ?>]" id="wptwa_target[<?php echo $post_type->name; ?>]" value="<?php echo $post_type->name; ?>" <?php echo in_array( $post_type->name, $target ) ? 'checked' : '' ?>/>
							<label for="wptwa_target[<?php echo $post_type->name; ?>]"><?php echo esc_html( $post_type->label ); ?></label>
						</p>
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Include Pages' , 'wptwa'); ?></th>
					<td>
						<div class="wptwa-search-posts">
							<input type="text" class="regular-text" placeholder="<?php esc_attr_e( 'Type the title of page/post to include', 'wptwa' ); ?>" data-nonce="<?php echo wp_create_nonce( 'wptwa-search-nonce' ); ?>" />
							<div class="wptwa-search-result">
								<ul></ul>
							</div>
						</div>
						<p class="wptwa-listing-info"><span><?php esc_html_e( 'Included pages:', 'wptwa' ); ?></span></p>
						
						<ul class="wptwa-inclusion wptwa-included-posts" data-delete-label="<?php esc_attr_e( 'Delete', 'wptwa' ); ?>">
							<?php echo $included_html; ?>
							<li class="wptwa-placeholder"><?php esc_html_e( 'No specific page is included.', 'wptwa' ); ?></li>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Exclude Pages' , 'wptwa'); ?></th>
					<td>
						<div class="wptwa-search-posts">
							<input type="text" class="regular-text" placeholder="<?php esc_attr_e( 'Type the title of page/post to exclude', 'wptwa' ); ?>" data-nonce="<?php echo wp_create_nonce( 'wptwa-search-nonce' ); ?>" />
							<div class="wptwa-search-result">
								<ul></ul>
							</div>
						</div>
						<p class="wptwa-listing-info"><span><?php esc_html_e( 'Excluded pages:', 'wptwa' ); ?></span></p>
						
						<ul class="wptwa-inclusion wptwa-excluded-posts" data-delete-label="<?php esc_attr_e( 'Delete', 'wptwa' ); ?>">
							<?php echo $excluded_html; ?>
							<li class="wptwa-placeholder"><?php esc_html_e( 'None. All pages from checked post types above are included.', 'wptwa' ); ?></li>
						</ul>
					</td>
				</tr>
				
				<?php if ( is_array( $languages ) ) : ?>
					<tr>
						<th scope="row"><?php esc_html_e( 'WPML Languages' , 'wptwa'); ?></th>
						<td>
							<?php foreach ( $languages as $k => $v ) : ?>
							<p>
								<input type="checkbox" name="wptwa_target_languages[<?php echo $v['code']; ?>]" id="wptwa_target_languages[<?php echo $v['code']; ?>]" value="<?php echo $v['code']; ?>" <?php echo in_array( $v['code'], $current_target_languages ) ? 'checked' : '' ?>/>
								<label for="wptwa_target_languages[<?php echo $v['code']; ?>]"><?php echo esc_html( $v['translated_name'] ); ?></label>
							</p>
							<?php endforeach;?>
							<p class="description"><span><?php esc_html_e( 'If none are selected, then the account will be displayed on all languages.', 'wptwa' ); ?></span></p>
						</td>
					</tr>
				<?php endif; ?>
				
			</tbody>
		</table>
		
		<?php
		
	}
	
	public function buttonStyle ( $post ) {
		
		global $pagenow;
		
		$new = 'post-new.php' === $pagenow ? true : false;
		
		$background_color = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_background_color', true ) );
		$background_color_on_hover = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_background_color_on_hover', true ) );
		$text_color = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_text_color', true ) );
		$text_color_on_hover = sanitize_text_field( get_post_meta( $post->ID, 'wptwa_text_color_on_hover', true ) );
		
		?>
		<p class="description"><?php printf( esc_html__( 'This styling applies only to the shortcode buttons for this account. Floating widget has its own styling. If left blank (recommended for consistency), then the button will use the %1$s.', 'wptwa' ), '<a href="admin.php?page=wptwa_settings#wptwa-default-settings">' . esc_html__( 'default styles set on the settings page', 'wptwa' ) . '</a>' ); ?></p>
		<table class="form-table" id="wptwa-custom-wc-button-settings">
			<tbody>
				<tr>
					<th scope="row"><label for="wptwa_background_color"><?php esc_html_e( 'Button Background Color', 'wptwa' ); ?></label></th>
					<td><input name="wptwa_background_color" type="text" id="wptwa_background_color" class="minicolors" value="<?php echo $background_color; ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="wptwa_text_color"><?php esc_html_e( 'Button Text Color', 'wptwa' ); ?></label></th>
					<td><input name="wptwa_text_color" type="text" id="wptwa_text_color" class="minicolors" value="<?php echo $text_color; ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="wptwa_background_color_on_hover"><?php esc_html_e( 'Button Background Color on Hover', 'wptwa' ); ?></label></th>
					<td><input name="wptwa_background_color_on_hover" type="text" id="wptwa_background_color_on_hover" class="minicolors" value="<?php echo $background_color_on_hover; ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="wptwa_text_color_on_hover"><?php esc_html_e( 'Button Text Color on Hover', 'wptwa' ); ?></label></th>
					<td><input name="wptwa_text_color_on_hover" type="text" id="wptwa_text_color_on_hover" class="minicolors" value="<?php echo $text_color_on_hover; ?>"></td>
				</tr>
			</tbody>
		</table>
		
		<?php
	}
	
	public function copyShortcode ( $post ) {
		
		?>
		
		<p><?php esc_html_e( 'Copy the shortcode below and paste it into the editor to display the button.', 'wptwa' ); ?></p>
		<p><input type="text" value='[whatsapp_button id="<?php echo get_the_ID(); ?>"]' class="widefat" onkeypress="return event.keyCode != 13;" readonly /></p>
		<?php
		
	}
	
	public function links ( $post ) {
		
		echo '	<ul>
					<li><a href="http://docs.indieplugins.com/wptwa/#adding-whatsapp-accounts" target="_blank">' . esc_html__( 'Documentation', 'wptwa' ) . '</a></li>
					<li><a href="http://wp.indieplugins.com/wptwa2/" target="_blank">' . esc_html__( 'Live Demo', 'wptwa' ) . '</a></li>
					<li><a href="https://codecanyon.net/item/whatsapp-click-to-chat-for-wordpress/20248537/support" target="_blank">' . esc_html__( 'Support', 'wptwa' ) . '</a></li>
				</ul>';
		
	}
	
	public function saveMetaBoxes ( $post_id ) {
		
		/* Check if our nonce is set. */
		if ( ! isset( $_POST['wptwa_account_meta_box_nonce'] ) ) {
			return;
		}
		
		$nonce = $_POST['wptwa_account_meta_box_nonce'];
		
		/* Verify that the nonce is valid. */
		if ( ! wp_verify_nonce( $nonce, 'wptwa_account_meta_box' ) ) {
			return;
		}
		
		/* WhatsApp Account Information */
		
		$number = isset( $_POST['wptwa_number'] ) ? sanitize_text_field( trim( $_POST['wptwa_number'] ) ) : '';
		$name = isset( $_POST['wptwa_name'] ) ? sanitize_text_field( trim( $_POST['wptwa_name'] ) ) : '';
		$title = isset( $_POST['wptwa_title'] ) ? sanitize_text_field( trim( $_POST['wptwa_title'] ) ) : '';
		$predefined_text = isset( $_POST['wptwa_predefined_text'] ) ? sanitize_text_field( trim( $_POST['wptwa_predefined_text'] ) ) : '';
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			$predefined_text = isset( $_POST['wptwa_predefined_text'] ) ? sanitize_textarea_field( trim( $_POST['wptwa_predefined_text'] ) ) : '';
		}
		
		$button_label = isset( $_POST['wptwa_button_label'] ) ? sanitize_text_field( trim( $_POST['wptwa_button_label'] ) ) : '';
		$availability = isset( $_POST['wptwa_availability'] ) ? json_encode( $_POST['wptwa_availability'] ) : json_encode( array() );
		
		$offline_text = isset( $_POST['wptwa_offline_text'] ) ? sanitize_text_field( trim( $_POST['wptwa_offline_text'] ) ) : '';
		
		$hide_on_large_screen = isset( $_POST['wptwa_hide_on_large_screen'] ) ? 'on' : 'off';
		$hide_on_small_screen = isset( $_POST['wptwa_hide_on_small_screen'] ) ? 'on' : 'off';
		
		$pin_account = isset( $_POST['wptwa_pin_account'] ) ? 'on' : 'off';
		
		update_post_meta( $post_id, 'wptwa_number', $number );
		update_post_meta( $post_id, 'wptwa_name', $name );
		update_post_meta( $post_id, 'wptwa_title', $title );
		update_post_meta( $post_id, 'wptwa_predefined_text', $predefined_text );
		update_post_meta( $post_id, 'wptwa_button_label', $button_label );
		update_post_meta( $post_id, 'wptwa_availability', $availability );
		update_post_meta( $post_id, 'wptwa_offline_text', $offline_text );
		
		update_post_meta( $post_id, 'wptwa_hide_on_large_screen', $hide_on_large_screen );
		update_post_meta( $post_id, 'wptwa_hide_on_small_screen', $hide_on_small_screen );
		
		update_post_meta( $post_id, 'wptwa_pin_account', $pin_account );
		
		
		/* Button Style */
		
		$background_color = isset( $_POST['wptwa_background_color'] ) ? sanitize_text_field( trim( $_POST['wptwa_background_color'] ) ) : '';
		$background_color_on_hover = isset( $_POST['wptwa_background_color_on_hover'] ) ? sanitize_text_field( trim( $_POST['wptwa_background_color_on_hover'] ) ) : '';
		$text_color = isset( $_POST['wptwa_text_color'] ) ? sanitize_text_field( trim( $_POST['wptwa_text_color'] ) ) : '';
		$text_color_on_hover = isset( $_POST['wptwa_text_color_on_hover'] ) ? sanitize_text_field( trim( $_POST['wptwa_text_color_on_hover'] ) ) : '';
		
		update_post_meta( $post_id, 'wptwa_background_color', $background_color );
		update_post_meta( $post_id, 'wptwa_background_color_on_hover', $background_color_on_hover );
		update_post_meta( $post_id, 'wptwa_text_color', $text_color );
		update_post_meta( $post_id, 'wptwa_text_color_on_hover', $text_color_on_hover );
		
		/* Page Targeting */
		
		if ( isset( $_POST['wptwa_target'] ) ) {
			$t = array();
			foreach ( $_POST['wptwa_target'] as $value ) {
				$t[] = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, 'wptwa_target', json_encode( $t ) );
		}
		else {
			update_post_meta( $post_id, 'wptwa_target', json_encode( array() ) );
		}
		
		/* Included pages */
		if ( isset( $_POST['wptwa_included'] ) ) {
			$in_ids = array();
			foreach ( $_POST['wptwa_included'] as $value ) {
				$in_ids[] = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, 'wptwa_included_ids', json_encode( $in_ids ) );
		}
		else {
			update_post_meta( $post_id, 'wptwa_included_ids', json_encode( array() ) );
		}
		
		/* Excluded pages */
		if ( isset( $_POST['wptwa_excluded'] ) ) {
			$ex_ids = array();
			foreach ( $_POST['wptwa_excluded'] as $value ) {
				$ex_ids[] = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, 'wptwa_excluded_ids', json_encode( $ex_ids ) );
		}
		else {
			update_post_meta( $post_id, 'wptwa_excluded_ids', json_encode( array() ) );
		}
		
		/* WPML languages */
		if ( isset( $_POST['wptwa_target_languages'] ) ) {
			$t = array();
			foreach ( $_POST['wptwa_target_languages'] as $value ) {
				$t[] = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, 'wptwa_target_languages', json_encode( $t ) );
		}
		else {
			update_post_meta( $post_id, 'wptwa_target_languages', json_encode( array() ) );
		}
		
	}
	
}

?>