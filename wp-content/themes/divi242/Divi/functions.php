<?php

if ( ! isset( $content_width ) ) $content_width = 1080;

function et_setup_theme() {
	global $themename, $shortname, $et_store_options_in_one_row, $default_colorscheme;
	$themename = 'Divi';
	$shortname = 'divi';
	$et_store_options_in_one_row = true;

	$default_colorscheme = "Default";

	$template_directory = get_template_directory();

	require_once( $template_directory . '/epanel/custom_functions.php' );

	require_once( $template_directory . '/includes/functions/comments.php' );

	require_once( $template_directory . '/includes/functions/sidebars.php' );

	load_theme_textdomain( 'Divi', $template_directory . '/lang' );

	require_once( $template_directory . '/epanel/core_functions.php' );

	require_once( $template_directory . '/epanel/post_thumbnails_divi.php' );

	include( $template_directory . '/includes/widgets.php' );

	register_nav_menus( array(
		'primary-menu'   => __( 'Primary Menu', 'Divi' ),
		'secondary-menu' => __( 'Secondary Menu', 'Divi' ),
		'footer-menu'    => __( 'Footer Menu', 'Divi' ),
	) );

	// don't display the empty title bar if the widget title is not set
	remove_filter( 'widget_title', 'et_widget_force_title' );

	remove_filter( 'body_class', 'et_add_fullwidth_body_class' );

	add_action( 'wp_enqueue_scripts', 'et_add_responsive_shortcodes_css', 11 );

	add_theme_support( 'post-formats', array(
		'video', 'audio', 'quote', 'gallery', 'link'
	) );

	add_theme_support( 'woocommerce' );

	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	add_action( 'woocommerce_before_main_content', 'et_divi_output_content_wrapper', 10 );

	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_after_main_content', 'et_divi_output_content_wrapper_end', 10 );

	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

	// deactivate page templates and custom import functions
	remove_action( 'init', 'et_activate_features' );

	remove_action('admin_menu', 'et_add_epanel');
}
add_action( 'after_setup_theme', 'et_setup_theme' );

function et_theme_epanel_reminder(){
	global $shortname, $themename;

	$documentation_url         = 'http://www.elegantthemes.com/gallery/divi/readme.html';
	$documentation_option_name = $shortname . '_2_4_documentation_message';

	if ( false === et_get_option( $shortname . '_logo' ) && false === et_get_option( $documentation_option_name ) ) {
		$message = sprintf(
			__( 'Welcome to Divi! Before diving in to your new theme, please visit the <a style="color: #fff; font-weight: bold;" href="%1$s" target="_blank">Divi Documentation</a> page for access to dozens of in-depth tutorials.', $themename ),
			esc_url( $documentation_url )
		);

		printf(
			'<div class="notice is-dismissible" style="background-color: #6C2EB9; color: #fff; border-left: none;">
				<p>%1$s</p>
			</div>',
			$message
		);

		et_update_option( $documentation_option_name, 'triggered' );
	}
}
add_action( 'admin_init', 'et_theme_epanel_reminder' );

if ( ! function_exists( 'et_divi_fonts_url' ) ) :
function et_divi_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Open Sans, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'Divi' );

	if ( 'off' !== $open_sans ) {
		$font_families = array();

		if ( 'off' !== $open_sans )
			$font_families[] = 'Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => implode( '%7C', $font_families ),
			'subset' => 'latin,latin-ext',
		);
		$fonts_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );
	}

	return $fonts_url;
}
endif;

function et_divi_load_fonts() {
	$fonts_url = et_divi_fonts_url();
	if ( ! empty( $fonts_url ) )
		wp_enqueue_style( 'divi-fonts', esc_url_raw( $fonts_url ), array(), null );
}
add_action( 'wp_enqueue_scripts', 'et_divi_load_fonts' );

function et_add_home_link( $args ) {
	// add Home link to the custom menu WP-Admin page
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'et_add_home_link' );

function et_divi_load_scripts_styles(){
	global $wp_styles;

	$template_dir = get_template_directory_uri();

	$theme_version = et_get_theme_version();

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_register_script( 'google-maps-api', esc_url( add_query_arg( array( 'v' => 3, 'sensor' => 'false' ), is_ssl() ? 'https://maps-api-ssl.google.com/maps/api/js' : 'http://maps.google.com/maps/api/js' ) ), array(), $theme_version, true );
	wp_enqueue_script( 'divi-fitvids', $template_dir . '/js/jquery.fitvids.js', array( 'jquery' ), $theme_version, true );
	wp_enqueue_script( 'waypoints', $template_dir . '/js/waypoints.min.js', array( 'jquery' ), $theme_version, true );
	wp_enqueue_script( 'magnific-popup', $template_dir . '/js/jquery.magnific-popup.min.js', array( 'jquery' ), $theme_version, true );
	wp_register_script( 'hashchange', $template_dir . '/js/jquery.hashchange.js', array( 'jquery' ), $theme_version, true );
	wp_register_script( 'salvattore', $template_dir . '/js/salvattore.min.js', array(), $theme_version, true );
	wp_register_script( 'easypiechart', $template_dir . '/js/jquery.easypiechart.js', array( 'jquery' ), $theme_version, true );
	wp_enqueue_script( 'divi-custom-script', $template_dir . '/js/custom.min.js', array( 'jquery' ), $theme_version, true );
	wp_localize_script( 'divi-custom-script', 'et_custom', array(
		'ajaxurl'             => admin_url( 'admin-ajax.php' ),
		'images_uri'          => get_template_directory_uri() . '/images',
		'builder_images_uri'  => get_template_directory_uri() . '/includes/builder/images',
		'et_load_nonce'       => wp_create_nonce( 'et_load_nonce' ),
		'subscription_failed' => __( 'Please, check the fields below to make sure you entered the correct information.', 'Divi' ),
		'fill'                => esc_html__( 'Fill', 'Divi' ),
		'field'               => esc_html__( 'field', 'Divi' ),
		'invalid'             => esc_html__( 'Invalid email', 'Divi' ),
		'captcha'             => esc_html__( 'Captcha', 'Divi' ),
		'prev'				  => esc_html__( 'Prev', 'Divi' ),
		'previous'            => esc_html__( 'Previous', 'Divi' ),
		'next'				  => esc_html__( 'Next', 'Divi' ),
	) );

	if ( 'on' === et_get_option( 'divi_smooth_scroll', false ) ) {
		wp_enqueue_script( 'smooth-scroll', $template_dir . '/js/smoothscroll.js', array( 'jquery' ), $theme_version, true );
	}

	$et_gf_enqueue_fonts = array();
	$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
	$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );
	$et_gf_button_font = sanitize_text_field( et_get_option( 'all_buttons_font', 'none' ) );
	$et_gf_primary_nav_font = sanitize_text_field( et_get_option( 'primary_nav_font', 'none' ) );
	$et_gf_secondary_nav_font = sanitize_text_field( et_get_option( 'secondary_nav_font', 'none' ) );

	$site_domain = get_locale();
	$et_one_font_languages = et_get_one_font_languages();

	if ( 'none' != $et_gf_heading_font ) $et_gf_enqueue_fonts[] = $et_gf_heading_font;
	if ( 'none' != $et_gf_body_font ) $et_gf_enqueue_fonts[] = $et_gf_body_font;
	if ( 'none' != $et_gf_button_font ) $et_gf_enqueue_fonts[] = $et_gf_button_font;
	if ( 'none' != $et_gf_primary_nav_font ) $et_gf_enqueue_fonts[] = $et_gf_primary_nav_font;
	if ( 'none' != $et_gf_secondary_nav_font ) $et_gf_enqueue_fonts[] = $et_gf_secondary_nav_font;

	if ( isset( $et_one_font_languages[$site_domain] ) ) {
		$et_gf_font_name_slug = strtolower( str_replace( ' ', '-', $et_one_font_languages[$site_domain]['language_name'] ) );
		wp_enqueue_style( 'et-gf-' . $et_gf_font_name_slug, $et_one_font_languages[$site_domain]['google_font_url'], array(), null );
	} else if ( ! empty( $et_gf_enqueue_fonts ) ) {
		foreach ( $et_gf_enqueue_fonts as $single_font ) {
			et_builder_enqueue_font( $single_font );
		}
	}

	/*
	 * Loads the main stylesheet.
	 */
	wp_enqueue_style( 'divi-style', get_stylesheet_uri(), array(), $theme_version );
}
add_action( 'wp_enqueue_scripts', 'et_divi_load_scripts_styles' );

function et_add_mobile_navigation(){
	printf(
		'<div id="et_mobile_nav_menu">
			<a href="#" class="mobile_nav closed">
				<span class="select_page">%1$s</span>
				<span class="mobile_menu_bar"></span>
			</a>
		</div>',
		esc_html__( 'Select Page', 'Divi' )
	);
}
add_action( 'et_header_top', 'et_add_mobile_navigation' );

function et_add_viewport_meta(){
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />';
}
add_action( 'wp_head', 'et_add_viewport_meta' );

function et_remove_additional_stylesheet( $stylesheet ){
	global $default_colorscheme;
	return $default_colorscheme;
}
add_filter( 'et_get_additional_color_scheme', 'et_remove_additional_stylesheet' );

if ( ! function_exists( 'et_list_pings' ) ) :
function et_list_pings($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
<?php }
endif;

if ( ! function_exists( 'et_get_theme_version' ) ) :
function et_get_theme_version() {
	$theme_info = wp_get_theme();

	if ( is_child_theme() ) {
		$theme_info = wp_get_theme( $theme_info->parent_theme );
	}

	$theme_version = $theme_info->display( 'Version' );

	return $theme_version;
}
endif;

function et_add_post_meta_box() {
	add_meta_box( 'et_settings_meta_box', __( 'Divi Page Settings', 'Divi' ), 'et_single_settings_meta_box', 'page', 'side', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'Divi Post Settings', 'Divi' ), 'et_single_settings_meta_box', 'post', 'side', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'Divi Product Settings', 'Divi' ), 'et_single_settings_meta_box', 'product', 'side', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'Divi Project Settings', 'Divi' ), 'et_single_settings_meta_box', 'project', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'et_add_post_meta_box' );

function et_pb_register_posttypes() {
	$labels = array(
		'name'               => __( 'Projects', 'Divi' ),
		'singular_name'      => __( 'Project', 'Divi' ),
		'add_new'            => __( 'Add New', 'Divi' ),
		'add_new_item'       => __( 'Add New Project', 'Divi' ),
		'edit_item'          => __( 'Edit Project', 'Divi' ),
		'new_item'           => __( 'New Project', 'Divi' ),
		'all_items'          => __( 'All Projects', 'Divi' ),
		'view_item'          => __( 'View Project', 'Divi' ),
		'search_items'       => __( 'Search Projects', 'Divi' ),
		'not_found'          => __( 'Nothing found', 'Divi' ),
		'not_found_in_trash' => __( 'Nothing found in Trash', 'Divi' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'can_export'         => true,
		'show_in_nav_menus'  => true,
		'query_var'          => true,
		'has_archive'        => true,
		'rewrite'            => apply_filters( 'et_project_posttype_rewrite_args', array(
			'feeds'      => true,
			'slug'       => 'project',
			'with_front' => false,
		) ),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
	);

	register_post_type( 'project', apply_filters( 'et_project_posttype_args', $args ) );

	$labels = array(
		'name'              => __( 'Categories', 'Divi' ),
		'singular_name'     => __( 'Category', 'Divi' ),
		'search_items'      => __( 'Search Categories', 'Divi' ),
		'all_items'         => __( 'All Categories', 'Divi' ),
		'parent_item'       => __( 'Parent Category', 'Divi' ),
		'parent_item_colon' => __( 'Parent Category:', 'Divi' ),
		'edit_item'         => __( 'Edit Category', 'Divi' ),
		'update_item'       => __( 'Update Category', 'Divi' ),
		'add_new_item'      => __( 'Add New Category', 'Divi' ),
		'new_item_name'     => __( 'New Category Name', 'Divi' ),
		'menu_name'         => __( 'Categories', 'Divi' ),
	);

	register_taxonomy( 'project_category', array( 'project' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	) );

	$labels = array(
		'name'              => __( 'Tags', 'Divi' ),
		'singular_name'     => __( 'Tag', 'Divi' ),
		'search_items'      => __( 'Search Tags', 'Divi' ),
		'all_items'         => __( 'All Tags', 'Divi' ),
		'parent_item'       => __( 'Parent Tag', 'Divi' ),
		'parent_item_colon' => __( 'Parent Tag:', 'Divi' ),
		'edit_item'         => __( 'Edit Tag', 'Divi' ),
		'update_item'       => __( 'Update Tag', 'Divi' ),
		'add_new_item'      => __( 'Add New Tag', 'Divi' ),
		'new_item_name'     => __( 'New Tag Name', 'Divi' ),
		'menu_name'         => __( 'Tags', 'Divi' ),
	);

	register_taxonomy( 'project_tag', array( 'project' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	) );
}
add_action( 'init', 'et_pb_register_posttypes', 0 );

if ( ! function_exists( 'et_pb_portfolio_meta_box' ) ) :
function et_pb_portfolio_meta_box() { ?>
	<div class="et_project_meta">
		<strong class="et_project_meta_title"><?php echo esc_html__( 'Skills', 'Divi' ); ?></strong>
		<p><?php echo get_the_term_list( get_the_ID(), 'project_tag', '', ', ' ); ?></p>

		<strong class="et_project_meta_title"><?php echo esc_html__( 'Posted on', 'Divi' ); ?></strong>
		<p><?php echo get_the_date(); ?></p>
	</div>
<?php }
endif;

if ( ! function_exists( 'et_single_settings_meta_box' ) ) :
function et_single_settings_meta_box( $post ) {
	$post_id = get_the_ID();

	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );

	$page_layout = get_post_meta( $post_id, '_et_pb_page_layout', true );

	$side_nav = get_post_meta( $post_id, '_et_pb_side_nav', true );

	$post_hide_nav = get_post_meta( $post_id, '_et_pb_post_hide_nav', true );

	$show_title = get_post_meta( $post_id, '_et_pb_show_title', true );

	$page_layouts = array(
		'et_right_sidebar'   => __( 'Right Sidebar', 'Divi' ),
		'et_left_sidebar'    => __( 'Left Sidebar', 'Divi' ),
		'et_full_width_page' => __( 'Full Width', 'Divi' ),
	);

	$layouts = array(
		'light' => __( 'Light', 'Divi' ),
		'dark'  => __( 'Dark', 'Divi' ),
	);
	$post_bg_color  = ( $bg_color = get_post_meta( $post_id, '_et_post_bg_color', true ) ) && '' !== $bg_color
		? $bg_color
		: '#ffffff';
	$post_use_bg_color = get_post_meta( $post_id, '_et_post_use_bg_color', true )
		? true
		: false;
	$post_bg_layout = ( $layout = get_post_meta( $post_id, '_et_post_bg_layout', true ) ) && '' !== $layout
		? $layout
		: 'light'; ?>

	<p class="et_pb_page_settings et_pb_page_layout_settings">
		<label for="et_pb_page_layout" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Page Layout', 'Divi' ); ?>: </label>

		<select id="et_pb_page_layout" name="et_pb_page_layout">
		<?php
		foreach ( $page_layouts as $layout_value => $layout_name ) {
			printf( '<option value="%2$s"%3$s>%1$s</option>',
				esc_html( $layout_name ),
				esc_attr( $layout_value ),
				selected( $layout_value, $page_layout, false )
			);
		} ?>
		</select>
	</p>
	<p class="et_pb_page_settings et_pb_side_nav_settings" style="display: none;">
		<label for="et_pb_side_nav" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Dot Navigation', 'Divi' ); ?>: </label>

		<select id="et_pb_side_nav" name="et_pb_side_nav">
			<option value="off" <?php selected( 'off', $side_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
			<option value="on" <?php selected( 'on', $side_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
		</select>
	</p>
	<p class="et_pb_page_settings">
		<label for="et_pb_post_hide_nav" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Hide Nav Before Scroll', 'Divi' ); ?>: </label>

		<select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
			<option value="off" <?php selected( 'off', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
			<option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
		</select>
	</p>

<?php if ( 'post' === $post->post_type ) : ?>
	<p class="et_pb_page_settings et_pb_single_title" style="display: none;">
		<label for="et_single_title" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Post Title', 'Divi' ); ?>: </label>

		<select id="et_single_title" name="et_single_title">
			<option value="on" <?php selected( 'on', $show_title ); ?>><?php esc_html_e( 'Show', 'Divi' ); ?></option>
			<option value="off" <?php selected( 'off', $show_title ); ?>><?php esc_html_e( 'Hide', 'Divi' ); ?></option>
		</select>
	</p>

	<p class="et_divi_quote_settings et_divi_audio_settings et_divi_link_settings et_divi_format_setting et_pb_page_settings">
		<label for="et_post_use_bg_color" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Use Background Color', 'Divi' ); ?></label>
		<input name="et_post_use_bg_color" type="checkbox" id="et_post_use_bg_color" <?php checked( $post_use_bg_color ); ?> />
	</p>

	<p class="et_post_bg_color_setting et_divi_format_setting et_pb_page_settings">
		<input id="et_post_bg_color" name="et_post_bg_color" class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'Divi' ); ?>" value="<?php echo esc_attr( $post_bg_color ); ?>" data-default-color="#ffffff" />
	</p>

	<p class="et_divi_quote_settings et_divi_audio_settings et_divi_link_settings et_divi_format_setting">
		<label for="et_post_bg_layout" style="font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Text Color', 'Divi' ); ?>: </label>
		<select id="et_post_bg_layout" name="et_post_bg_layout">
	<?php
		foreach ( $layouts as $layout_name => $layout_title )
			printf( '<option value="%s"%s>%s</option>',
				esc_attr( $layout_name ),
				selected( $layout_name, $post_bg_layout, false ),
				esc_html( $layout_title )
			);
	?>
		</select>
	</p>
<?php endif;

}
endif;

function et_divi_post_settings_save_details( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( ! isset( $_POST['et_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	if ( isset( $_POST['et_post_use_bg_color'] ) )
		update_post_meta( $post_id, '_et_post_use_bg_color', true );
	else
		delete_post_meta( $post_id, '_et_post_use_bg_color' );

	if ( isset( $_POST['et_post_bg_color'] ) )
		update_post_meta( $post_id, '_et_post_bg_color', sanitize_text_field( $_POST['et_post_bg_color'] ) );
	else
		delete_post_meta( $post_id, '_et_post_bg_color' );

	if ( isset( $_POST['et_post_bg_layout'] ) )
		update_post_meta( $post_id, '_et_post_bg_layout', sanitize_text_field( $_POST['et_post_bg_layout'] ) );
	else
		delete_post_meta( $post_id, '_et_post_bg_layout' );

	if ( isset( $_POST['et_single_title'] ) )
		update_post_meta( $post_id, '_et_pb_show_title', sanitize_text_field( $_POST['et_single_title'] ) );
	else
		delete_post_meta( $post_id, '_et_pb_show_title' );

	if ( isset( $_POST['et_pb_post_hide_nav'] ) )
		update_post_meta( $post_id, '_et_pb_post_hide_nav', sanitize_text_field( $_POST['et_pb_post_hide_nav'] ) );
	else
		delete_post_meta( $post_id, '_et_pb_post_hide_nav' );

}
add_action( 'save_post', 'et_divi_post_settings_save_details', 10, 2 );

function et_get_one_font_languages() {
	$one_font_languages = array(
		'he_IL' => array(
			'language_name'   => 'Hebrew',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/alefhebrew.css',
			'font_family'     => "'Alef Hebrew', serif",
		),
		'ja' => array(
			'language_name'   => 'Japanese',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansjapanese.css',
			'font_family'     => "'Noto Sans Japanese', serif",
		),
		'ko_KR' => array(
			'language_name'   => 'Korean',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/hanna.css',
			'font_family'     => "'Hanna', serif",
		),
		'ar' => array(
			'language_name'   => 'Arabic',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/lateef.css',
			'font_family'     => "'Lateef', serif",
		),
		'th' => array(
			'language_name'   => 'Thai',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansthai.css',
			'font_family'     => "'Noto Sans Thai', serif",
		),
		'ms_MY' => array(
			'language_name'   => 'Malay',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansmalayalam.css',
			'font_family'     => "'Noto Sans Malayalam', serif",
		),
		'zh_CN' => array(
			'language_name'   => 'Chinese',
			'google_font_url' => '//fonts.googleapis.com/earlyaccess/cwtexfangsong.css',
			'font_family'     => "'cwTeXFangSong', serif",
		),
	);

	return $one_font_languages;
}

function et_divi_customize_register( $wp_customize ) {
	$wp_customize->remove_section( 'title_tagline' );
	$wp_customize->remove_section( 'background_image' );
	$wp_customize->remove_section( 'colors' );
	$wp_customize->register_control_type( 'ET_Divi_Customize_Color_Alpha_Control' );

	wp_register_script( 'wp-color-picker-alpha', get_template_directory_uri() . '/includes/builder/scripts/ext/wp-color-picker-alpha.min.js', array( 'jquery', 'wp-color-picker' ) );

	$option_set_name           = 'et_customizer_option_set';
	$option_set_allowed_values = apply_filters( 'et_customizer_option_set_allowed_values', array( 'module', 'theme' ) );

	$customizer_option_set_cookie = '';

	/**
	 * Set cookie,
	 * if 'et_customizer_option_set' query parameter is set to one of the allowed values
	 */
	if ( isset( $_GET[ $option_set_name ] ) && in_array( $_GET[ $option_set_name ], $option_set_allowed_values ) ) {
		$customizer_option_set_cookie = $_GET[ $option_set_name ];

		$secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) );
		setcookie( $option_set_name, $customizer_option_set_cookie, time() + DAY_IN_SECONDS, SITECOOKIEPATH, null, $secure );
	}

	if ( '' === $customizer_option_set_cookie && isset( $_COOKIE[ $option_set_name ] ) ) {
		$customizer_option_set_cookie = $_COOKIE[ $option_set_name ];
	}

	et_builder_init_global_settings();

	if ( isset( $customizer_option_set_cookie ) && 'module' === $customizer_option_set_cookie ) {
		$removed_default_sections = array( 'nav', 'static_front_page' );
		foreach ( $removed_default_sections as $default_section ) {
			$wp_customize->remove_section( $default_section );
		}

		$wp_customize->remove_panel( 'widgets' );

		et_divi_customizer_module_settings( $wp_customize );
	} else {
		et_divi_customizer_theme_settings( $wp_customize );
	}
}
add_action( 'customize_register', 'et_divi_customize_register' );

if ( ! function_exists( 'et_divi_customizer_theme_settings' ) ) :
function et_divi_customizer_theme_settings( $wp_customize ) {
	$site_domain = get_locale();

	$google_fonts = et_builder_get_google_fonts();

	$et_domain_fonts = array(
		'ru_RU' => 'cyrillic',
		'uk' => 'cyrillic',
		'bg_BG' => 'cyrillic',
		'vi' => 'vietnamese',
		'el' => 'greek',
	);

	$et_one_font_languages = et_get_one_font_languages();

	$font_choices = array();
	$font_choices['none'] = array(
		'label' => 'Default Theme Font'
	);

	foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
		if ( '' !== $site_domain && isset( $et_domain_fonts[$site_domain] ) && false === strpos( $google_font_properties['character_set'], $et_domain_fonts[$site_domain] ) ) {
			continue;
		}
		$font_choices[ $google_font_name ] = array(
			'label' => $google_font_name,
			'data'  => array(
				'parent_font'    => isset( $google_font_properties['parent_font'] ) ? $google_font_properties['parent_font'] : '',
				'parent_styles'  => isset( $google_font_properties['parent_font'] ) && isset( $google_fonts[$google_font_properties['parent_font']]['styles'] ) ? $google_fonts[$google_font_properties['parent_font']]['styles'] : $google_font_properties['styles'],
				'current_styles' => isset( $google_font_properties['parent_font'] ) && isset( $google_fonts[$google_font_properties['parent_font']]['styles'] ) && isset( $google_font_properties['styles'] ) ? $google_font_properties['styles'] : '',
				'parent_subset'  => isset( $google_font_properties['parent_font'] ) && isset( $google_fonts[$google_font_properties['parent_font']]['character_set'] ) ? $google_fonts[$google_font_properties['parent_font']]['character_set'] : ''
			)
		);
	}

	$wp_customize->add_panel( 'et_divi_general_settings' , array(
		'title'		=> __( 'General Settings', 'Divi' ),
		'priority'	=> 1,
	) );

	$wp_customize->add_section( 'et_divi_general_layout' , array(
		'title'		=> __( 'Layout Settings', 'Divi' ),
		'panel' => 'et_divi_general_settings',
	) );

	$wp_customize->add_section( 'et_divi_general_typography' , array(
		'title'		=> __( 'Typography', 'Divi' ),
		'panel' => 'et_divi_general_settings',
	) );

	$wp_customize->add_panel( 'et_divi_mobile' , array(
		'title'		=> __( 'Mobile Styles', 'Divi' ),
		'priority' => 6,
	) );

	$wp_customize->add_section( 'et_divi_mobile_tablet' , array(
		'title'		=> __( 'Tablet', 'Divi' ),
		'panel' => 'et_divi_mobile',
	) );

	$wp_customize->add_section( 'et_divi_mobile_phone' , array(
		'title'		=> __( 'Phone', 'Divi' ),
		'panel' => 'et_divi_mobile',
	) );

	$wp_customize->add_section( 'et_divi_mobile_menu' , array(
		'title'		=> __( 'Mobile Menu', 'Divi' ),
		'panel' => 'et_divi_mobile',
	) );

	$wp_customize->add_section( 'et_divi_general_background' , array(
		'title'		=> __( 'Background', 'Divi' ),
		'panel' => 'et_divi_general_settings',
	) );

	$wp_customize->add_panel( 'et_divi_header_panel', array(
	    'title' => __( 'Header & Navigation', 'Divi' ),
	    'priority' => 2,
	) );

	$wp_customize->add_section( 'et_divi_header_layout' , array(
		'title'		=> __( 'Header Format', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_section( 'et_divi_header_primary' , array(
		'title'		=> __( 'Primary Menu Bar', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_section( 'et_divi_header_secondary' , array(
		'title'		=> __( 'Secondary Menu Bar', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_section( 'et_divi_header_fixed' , array(
		'title'		=> __( 'Fixed Navigation Settings', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_section( 'et_divi_header_information' , array(
		'title'		=> __( 'Header Elements', 'Divi' ),
		'panel' => 'et_divi_header_panel',
	) );

	$wp_customize->add_panel( 'et_divi_footer_panel' , array(
		'title'		=> __( 'Footer', 'Divi' ),
		'priority'	=> 3,
	) );

	$wp_customize->add_section( 'et_divi_footer_layout' , array(
		'title'		=> __( 'Layout', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_divi_footer_widgets' , array(
		'title'		=> __( 'Widgets', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_divi_footer_elements' , array(
		'title'		=> __( 'Footer Elements', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_divi_footer_menu' , array(
		'title'		=> __( 'Footer Menu', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_divi_bottom_bar' , array(
		'title'		=> __( 'Bottom Bar', 'Divi' ),
		'panel' => 'et_divi_footer_panel',
	) );

	$wp_customize->add_section( 'et_color_schemes' , array(
		'title'       => __( 'Color Schemes', 'Divi' ),
		'priority'    => 7,
		'description' => __( 'Note: Color settings set above should be applied to the Default color scheme.', 'Divi' ),
	) );

	$wp_customize->add_panel( 'et_divi_buttons_settings' , array(
		'title'		=> __( 'Buttons', 'Divi' ),
		'priority'	=> 4,
	) );

	$wp_customize->add_section( 'et_divi_buttons' , array(
		'title'       => __( 'Buttons Style', 'Divi' ),
		'panel'       => 'et_divi_buttons_settings',
	) );

	$wp_customize->add_section( 'et_divi_buttons_hover' , array(
		'title'       => __( 'Buttons Hover Style', 'Divi' ),
		'panel'       => 'et_divi_buttons_settings',
	) );

	$wp_customize->add_panel( 'et_divi_blog_settings' , array(
		'title'		=> __( 'Blog', 'Divi' ),
		'priority'	=> 5,
	) );

	$wp_customize->add_section( 'et_divi_blog_post' , array(
		'title'       => __( 'Post', 'Divi' ),
		'panel'       => 'et_divi_blog_settings',
	) );

	$wp_customize->add_setting( 'et_divi[post_meta_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_meta_font_size]', array(
		'label'	      => __( 'Meta Text Size', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_meta_height]', array(
		'default'       => '1',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_meta_height]', array(
		'label'	      => __( 'Meta Line Height', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => .8,
			'max'  => 3,
			'step' => .1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_meta_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_meta_spacing]', array(
		'label'	      => __( 'Meta Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_meta_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[post_meta_style]', array(
		'label'	      => __( 'Meta Font Style', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'       => __( 'Bold', 'Divi' ),
			'italic'     => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_header_font_size]', array(
		'default'       => '30',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_header_font_size]', array(
		'label'	      => __( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 72,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_header_height]', array(
		'default'       => '1',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_header_height]', array(
		'label'	      => __( 'Header Line Height', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0.8,
			'max'  => 3,
			'step' => 0.1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_header_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[post_header_spacing]', array(
		'label'	      => __( 'Header Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[post_header_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[post_header_style]', array(
		'label'	      => __( 'Header Font Style', 'Divi' ),
		'section'     => 'et_divi_blog_post',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'       => __( 'Bold', 'Divi' ),
			'italic'     => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[boxed_layout]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[boxed_layout]', array(
		'label'		=> __( 'Enable Boxed Layout', 'Divi' ),
		'section'	=> 'et_divi_general_layout',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[content_width]', array(
		'default'       => '1080',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[content_width]', array(
		'label'	      => __( 'Website Content Width', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 960,
			'max'  => 1920,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[gutter_width]', array(
		'default'       => '3',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[gutter_width]', array(
		'label'	      => __( 'Website Gutter Width', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 1,
			'max'  => 4,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[use_sidebar_width]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[use_sidebar_width]', array(
		'label'		=> __( 'Use Custom Sidebar Width', 'Divi' ),
		'section'	=> 'et_divi_general_layout',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[sidebar_width]', array(
		'default'       => '21',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[sidebar_width]', array(
		'label'	      => __( 'Sidebar Width', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 19,
			'max'  => 33,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[section_padding]', array(
		'default'       => '4',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[section_padding]', array(
		'label'	      => __( 'Section Height', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[phone_section_height]', array(
		'default'       => et_get_option( 'tablet_section_height', '50' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[phone_section_height]', array(
		'label'	      => __( 'Section Height', 'Divi' ),
		'section'     => 'et_divi_mobile_phone',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 150,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[tablet_section_height]', array(
		'default'       => '50',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[tablet_section_height]', array(
		'label'	      => __( 'Section Height', 'Divi' ),
		'section'     => 'et_divi_mobile_tablet',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 150,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[row_padding]', array(
		'default'       => '2',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[row_padding]', array(
		'label'	      => __( 'Row Height', 'Divi' ),
		'section'     => 'et_divi_general_layout',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[phone_row_height]', array(
		'default'       => et_get_option( 'tablet_row_height', '30' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[phone_row_height]', array(
		'label'	      => __( 'Row Height', 'Divi' ),
		'section'     => 'et_divi_mobile_phone',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 150,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[tablet_row_height]', array(
		'default'       => '30',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[tablet_row_height]', array(
		'label'	      => __( 'Row Height', 'Divi' ),
		'section'     => 'et_divi_mobile_tablet',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 150,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[cover_background]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[cover_background]', array(
		'label'		=> __( 'Stretch Background Image', 'Divi' ),
		'section'	=> 'et_divi_general_background',
		'type'      => 'checkbox',
	) );

	if ( ! is_null( $wp_customize->get_setting( 'background_color' ) ) ) {
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
			'label'		=> __( 'Background Color', 'Divi' ),
			'section'	=> 'et_divi_general_background',
		) ) );
	}

	if ( ! is_null( $wp_customize->get_setting( 'background_image' ) ) ) {
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'background_image', array(
			'label'		=> __( 'Background Image', 'Divi' ),
			'section'	=> 'et_divi_general_background',
		) ) );
	}

	$wp_customize->add_control( 'background_repeat', array(
		'label'		=> __( 'Background Repeat', 'Divi' ),
		'section'	=> 'et_divi_general_background',
		'type'      => 'radio',
		'choices'    => array(
				'no-repeat'  => __( 'No Repeat', 'Divi' ),
				'repeat'     => __( 'Tile', 'Divi' ),
				'repeat-x'   => __( 'Tile Horizontally', 'Divi' ),
				'repeat-y'   => __( 'Tile Vertically', 'Divi' ),
			),
	) );

	$wp_customize->add_control( 'background_position_x', array(
		'label'		=> __( 'Background Position', 'Divi' ),
		'section'	=> 'et_divi_general_background',
		'type'      => 'radio',
		'choices'    => array(
				'left'       => __( 'Left', 'Divi' ),
				'center'     => __( 'Center', 'Divi' ),
				'right'      => __( 'Right', 'Divi' ),
			),
	) );

	$wp_customize->add_control( 'background_attachment', array(
		'label'		=> __( 'Background Position', 'Divi' ),
		'section'	=> 'et_divi_general_background',
		'type'      => 'radio',
		'choices'    => array(
				'scroll'     => __( 'Scroll', 'Divi' ),
				'fixed'      => __( 'Fixed', 'Divi' ),
			),
	) );

	$wp_customize->add_setting( 'et_divi[body_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_font_size]', array(
		'label'	      => __( 'Body Text Size', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_font_height]', array(
		'default'       => '1.7',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_font_height]', array(
		'label'	      => __( 'Body Line Height', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0.8,
			'max'  => 3,
			'step' => 0.1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[phone_body_font_size]', array(
		'default'       => et_get_option( 'tablet_body_font_size', '14' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[phone_body_font_size]', array(
		'label'	      => __( 'Body Text Size', 'Divi' ),
		'section'     => 'et_divi_mobile_phone',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[tablet_body_font_size]', array(
		'default'       => et_get_option( 'body_font_size', '14' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[tablet_body_font_size]', array(
		'label'	      => __( 'Body Text Size', 'Divi' ),
		'section'     => 'et_divi_mobile_tablet',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_header_size]', array(
		'default'       => '30',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_header_size]', array(
		'label'	      => __( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 22,
			'max'  => 72,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_header_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_header_spacing]', array(
		'label'	      => __( 'Header Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_header_height]', array(
		'default'       => '1',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[body_header_height]', array(
		'label'	      => __( 'Header Line Height', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0.8,
			'max'  => 3,
			'step' => 0.1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[body_header_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[body_header_style]', array(
		'label'	      => __( 'Header Font Style', 'Divi' ),
		'section'     => 'et_divi_general_typography',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'       => __( 'Bold', 'Divi' ),
			'italic'     => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[phone_header_font_size]', array(
		'default'       => et_get_option( 'tablet_header_font_size', '30' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[phone_header_font_size]', array(
		'label'	      => __( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_mobile_phone',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 22,
			'max'  => 72,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[tablet_header_font_size]', array(
		'default'       => et_get_option( 'body_header_size', '30' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[tablet_header_font_size]', array(
		'label'	      => __( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_mobile_tablet',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 22,
			'max'  => 72,
			'step' => 1
		),
	) ) );

	if ( ! isset( $et_one_font_languages[$site_domain] ) ) {
		$wp_customize->add_setting( 'et_divi[heading_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[heading_font]', array(
			'label'		=> __( 'Header Font', 'Divi' ),
			'section'	=> 'et_divi_general_typography',
			'settings'	=> 'et_divi[heading_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );

		$wp_customize->add_setting( 'et_divi[body_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[body_font]', array(
			'label'		=> __( 'Body Font', 'Divi' ),
			'section'	=> 'et_divi_general_typography',
			'settings'	=> 'et_divi[body_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[link_color]', array(
		'default'	=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[link_color]', array(
		'label'		=> __( 'Body Link Color', 'Divi' ),
		'section'	=> 'et_divi_general_typography',
		'settings'	=> 'et_divi[link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[font_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[font_color]', array(
		'label'		=> __( 'Body Text Color', 'Divi' ),
		'section'	=> 'et_divi_general_typography',
		'settings'	=> 'et_divi[font_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[header_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[header_color]', array(
		'label'		=> __( 'Header Text Color', 'Divi' ),
		'section'	=> 'et_divi_general_typography',
		'settings'	=> 'et_divi[header_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[accent_color]', array(
		'default'		=> '#2ea3f2',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[accent_color]', array(
		'label'		=> __( 'Theme Accent Color', 'Divi' ),
		'section'	=> 'et_divi_general_layout',
		'settings'	=> 'et_divi[accent_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[color_schemes]', array(
		'default'		=> 'none',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( 'et_divi[color_schemes]', array(
		'label'		=> __( 'Color Schemes', 'Divi' ),
		'section'	=> 'et_color_schemes',
		'settings'	=> 'et_divi[color_schemes]',
		'type'		=> 'select',
		'choices'	=> array(
			'none'   => __( 'Default', 'Divi' ),
			'green'  => __( 'Green', 'Divi' ),
			'orange' => __( 'Orange', 'Divi' ),
			'pink'   => __( 'Pink', 'Divi' ),
			'red'    => __( 'Red', 'Divi' ),
		),
	) );

	$wp_customize->add_setting( 'et_divi[header_style]', array(
		'default'       => 'left',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[header_style]', array(
		'label'		=> __( 'Header Style', 'Divi' ),
		'section'	=> 'et_divi_header_layout',
		'type'      => 'select',
		'choices'	=> array(
			'left'     => __( 'Default', 'Divi' ),
			'centered' => __( 'Centered', 'Divi' ),
			'split'	   => __( 'Centered Inline Logo', 'Divi' )
		),
	) );

	$wp_customize->add_setting( 'et_divi[vertical_nav]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[vertical_nav]', array(
		'label'		=> __( 'Enable Vertical Navigation', 'Divi' ),
		'section'	=> 'et_divi_header_layout',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[hide_nav]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[hide_nav]', array(
		'label'		=> __( 'Hide Navigation Until Scroll', 'Divi' ),
		'section'	=> 'et_divi_header_layout',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[show_header_social_icons]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[show_header_social_icons]', array(
		'label'		=> __( 'Show Social Icons', 'Divi' ),
		'section'	=> 'et_divi_header_information',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[show_search_icon]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[show_search_icon]', array(
		'label'		=> __( 'Show Search Icon', 'Divi' ),
		'section'	=> 'et_divi_header_information',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[nav_fullwidth]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[nav_fullwidth]', array(
		'label'		=> __( 'Make Full Width', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[hide_primary_logo]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[hide_primary_logo]', array(
		'label'		=> __( 'Hide Logo Image', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[menu_height]', array(
		'default'       => '66',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[menu_height]', array(
		'label'	      => __( 'Menu/Logo Height', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 30,
			'max'  => 300,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[primary_nav_font_size]', array(
		'label'	      => __( 'Text Size', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 24,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_font_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[primary_nav_font_spacing]', array(
		'label'	      => __( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -1,
			'max'  => 8,
			'step' => 1
		),
	) ) );

	if ( ! isset( $et_one_font_languages[$site_domain] ) ) {
		$wp_customize->add_setting( 'et_divi[primary_nav_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[primary_nav_font]', array(
			'label'		=> __( 'Font', 'Divi' ),
			'section'	=> 'et_divi_header_primary',
			'settings'	=> 'et_divi[primary_nav_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[primary_nav_font_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[primary_nav_font_style]', array(
		'label'	      => __( 'Font Style', 'Divi' ),
		'section'     => 'et_divi_header_primary',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'       => __( 'Bold', 'Divi' ),
			'italic'     => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_font_size]', array(
		'default'       => '12',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_fullwidth]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[secondary_nav_fullwidth]', array(
		'label'		=> __( 'Make Full Width', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[secondary_nav_font_size]', array(
		'label'	      => __( 'Text Size', 'Divi' ),
		'section'     => 'et_divi_header_secondary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 20,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_font_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[secondary_nav_font_spacing]', array(
		'label'	      => __( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_header_secondary',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -1,
			'max'  => 8,
			'step' => 1
		),
	) ) );

	if ( ! isset( $et_one_font_languages[$site_domain] ) ) {
		$wp_customize->add_setting( 'et_divi[secondary_nav_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[secondary_nav_font]', array(
			'label'		=> __( 'Font', 'Divi' ),
			'section'	=> 'et_divi_header_secondary',
			'settings'	=> 'et_divi[secondary_nav_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[secondary_nav_font_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[secondary_nav_font_style]', array(
		'label'	      => __( 'Font Style', 'Divi' ),
		'section'     => 'et_divi_header_secondary',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'       => __( 'Bold', 'Divi' ),
			'italic'     => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[menu_link]', array(
		'default'		=> 'rgba(0,0,0,0.6)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[menu_link]', array(
		'label'		=> __( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[menu_link]',
	) ) );

	$wp_customize->add_setting( 'et_divi[hide_mobile_logo]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[hide_mobile_logo]', array(
		'label'		=> __( 'Hide Logo Image', 'Divi' ),
		'section'	=> 'et_divi_mobile_menu',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[mobile_menu_link]', array(
		'default'		=> et_get_option( 'menu_link', 'rgba(0,0,0,0.6)' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[mobile_menu_link]', array(
		'label'		=> __( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_mobile_menu',
		'settings'	=> 'et_divi[mobile_menu_link]',
	) ) );

	$wp_customize->add_setting( 'et_divi[menu_link_active]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[menu_link_active]', array(
		'label'		=> __( 'Active Link Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[menu_link_active]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_bg]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[primary_nav_bg]', array(
		'label'		=> __( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[primary_nav_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_dropdown_bg]', array(
		'default'		=> et_get_option( 'primary_nav_bg', '#ffffff' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[primary_nav_dropdown_bg]', array(
		'label'		=> __( 'Dropdown Menu Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[primary_nav_dropdown_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_dropdown_line_color]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[primary_nav_dropdown_line_color]', array(
		'label'		=> __( 'Dropdown Menu Line Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[primary_nav_dropdown_line_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_dropdown_link_color]', array(
		'default'		=> et_get_option( 'menu_link', 'rgba(0,0,0,0.7)' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[primary_nav_dropdown_link_color]', array(
		'label'		=> __( 'Dropdown Menu Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'settings'	=> 'et_divi[primary_nav_dropdown_link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_dropdown_animation]', array(
		'default'       => 'fade',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[primary_nav_dropdown_animation]', array(
		'label'		=> __( 'Dropdown Menu Animation', 'Divi' ),
		'section'	=> 'et_divi_header_primary',
		'type'      => 'select',
		'choices'	=> array(
			'fade'     => __( 'Fade', 'Divi' ),
			'expand' => __( 'Expand', 'Divi' ),
			'slide'	   => __( 'Slide', 'Divi' ),
			'flip'	   => __( 'Flip', 'Divi' )
		),
	) );

	$wp_customize->add_setting( 'et_divi[mobile_primary_nav_bg]', array(
		'default'		=> et_get_option( 'primary_nav_bg', '#ffffff' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[mobile_primary_nav_bg]', array(
		'label'		=> __( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_mobile_menu',
		'settings'	=> 'et_divi[mobile_primary_nav_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_bg]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[secondary_nav_bg]', array(
		'label'		=> __( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'settings'	=> 'et_divi[secondary_nav_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_text_color_new]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[secondary_nav_text_color_new]', array(
		'label'		=> __( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'settings'	=> 'et_divi[secondary_nav_text_color_new]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_dropdown_bg]', array(
		'default'		=> et_get_option( 'secondary_nav_bg', et_get_option( 'accent_color', '#2ea3f2' ) ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[secondary_nav_dropdown_bg]', array(
		'label'		=> __( 'Dropdown Menu Background Color', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'settings'	=> 'et_divi[secondary_nav_dropdown_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_dropdown_link_color]', array(
		'default'		=> et_get_option( 'secondary_nav_text_color_new', '#ffffff' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[secondary_nav_dropdown_link_color]', array(
		'label'		=> __( 'Dropdown Menu Text Color', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'settings'	=> 'et_divi[secondary_nav_dropdown_link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_dropdown_animation]', array(
		'default'       => 'fade',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[secondary_nav_dropdown_animation]', array(
		'label'		=> __( 'Dropdown Menu Animation', 'Divi' ),
		'section'	=> 'et_divi_header_secondary',
		'type'      => 'select',
		'choices'	=> array(
			'fade'     => __( 'Fade', 'Divi' ),
			'expand' => __( 'Expand', 'Divi' ),
			'slide'	   => __( 'Slide', 'Divi' ),
			'flip'	   => __( 'Flip', 'Divi' )
		),
	) );

	// Setting with no control kept for backwards compatbility
	$wp_customize->add_setting( 'et_divi[primary_nav_text_color]', array(
		'default'       => 'dark',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	// Setting with no control kept for backwards compatbility
	$wp_customize->add_setting( 'et_divi[secondary_nav_text_color]', array(
		'default'       => 'light',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	if ( 'on' === et_get_option( 'divi_fixed_nav', 'on' ) ) {
		$wp_customize->add_setting( 'et_divi[hide_fixed_logo]', array(
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage',
		) );

		$wp_customize->add_control( 'et_divi[hide_fixed_logo]', array(
			'label'		=> __( 'Hide Logo Image', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'type'      => 'checkbox',
		) );

		$wp_customize->add_setting( 'et_divi[minimized_menu_height]', array(
			'default'       => '40',
			'type'          => 'option',
			'capability'    => 'edit_theme_options',
			'transport'     => 'postMessage',
		) );

		$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[minimized_menu_height]', array(
			'label'	      => __( 'Fixed Menu/Logo Height', 'Divi' ),
			'section'     => 'et_divi_header_fixed',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 30,
				'max'  => 300,
				'step' => 1
			),
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_primary_nav_font_size]', array(
			'default'       => et_get_option( 'primary_nav_font_size', '14' ),
			'type'          => 'option',
			'capability'    => 'edit_theme_options',
			'transport'     => 'postMessage',
		) );

		$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[fixed_primary_nav_font_size]', array(
			'label'	      => __( 'Text Size', 'Divi' ),
			'section'     => 'et_divi_header_fixed',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 12,
				'max'  => 24,
				'step' => 1
			),
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_primary_nav_bg]', array(
			'default'		=> et_get_option( 'primary_nav_bg', '#ffffff' ),
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[fixed_primary_nav_bg]', array(
			'label'		=> __( 'Primary Menu Background Color', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'settings'	=> 'et_divi[fixed_primary_nav_bg]',
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_secondary_nav_bg]', array(
			'default'		=> et_get_option( 'secondary_nav_bg', '#2ea3f2' ),
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[fixed_secondary_nav_bg]', array(
			'label'		=> __( 'Secondary Menu Background Color', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'settings'	=> 'et_divi[fixed_secondary_nav_bg]',
		) ) );

		$wp_customize->add_setting( 'et_divi[fixed_menu_link]', array(
			'default'       => et_get_option( 'menu_link', 'rgba(0,0,0,0.6)' ),
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[fixed_menu_link]', array(
			'label'		=> __( 'Menu Link Color', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'settings'	=> 'et_divi[fixed_menu_link]',
		) ) );

			$wp_customize->add_setting( 'et_divi[fixed_menu_link_active]', array(
			'default'       => et_get_option( 'menu_link_active', '#2ea3f2' ),
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[fixed_menu_link_active]', array(
			'label'		=> __( 'Active Menu Link Color', 'Divi' ),
			'section'	=> 'et_divi_header_fixed',
			'settings'	=> 'et_divi[fixed_menu_link_active]',
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[phone_number]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[phone_number]', array(
		'label'		=> __( 'Phone Number', 'Divi' ),
		'section'	=> 'et_divi_header_information',
		'type'      => 'text',
	) );

	$wp_customize->add_setting( 'et_divi[header_email]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[header_email]', array(
		'label'		=> __( 'Email', 'Divi' ),
		'section'	=> 'et_divi_header_information',
		'type'      => 'text',
	) );

	$wp_customize->add_setting( 'et_divi[show_footer_social_icons]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[show_footer_social_icons]', array(
		'label'		=> __( 'Show Social Icons', 'Divi' ),
		'section'	=> 'et_divi_footer_elements',
		'type'      => 'checkbox',
	) );

	$wp_customize->add_setting( 'et_divi[footer_columns]', array(
		'default'       => '4',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[footer_columns]', array(
		'label'		=> __( 'Column Layout', 'Divi' ),
		'section'	=> 'et_divi_footer_layout',
		'settings'	=> 'et_divi[footer_columns]',
		'type'		=> 'select',
		'choices'	=> array(
			'4'			=> sprintf( __( '%1$s Columns', 'Divi' ), esc_html( '4' ) ),
			'3' 		=> sprintf( __( '%1$s Columns', 'Divi' ), esc_html( '3' ) ),
			'2' 		=> sprintf( __( '%1$s Columns', 'Divi' ), esc_html( '2' ) ),
			'1'  		=> __( '1 Column', 'Divi' ),
			'_1_4__3_4' => sprintf( __( '%1$s Columns', 'Divi' ), esc_html( '1/4 + 3/4' ) ),
			'_3_4__1_4' => sprintf( __( '%1$s Columns', 'Divi' ), esc_html( '3/4 + 1/4' ) ),
			'_1_3__2_3' => sprintf( __( '%1$s Columns', 'Divi' ), esc_html( '1/3 + 2/3' ) ),
			'_2_3__1_3' => sprintf( __( '%1$s Columns', 'Divi' ), esc_html( '2/3 + 1/3' ) ),
			'_1_4__1_2' => sprintf( __( '%1$s Columns', 'Divi' ), esc_html( '1/4 + 1/4 + 1/2' ) ),
			'_1_2__1_4' => sprintf( __( '%1$s Columns', 'Divi' ), esc_html( '1/2 + 1/4 + 1/4' ) ),
		),
	) );

	$wp_customize->add_setting( 'et_divi[footer_bg]', array(
		'default'		=> '#222222',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_bg]', array(
		'label'		=> __( 'Footer Background Color', 'Divi' ),
		'section'	=> 'et_divi_footer_layout',
		'settings'	=> 'et_divi[footer_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_header_font_size]', array(
		'default'       => et_get_option( 'body_header_size' * .6, '18' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[widget_header_font_size]', array(
		'label'	      => __( 'Header Text Size', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 72,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_header_font_style]', array(
		'default'       => et_get_option( 'widget_header_font_style', '' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[widget_header_font_style]', array(
		'label'	      => __( 'Header Font Style', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'    => __( 'Bold', 'Divi' ),
			'italic'  => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_body_font_size]', array(
		'default'       => et_get_option( 'body_font_size', '14' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[widget_body_font_size]', array(
		'label'	      => __( 'Body/Link Text Size', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_body_line_height]', array(
		'default'       => '1.7',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[widget_body_line_height]', array(
		'label'	      => __( 'Body/Link Line Height', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0.8,
			'max'  => 3,
			'step' => 0.1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[widget_body_font_style]', array(
		'default'       => et_get_option( 'footer_widget_body_font_style', '' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[widget_body_font_style]', array(
		'label'	      => __( 'Body Font Style', 'Divi' ),
		'section'     => 'et_divi_footer_widgets',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'    => __( 'Bold', 'Divi' ),
			'italic'  => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_widget_text_color]', array(
		'default'		=> '#fff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_widget_text_color]', array(
		'label'		=> __( 'Widget Text Color', 'Divi' ),
		'section'	=> 'et_divi_footer_widgets',
		'settings'	=> 'et_divi[footer_widget_text_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_widget_link_color]', array(
		'default'		=> '#fff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_widget_link_color]', array(
		'label'		=> __( 'Widget Link Color', 'Divi' ),
		'section'	=> 'et_divi_footer_widgets',
		'settings'	=> 'et_divi[footer_widget_link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_widget_header_color]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_widget_header_color]', array(
		'label'		=> __( 'Widget Header Color', 'Divi' ),
		'section'	=> 'et_divi_footer_widgets',
		'settings'	=> 'et_divi[footer_widget_header_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_widget_bullet_color]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_widget_bullet_color]', array(
		'label'		=> __( 'Widget Bullet Color', 'Divi' ),
		'section'	=> 'et_divi_footer_widgets',
		'settings'	=> 'et_divi[footer_widget_bullet_color]',
	) ) );

	/* Footer Menu */
	$wp_customize->add_setting( 'et_divi[footer_menu_background_color]', array(
		'default'		=> 'rgba(255,255,255,0.05)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[footer_menu_background_color]', array(
		'label'		=> __( 'Footer Menu Background Color', 'Divi' ),
		'section'	=> 'et_divi_footer_menu',
		'settings'	=> 'et_divi[footer_menu_background_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_text_color]', array(
		'default'		=> '#bbbbbb',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[footer_menu_text_color]', array(
		'label'		=> __( 'Footer Menu Text Color', 'Divi' ),
		'section'	=> 'et_divi_footer_menu',
		'settings'	=> 'et_divi[footer_menu_text_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_active_link_color]', array(
		'default'		=> et_get_option( 'accent_color', '#2ea3f2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[footer_menu_active_link_color]', array(
		'label'		=> __( 'Footer Menu Active Link Color', 'Divi' ),
		'section'	=> 'et_divi_footer_menu',
		'settings'	=> 'et_divi[footer_menu_active_link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_letter_spacing]', array(
		'default'       => '0',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[footer_menu_letter_spacing]', array(
		'label'	      => __( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_footer_menu',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 20,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_font_style]', array(
		'default'       => et_get_option( 'footer_footer_menu_font_style', '' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[footer_menu_font_style]', array(
		'label'	      => __( 'Font Style', 'Divi' ),
		'section'     => 'et_divi_footer_menu',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'    => __( 'Bold', 'Divi' ),
			'italic'  => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_menu_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[footer_menu_font_size]', array(
		'label'	      => __( 'Font Size', 'Divi' ),
		'section'     => 'et_divi_footer_menu',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1,
		),
	) ) );

	/* Bottom Bar */
	$wp_customize->add_setting( 'et_divi[bottom_bar_background_color]', array(
		'default'		=> 'rgba(0,0,0,0.32)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[bottom_bar_background_color]', array(
		'label'		=> __( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_bottom_bar',
		'settings'	=> 'et_divi[bottom_bar_background_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_text_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[bottom_bar_text_color]', array(
		'label'		=> __( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_bottom_bar',
		'settings'	=> 'et_divi[bottom_bar_text_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_font_style]', array(
		'default'       => et_get_option( 'footer_bottom_bar_font_style', '' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[bottom_bar_font_style]', array(
		'label'	      => __( 'Font Style', 'Divi' ),
		'section'     => 'et_divi_bottom_bar',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'    => __( 'Bold', 'Divi' ),
			'italic'  => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_font_size]', array(
		'default'       => '14',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[bottom_bar_font_size]', array(
		'label'	      => __( 'Font Size', 'Divi' ),
		'section'     => 'et_divi_bottom_bar',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_social_icon_size]', array(
		'default'       => '24',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[bottom_bar_social_icon_size]', array(
		'label'	      => __( 'Social Icon Size', 'Divi' ),
		'section'     => 'et_divi_bottom_bar',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 32,
			'step' => 1,
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[bottom_bar_social_icon_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[bottom_bar_social_icon_color]', array(
		'label'		=> __( 'Social Icon Color', 'Divi' ),
		'section'	=> 'et_divi_bottom_bar',
		'settings'	=> 'et_divi[bottom_bar_social_icon_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_font_size]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_font_size', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_font_size]', array(
		'label'	      => __( 'Text Size', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 12,
			'max'  => 30,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_text_color]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_text_color]', array(
		'label'		=> __( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'settings'	=> 'et_divi[all_buttons_text_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_bg_color]', array(
		'default'		=> 'rgba(0,0,0,0)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_bg_color]', array(
		'label'		=> __( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'settings'	=> 'et_divi[all_buttons_bg_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_width]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_border_width', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_border_width]', array(
		'label'	      => __( 'Border Width', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_color]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_border_color]', array(
		'label'		=> __( 'Border Color', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'settings'	=> 'et_divi[all_buttons_border_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_radius]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_border_radius', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_border_radius]', array(
		'label'	      => __( 'Border Radius', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 50,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_spacing]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_spacing', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_spacing]', array(
		'label'	      => __( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_font_style]', array(
		'default'       => '',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[all_buttons_font_style]', array(
		'label'	      => __( 'Button Font Style', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'font_style',
		'choices'     => array(
			'bold'       => __( 'Bold', 'Divi' ),
			'italic'     => __( 'Italic', 'Divi' ),
			'uppercase'  => __( 'Uppercase', 'Divi' ),
			'underline'  => __( 'Underline', 'Divi' ),
		),
	) ) );

	if ( ! isset( $et_one_font_languages[$site_domain] ) ) {
		$wp_customize->add_setting( 'et_divi[all_buttons_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new ET_Divi_Select_Option ( $wp_customize, 'et_divi[all_buttons_font]', array(
			'label'		=> __( 'Buttons Font', 'Divi' ),
			'section'	=> 'et_divi_buttons',
			'settings'	=> 'et_divi[all_buttons_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) ) );
	}

	$wp_customize->add_setting( 'et_divi[all_buttons_icon]', array(
		'default'       => 'yes',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[all_buttons_icon]', array(
		'label'		=> __( 'Add Button Icon', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'type'      => 'select',
		'choices'	=> array(
			'yes'  => __( 'Yes', 'Divi' ),
			'no'   => __( 'No', 'Divi' )
		),
	) );

	$wp_customize->add_setting( 'et_divi[all_buttons_selected_icon]', array(
		'default'       => '5',
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Icon_Picker_Option ( $wp_customize, 'et_divi[all_buttons_selected_icon]', array(
		'label'	      => __( 'Select Icon', 'Divi' ),
		'section'     => 'et_divi_buttons',
		'type'        => 'icon_picker',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_icon_color]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_icon_color]', array(
		'label'		=> __( 'Icon Color', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'settings'	=> 'et_divi[all_buttons_icon_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_icon_placement]', array(
		'default'       => 'right',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[all_buttons_icon_placement]', array(
		'label'		=> __( 'Icon Placement', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'type'      => 'select',
		'choices'	=> array(
			'right'  => __( 'Right', 'Divi' ),
			'left'   => __( 'Left', 'Divi' )
		),
	) );

	$wp_customize->add_setting( 'et_divi[all_buttons_icon_hover]', array(
		'default'       => 'yes',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[all_buttons_icon_hover]', array(
		'label'		=> __( 'Only Show Icon on Hover', 'Divi' ),
		'section'	=> 'et_divi_buttons',
		'type'      => 'select',
		'choices'	=> array(
			'yes'  => __( 'Yes', 'Divi' ),
			'no'   => __( 'No', 'Divi' )
		),
	) );

	$wp_customize->add_setting( 'et_divi[all_buttons_text_color_hover]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_text_color_hover]', array(
		'label'		=> __( 'Text Color', 'Divi' ),
		'section'	=> 'et_divi_buttons_hover',
		'settings'	=> 'et_divi[all_buttons_text_color_hover]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_bg_color_hover]', array(
		'default'		=> 'rgba(255,255,255,0.2)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_bg_color_hover]', array(
		'label'		=> __( 'Background Color', 'Divi' ),
		'section'	=> 'et_divi_buttons_hover',
		'settings'	=> 'et_divi[all_buttons_bg_color_hover]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_color_hover]', array(
		'default'		=> 'rgba(0,0,0,0)',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[all_buttons_border_color_hover]', array(
		'label'		=> __( 'Border Color', 'Divi' ),
		'section'	=> 'et_divi_buttons_hover',
		'settings'	=> 'et_divi[all_buttons_border_color_hover]',
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_border_radius_hover]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_border_radius_hover', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_border_radius_hover]', array(
		'label'	      => __( 'Border Radius', 'Divi' ),
		'section'     => 'et_divi_buttons_hover',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 50,
			'step' => 1
		),
	) ) );

	$wp_customize->add_setting( 'et_divi[all_buttons_spacing_hover]', array(
		'default'       => ET_Global_Settings::get_value( 'all_buttons_spacing_hover', 'default' ),
		'type'          => 'option',
		'capability'    => 'edit_theme_options',
		'transport'     => 'postMessage',
	) );

	$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[all_buttons_spacing_hover]', array(
		'label'	      => __( 'Letter Spacing', 'Divi' ),
		'section'     => 'et_divi_buttons_hover',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => -2,
			'max'  => 10,
			'step' => 1
		),
	) ) );
}
endif;

if ( ! function_exists( 'et_divi_customizer_module_settings' ) ) :
function et_divi_customizer_module_settings( $wp_customize ) {
	$animation_choices = array(
		'left' 		=> __( 'Left to Right', 'Divi' ),
		'right' 	=> __( 'Right to Left', 'Divi' ),
		'top' 		=> __( 'Top to Bottom', 'Divi' ),
		'bottom' 	=> __( 'Bottom to Top', 'Divi' ),
		'fade_in'	=> __( 'Fade In', 'Divi' ),
		'off' 		=> __( 'No Animation', 'Divi' ),
	);

		/* Section: Image */
		$wp_customize->add_section( 'et_pagebuilder_image', array(
		    'priority'       => 10,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Image', 'Divi' ),
		    'description'    => __( 'Image Module Settings', 'Divi' ),
		) );

			$wp_customize->add_setting( 'et_divi[et_pb_image-animation]', array(
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
			) );

			$wp_customize->add_control( 'et_divi[et_pb_image-animation]', array(
				'label'		=> __( 'Animation', 'Divi' ),
				'description' => __( 'This controls default direction of the lazy-loading animation.', 'Divi' ),
				'section'	=> 'et_pagebuilder_image',
				'type'      => 'select',
				'choices'	=> $animation_choices,
			) );

		/* Section: Gallery */
		$wp_customize->add_section( 'et_pagebuilder_gallery', array(
		    'priority'       => 20,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Gallery', 'Divi' ),
		    // 'description'    => '',
		) );

			// Zoom Icon Color
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-zoom_icon_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_gallery-zoom_icon_color', 'default' ), // default color should be theme's accent color
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[et_pb_gallery-zoom_icon_color]', array(
				'label'		=> __( 'Zoom Icon Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_gallery',
				'settings'	=> 'et_divi[et_pb_gallery-zoom_icon_color]',
			) ) );

			// Hover Overlay Color
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-hover_overlay_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_gallery-hover_overlay_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[et_pb_gallery-hover_overlay_color]', array(
				'label'		=> __( 'Hover Overlay Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_gallery',
				'settings'	=> 'et_divi[et_pb_gallery-hover_overlay_color]',
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_gallery-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_gallery-title_font_size]', array(
				'label'	      => __( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_gallery',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_gallery-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_gallery-title_font_style]', array(
				'label'	      => __( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_gallery',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// caption font size Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-caption_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_gallery-caption_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_gallery-caption_font_size]', array(
				'label'	      => __( 'Caption Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_gallery',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// caption font style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_gallery-caption_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_gallery-caption_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_gallery-caption_font_style]', array(
				'label'	      => __( 'Caption Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_gallery',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Blurb */
		$wp_customize->add_section( 'et_pagebuilder_blurb', array(
		    'priority'       => 30,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Blurb', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_blurb-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blurb-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blurb-header_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_blurb',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

		/* Section: Tabs */
		$wp_customize->add_section( 'et_pagebuilder_tabs', array(
		    'priority'       => 40,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Tabs', 'Divi' ),
		    // 'description'    => '',
		) );

			// Tab Title Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_tabs-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_tabs-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_tabs-title_font_size]', array(
				'label'	      => __( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_tabs',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Tab Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_tabs-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_tabs-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_tabs-title_font_style]', array(
				'label'	      => __( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_tabs',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Padding: Range 0 - 50px
			/* If padding is 20px then the content padding is 20px and the tab padding is: { padding: 10px(50%) 20px; }	*/
			$wp_customize->add_setting( 'et_divi[et_pb_tabs-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_tabs-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_tabs-padding]', array(
				'label'	      => __( 'Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_tabs',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

		/* Section: Slider */
		$wp_customize->add_section( 'et_pagebuilder_slider', array(
		    'priority'       => 50,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Slider', 'Divi' ),
		    // 'description'    => '',
		) );

			// Slider Padding: Top/Bottom Only
			$wp_customize->add_setting( 'et_divi[et_pb_slider-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_slider-padding]', array(
				'label'	      => __( 'Top & Bottom Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 5,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

			// Header Font size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_slider-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_slider-header_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_slider-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_slider-header_font_style]', array(
				'label'	      => __( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Content Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_slider-body_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-body_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_slider-body_font_size]', array(
				'label'	      => __( 'Content Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Content Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_slider-body_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_slider-body_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_slider-body_font_style]', array(
				'label'	      => __( 'Content Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_slider',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Testimonial */
		$wp_customize->add_section( 'et_pagebuilder_testimonial', array(
		    'priority'       => 60,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Testimonial', 'Divi' ),
		    // 'description'    => '',
		) );

			// Author Name Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-author_name_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-author_name_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_testimonial-author_name_font_style]', array(
				'label'	      => __( 'Name Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Author Details Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-author_details_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-author_details_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_testimonial-author_details_font_style]', array(
				'label'	      => __( 'Details Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Portrait Border Radius
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-portrait_border_radius]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-portrait_border_radius', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_testimonial-portrait_border_radius]', array(
				'label'	      => __( 'Portrait Border Radius', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
			) ) );

			// Portrait Width
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-portrait_width]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-portrait_width', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_testimonial-portrait_width]', array(
				'label'	      => __( 'Image Width', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

			// Portrait Height
			$wp_customize->add_setting( 'et_divi[et_pb_testimonial-portrait_height]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_testimonial-portrait_height', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_testimonial-portrait_height]', array(
				'label'	      => __( 'Image Height', 'Divi' ),
				'section'     => 'et_pagebuilder_testimonial',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

		/* Section: Pricing Table */
		$wp_customize->add_section( 'et_pagebuilder_pricing_table', array(
		    'priority'       => 70,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Pricing Table', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-header_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-header_font_style]', array(
				'label'	      => __( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Subhead Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-subheader_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-subheader_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-subheader_font_size]', array(
				'label'	      => __( 'Subheader Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Subhead Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-subheader_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-subheader_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-subheader_font_style]', array(
				'label'	      => __( 'Subheader Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Price font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-price_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-price_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-price_font_size]', array(
				'label'	      => __( 'Price Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 100,
					'step' => 1,
				),
			) ) );

			// Price font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_pricing_tables-price_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_pricing_tables-price_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_pricing_tables-price_font_style]', array(
				'label'	      => __( 'Pricing Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_pricing_table',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Call To Action */
		$wp_customize->add_section( 'et_pagebuilder_call_to_action', array(
		    'priority'       => 80,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Call To Action', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_cta-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_cta-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_cta-header_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_call_to_action',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_cta-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_cta-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_cta-header_font_style]', array(
				'label'	      => __( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_call_to_action',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Padding: Range 0px - 200px
			$wp_customize->add_setting( 'et_divi[et_pb_cta-custom_padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_cta-custom_padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_cta-custom_padding]', array(
				'label'	      => __( 'Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_call_to_action',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

		/* Section: Audio */
		$wp_customize->add_section( 'et_pagebuilder_audio', array(
		    'priority'       => 90,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Audio', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_audio-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_audio-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_audio-title_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_audio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_audio-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_audio-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_audio-title_font_style]', array(
				'label'	      => __( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_audio',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Subhead Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_audio-caption_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_audio-caption_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_audio-caption_font_size]', array(
				'label'	      => __( 'Subheader Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_audio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Subhead Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_audio-caption_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_audio-caption_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_audio-caption_font_style]', array(
				'label'	      => __( 'Subheader Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_audio',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Subscribe */
		$wp_customize->add_section( 'et_pagebuilder_subscribe', array(
		    'priority'       => 100,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Subscribe', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_signup-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_signup-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_signup-header_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_subscribe',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_signup-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_signup-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_signup-header_font_style]', array(
				'label'	      => __( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_subscribe',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Padding: Range 0px - 200px
			$wp_customize->add_setting( 'et_divi[et_pb_signup-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_signup-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_signup-padding]', array(
				'label'	      => __( 'Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_subscribe',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

		/* Section: Login */
		$wp_customize->add_section( 'et_pagebuilder_login', array(
		    'priority'       => 110,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Login', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_login-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_login-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_login-header_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_login',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_login-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_login-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_login-header_font_style]', array(
				'label'	      => __( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_login',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Padding: Range 0px - 200px
			$wp_customize->add_setting( 'et_divi[et_pb_login-custom_padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_login-custom_padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_login-custom_padding]', array(
				'label'	      => __( 'Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_login',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
			) ) );

		/* Section: Portfolio */
		$wp_customize->add_section( 'et_pagebuilder_portfolio', array(
		    'priority'       => 120,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Portfolio', 'Divi' ),
		    // 'description'    => '',
		) );

			// Zoom Icon Color
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-zoom_icon_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_portfolio-zoom_icon_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[et_pb_portfolio-zoom_icon_color]', array(
				'label'		=> __( 'Zoom Icon Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_portfolio',
				'settings'	=> 'et_divi[et_pb_portfolio-zoom_icon_color]',
			) ) );

			// Hover Overlay Color
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-hover_overlay_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_portfolio-hover_overlay_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[et_pb_portfolio-hover_overlay_color]', array(
				'label'		=> __( 'Hover Overlay Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_portfolio',
				'settings'	=> 'et_divi[et_pb_portfolio-hover_overlay_color]',
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_portfolio-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_portfolio-title_font_size]', array(
				'label'	      => __( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_portfolio-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_portfolio-title_font_style]', array(
				'label'	      => __( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_portfolio',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Category font size Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-caption_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_portfolio-caption_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_portfolio-caption_font_size]', array(
				'label'	      => __( 'Caption Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Category Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_portfolio-caption_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_portfolio-caption_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_portfolio-caption_font_style]', array(
				'label'	      => __( 'Caption Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_portfolio',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Filterable Portfolio */
		$wp_customize->add_section( 'et_pagebuilder_filterable_portfolio', array(
		    'priority'       => 130,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Filterable Portfolio', 'Divi' ),
		    // 'description'    => '',
		) );

			// Zoom Icon Color
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-zoom_icon_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-zoom_icon_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[et_pb_filterable_portfolio-zoom_icon_color]', array(
				'label'		=> __( 'Zoom Icon Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_filterable_portfolio',
				'settings'	=> 'et_divi[et_pb_filterable_portfolio-zoom_icon_color]',
			) ) );

			// Hover Overlay Color
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-hover_overlay_color]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-hover_overlay_color', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Customize_Color_Alpha_Control( $wp_customize, 'et_divi[et_pb_filterable_portfolio-hover_overlay_color]', array(
				'label'		=> __( 'Hover Overlay Color', 'Divi' ),
				'section'	=> 'et_pagebuilder_filterable_portfolio',
				'settings'	=> 'et_divi[et_pb_filterable_portfolio-hover_overlay_color]',
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-title_font_size]', array(
				'label'	      => __( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-title_font_style]', array(
				'label'	      => __( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Category font size Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-caption_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-caption_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-caption_font_size]', array(
				'label'	      => __( 'Caption Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Category Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-caption_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-caption_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-caption_font_style]', array(
				'label'	      => __( 'Caption Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Filters Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-filter_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-filter_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-filter_font_size]', array(
				'label'	      => __( 'Filters Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Filters Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_filterable_portfolio-filter_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_filterable_portfolio-filter_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_filterable_portfolio-filter_font_style]', array(
				'label'	      => __( 'Filters Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_filterable_portfolio',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Bar Counter */
		$wp_customize->add_section( 'et_pagebuilder_bar_counter', array(
		    'priority'       => 140,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Bar Counter', 'Divi' ),
		    // 'description'    => '',
		) );

			// Label Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_counters-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_counters-title_font_size]', array(
				'label'	      => __( 'Label Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Labels Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_counters-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_counters-title_font_style]', array(
				'label'	      => __( 'Label Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Percent Font Size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_counters-percent_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-percent_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_counters-percent_font_size]', array(
				'label'	      => __( 'Percent Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Percent Font Style: : B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_counters-percent_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-percent_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_counters-percent_font_style]', array(
				'label'	      => __( 'Percent Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Bar Padding: Range 0px - 30px (top and bottom padding only)
			$wp_customize->add_setting( 'et_divi[et_pb_counters-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_counters-padding]', array(
				'label'	      => __( 'Bar Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

			// Bar Border Radius
			$wp_customize->add_setting( 'et_divi[et_pb_counters-border_radius]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_counters-border_radius', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_counters-border_radius]', array(
				'label'	      => __( 'Bar Border Radius', 'Divi' ),
				'section'     => 'et_pagebuilder_bar_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 80,
					'step' => 1,
				),
			) ) );

		/* Section: Circle Counter */
		$wp_customize->add_section( 'et_pagebuilder_circle_counter', array(
		    'priority'       => 150,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Circle Counter', 'Divi' ),
		    // 'description'    => '',
		) );
			// Number Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_circle_counter-number_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_circle_counter-number_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_circle_counter-number_font_size]', array(
				'label'	      => __( 'Number Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_circle_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Number Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_circle_counter-number_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_circle_counter-number_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_circle_counter-number_font_style]', array(
				'label'	      => __( 'Number Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_circle_counter',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_circle_counter-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_circle_counter-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_circle_counter-title_font_size]', array(
				'label'	      => __( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_circle_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_circle_counter-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_circle_counter-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_circle_counter-title_font_style]', array(
				'label'	      => __( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_circle_counter',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Number Counter */
		$wp_customize->add_section( 'et_pagebuilder_number_counter', array(
		    'priority'       => 160,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Number Counter', 'Divi' ),
		    // 'description'    => '',
		) );

			// Number Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_number_counter-number_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_number_counter-number_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_number_counter-number_font_size]', array(
				'label'	      => __( 'Number Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_number_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Number Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_number_counter-number_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_number_counter-number_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_number_counter-number_font_style]', array(
				'label'	      => __( 'Number Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_number_counter',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Title Font Size: Range 10px - 72px
			$wp_customize->add_setting( 'et_divi[et_pb_number_counter-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_number_counter-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_number_counter-title_font_size]', array(
				'label'	      => __( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_number_counter',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_number_counter-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_number_counter-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_number_counter-title_font_style]', array(
				'label'	      => __( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_number_counter',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Accordion */
		$wp_customize->add_section( 'et_pagebuilder_accordion', array(
		    'priority'       => 170,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Accordion', 'Divi' ),
		    // 'description'    => '',
		) );
			// Title Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-toggle_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-toggle_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_accordion-toggle_font_size]', array(
				'label'	      => __( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Accordion Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-toggle_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-toggle_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_accordion-toggle_font_style]', array(
				'label'	      => __( 'Opened Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Inactive Accordion Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-inactive_toggle_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-inactive_title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_accordion-inactive_toggle_font_style]', array(
				'label'	      => __( 'Closed Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Toggle Accordion Icon Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-toggle_icon_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-toggle_icon_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_accordion-toggle_icon_size]', array(
				'label'	      => __( 'Toggle Icon Size', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 16,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Padding: Range 0 - 50px
			/* Padding effects each individual Accordion */
			$wp_customize->add_setting( 'et_divi[et_pb_accordion-custom_padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_accordion-custom_padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_accordion-custom_padding]', array(
				'label'	      => __( 'Toggle Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_accordion',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

		/* Section: Toggle */
		$wp_customize->add_section( 'et_pagebuilder_toggle', array(
		    'priority'       => 180,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Toggle', 'Divi' ),
		    // 'description'    => '',
		) );

			// Title Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_toggle-title_font_size]', array(
				'label'	      => __( 'Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Toggle Title Font Style
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_toggle-title_font_style]', array(
				'label'	      => __( 'Opened Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Inactive Toggle Title Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-inactive_title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-inactive_title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_toggle-inactive_title_font_style]', array(
				'label'	      => __( 'Closed Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Open& Close Icon Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-toggle_icon_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-toggle_icon_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_toggle-toggle_icon_size]', array(
				'label'	      => __( 'Toggle Icon Size', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 16,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Padding: Range 0 - 50px
			$wp_customize->add_setting( 'et_divi[et_pb_toggle-custom_padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_toggle-custom_padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_toggle-custom_padding]', array(
				'label'	      => __( 'Toggle Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_toggle',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

		/* Section: Contact Form */
		$wp_customize->add_section( 'et_pagebuilder_contact_form', array(
		    'priority'       => 190,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Contact Form', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_contact_form-title_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_contact_form-title_font_style]', array(
				'label'	      => __( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Input Field Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-form_field_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-form_field_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_contact_form-form_field_font_size]', array(
				'label'	      => __( 'Input Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Input Field Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-form_field_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-form_field_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_contact_form-form_field_font_style]', array(
				'label'	      => __( 'Input Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Input Field Padding: Range 0 - 50px
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_contact_form-padding]', array(
				'label'	      => __( 'Input Field Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

			// Captcha Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-captcha_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-captcha_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_contact_form-captcha_font_size]', array(
				'label'	      => __( 'Captcha Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Captcha Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_contact_form-captcha_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_contact_form-captcha_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_contact_form-captcha_font_style]', array(
				'label'	      => __( 'Captcha Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_contact_form',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Sidebar */
		$wp_customize->add_section( 'et_pagebuilder_sidebar', array(
		    'priority'       => 200,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Sidebar', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_sidebar-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_sidebar-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_sidebar-header_font_size]', array(
				'label'	      => __( 'Widget Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_sidebar',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header font style
			$wp_customize->add_setting( 'et_divi[et_pb_sidebar-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_sidebar-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_sidebar-header_font_style]', array(
				'label'	      => __( 'Widget Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_sidebar',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Show/hide Vertical Divider
			$wp_customize->add_setting( 'et_divi[et_pb_sidebar-remove_border]', array(
				'default'		=> ET_Global_Settings::get_checkbox_value( 'et_pb_sidebar-remove_border', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
			) );

			$wp_customize->add_control( 'et_divi[et_pb_sidebar-remove_border]', array(
				'label'		=> __( 'Remove Vertical Divider', 'Divi' ),
				'section'	=> 'et_pagebuilder_sidebar',
				'type'      => 'checkbox',
			) );

		/* Section: Divider */
		$wp_customize->add_section( 'et_pagebuilder_divider', array(
		    'priority'       => 200,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Divider', 'Divi' ),
		    // 'description'    => '',
		) );

			// Show/hide Divider
			$wp_customize->add_setting( 'et_divi[et_pb_divider-show_divider]', array(
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage',
			) );

			$wp_customize->add_control( 'et_divi[et_pb_divider-show_divider]', array(
				'label'		=> __( 'Show Divider', 'Divi' ),
				'section'	=> 'et_pagebuilder_divider',
				'type'      => 'checkbox',
			) );

			// Divider Style
			$wp_customize->add_setting( 'et_divi[et_pb_divider-divider_style]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_divider-divider_style', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage'
			) );

			$wp_customize->add_control( 'et_divi[et_pb_divider-divider_style]', array(
				'label'		=> __( 'Divider Style', 'Divi' ),
				'section'	=> 'et_pagebuilder_divider',
				'settings'	=> 'et_divi[et_pb_divider-divider_style]',
				'type'		=> 'select',
				'choices'	=> array(
					'solid'		=> __( 'Solid', 'Divi' ),
					'dotted'	=> __( 'Dotted', 'Divi' ),
					'dashed'	=> __( 'Dashed', 'Divi' ),
					'double'	=> __( 'Double', 'Divi' ),
					'groove'	=> __( 'Groove', 'Divi' ),
					'ridge'		=> __( 'Ridge', 'Divi' ),
					'inset'		=> __( 'Inset', 'Divi' ),
					'outset'	=> __( 'Outset', 'Divi' ),
				),
			) );

			// Divider Weight
			$wp_customize->add_setting( 'et_divi[et_pb_divider-divider_weight]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_divider-divider_weight', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_divider-divider_weight]', array(
				'label'	      => __( 'Divider Weight', 'Divi' ),
				'section'     => 'et_pagebuilder_divider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
			) ) );

			// Divider Height
			$wp_customize->add_setting( 'et_divi[et_pb_divider-height]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_divider-height', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_divider-height]', array(
				'label'	      => __( 'Divider Height', 'Divi' ),
				'section'     => 'et_pagebuilder_divider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
			) ) );

			// Divider Position
			$wp_customize->add_setting( 'et_divi[et_pb_divider-divider_position]', array(
				'default'		=> ET_Global_Settings::get_value( 'et_pb_divider-divider_position', 'default' ),
				'type'			=> 'option',
				'capability'	=> 'edit_theme_options',
				'transport'		=> 'postMessage'
			) );

			$wp_customize->add_control( 'et_divi[et_pb_divider-divider_position]', array(
				'label'		=> __( 'Divider Position', 'Divi' ),
				'section'	=> 'et_pagebuilder_divider',
				'settings'	=> 'et_divi[et_pb_divider-divider_position]',
				'type'		=> 'select',
				'choices'	=> array(
					'top'		=> __( 'Top', 'Divi' ),
					'center'	=> __( 'Vertically Centered', 'Divi' ),
					'bottom'	=> __( 'Bottom', 'Divi' ),
				),
			) );

		/* Section: Person */
		$wp_customize->add_section( 'et_pagebuilder_person', array(
		    'priority'       => 210,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Person', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_team_member-header_font_size]', array(
				'label'	      => __( 'Name Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header font style
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_team_member-header_font_style]', array(
				'label'	      => __( 'Name Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Subhead Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-subheader_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-subheader_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_team_member-subheader_font_size]', array(
				'label'	      => __( 'Subheader Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Subhead Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-subheader_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-subheader_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_team_member-subheader_font_style]', array(
				'label'	      => __( 'Subheader Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Network Icons size: Range 16px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_team_member-social_network_icon_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_team_member-social_network_icon_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_team_member-social_network_icon_size]', array(
				'label'	      => __( 'Social Network Icon Size', 'Divi' ),
				'section'     => 'et_pagebuilder_person',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 16,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

		/* Section: Blog */
		$wp_customize->add_section( 'et_pagebuilder_blog', array(
		    'priority'       => 220,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Blog', 'Divi' ),
		    // 'description'    => '',
		) );

			// Post Title Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_blog-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blog-header_font_size]', array(
				'label'	      => __( 'Post Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_blog',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Post Title Font Style
			$wp_customize->add_setting( 'et_divi[et_pb_blog-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_blog-header_font_style]', array(
				'label'	      => __( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_blog',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Meta Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_blog-meta_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog-meta_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blog-meta_font_size]', array(
				'label'	      => __( 'Meta Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_blog',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Meta Field Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_blog-meta_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog-meta_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_blog-meta_font_style]', array(
				'label'	      => __( 'Meta Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_blog',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Blog Grid */
		$wp_customize->add_section( 'et_pagebuilder_masonry_blog', array(
		    'priority'       => 230,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Blog Grid', 'Divi' ),
		    // 'description'    => '',
		) );

			// Post Title Font Size
			$wp_customize->add_setting( 'et_divi[et_pb_blog_masonry-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog_masonry-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blog_masonry-header_font_size]', array(
				'label'	      => __( 'Post Title Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_masonry_blog',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Post Title Font Style
			$wp_customize->add_setting( 'et_divi[et_pb_blog_masonry-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog_masonry-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_blog_masonry-header_font_style]', array(
				'label'	      => __( 'Title Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_masonry_blog',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Meta Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_blog_masonry-meta_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog_masonry-meta_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_blog_masonry-meta_font_size]', array(
				'label'	      => __( 'Meta Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_masonry_blog',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Meta Field Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_blog_masonry-meta_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_blog_masonry-meta_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_blog_masonry-meta_font_style]', array(
				'label'	      => __( 'Meta Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_masonry_blog',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Shop */
		$wp_customize->add_section( 'et_pagebuilder_shop', array(
		    'priority'       => 240,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Shop', 'Divi' ),
		    // 'description'    => '',
		) );

			// Product Name Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_shop-title_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-title_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_shop-title_font_size]', array(
				'label'	      => __( 'Product Name Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Product Name Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_shop-title_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-title_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_shop-title_font_style]', array(
				'label'	      => __( 'Product Name Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Sale Badge Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_shop-sale_badge_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-sale_badge_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_shop-sale_badge_font_size]', array(
				'label'	      => __( 'Sale Badge Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Sale Badge Font Style:  B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_shop-sale_badge_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-sale_badge_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_shop-sale_badge_font_style]', array(
				'label'	      => __( 'Sale Badge Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Price Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_shop-price_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-price_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_shop-price_font_size]', array(
				'label'	      => __( 'Price Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Price Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_shop-price_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-price_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_shop-price_font_style]', array(
				'label'	      => __( 'Price Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Sale Price Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_shop-sale_price_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-sale_price_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_shop-sale_price_font_size]', array(
				'label'	      => __( 'Sale Price Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Sale Price Font Style: B / I / TT / U/
			$wp_customize->add_setting( 'et_divi[et_pb_shop-sale_price_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_shop-sale_price_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_shop-sale_price_font_style]', array(
				'label'	      => __( 'Sale Price Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_shop',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Countdown */
		$wp_customize->add_section( 'et_pagebuilder_countdown', array(
		    'priority'       => 250,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Countdown', 'Divi' ),
		    // 'description'    => '',
		) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_countdown_timer-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_countdown_timer-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_countdown_timer-header_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_countdown',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_countdown_timer-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_countdown_timer-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_countdown_timer-header_font_style]', array(
				'label'	      => __( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_countdown',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Social Follow */
		$wp_customize->add_section( 'et_pagebuilder_social_follow', array(
		    'priority'       => 250,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Social Follow', 'Divi' ),
		    // 'description'    => '',
		) );

			// Follow Button Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_social_media_follow-icon_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_social_media_follow-icon_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_social_media_follow-icon_size]', array(
				'label'	      => __( 'Follow Font & Icon Size', 'Divi' ),
				'section'     => 'et_pagebuilder_social_follow',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Follow Button Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_social_media_follow-button_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_social_media_follow-button_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_social_media_follow-button_font_style]', array(
				'label'	      => __( 'Button Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_social_follow',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

		/* Section: Fullwidth Slider */
		$wp_customize->add_section( 'et_pagebuilder_fullwidth_slider', array(
		    'priority'       => 270,
		    'capability'     => 'edit_theme_options',
		    'title'          => __( 'Fullwidth Slider', 'Divi' ),
		    // 'description'    => '',
		) );

			// Slider Padding: Top/Bottom Only
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-padding]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-padding', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-padding]', array(
				'label'	      => __( 'Top & Bottom Padding', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 5,
					'max'  => 50,
					'step' => 1,
				),
			) ) );

			// Header Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-header_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-header_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-header_font_size]', array(
				'label'	      => __( 'Header Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 72,
					'step' => 1,
				),
			) ) );

			// Header Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-header_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-header_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-header_font_style]', array(
				'label'	      => __( 'Header Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );

			// Content Font size: Range 10px - 32px
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-body_font_size]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-body_font_size', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Range_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-body_font_size]', array(
				'label'	      => __( 'Content Font Size', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				),
			) ) );

			// Content Font Style: B / I / TT / U
			$wp_customize->add_setting( 'et_divi[et_pb_fullwidth_slider-body_font_style]', array(
				'default'       => ET_Global_Settings::get_value( 'et_pb_fullwidth_slider-body_font_style', 'default' ),
				'type'          => 'option',
				'capability'    => 'edit_theme_options',
				'transport'     => 'postMessage',
			) );

			$wp_customize->add_control( new ET_Divi_Font_Style_Option ( $wp_customize, 'et_divi[et_pb_fullwidth_slider-body_font_style]', array(
				'label'	      => __( 'Content Font Style', 'Divi' ),
				'section'     => 'et_pagebuilder_fullwidth_slider',
				'type'        => 'font_style',
				'choices'     => array(
					'bold'    => __( 'Bold', 'Divi' ),
					'italic'  => __( 'Italic', 'Divi' ),
					'uppercase'  => __( 'Uppercase', 'Divi' ),
					'underline'  => __( 'Underline', 'Divi' ),
				),
			) ) );
}
endif;

/**
 * Add action hook to the footer in customizer preview.
 */
function et_customizer_preview_footer_action() {
	if ( is_customize_preview() ) {
		do_action( 'et_customizer_footer_preview' );
	}
}
add_action( 'wp_footer', 'et_customizer_preview_footer_action' );

/**
 * Add container with social icons to the footer in customizer preview.
 * Used to get the icons and append them into the header when user enables the header social icons in customizer.
 */
function et_load_social_icons() {
	echo '<div class="et_customizer_social_icons" style="display:none;">';
		get_template_part( 'includes/social_icons', 'header' );
	echo '</div>';
}
add_action( 'et_customizer_footer_preview', 'et_load_social_icons' );

function et_divi_customize_preview_js() {
	$theme_version = et_get_theme_version();
	wp_enqueue_script( 'divi-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), $theme_version, true );
}
add_action( 'customize_preview_init', 'et_divi_customize_preview_js' );

function et_divi_customize_preview_css() {
	$theme_version = et_get_theme_version();

	wp_enqueue_style( 'divi-custommizer-controls-styles', get_template_directory_uri() . '/css/theme-customizer-controls-styles.css', array(), $theme_version );
	wp_enqueue_script( 'divi-customizer-controls-js', get_template_directory_uri() . '/js/theme-customizer-controls.js', array( 'jquery' ), $theme_version, true );
}
add_action( 'customize_controls_enqueue_scripts', 'et_divi_customize_preview_css' );

/**
 * Add custom customizer control
 * Check for WP_Customizer_Control existence before adding custom control because WP_Customize_Control is loaded on customizer page only
 *
 * @see _wp_customize_include()
 */
if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Font style control for Customizer
	 */
	class ET_Divi_Font_Style_Option extends WP_Customize_Control {
		public $type = 'font_style';
		public function render_content() {
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
			</label>
			<?php $current_values = explode('|', $this->value() );
			if ( empty( $this->choices ) )
				return;
			foreach ( $this->choices as $value => $label ) :
				$checked_class = in_array( $value, $current_values ) ? ' et_font_style_checked' : '';
				?>
					<span class="et_font_style et_font_value_<?php echo $value; echo $checked_class; ?>">
						<input type="checkbox" class="et_font_style_checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $current_values ) ); ?> />
					</span>
				<?php
			endforeach;
			?>
			<input type="hidden" class="et_font_styles" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
			<?php
		}
	}

	/**
	 * Icon picker control for Customizer
	 */
	class ET_Divi_Icon_Picker_Option extends WP_Customize_Control {
		public $type = 'icon_picker';

		public function render_content() {

		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			et_pb_font_icon_list(); ?>
			<input type="hidden" class="et_selected_icon" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
		</label>
		<?php
		}
	}

	/**
	 * Range-based sliding value picker for Customizer
	 */
	class ET_Divi_Range_Option extends WP_Customize_Control {
		public $type = 'range';

		public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
			<input type="<?php echo esc_attr( $this->type ); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> data-reset_value="<?php echo esc_attr( $this->setting->default ); ?>" />
			<span class="et_divi_reset_slider"></span>
		</label>
		<?php
		}
	}

	/**
	 * Custom Select option which supports data attributes for the <option> tags
	 */
	class ET_Divi_Select_Option extends WP_Customize_Control {
		public $type = 'select';

		public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<select <?php $this->link(); ?>>
				<?php
				foreach ( $this->choices as $value => $attributes ) {
					$data_output = '';

					if ( ! empty( $attributes['data'] ) ) {
						foreach( $attributes['data'] as $data_name => $data_value ) {
							if ( '' !== $data_value ) {
								$data_output .= sprintf( ' data-%1$s="%2$s"',
									esc_attr( $data_name ),
									esc_attr( $data_value )
								);
							}
						}
					}

					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . $data_output . '>' . $attributes['label'] . '</option>';
				}
				?>
			</select>
		</label>
		<?php
		}
	}

	/**
	 * Color picker with alpha color support for Customizer
	 */
	class ET_Divi_Customize_Color_Alpha_Control extends WP_Customize_Control {
		public $type = 'et_coloralpha';

		public $statuses;

		public function __construct( $manager, $id, $args = array() ) {
			$this->statuses = array( '' => __( 'Default', 'Divi' ) );
			parent::__construct( $manager, $id, $args );
		}

		public function enqueue() {
			wp_enqueue_script( 'wp-color-picker-alpha' );
			wp_enqueue_style( 'wp-color-picker' );
		}

		public function to_json() {
			parent::to_json();
			$this->json['statuses'] = $this->statuses;
			$this->json['defaultValue'] = $this->setting->default;
		}

		public function render_content() {}

		public function content_template() {
			?>
			<# var defaultValue = '';
			if ( data.defaultValue ) {
				if ( '#' !== data.defaultValue.substring( 0, 1 ) && 'rgba' !== data.defaultValue.substring( 0, 4 ) ) {
					defaultValue = '#' + data.defaultValue;
				} else {
					defaultValue = data.defaultValue;
				}
				defaultValue = ' data-default-color=' + defaultValue; // Quotes added automatically.
			} #>
			<label>
				<# if ( data.label ) { #>
					<span class="customize-control-title">{{{ data.label }}}</span>
				<# } #>
				<# if ( data.description ) { #>
					<span class="description customize-control-description">{{{ data.description }}}</span>
				<# } #>
				<div class="customize-control-content">
					<input class="color-picker-hex" data-alpha="true" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'Divi' ); ?>" {{ defaultValue }} />
				</div>
			</label>
			<?php
		}
	}

}

function et_divi_add_customizer_css(){ ?>
	<?php
		// Detect legacy settings
		$detect_legacy_secondary_nav_color = et_get_option( 'secondary_nav_text_color', 'Light' );
		$detect_legacy_primary_nav_color = et_get_option( 'primary_nav_text_color', 'Dark' );

		if ( $detect_legacy_primary_nav_color == 'Light' ) {
			$legacy_primary_nav_color = '#ffffff';
		} else {
			$legacy_primary_nav_color = 'rgba(0,0,0,0.6)';
		}

		if ( $detect_legacy_secondary_nav_color == 'Light' ) {
			$legacy_secondary_nav_color = '#ffffff';
		} else {
			$legacy_secondary_nav_color = 'rgba(0,0,0,0.7)';
		}

		$body_font_size = et_get_option( 'body_font_size', '14' );
		$body_font_height = et_get_option( 'body_font_height', '1.7' );
		$body_header_size = et_get_option( 'body_header_size', '30' );
		$body_header_style = et_get_option( 'body_header_style', '', '', true );
		$body_header_spacing = et_get_option( 'body_header_spacing', '0' );
		$body_header_height = et_get_option( 'body_header_height', '1' );
		$body_font_color = et_get_option( 'font_color', '#666666' );
		$body_header_color = et_get_option( 'header_color', '#666666' );

		$accent_color = et_get_option( 'accent_color', '#2ea3f2' );
		$link_color = et_get_option( 'link_color', $accent_color );

		$content_width = et_get_option( 'content_width', '1080' );
		$large_content_width = intval ( $content_width * 1.25 );
		$use_sidebar_width = et_get_option( 'use_sidebar_width', false );
		$sidebar_width = intval( et_get_option( 'sidebar_width', 21 ) );
		$section_padding = et_get_option( 'section_padding', '4' );
		$row_padding = et_get_option( 'row_padding', '2' );

		$tablet_header_font_size = et_get_option( 'tablet_header_font_size', $body_header_size );
		$tablet_body_font_size = et_get_option( 'tablet_body_font_size', $body_font_size );
		$tablet_section_height = et_get_option( 'tablet_section_height', '50' );
		$tablet_row_height = et_get_option( 'tablet_row_height', '30' );

		$phone_header_font_size = et_get_option( 'phone_header_font_size', $tablet_header_font_size );
		$phone_body_font_size = et_get_option( 'phone_body_font_size', $tablet_body_font_size );
		$phone_section_height = et_get_option( 'phone_section_height', $tablet_section_height );
		$phone_row_height = et_get_option( 'phone_row_height', $tablet_row_height );

		$menu_height = et_get_option( 'menu_height', '66' );
		$menu_link = et_get_option( 'menu_link', $legacy_primary_nav_color );
		$menu_link_active = et_get_option( 'menu_link_active', '#2ea3f2' );

		$hide_primary_logo = et_get_option( 'hide_primary_logo', 'false' );
		$hide_fixed_logo = et_get_option( 'hide_fixed_logo', 'false' );

		$primary_nav_font_size = et_get_option( 'primary_nav_font_size', '14' );
		$primary_nav_font_spacing = et_get_option( 'primary_nav_font_spacing', '0' );
		$primary_nav_bg = et_get_option( 'primary_nav_bg', '#ffffff' );
		$primary_nav_font_style = et_get_option( 'primary_nav_font_style', '', '', true );
		$primary_nav_dropdown_bg = et_get_option( 'primary_nav_dropdown_bg', $primary_nav_bg );
		$primary_nav_dropdown_link_color = et_get_option( 'primary_nav_dropdown_link_color', $menu_link );
		$primary_nav_dropdown_line_color = et_get_option( 'primary_nav_dropdown_line_color', $accent_color );

		$mobile_menu_link = et_get_option( 'mobile_menu_link', $menu_link );
		$mobile_primary_nav_bg = et_get_option( 'mobile_primary_nav_bg', $primary_nav_bg );

		$secondary_nav_font_size = et_get_option( 'secondary_nav_font_size', '12' );
		$secondary_nav_font_spacing = et_get_option( 'secondary_nav_font_spacing', '0' );
		$secondary_nav_font_style = et_get_option( 'secondary_nav_font_style', '', '', true );
		$secondary_nav_text_color_new = et_get_option( 'secondary_nav_text_color_new', $legacy_secondary_nav_color );
		$secondary_nav_bg = et_get_option( 'secondary_nav_bg', et_get_option( 'accent_color', '#2ea3f2' ) );
		$secondary_nav_dropdown_bg = et_get_option( 'secondary_nav_dropdown_bg', $secondary_nav_bg );
		$secondary_nav_dropdown_link_color = et_get_option( 'secondary_nav_dropdown_link_color', $secondary_nav_text_color_new );

		$fixed_primary_nav_font_size = et_get_option( 'fixed_primary_nav_font_size', $primary_nav_font_size );
		$fixed_primary_nav_bg = et_get_option( 'fixed_primary_nav_bg', $primary_nav_bg );
		$fixed_secondary_nav_bg = et_get_option( 'fixed_secondary_nav_bg', $secondary_nav_bg );
		$fixed_menu_height = et_get_option( 'minimized_menu_height', '40' );
		$fixed_menu_link = et_get_option( 'fixed_menu_link', $menu_link );
		$fixed_menu_link_active = et_get_option( 'fixed_menu_link_active', $menu_link_active );

		$footer_bg = et_get_option( 'footer_bg', '#222222' );
		$footer_widget_link_color = et_get_option( 'footer_widget_link_color', '#fff' );
		$footer_widget_text_color = et_get_option( 'footer_widget_text_color', '#fff' );
		$footer_widget_header_color = et_get_option( 'footer_widget_header_color', $accent_color );
		$footer_widget_bullet_color = et_get_option( 'footer_widget_bullet_color', $accent_color );

		$widget_header_font_size = et_get_option( 'widget_header_font_size', intval( et_get_option( 'body_header_size' * .6, '18' ) ) );
		$widget_body_font_size = et_get_option( 'widget_body_font_size', $body_font_size );
		$widget_body_line_height = et_get_option( 'widget_body_line_height', '1.7' );

		$button_text_size = et_get_option( 'all_buttons_font_size', '20' );
		$button_text_color = et_get_option( 'all_buttons_text_color', '#ffffff' );
		$button_bg_color = et_get_option( 'all_buttons_bg_color', 'rgba(0,0,0,0)' );
		$button_border_width = et_get_option( 'all_buttons_border_width', '2' );
		$button_border_color = et_get_option( 'all_buttons_border_color', '#ffffff' );
		$button_border_radius = et_get_option( 'all_buttons_border_radius', '3' );
		$button_text_style = et_get_option( 'all_buttons_font_style', '', '', true );
		$button_icon = et_get_option( 'all_buttons_selected_icon', '5' );
		$button_spacing = et_get_option( 'all_buttons_spacing', '0' );
		$button_icon_color = et_get_option( 'all_buttons_icon_color', '#ffffff' );
		$button_text_color_hover = et_get_option( 'all_buttons_text_color_hover', '#ffffff' );
		$button_bg_color_hover = et_get_option( 'all_buttons_bg_color_hover', 'rgba(255,255,255,0.2)' );
		$button_border_color_hover = et_get_option( 'all_buttons_border_color_hover', 'rgba(0,0,0,0)' );
		$button_border_radius_hover = et_get_option( 'all_buttons_border_radius_hover', '3' );
		$button_spacing_hover = et_get_option( 'all_buttons_spacing_hover', '0' );
		$button_icon_size = 1.6 * intval( $button_text_size );
	?>
	<style id="theme-customizer-css">
		<?php if ( '14' !== $body_font_size || '#666666' !== $body_font_color) { ?>
			@media only screen and ( min-width: 767px ) {
				body, .et_pb_column_1_2 .et_quote_content blockquote cite, .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_pb_column_1_3 .et_quote_content blockquote cite, .et_pb_column_3_8 .et_quote_content blockquote cite, .et_pb_column_1_4 .et_quote_content blockquote cite, .et_pb_blog_grid .et_quote_content blockquote cite, .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_pb_blog_grid .et_link_content a.et_link_main_url, body .et_pb_bg_layout_light .et_pb_post p,  body .et_pb_bg_layout_dark .et_pb_post p { font-size: <?php echo esc_html( $body_font_size ); ?>px; }
				.et_pb_slide_content, .et_pb_best_value { font-size: <?php echo esc_html( intval( $body_font_size * 1.14 ) ); ?>px; }
			}
		<?php } ?>
		<?php if ( '#666666' !== $body_font_color) { ?>
			body { color: <?php echo esc_html( $body_font_color ); ?>; }
		<?php } ?>
		<?php if ( '#666666' !== $body_header_color ) { ?>
				h1, h2, h3, h4, h5, h6 { color: <?php echo esc_html( $body_header_color ); ?>; }
			<?php } ?>
		<?php if ( '1.7' !== $body_font_height ) { ?>
			body { line-height: <?php echo esc_html( $body_font_height ); ?>em; }
		<?php } ?>
		<?php if ( $accent_color !== '#2ea3f2' ) { ?>
			.woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .woocommerce-message, .woocommerce-error, .woocommerce-info { background: <?php echo esc_html( $accent_color ); ?> !important; }
			#et_search_icon:hover, .mobile_menu_bar:before, .et-social-icon a:hover, .et_pb_sum, .et_pb_pricing li a, .et_pb_pricing_table_button, .et_overlay:before, .entry-summary p.price ins, .woocommerce div.product span.price, .woocommerce-page div.product span.price, .woocommerce #content div.product span.price, .woocommerce-page #content div.product span.price, .woocommerce div.product p.price, .woocommerce-page div.product p.price, .woocommerce #content div.product p.price, .woocommerce-page #content div.product p.price, .et_pb_member_social_links a:hover, .woocommerce .star-rating span:before, .woocommerce-page .star-rating span:before, .et_pb_widget li a:hover, .et_pb_filterable_portfolio .et_pb_portfolio_filters li a.active, .et_pb_filterable_portfolio .et_pb_portofolio_pagination ul li a.active, .et_pb_gallery .et_pb_gallery_pagination ul li a.active, .wp-pagenavi span.current, .wp-pagenavi a:hover, .nav-single a, .posted_in a { color: <?php echo esc_html( $accent_color ); ?> !important; }
			.et_pb_contact_submit, .et_password_protected_form .et_submit_button, .et_pb_bg_layout_light .et_pb_newsletter_button, .comment-reply-link, .form-submit input, .et_pb_bg_layout_light .et_pb_promo_button, .et_pb_bg_layout_light .et_pb_more_button, .woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button { color: <?php echo esc_html( $accent_color ); ?>; }
			.footer-widget h4 { color: <?php echo esc_html( $accent_color ); ?>; }
			.et-search-form, .nav li ul, .et_mobile_menu, .footer-widget li:before, .et_pb_pricing li:before, blockquote { border-color: <?php echo esc_html( $accent_color ); ?>; }
			.et_pb_counter_amount, .et_pb_featured_table .et_pb_pricing_heading, .et_quote_content, .et_link_content, .et_audio_content { background-color: <?php echo esc_html( $accent_color ); ?>; }
		<?php } ?>
		<?php if ( '1080' !== $content_width ) { ?>
			.container, .et_pb_row, .et_pb_slider .et_pb_container, .et_pb_fullwidth_section .et_pb_title_container, .et_pb_fullwidth_section .et_pb_title_featured_container, .et_pb_fullwidth_header:not(.et_pb_fullscreen) .et_pb_fullwidth_header_container { max-width: <?php echo esc_html( $content_width ); ?>px; }
			.et_boxed_layout #page-container, .et_fixed_nav.et_boxed_layout #page-container #top-header, .et_fixed_nav.et_boxed_layout #page-container #main-header, .et_boxed_layout #page-container .container, .et_boxed_layout #page-container .et_pb_row { max-width: <?php echo esc_html( intval( et_get_option( 'content_width', '1080' ) ) + 160 ); ?>px; }
		<?php } ?>
		<?php if ( $link_color !== '#2ea3f2' ) { ?>
			a { color: <?php echo esc_html( $link_color ); ?>; }
		<?php } ?>
		<?php if ( $primary_nav_bg !== '#ffffff' ) { ?>
			#main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu { background-color: <?php echo esc_html( $primary_nav_bg ); ?>; }
		<?php } ?>
		<?php if ( $primary_nav_dropdown_bg !== $primary_nav_bg ) { ?>
			#main-header .nav li ul { background-color: <?php echo esc_html( $primary_nav_dropdown_bg ); ?>; }
		<?php } ?>
		<?php if ( $primary_nav_dropdown_line_color !== $accent_color ) { ?>
			.nav li ul { border-color: <?php echo esc_html( $primary_nav_dropdown_line_color ); ?>; }
		<?php } ?>
		<?php if ( $secondary_nav_bg !== '#2ea3f2' ) { ?>
			#top-header, #et-secondary-nav li ul { background-color: <?php echo esc_html( $secondary_nav_bg ); ?>; }
		<?php } ?>
		<?php if ( $secondary_nav_dropdown_bg !== $secondary_nav_bg ) { ?>
			#et-secondary-nav li ul { background-color: <?php echo esc_html( $secondary_nav_dropdown_bg ); ?>; }
		<?php } ?>
		<?php if ( $secondary_nav_text_color_new !== '#ffffff' ) { ?>
		#top-header, #top-header a { color: <?php echo esc_html( $secondary_nav_text_color_new ); ?>; }
		<?php } ?>
		<?php if ( $secondary_nav_dropdown_link_color !== $secondary_nav_text_color_new ) { ?>
			#et-secondary-nav li ul a { color: <?php echo esc_html( $secondary_nav_dropdown_link_color ); ?>; }
		<?php } ?>
		<?php if ( $menu_link !== 'rgba(0,0,0,0.6)' ) { ?>
			.et_header_style_centered .mobile_nav .select_page, .et_header_style_split .mobile_nav .select_page, .et_nav_text_color_light #top-menu > li > a, .et_nav_text_color_dark #top-menu > li > a, #top-menu a, .et_mobile_menu li a, .et_nav_text_color_light .et_mobile_menu li a, .et_nav_text_color_dark .et_mobile_menu li a, #et_search_icon:before, .et_search_form_container input, span.et_close_search_field:after, #et-top-navigation .et-cart-info, .mobile_menu_bar:before { color: <?php echo esc_html( $menu_link ); ?>; }
			.et_search_form_container input::-moz-placeholder { color: <?php echo esc_html( $menu_link ); ?>; }
			.et_search_form_container input::-webkit-input-placeholder { color: <?php echo esc_html( $menu_link ); ?>; }
			.et_search_form_container input:-ms-input-placeholder { color: <?php echo esc_html( $menu_link ); ?>; }
		<?php } ?>
		<?php if ( $primary_nav_dropdown_link_color !== $menu_link ) { ?>
			#main-header .nav li ul a { color: <?php echo esc_html( $primary_nav_dropdown_link_color ); ?>; }
		<?php } ?>
		<?php if ( $secondary_nav_font_size !== '12' || $secondary_nav_font_style !== '' || $secondary_nav_font_spacing !=='0' ) { ?>
			#top-header, #top-header a, #et-secondary-nav li li a, #top-header .et-social-icon a:before {
				<?php if ( $secondary_nav_font_size !== '12' ) { ?>
					font-size: <?php echo esc_html( $secondary_nav_font_size ); ?>px;
				<?php } ?>
				<?php if ( '' !== $secondary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $secondary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( '0' !== $secondary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $secondary_nav_font_spacing ); ?>px;
				<?php } ?>
			}
		<?php } ?>
		<?php if ( $primary_nav_font_size !== '14' ) { ?>
			#top-menu li a { font-size: <?php echo esc_html( $primary_nav_font_size ); ?>px; }
			body.et_vertical_nav .container.et_search_form_container .et-search-form input { font-size: <?php echo esc_html( $primary_nav_font_size ); ?>px !important; }
		<?php } ?>

		<?php if ( $primary_nav_font_spacing !== '0' || $primary_nav_font_style !== '' ) { ?>
			#top-menu li a, .et_search_form_container input {
				<?php if ( '' !== $primary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $primary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( '0' !== $primary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $primary_nav_font_spacing ); ?>px;
				<?php } ?>
			}

			.et_search_form_container input::-moz-placeholder {
				<?php if ( '' !== $primary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $primary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( '0' !== $primary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $primary_nav_font_spacing ); ?>px;
				<?php } ?>
			}
			.et_search_form_container input::-webkit-input-placeholder {
				<?php if ( '' !== $primary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $primary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( '0' !== $primary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $primary_nav_font_spacing ); ?>px;
				<?php } ?>
			}
			.et_search_form_container input:-ms-input-placeholder {
				<?php if ( '' !== $primary_nav_font_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $primary_nav_font_style ) ); ?>
				<?php } ?>
				<?php if ( '0' !== $primary_nav_font_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $primary_nav_font_spacing ); ?>px;
				<?php } ?>
			}
		<?php } ?>

		<?php if ( $menu_link_active !== '#2ea3f2' ) { ?>
			#top-menu li.current-menu-ancestor > a, #top-menu li.current-menu-item > a { color: <?php echo esc_html( $menu_link_active ); ?>; }
		<?php } ?>
		<?php if ( $footer_bg !== '#222222' ) { ?>
			#main-footer { background-color: <?php echo esc_html( $footer_bg ); ?>; }
		<?php } ?>
		<?php if ( $footer_widget_link_color !== '#fff' ) { ?>
			#footer-widgets .footer-widget li a { color: <?php echo esc_html( $footer_widget_link_color ); ?>; }
		<?php } ?>
		<?php if ( $footer_widget_text_color !== '#fff' ) { ?>
			.footer-widget { color: <?php echo esc_html( $footer_widget_text_color ); ?>; }
		<?php } ?>
		<?php if ( $footer_widget_header_color !== '#2ea3f2' ) { ?>
			#main-footer .footer-widget h4 { color: <?php echo esc_html( $footer_widget_header_color ); ?>; }
		<?php } ?>
		<?php if ( $footer_widget_bullet_color !== '#2ea3f2' ) { ?>
			.footer-widget li:before { border-color: <?php echo esc_html( $footer_widget_bullet_color ); ?>; }
		<?php } ?>
		<?php if ( $body_font_size !== $widget_body_font_size ) { ?>
			.footer-widget, .footer-widget li, .footer-widget li a, #footer-info { font-size: <?php echo esc_html( $widget_body_font_size ); ?>px; }
		<?php } ?>
		<?php
			/* Widget */
			et_pb_print_styles_css( array(
				array(
					'key' 		=> 'widget_header_font_style',
					'type' 		=> 'font-style',
					'default' 	=> '',
					'selector' 	=> '.footer-widget h4',
				),
				array(
					'key' 		=> 'widget_body_font_style',
					'type' 		=> 'font-style',
					'default' 	=> '',
					'selector' 	=> '.footer-widget .et_pb_widget div, .footer-widget .et_pb_widget ul, .footer-widget .et_pb_widget ol, .footer-widget .et_pb_widget label',
				),
				array(
					'key' 		=> 'widget_body_line_height',
					'type' 		=> 'line-height',
					'default' 	=> '',
					'selector' 	=> '.footer-widget .et_pb_widget div, .footer-widget .et_pb_widget ul, .footer-widget .et_pb_widget ol, .footer-widget .et_pb_widget label',
				),
			) );

			/* Footer widget bullet fix */
			if ( '1.7' !==  $widget_body_line_height || '14' !== $widget_body_font_size ) {
				// line_height (em) * font_size (px) = line height in px
				$widget_body_line_height_px 		= floatval( $widget_body_line_height ) * intval( $widget_body_font_size );

				// ( line height in px / 2 ) - half of bullet diameter
				$footer_widget_bullet_top 			= ( $widget_body_line_height_px / 2 ) - 3;

				printf( "#footer-widgets .footer-widget li:before { top: %spx; }", esc_html( $footer_widget_bullet_top ) );
			}

			/* Footer Menu */
			et_pb_print_styles_css( array(
				array(
					'key' 		=> 'footer_menu_background_color',
					'type' 		=> 'background-color',
					'default' 	=> 'rgba(255,255,255,0.05)',
					'selector' 	=> '#et-footer-nav'
 				),
				array(
					'key' 		=> 'footer_menu_text_color',
					'type' 		=> 'color',
					'default' 	=> '#bbbbbb',
					'selector' 	=> '.bottom-nav, .bottom-nav a, .bottom-nav li.current-menu-item a'
 				),
				array(
					'key' 		=> 'footer_menu_active_link_color',
					'type' 		=> 'color',
					'default' 	=> '#bbbbbb',
					'selector' 	=> '#et-footer-nav .bottom-nav li.current-menu-item a'
 				),
				array(
					'key' 		=> 'footer_menu_letter_spacing',
					'type' 		=> 'letter-spacing',
					'default' 	=> '0',
					'selector' 	=> '.bottom-nav'
 				),
				array(
					'key' 		=> 'footer_menu_font_style',
					'type' 		=> 'font-style',
					'default' 	=> '',
					'selector' 	=> '.bottom-nav a'
 				),
				array(
					'key' 		=> 'footer_menu_font_size',
					'type' 		=> 'font-size',
					'default' 	=> '14',
					'selector' 	=> '.bottom-nav, .bottom-nav a'
 				),
			) );

			/* Bottom Bar */
			et_pb_print_styles_css( array(
				array(
					'key' 		=> 'bottom_bar_background_color',
					'type' 		=> 'background-color',
					'default' 	=> 'rgba(0,0,0,0.32)',
					'selector' 	=> '#footer-bottom'
 				),
				array(
					'key' 		=> 'bottom_bar_text_color',
					'type' 		=> 'color',
					'default' 	=> '#666666',
					'selector' 	=> '#footer-info, #footer-info a'
 				),
				array(
					'key' 		=> 'bottom_bar_font_style',
					'type' 		=> 'font-style',
					'default' 	=> '',
					'selector' 	=> '#footer-info, #footer-info a'
 				),
				array(
					'key' 		=> 'bottom_bar_font_size',
					'type' 		=> 'font-size',
					'default' 	=> '14',
					'selector' 	=> '#footer-info'
 				),
				array(
					'key' 		=> 'bottom_bar_social_icon_size',
					'type' 		=> 'font-size',
					'default' 	=> '24',
					'selector' 	=> '#footer-bottom .et-social-icon a'
 				),
				array(
					'key' 		=> 'bottom_bar_social_icon_color',
					'type' 		=> 'color',
					'default' 	=> '#666666',
					'selector' 	=> '#footer-bottom .et-social-icon a'
 				),
			) );
		?>
		<?php if ( 'rgba' === substr( $primary_nav_bg, 0, 4 ) ) { ?>
			#main-header { box-shadow: none; }
		<?php } ?>
		<?php if ( 'rgba' === substr( $fixed_primary_nav_bg, 0, 4 ) || ( 'rgba' === substr( $primary_nav_bg, 0, 4 ) && '#ffffff' === $fixed_primary_nav_bg ) ) { ?>
			.et-fixed-header#main-header { box-shadow: none !important; }
		<?php } ?>
		<?php if ( '20' !== $button_text_size || '#ffffff' !== $button_text_color || 'rgba(0,0,0,0)' !== $button_bg_color || '2' !== $button_border_width || '#ffffff' !== $button_border_color || '3' !== $button_border_radius || '' !== $button_text_style || '0' !== $button_spacing ) { ?>
			body .et_pb_button,
			.woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt,
			.woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button
			{
				<?php if ( '20' !== $button_text_size ) { ?>
					 font-size: <?php echo esc_html( $button_text_size ); ?>px;
				<?php } ?>
				<?php if ( 'rgba(0,0,0,0)' !== $button_bg_color ) { ?>
					background: <?php echo esc_html( $button_bg_color ); ?>;
				<?php } ?>
				<?php if ( '2' !== $button_border_width ) { ?>
					border-width: <?php echo esc_html( $button_border_width ); ?>px !important;
				<?php } ?>
				<?php if ( '#ffffff' !== $button_border_color ) { ?>
					border-color: <?php echo esc_html( $button_border_color ); ?>;
				<?php } ?>
				<?php if ( '3' !== $button_border_radius ) { ?>
					border-radius: <?php echo esc_html( $button_border_radius ); ?>px;
				<?php } ?>
				<?php if ( '' !== $button_text_style ) { ?>
					<?php echo esc_html( et_pb_print_font_style( $button_text_style ) ); ?>;
				<?php } ?>
				<?php if ( '0' !== $button_spacing ) { ?>
					letter-spacing: <?php echo esc_html( $button_spacing ); ?>px;
				<?php } ?>
			}
			body.et_pb_button_helper_class .et_pb_button,
			.woocommerce.et_pb_button_helper_class a.button.alt, .woocommerce-page.et_pb_button_helper_class a.button.alt, .woocommerce.et_pb_button_helper_class button.button.alt, .woocommerce-page.et_pb_button_helper_class button.button.alt, .woocommerce.et_pb_button_helper_class input.button.alt, .woocommerce-page.et_pb_button_helper_class input.button.alt, .woocommerce.et_pb_button_helper_class #respond input#submit.alt, .woocommerce-page.et_pb_button_helper_class #respond input#submit.alt, .woocommerce.et_pb_button_helper_class #content input.button.alt, .woocommerce-page.et_pb_button_helper_class #content input.button.alt,
			.woocommerce.et_pb_button_helper_class a.button, .woocommerce-page.et_pb_button_helper_class a.button, .woocommerce.et_pb_button_helper_class button.button, .woocommerce-page.et_pb_button_helper_class button.button, .woocommerce.et_pb_button_helper_class input.button, .woocommerce-page.et_pb_button_helper_class input.button, .woocommerce.et_pb_button_helper_class #respond input#submit, .woocommerce-page.et_pb_button_helper_class #respond input#submit, .woocommerce.et_pb_button_helper_class #content input.button, .woocommerce-page.et_pb_button_helper_class #content input.button {
				<?php if ( '#ffffff' !== $button_text_color ) { ?>
					color: <?php echo esc_html( $button_text_color ); ?>;
				<?php } ?>
			}
		<?php } ?>
		<?php if ( '5' !== $button_icon || '#ffffff' !== $button_icon_color || '20' !== $button_text_size ) { ?>
			body .et_pb_button:after,
			.woocommerce a.button.alt:after, .woocommerce-page a.button.alt:after, .woocommerce button.button.alt:after, .woocommerce-page button.button.alt:after, .woocommerce input.button.alt:after, .woocommerce-page input.button.alt:after, .woocommerce #respond input#submit.alt:after, .woocommerce-page #respond input#submit.alt:after, .woocommerce #content input.button.alt:after, .woocommerce-page #content input.button.alt:after,
			.woocommerce a.button:after, .woocommerce-page a.button:after, .woocommerce button.button:after, .woocommerce-page button.button:after, .woocommerce input.button:after, .woocommerce-page input.button:after, .woocommerce #respond input#submit:after, .woocommerce-page #respond input#submit:after, .woocommerce #content input.button:after, .woocommerce-page #content input.button:after
			{
				<?php if ( '5' !== $button_icon ) { ?>
					<?php if ( "'" === $button_icon ) { ?>
						content: "<?php echo htmlspecialchars_decode( $button_icon ); ?>";
					<?php } else { ?>
						content: '<?php echo htmlspecialchars_decode( $button_icon ); ?>';
					<?php } ?>
					font-size: <?php echo esc_html( $button_text_size ); ?>px;
				<?php } else { ?>
					font-size: <?php echo esc_html( $button_icon_size ); ?>px;
				<?php } ?>
				<?php if ( '#ffffff' !== $button_icon_color ) { ?>
					color: <?php echo esc_html( $button_icon_color ); ?>;
				<?php } ?>
			}
		<?php } ?>
		<?php if ( '#ffffff' !== $button_text_color_hover || 'rgba(255,255,255,0.2)' !== $button_bg_color_hover || 'rgba(0,0,0,0)' !== $button_border_color_hover || '3' !== $button_border_radius_hover || '0' !== $button_spacing_hover ) { ?>
			body .et_pb_button:hover,
			.woocommerce a.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page #content input.button.alt:hover,
			.woocommerce a.button:hover, .woocommerce-page a.button:hover, .woocommerce button.button, .woocommerce-page button.button:hover, .woocommerce input.button:hover, .woocommerce-page input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-page #respond input#submit:hover, .woocommerce #content input.button:hover, .woocommerce-page #content input.button:hover
			{
				<?php if ( '#ffffff' !== $button_text_color_hover ) { ?>
					 color: <?php echo esc_html( $button_text_color_hover ); ?> !important;
				<?php } ?>
				<?php if ( 'rgba(255,255,255,0.2)' !== $button_bg_color_hover ) { ?>
					background: <?php echo esc_html( $button_bg_color_hover ); ?> !important;
				<?php } ?>
				<?php if ( 'rgba(0,0,0,0)' !== $button_border_color_hover ) { ?>
					border-color: <?php echo esc_html( $button_border_color_hover ); ?> !important;
				<?php } ?>
				<?php if ( '3' !== $button_border_radius_hover ) { ?>
					border-radius: <?php echo esc_html( $button_border_radius_hover ); ?>px;
				<?php } ?>
				<?php if ( '0' !== $button_spacing_hover ) { ?>
					letter-spacing: <?php echo esc_html( $button_spacing_hover ); ?>px;
				<?php } ?>
			}
		<?php } ?>

		<?php if ( '' !== $body_header_style || '0' !== $body_header_spacing || '1' !== $body_header_height) { ?>
				h1, h2, h3, h4, h5, h6, .et_quote_content blockquote p, .et_pb_slide_description h2 {
					<?php if ( $body_header_style !== '' ) { ?>
						<?php echo esc_html( et_pb_print_font_style( $body_header_style ) ); ?>
					<?php } ?>
					<?php if ( $body_header_spacing !== '0' ) { ?>
						letter-spacing: <?php echo esc_html( $body_header_spacing ); ?>px;
					<?php } ?>

					<?php if ( $body_header_height !== '1' ) { ?>
						line-height: <?php echo esc_html( $body_header_height ); ?>em;
					<?php } ?>
				}
		<?php } ?>

		<?php
			/* Blog Meta */
			$et_pb_print_selectors_post_meta = "body.home-posts #left-area .et_pb_post .post-meta, body.archive #left-area .et_pb_post .post-meta, body.search #left-area .et_pb_post .post-meta, body.single #left-area .et_pb_post .post-meta";

			et_pb_print_styles_css( array(
				array(
					'key'      => 'post_meta_height',
					'type'     => 'line-height',
					'default'  => '1',
					'selector' => $et_pb_print_selectors_post_meta,
 				),
				array(
					'key'      => 'post_meta_spacing',
					'type'     => 'letter-spacing',
					'default'  => '0',
					'selector' => $et_pb_print_selectors_post_meta,
 				),
				array(
					'key'      => 'post_meta_style',
					'type'     => 'font-style',
					'default'  => '',
					'selector' => $et_pb_print_selectors_post_meta,
 				),
			) );

			/* Blog Title */
			$et_pb_print_selectors_post_header = "body.home-posts #left-area .et_pb_post h2, body.archive #left-area .et_pb_post h2, body.search #left-area .et_pb_post h2, body.single .et_post_meta_wrapper h1";

			et_pb_print_styles_css( array(
				array(
					'key'      => 'post_header_height',
					'type'     => 'line-height',
					'default'  => '1',
					'selector' => $et_pb_print_selectors_post_header,
 				),
				array(
					'key'      => 'post_header_spacing',
					'type'     => 'letter-spacing',
					'default'  => '0',
					'selector' => $et_pb_print_selectors_post_header,
 				),
				array(
					'key'      => 'post_header_style',
					'type'     => 'font-style',
					'default'  => '',
					'selector' => $et_pb_print_selectors_post_header,
 				),
			) );
		?>

		@media only screen and ( min-width: 981px ) {
			<?php if ( '4' !== $section_padding ) { ?>
				.et_pb_section { padding: <?php echo esc_html( $section_padding ); ?>% 0; }
				.et_pb_section.et_pb_section_first { padding-top: inherit; }
			<?php } ?>
			<?php if ( '2' !== $row_padding ) { ?>
				.et_pb_row { padding: <?php echo esc_html( $row_padding ); ?>% 0; }
			<?php } ?>
			<?php if ( '30' !== $body_header_size ) { ?>
				h1 { font-size: <?php echo esc_html( $body_header_size ); ?>px; }
				h2, .product .related h2, .et_pb_column_1_2 .et_quote_content blockquote p { font-size: <?php echo esc_html( intval( $body_header_size * .86 ) ) ; ?>px; }
				h3 { font-size: <?php echo esc_html( intval( $body_header_size * .73 ) ); ?>px; }
				h4, .et_pb_circle_counter h3, .et_pb_number_counter h3, .et_pb_column_1_3 .et_pb_post h2, .et_pb_column_1_4 .et_pb_post h2, .et_pb_blog_grid h2, .et_pb_column_1_3 .et_quote_content blockquote p, .et_pb_column_3_8 .et_quote_content blockquote p, .et_pb_column_1_4 .et_quote_content blockquote p, .et_pb_blog_grid .et_quote_content blockquote p, .et_pb_column_1_3 .et_link_content h2, .et_pb_column_3_8 .et_link_content h2, .et_pb_column_1_4 .et_link_content h2, .et_pb_blog_grid .et_link_content h2, .et_pb_column_1_3 .et_audio_content h2, .et_pb_column_3_8 .et_audio_content h2, .et_pb_column_1_4 .et_audio_content h2, .et_pb_blog_grid .et_audio_content h2, .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2 { font-size: <?php echo esc_html( intval( $body_header_size * .6 ) ); ?>px; }
				.et_pb_slide_description h2 { font-size: <?php echo esc_html( intval( $body_header_size * 1.53 ) ); ?>px; }
				.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: <?php echo esc_html( intval( $body_header_size * .53 ) ); ?>px; }
			<?php } ?>
			<?php if ( intval( $body_header_size * .6 ) !== $widget_header_font_size ) { ?>
				.footer-widget h4 { font-size: <?php echo esc_html( $widget_header_font_size ); ?>px; }
			<?php } ?>
			<?php if ( '66' !== $menu_height ) { ?>
				.et_header_style_left #et-top-navigation, .et_header_style_split #et-top-navigation  { padding: <?php echo esc_html( round( $menu_height / 2 ) ); ?>px 0 0 0; }
				.et_header_style_left #et-top-navigation nav > ul > li > a, .et_header_style_split #et-top-navigation nav > ul > li > a { padding-bottom: <?php echo esc_html( round ( $menu_height / 2 ) ); ?>px; }
				.et_header_style_centered #main-header .logo_container { height: <?php echo esc_html( $menu_height ); ?>px; }
				.et_header_style_centered #top-menu > li > a { padding-bottom: <?php echo esc_html( round ( $menu_height * .18 ) ); ?>px; }
				.et_header_style_split .centered-inline-logo-wrap { width: <?php echo esc_html( $menu_height ); ?>px; margin: -<?php echo esc_html( $menu_height ); ?>px 0; }
				.et_header_style_split .centered-inline-logo-wrap #logo { max-height: <?php echo esc_html( $menu_height ); ?>px; }

			<?php } ?>
			<?php if ( 'false' !== $hide_primary_logo || 'false' !== $hide_fixed_logo ) { ?>
				.et_header_style_centered.et_hide_primary_logo #main-header:not(.et-fixed-header) .logo_container, .et_header_style_centered.et_hide_fixed_logo #main-header.et-fixed-header .logo_container { height: <?php echo esc_html( $menu_height * .18 ); ?>px; }
			<?php } ?>
			<?php if ( '40' !== $fixed_menu_height ) { ?>
				.et_header_style_left .et-fixed-header #et-top-navigation, .et_header_style_split .et-fixed-header #et-top-navigation { padding: <?php echo esc_html( intval( round( $fixed_menu_height / 2 ) ) ); ?>px 0 0 0; }
				.et_header_style_left .et-fixed-header #et-top-navigation nav > ul > li > a, .et_header_style_split .et-fixed-header #et-top-navigation nav > ul > li > a  { padding-bottom: <?php echo esc_html( round( $fixed_menu_height / 2 ) ); ?>px; }
				.et_header_style_centered #main-header.et-fixed-header .logo_container { height: <?php echo esc_html( $fixed_menu_height ); ?>px; }
				.et_header_style_split .et-fixed-header .centered-inline-logo-wrap { width: <?php echo esc_html( $fixed_menu_height ); ?>px; margin: -<?php echo esc_html( $fixed_menu_height ); ?>px 0;  }
				.et_header_style_split .et-fixed-header .centered-inline-logo-wrap #logo { max-height: <?php echo esc_html( $fixed_menu_height ); ?>px; }
			<?php } ?>
			<?php if ( $fixed_secondary_nav_bg !== '#2ea3f2' ) { ?>
				.et-fixed-header#top-header, .et-fixed-header#top-header #et-secondary-nav li ul { background-color: <?php echo esc_html( $fixed_secondary_nav_bg ); ?>; }
			<?php } ?>
			<?php if ( $fixed_primary_nav_bg !== $primary_nav_bg ) { ?>
				.et-fixed-header#main-header, .et-fixed-header#main-header .nav li ul, .et-fixed-header .et-search-form { background-color: <?php echo esc_html( $fixed_primary_nav_bg ); ?>; }
			<?php } ?>
			<?php if ( $fixed_primary_nav_font_size !== '14' ) { ?>
				.et-fixed-header #top-menu li a { font-size: <?php echo esc_html( $fixed_primary_nav_font_size ); ?>px; }
			<?php } ?>
			<?php if ( $fixed_menu_link !== 'rgba(0,0,0,0.6)' ) { ?>
				.et-fixed-header #top-menu a, .et-fixed-header #et_search_icon:before, .et-fixed-header #et_top_search .et-search-form input, .et-fixed-header .et_search_form_container input, .et-fixed-header .et_close_search_field:after, .et-fixed-header #et-top-navigation .et-cart-info { color: <?php echo esc_html( $fixed_menu_link ); ?> !important; }
				.et-fixed-header .et_search_form_container input::-moz-placeholder { color: <?php echo esc_html( $fixed_menu_link ); ?> !important; }
				.et-fixed-header .et_search_form_container input::-webkit-input-placeholder { color: <?php echo esc_html( $fixed_menu_link ); ?> !important; }
				.et-fixed-header .et_search_form_container input:-ms-input-placeholder { color: <?php echo esc_html( $fixed_menu_link ); ?> !important; }
			<?php } ?>
			<?php if ( $fixed_menu_link_active !== '#2ea3f2' ) { ?>
				.et-fixed-header #top-menu li.current-menu-ancestor > a, .et-fixed-header #top-menu li.current-menu-item > a { color: <?php echo esc_html( $fixed_menu_link_active ); ?> !important; }
			<?php } ?>

			<?php
				/* Blog Meta & Title */
				et_pb_print_styles_css( array(
					array(
						'key'      => 'post_meta_font_size',
						'type'     => 'font-size',
						'default'  => '14',
						'selector' => $et_pb_print_selectors_post_meta,
	 				),
					array(
						'key'      => 'post_header_font_size',
						'type'     => 'font-size-post-header',
						'default'  => '30',
						'selector' => '',
	 				),
				) );
			?>
		}
		@media only screen and ( min-width: <?php echo esc_html( $large_content_width ); ?>px) {
			.et_pb_row { padding: <?php echo esc_html( intval( $large_content_width * $row_padding / 100 ) ); ?>px 0; }
			.et_pb_section { padding: <?php echo esc_html( intval( $large_content_width * $section_padding / 100 ) ); ?>px 0; }
			.single.et_pb_pagebuilder_layout.et_full_width_page .et_post_meta_wrapper { padding-top: <?php echo esc_html( intval( $large_content_width * $row_padding / 100 * 3 ) ); ?>px; }
			.et_pb_section.et_pb_section_first { padding-top: inherit; }
			.et_pb_fullwidth_section { padding: 0; }
		}
		@media only screen and ( max-width: 980px ) {
			<?php if ( $mobile_primary_nav_bg !== $primary_nav_bg ) { ?>
				#main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu { background-color: <?php echo esc_html( $mobile_primary_nav_bg ); ?>; }
			<?php } ?>
			<?php if ( $menu_link !== $mobile_menu_link ) { ?>
				.et_header_style_centered .mobile_nav .select_page, .et_header_style_split .mobile_nav .select_page, .et_mobile_menu li a, .mobile_menu_bar:before, .et_nav_text_color_light #top-menu > li > a, .et_nav_text_color_dark #top-menu > li > a, #top-menu a, .et_mobile_menu li a, #et_search_icon:before, #et_top_search .et-search-form input, .et_search_form_container input, #et-top-navigation .et-cart-info { color: <?php echo esc_html( $mobile_menu_link ); ?>; }
				.et_close_search_field:after { color: <?php echo esc_html( $mobile_menu_link ); ?> !important; }
				.et_search_form_container input::-moz-placeholder { color: <?php echo esc_html( $mobile_menu_link ); ?>; }
				.et_search_form_container input::-webkit-input-placeholder { color: <?php echo esc_html( $mobile_menu_link ); ?>; }
				.et_search_form_container input:-ms-input-placeholder { color: <?php echo esc_html( $mobile_menu_link ); ?>; }
			<?php } ?>
			<?php if ( '14' !== $tablet_body_font_size && $body_font_size !== $tablet_body_font_size ) { ?>
				body, .et_pb_column_1_2 .et_quote_content blockquote cite, .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_pb_column_1_3 .et_quote_content blockquote cite, .et_pb_column_3_8 .et_quote_content blockquote cite, .et_pb_column_1_4 .et_quote_content blockquote cite, .et_pb_blog_grid .et_quote_content blockquote cite, .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_pb_blog_grid .et_link_content a.et_link_main_url { font-size: <?php echo esc_html( $tablet_body_font_size ); ?>px; }
				.et_pb_slide_content, .et_pb_best_value { font-size: <?php echo esc_html( intval( $tablet_body_font_size * 1.14 ) ); ?>px; }
			<?php } ?>
			<?php if ( '30' !== $tablet_header_font_size && $tablet_header_font_size !== $body_header_size ) { ?>
				h1 { font-size: <?php echo esc_html( $tablet_header_font_size ); ?>px; }
				h2, .product .related h2, .et_pb_column_1_2 .et_quote_content blockquote p { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .86 ) ) ; ?>px; }
				h3 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .73 ) ); ?>px; }
				h4, .et_pb_circle_counter h3, .et_pb_number_counter h3, .et_pb_column_1_3 .et_pb_post h2, .et_pb_column_1_4 .et_pb_post h2, .et_pb_blog_grid h2, .et_pb_column_1_3 .et_quote_content blockquote p, .et_pb_column_3_8 .et_quote_content blockquote p, .et_pb_column_1_4 .et_quote_content blockquote p, .et_pb_blog_grid .et_quote_content blockquote p, .et_pb_column_1_3 .et_link_content h2, .et_pb_column_3_8 .et_link_content h2, .et_pb_column_1_4 .et_link_content h2, .et_pb_blog_grid .et_link_content h2, .et_pb_column_1_3 .et_audio_content h2, .et_pb_column_3_8 .et_audio_content h2, .et_pb_column_1_4 .et_audio_content h2, .et_pb_blog_grid .et_audio_content h2, .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .6 ) ); ?>px; }
				.et_pb_slide_description h2 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * 1.53 ) ); ?>px; }
				.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: <?php echo esc_html( intval( $tablet_header_font_size * .53 ) ); ?>px; }
			<?php } ?>
			<?php if ( '50' !== $tablet_section_height ) { ?>
				.et_pb_section { padding: <?php echo esc_html( $tablet_section_height ); ?>px 0; }
				.et_pb_section.et_pb_section_first { padding-top: inherit; }
				.et_pb_section.et_pb_fullwidth_section { padding: 0; }
			<?php } ?>
			<?php if ( '30' !== $tablet_row_height ) { ?>
				.et_pb_row, .et_pb_column .et_pb_row_inner { padding: <?php echo esc_html( $tablet_row_height ); ?>px 0 !important; }
			<?php } ?>
		}
		@media only screen and ( max-width: 767px ) {
			<?php if ( '14' !== $phone_body_font_size && $phone_body_font_size !== $tablet_body_font_size ) { ?>
				body, .et_pb_column_1_2 .et_quote_content blockquote cite, .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_pb_column_1_3 .et_quote_content blockquote cite, .et_pb_column_3_8 .et_quote_content blockquote cite, .et_pb_column_1_4 .et_quote_content blockquote cite, .et_pb_blog_grid .et_quote_content blockquote cite, .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_pb_blog_grid .et_link_content a.et_link_main_url { font-size: <?php echo esc_html( $phone_body_font_size ); ?>px; }
				.et_pb_slide_content, .et_pb_best_value { font-size: <?php echo esc_html( intval( $phone_body_font_size * 1.14 ) ); ?>px; }
			<?php } ?>
			<?php if ( '30' !== $phone_header_font_size && $tablet_header_font_size !== $phone_header_font_size ) { ?>
				h1 { font-size: <?php echo esc_html( $phone_header_font_size ); ?>px; }
				h2, .product .related h2, .et_pb_column_1_2 .et_quote_content blockquote p { font-size: <?php echo esc_html( intval( $phone_header_font_size * .86 ) ) ; ?>px; }
				h3 { font-size: <?php echo esc_html( intval( $phone_header_font_size * .73 ) ); ?>px; }
				h4, .et_pb_circle_counter h3, .et_pb_number_counter h3, .et_pb_column_1_3 .et_pb_post h2, .et_pb_column_1_4 .et_pb_post h2, .et_pb_blog_grid h2, .et_pb_column_1_3 .et_quote_content blockquote p, .et_pb_column_3_8 .et_quote_content blockquote p, .et_pb_column_1_4 .et_quote_content blockquote p, .et_pb_blog_grid .et_quote_content blockquote p, .et_pb_column_1_3 .et_link_content h2, .et_pb_column_3_8 .et_link_content h2, .et_pb_column_1_4 .et_link_content h2, .et_pb_blog_grid .et_link_content h2, .et_pb_column_1_3 .et_audio_content h2, .et_pb_column_3_8 .et_audio_content h2, .et_pb_column_1_4 .et_audio_content h2, .et_pb_blog_grid .et_audio_content h2, .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2 { font-size: <?php echo esc_html( intval( $phone_header_font_size * .6 ) ); ?>px; }
				.et_pb_slide_description h2 { font-size: <?php echo esc_html( intval( $phone_header_font_size * 1.53 ) ); ?>px; }
				.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: <?php echo esc_html( intval( $phone_header_font_size * .53 ) ); ?>px; }
			<?php } ?>
			<?php if ( '50' !== $phone_section_height && $tablet_section_height !== $phone_section_height ) { ?>
				.et_pb_section { padding: <?php echo esc_html( $phone_section_height ); ?>px 0; }
				.et_pb_section.et_pb_section_first { padding-top: inherit; }
				.et_pb_section.et_pb_fullwidth_section { padding: 0; }
			<?php } ?>
			<?php if ( '30' !== $phone_row_height && $tablet_row_height !== $phone_row_height ) { ?>
				.et_pb_row, .et_pb_column .et_pb_row_inner { padding: <?php echo esc_html( $phone_row_height ); ?>px 0; }
			<?php } ?>
		}
		<?php
			$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
			$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );
			$et_gf_buttons_font = sanitize_text_field( et_get_option( 'all_buttons_font', 'none' ) );
			$et_gf_primary_nav_font = sanitize_text_field( et_get_option( 'primary_nav_font', 'none' ) );
			$et_gf_secondary_nav_font = sanitize_text_field( et_get_option( 'secondary_nav_font', 'none' ) );
			$site_domain = get_locale();

			$et_one_font_languages = et_get_one_font_languages();

			if ( isset( $et_one_font_languages[$site_domain] ) ) {
				printf( '%s { font-family: %s; }',
					'h1, h2, h3, h4, h5, h6, body, input, textarea, select',
					$et_one_font_languages[$site_domain]['font_family']
				);
			} else if ( 'none' != $et_gf_heading_font || 'none' != $et_gf_body_font || 'none' != $et_gf_buttons_font || 'none' != $et_gf_primary_nav_font || 'none' != $et_gf_secondary_nav_font ) {
				if ( 'none' != $et_gf_heading_font ) { ?>
					h1, h2, h3, h4, h5, h6 {
						<?php echo et_builder_get_font_family( $et_gf_heading_font ); ?>
					}
				<?php }

				if ( 'none' != $et_gf_body_font ) { ?>
					body, input, textarea, select {
						<?php echo et_builder_get_font_family( $et_gf_body_font ); ?>
					}
				<?php }

				if ( 'none' != $et_gf_buttons_font ) { ?>
					.et_pb_button {
						<?php echo et_builder_get_font_family( $et_gf_buttons_font ); ?>
					}
				<?php }

				if ( 'none' != $et_gf_primary_nav_font ) { ?>
					#main-header {
						<?php echo et_builder_get_font_family( $et_gf_primary_nav_font ); ?>
					}
				<?php }

				if ( 'none' != $et_gf_secondary_nav_font ) { ?>
					#top-header {
						<?php echo et_builder_get_font_family( $et_gf_secondary_nav_font ); ?>
					}
				<?php }
			}
		?>
	</style>

	<?php
	/**
	 * use_sidebar_width might invalidate the use of sidebar_width.
	 * It is placed outside other customizer style so live preview
	 * can invalidate and revalidate it for smoother experience
	 */
	if ( $use_sidebar_width && 21 !== $sidebar_width && 19 <= $sidebar_width && 33 >= $sidebar_width ) { ?>
	<style id="theme-customizer-sidebar-width-css">
		<?php
			$content_width = 100 - $sidebar_width;
			$content_width_percentage = $content_width . '%';
			$sidebar_width_percentage = $sidebar_width . '%';
			printf(
				'body #page-container #sidebar { width:%2$s; }
				body #page-container #left-area { width:%1$s; }
				.et_right_sidebar #main-content .container:before { right:%2$s !important; }
				.et_left_sidebar #main-content .container:before { left:%2$s !important; }',
				esc_html( $content_width_percentage  ),
				esc_html( $sidebar_width_percentage )
			);
		?>
	</style>
	<?php } ?>

	<style id="module-customizer-css">
		<?php

			/* Gallery */
			et_pb_print_module_styles_css( 'et_pb_gallery', array(
				array(
					'type'		=> 'color',
					'key' 		=> 'zoom_icon_color',
					'selector' 	=> '.et_pb_gallery_image .et_overlay:before',
					'important'	=> true,
				),
				array(
					'type'		=> 'background-color',
					'key' 		=> 'hover_overlay_color',
					'selector' 	=> '.et_pb_gallery_image .et_overlay',
				),
				array(
					'type'		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_gallery_grid .et_pb_gallery_item .et_pb_gallery_title',
				),
				array(
					'type'		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_gallery_grid .et_pb_gallery_item .et_pb_gallery_title',
				),
				array(
					'type'		=> 'font-size',
					'key' 		=> 'caption_font_size',
					'selector' 	=> '.et_pb_gallery .et_pb_gallery_item .et_pb_gallery_caption',
				),
				array(
					'type'		=> 'font-style',
					'key' 		=> 'caption_font_style',
					'selector' 	=> '.et_pb_gallery .et_pb_gallery_item .et_pb_gallery_caption',
				),
			) );

			/* Blurb */
			et_pb_print_module_styles_css( 'et_pb_blurb', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_blurb h4',
				),
			) );

			/* Tabs */
			et_pb_print_module_styles_css( 'et_pb_tabs', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_tabs_controls li',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_tabs_controls li',
				),
				array(
					'type' 		=> 'padding-tabs',
					'key' 		=> 'padding',
					'selector' 	=> '',
				),
			) );

			/* Slider */
			et_pb_print_module_styles_css( 'et_pb_slider', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_description h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_description h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'body_font_size',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_content',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'body_font_style',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_content',
				),
				array(
					'type' 		=> 'padding-slider',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_slider_fullwidth_off .et_pb_slide_description',
				),
			) );

			/* Testimonial */
			et_pb_print_module_styles_css( 'et_pb_testimonial', array(
				array(
					'type' 		=> 'border-radius',
					'key' 		=> 'portrait_border_radius',
					'selector' 	=> '.et_pb_testimonial_portrait, .et_pb_testimonial_portrait:before',
				),
				array(
					'type' 		=> 'width',
					'key' 		=> 'portrait_width',
					'selector' 	=> '.et_pb_testimonial_portrait',
				),
				array(
					'type' 		=> 'height',
					'key' 		=> 'portrait_height',
					'selector' 	=> '.et_pb_testimonial_portrait',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'author_name_font_style',
					'selector' 	=> '.et_pb_testimonial_author',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'author_details_font_style',
					'selector' 	=> 'p.et_pb_testimonial_meta',
				),
			) );

			/* Pricing Table */
			et_pb_print_module_styles_css( 'et_pb_pricing_tables', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_pricing_heading h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_pricing_heading h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'subheader_font_size',
					'selector' 	=> '.et_pb_best_value',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'subheader_font_style',
					'selector' 	=> '.et_pb_best_value',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'price_font_size',
					'selector' 	=> '.et_pb_sum',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'price_font_style',
					'selector' 	=> '.et_pb_sum',
				),
			) );

			/* Call to Action */
			et_pb_print_module_styles_css( 'et_pb_cta', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_promo h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_promo h2, .et_pb_promo h1',
				),
				array(
					'type' 		=> 'padding-call-to-action',
					'key' 		=> 'custom_padding',
					'selector' 	=> '',
					'important' => true,
				),
			) );

			/* Audio */
			et_pb_print_module_styles_css( 'et_pb_audio', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_audio_module_content h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_audio_module_content h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'caption_font_size',
					'selector' 	=> '.et_pb_audio_module p',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'caption_font_style',
					'selector' 	=> '.et_pb_audio_module p',
				),
			) );

			/* Subscribe */
			et_pb_print_module_styles_css( 'et_pb_signup', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_subscribe h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_subscribe h2',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_subscribe',
				),
			) );

			/* Login */
			et_pb_print_module_styles_css( 'et_pb_login', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_login h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_login h2',
				),
				array(
					'type' 		=> 'padding-top-bottom',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_login',
				),
			) );

			/* Portfolio */
			et_pb_print_module_styles_css( 'et_pb_portfolio', array(
				array(
					'type' 		=> 'color',
					'key' 		=> 'zoom_icon_color',
					'selector' 	=> '.et_pb_portfolio .et_overlay:before, .et_pb_fullwidth_portfolio .et_overlay:before, .et_pb_portfolio_grid .et_overlay:before',
					'important' => true,
				),
				array(
					'type' 		=> 'background-color',
					'key' 		=> 'hover_overlay_color',
					'selector' 	=> '.et_pb_portfolio .et_overlay, .et_pb_fullwidth_portfolio .et_overlay, .et_pb_portfolio_grid .et_overlay',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_portfolio .et_pb_portfolio_item h2, .et_pb_fullwidth_portfolio .et_pb_portfolio_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_portfolio .et_pb_portfolio_item h2, .et_pb_fullwidth_portfolio .et_pb_portfolio_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'caption_font_size',
					'selector' 	=> '.et_pb_portfolio .et_pb_portfolio_item .post-meta, .et_pb_fullwidth_portfolio .et_pb_portfolio_item .post-meta, .et_pb_portfolio_grid .et_pb_portfolio_item .post-meta',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'caption_font_style',
					'selector' 	=> '.et_pb_portfolio .et_pb_portfolio_item .post-meta, .et_pb_fullwidth_portfolio .et_pb_portfolio_item .post-meta, .et_pb_portfolio_grid .et_pb_portfolio_item .post-meta',
				),
			) );

			/* Filterable Portfolio */
			et_pb_print_module_styles_css( 'et_pb_filterable_portfolio', array(
				array(
					'type' 		=> 'color',
					'key' 		=> 'zoom_icon_color',
					'selector' 	=> '.et_pb_filterable_portfolio .et_overlay:before',
				),
				array(
					'type' 		=> 'background-color',
					'key' 		=> 'hover_overlay_color',
					'selector' 	=> '.et_pb_filterable_portfolio .et_overlay',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_item h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_item h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'caption_font_size',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_item .post-meta',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'caption_font_style',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_item .post-meta',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'filter_font_size',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_filters li',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'filter_font_style',
					'selector' 	=> '.et_pb_filterable_portfolio .et_pb_portfolio_filters li',
				),
			) );

			/* Bar Counter */
			et_pb_print_module_styles_css( 'et_pb_counters', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_counters .et_pb_counter_title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_counters .et_pb_counter_title',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'percent_font_size',
					'selector' 	=> '.et_pb_counters .et_pb_counter_amount',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'percent_font_style',
					'selector' 	=> '.et_pb_counters .et_pb_counter_amount',
				),
				array(
					'type' 		=> 'border-radius',
					'key' 		=> 'border_radius',
					'selector' 	=> '.et_pb_counters .et_pb_counter_amount, .et_pb_counters .et_pb_counter_container',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_counter_amount',
				),
			) );

			/* Circle Counter */
			et_pb_print_module_styles_css( 'et_pb_circle_counter', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'number_font_size',
					'selector' 	=> '.et_pb_circle_counter .percent p',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'number_font_style',
					'selector' 	=> '.et_pb_circle_counter .percent p',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_circle_counter h3',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_circle_counter h3',
				),
			) );

			/* Number Counter */
			et_pb_print_module_styles_css( 'et_pb_number_counter', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'number_font_size',
					'selector' 	=> '.et_pb_number_counter .percent p',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'number_font_style',
					'selector' 	=> '.et_pb_number_counter .percent p',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_number_counter h3',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_number_counter h3',
				),
			) );

			/* Accordion */
			et_pb_print_module_styles_css( 'et_pb_accordion', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'toggle_font_size',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle_title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'toggle_font_style',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle.et_pb_toggle_open .et_pb_toggle_title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'inactive_toggle_font_style',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle.et_pb_toggle_close .et_pb_toggle_title',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'toggle_icon_size',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle_title:before',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'custom_padding',
					'selector' 	=> '.et_pb_accordion .et_pb_toggle_open, .et_pb_accordion .et_pb_toggle_close',
				),
			) );

			/* Toggle */
			et_pb_print_module_styles_css( 'et_pb_toggle', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item h5',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item.et_pb_toggle_open h5',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'inactive_title_font_style',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item.et_pb_toggle_close h5',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'toggle_icon_size',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item .et_pb_toggle_title:before',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'custom_padding',
					'selector' 	=> '.et_pb_toggle.et_pb_toggle_item',
				),
			) );

			/* Contact Form */
			et_pb_print_module_styles_css( 'et_pb_contact_form', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.et_pb_contact_form_container .et_pb_contact_main_title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.et_pb_contact_form_container .et_pb_contact_main_title',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'form_field_font_size',
					'selector' 	=> '.et_pb_contact_form_container .et_pb_contact p input, .et_pb_contact_form_container .et_pb_contact p textarea',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'form_field_font_style',
					'selector' 	=> '.et_pb_contact_form_container .et_pb_contact p input, .et_pb_contact_form_container .et_pb_contact p textarea',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'captcha_font_size',
					'selector' 	=> '.et_pb_contact_captcha_question',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'captcha_font_style',
					'selector' 	=> '.et_pb_contact_captcha_question',
				),
				array(
					'type' 		=> 'padding',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_contact p input, .et_pb_contact p textarea',
				),
			) );

			/* Sidebar */
			et_pb_print_module_styles_css( 'et_pb_sidebar', array(
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_widget_area h4',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_widget_area h4',
				),
			) );

			/* Divider */
			et_pb_print_module_styles_css( 'et_pb_divider', array(
				array(
					'type' 		=> 'border-top-style',
					'key' 		=> 'divider_style',
					'selector' 	=> '.et_pb_space:before',
				),
				array(
					'type' 		=> 'border-top-width',
					'key' 		=> 'divider_weight',
					'selector' 	=> '.et_pb_space:before',
				),
				array(
					'type' 		=> 'height',
					'key' 		=> 'height',
					'selector' 	=> '.et_pb_space',
				),
			) );

			/* Person */
			et_pb_print_module_styles_css( 'et_pb_team_member', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_team_member h4',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_team_member h4',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'subheader_font_size',
					'selector' 	=> '.et_pb_team_member .et_pb_member_position',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'subheader_font_style',
					'selector' 	=> '.et_pb_team_member .et_pb_member_position',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'social_network_icon_size',
					'selector' 	=> '.et_pb_member_social_links a',
				),
			) );

			/* Blog */
			et_pb_print_module_styles_css( 'et_pb_blog', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_posts .et_pb_post h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_posts .et_pb_post h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'meta_font_size',
					'selector' 	=> '.et_pb_posts .et_pb_post .post-meta',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'meta_font_style',
					'selector' 	=> '.et_pb_posts .et_pb_post .post-meta',
				),
			) );

			/* Blog Masonry */
			et_pb_print_module_styles_css( 'et_pb_blog_masonry', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_blog_grid .et_pb_post h2',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_blog_grid .et_pb_post h2',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'meta_font_size',
					'selector' 	=> '.et_pb_blog_grid .et_pb_post .post-meta',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'meta_font_style',
					'selector' 	=> '.et_pb_blog_grid .et_pb_post .post-meta',
				),
			) );

			/* Shop */
			et_pb_print_module_styles_css( 'et_pb_shop', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'title_font_size',
					'selector' 	=> '.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3',
					'important' => false,
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'title_font_style',
					'selector' 	=> '.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3',
					'important' => false,
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'sale_badge_font_size',
					'selector' 	=> '.woocommerce span.onsale, .woocommerce-page span.onsale',
					'important' => false,
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'sale_badge_font_style',
					'selector' 	=> '.woocommerce span.onsale, .woocommerce-page span.onsale',
					'important' => true,
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'price_font_size',
					'selector' 	=> '.woocommerce ul.products li.product .price .amount, .woocommerce-page ul.products li.product .price .amount',
					'important' => false,
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'price_font_style',
					'selector' 	=> '.woocommerce ul.products li.product .price .amount, .woocommerce-page ul.products li.product .price .amount',
					'important' => true,
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'sale_price_font_size',
					'selector' 	=> '.woocommerce ul.products li.product .price ins .amount, .woocommerce-page ul.products li.product .price ins .amount',
					'important' => false,
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'sale_price_font_style',
					'selector' 	=> '.woocommerce ul.products li.product .price ins .amount, .woocommerce-page ul.products li.product .price ins .amount',
					'important' => true,
				),
			) );

			/* Countdown */
			et_pb_print_module_styles_css( 'et_pb_countdown_timer', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_countdown_timer .title',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_countdown_timer .title',
				),
			) );

			/* Social */
			et_pb_print_module_styles_css( 'et_pb_social_media_follow', array(
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'button_font_style',
					'selector' 	=> '.et_pb_social_media_follow li a.follow_button',
				),
				array(
					'type' 		=> 'social-icon-size',
					'key' 		=> 'icon_size',
					'selector' 	=> '',
				),
			) );

			/* Fullwidth Slider */
			et_pb_print_module_styles_css( 'et_pb_fullwidth_slider', array(
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'header_font_size',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_description h2',
					'default' 	=> '46',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'header_font_style',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_description h2',
					'default' 	=> '',
				),
				array(
					'type' 		=> 'font-size',
					'key' 		=> 'body_font_size',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_content',
					'default' 	=> '16',
				),
				array(
					'type' 		=> 'font-style',
					'key' 		=> 'body_font_style',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_content',
					'default' 	=> '',
				),
				array(
					'type' 		=> 'padding-slider',
					'key' 		=> 'padding',
					'selector' 	=> '.et_pb_fullwidth_section .et_pb_slide_description',
					'default' 	=> '16',
				),
			) );
		?>
	</style>

	<?php
}
add_action( 'wp_head', 'et_divi_add_customizer_css' );
add_action( 'customize_controls_print_styles', 'et_divi_add_customizer_css' );

/**
 * Outputting saved customizer style settings
 *
 * @return void
 */
function et_pb_print_css( $setting ) {

	// Defaults value
	$defaults = array(
		'key'       => false,
		'selector'  => false,
		'type'      => false,
		'default'   => false,
		'important' => false
	);

	// Parse given settings aginst defaults
	$setting = wp_parse_args( $setting, $defaults );

	if (
		$setting['key']      !== false ||
		$setting['selector'] !== false ||
		$setting['type']     !== false ||
		$setting['settings'] !== false
	) {

		// Some attribute requires !important tag
		if ( $setting['important'] ) {
			$important = "!important";
		} else {
			$important = "";
		}

		// get value
		$value = et_get_option( $setting['key'], $setting['default'] );

		// Output css based on its type
		if ( $value !== false && $value != $setting['default'] ) {
			switch ( $setting['type'] ) {
				case 'font-size':
					printf( '%1$s { font-size: %2$spx %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ),
						$important );
					break;

				case 'font-size-post-header':
					$posts_font_size = intval( $value ) * ( 26 / 30 );
					printf( 'body.home-posts #left-area .et_pb_post h2, body.archive #left-area .et_pb_post h2, body.search #left-area .et_pb_post h2 { font-size:%1$spx }
						body.single .et_post_meta_wrapper h1 { font-size:%2$spx; }',
						esc_html( $posts_font_size ),
						esc_html( $value )
					);
					break;

				case 'font-style':
					printf( '%1$s { %2$s }',
						esc_html( $setting['selector'] ),
						et_pb_print_font_style( $value, $important )
					);
					break;

				case 'letter-spacing':
					printf( '%1$s { letter-spacing: %2$spx %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ),
						$important
					);
					break;

				case 'line-height':
					printf( '%1$s { line-height: %2$sem %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ),
						$important
					);
					break;

				case 'color':
					printf( '%1$s { color: %2$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'background-color':
					printf( '%1$s { background-color: %2$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'border-radius':
					printf( '%1$s { -moz-border-radius: %2$spx; -webkit-border-radius: %2$spx; border-radius: %2$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'width':
					printf( '%1$s { width: %2$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'height':
					printf( '%1$s { height: %2$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'padding':
					printf( '%1$s { padding: %2$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'padding-top-bottom':
					printf( '%1$s { padding: %2$spx 0; }',
						esc_html( $setting['selector'] ),
						esc_html( $value )
					);
					break;

				case 'padding-tabs':
					printf( '%1$s { padding: %2$spx %3$spx; }',
						esc_html( $setting['selector'] ),
						esc_html( ( intval( $value ) * 0.5 ) ),
						esc_html( $value )
					);
					break;

				case 'padding-fullwidth-slider':
					printf( '%1$s { padding: %2$s %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ) . '%',
						'0'
					);
					break;

				case 'padding-slider':
					printf( '%1$s { padding: %2$s %3$s; }',
						esc_html( $setting['selector'] ),
						esc_html( $value ) . '%',
						esc_html( ( intval( $value ) / 2 ) ) . '%'
					);
					break;

				case 'social-icon-size':
					$icon_margin 	= intval( $value ) * 0.57;
					$icon_dimension = intval( $value ) * 2;
					?>
					.et_pb_social_media_follow li a.icon{
						margin-right: <?php echo esc_html( $icon_margin ); ?>px;
						width: <?php echo esc_html( $icon_dimension ); ?>px;
						height: <?php echo esc_html( $icon_dimension ); ?>px;
					}

					.et_pb_social_media_follow li a.icon::before{
						width: <?php echo esc_html( $icon_dimension ); ?>px;
						height: <?php echo esc_html( $icon_dimension ); ?>px;
						font-size: <?php echo esc_html( $value ); ?>px;
						line-height: <?php echo esc_html( $icon_dimension ); ?>px;
					}
					<?php
					break;
			}
		}
	}
}

/**
 * Outputting saved customizer style(s) settings
 */
function et_pb_print_styles_css( $settings = array() ) {

	// $settings should be in array
	if ( is_array( $settings ) && ! empty( $settings ) ) {

		// Loop settings
		foreach ( $settings as $setting ) {

			// Print css
			et_pb_print_css( $setting );

		}
	}
}

/**
 * Outputting saved module styles settings. DRY
 *
 * @return void
 */
function et_pb_print_module_styles_css( $section = '', $settings = array() ) {

	// Verify settings
	if ( is_array( $settings ) && ! empty( $settings ) ) {

		// Loop settings
		foreach ( $settings as $setting ) {

			// settings must have these elements: key, selector, default, and type
			if ( ! isset( $setting['key'] ) ||
				! isset( $setting['selector'] ) ||
				! isset( $setting['type'] ) ) {
				continue;
			}

			// Some attributes such as shop requires !important tag
			if ( isset( $setting['important'] ) && true === $setting['important'] ) {
				$important = ' !important';
			} else {
				$important = '';
			}

			// Prepare the setting key
			$key = "{$section}-{$setting['key']}";

			// Get the value
			$value = ET_Global_Settings::get_value( $key );
			$default_value = ET_Global_Settings::get_value( $key, 'default' );

			// Output CSS based on its type
			if ( false !== $value && $default_value !== $value ) {

				switch ( $setting['type'] ) {
					case 'font-size':

						printf( "%s { font-size: %spx%s; }\n", esc_html( $setting['selector'] ), esc_html( $value ), $important );

						// Option with specific adjustment for smaller columns
						$smaller_title_sections = array(
							'et_pb_audio-title_font_size',
							'et_pb_blog-header_font_size',
							'et_pb_cta-header_font_size',
							'et_pb_contact_form-title_font_size',
							'et_pb_login-header_font_size',
							'et_pb_signup-header_font_size',
							'et_pb_slider-header_font_size',
							'et_pb_slider-body_font_size',
							'et_pb_countdown_timer-header_font_size',
						);

						if ( in_array( $key, $smaller_title_sections ) ) {

							// font size coefficient
							switch ( $key ) {
								case 'et_pb_slider-header_font_size':
									$font_size_coefficient = .565217391; // 26/46
									break;

								case 'et_pb_slider-body_font_size':
									$font_size_coefficient = .777777778; // 14/16
									break;

								default:
									$font_size_coefficient = .846153846; // 22/26
									break;
							}

							printf( '.et_pb_column_1_3 %1$s, .et_pb_column_1_4 %1$s { font-size: %2$spx%3$s; }',
								esc_html( $setting['selector'] ),
								esc_html( $value * $font_size_coefficient ),
								$important
							);
						}

						break;

					case 'font-size':
						$value = intval( $value );

						printf( ".et_pb_countdown_timer .title { font-size: %spx; }", esc_html( $value ) );
						printf( ".et_pb_column_3_8 .et_pb_countdown_timer .title, .et_pb_column_1_3 .et_pb_countdown_timer .title, .et_pb_column_1_4 .et_pb_countdown_timer .title { font-size: %spx; }", esc_html( $value * ( 18 / 22 ) ) );
						break;

					case 'font-style':
						printf( "%s { %s }\n", esc_html( $setting['selector'] ), et_pb_print_font_style( $value, $important ) );
						break;

					case 'color':
						printf( "%s { color: %s%s; }\n", esc_html( $setting['selector'] ), esc_html( $value ), $important );
						break;

					case 'background-color':
						printf( "%s { background-color: %s%s; }\n", esc_html( $setting['selector'] ), esc_html( $value ), $important );
						break;

					case 'border-radius':
						printf( "%s { -moz-border-radius: %spx; -webkit-border-radius: %spx; border-radius: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ), esc_html( $value ), esc_html( $value ) );
						break;

					case 'width':
						printf( "%s { width: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'height':
						printf( "%s { height: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'padding':
						printf( "%s { padding: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'padding-top-bottom':
						printf( "%s { padding: %spx 0; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'padding-tabs':
						$padding_tab_top_bottom 	= intval( $value ) * 0.133333333;
						$padding_tab_active_top 	= $padding_tab_top_bottom + 1;
						$padding_tab_active_bottom 	= $padding_tab_top_bottom - 1;
						$padding_tab_content 		= intval( $value ) * 0.8;

						// negative result will cause layout issue
						if ( $padding_tab_active_bottom < 0 ) {
							$padding_tab_active_bottom = 0;
						}

						printf(
							".et_pb_tabs_controls li{ padding: %spx %spx %spx; } .et_pb_tabs_controls li.et_pb_tab_active{ padding: %spx %spx; } .et_pb_all_tabs { padding: %spx %spx; }\n",
							esc_html( $padding_tab_active_top ),
							esc_html( $value ),
							esc_html( $padding_tab_active_bottom ),
							esc_html( $padding_tab_top_bottom ),
							esc_html( $value ),
							esc_html( $padding_tab_content ),
							esc_html( $value )
						);
						break;

					case 'padding-slider':
						printf( "%s { padding-top: %s; padding-bottom: %s }\n", esc_html( $setting['selector'] ), esc_html( $value ) . '%', esc_html( $value ) . '%' );

						if ( 'et_pagebuilder_slider_padding' === $key ) {
							printf( '@media only screen and ( max-width: 767px ) { %1$s { padding-top: %2$s; padding-bottom: %2$s; } }', esc_html( $setting['selector'] ), '16%' );
						}
						break;

					case 'padding-call-to-action':
						$value = intval( $value );

						printf( ".et_pb_promo { padding: %spx %spx !important; }", esc_html( $value ), esc_html( $value * ( 60 / 40 ) ) );
						printf( ".et_pb_column_1_2 .et_pb_promo, .et_pb_column_1_3 .et_pb_promo, .et_pb_column_1_4 .et_pb_promo { padding: %spx; }", esc_html( $value ) );
						break;

					case 'social-icon-size':
						$icon_margin 	= intval( $value ) * 0.57;
						$icon_dimension = intval( $value ) * 2;
						?>
						.et_pb_social_media_follow li a.icon{
							margin-right: <?php echo esc_html( $icon_margin ); ?>px;
							width: <?php echo esc_html( $icon_dimension ); ?>px;
							height: <?php echo esc_html( $icon_dimension ); ?>px;
						}

						.et_pb_social_media_follow li a.icon::before{
							width: <?php echo esc_html( $icon_dimension ); ?>px;
							height: <?php echo esc_html( $icon_dimension ); ?>px;
							font-size: <?php echo esc_html( $value ); ?>px;
							line-height: <?php echo esc_html( $icon_dimension ); ?>px;
						}

						.et_pb_social_media_follow li a.follow_button{
							font-size: <?php echo esc_html( $value ); ?>px;
						}
						<?php
						break;

					case 'border-top-style':
						printf( "%s { border-top-style: %s; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;

					case 'border-top-width':
						printf( "%s { border-top-width: %spx; }\n", esc_html( $setting['selector'] ), esc_html( $value ) );
						break;
				}
			}
		}
	}
}

/**
 * Outputting font-style attributes & values saved by ET_Divi_Font_Style_Option on customizer
 *
 * @return string
 */
function et_pb_print_font_style( $styles = '', $important = '' ) {

	// Prepare variable
	$font_styles = "";

	if ( '' !== $styles && false !== $styles ) {
		// Convert string into array
		$styles_array = explode( '|', $styles );

		// If $important is in use, give it a space
		if ( $important && '' !== $important ) {
			$important = " " . $important;
		}

		// Use in_array to find values in strings. Otherwise, display default text

		// Font weight
		if ( in_array( 'bold', $styles_array ) ) {
			$font_styles .= "font-weight: bold{$important}; ";
		} else {
			$font_styles .= "font-weight: normal{$important}; ";
		}

		// Font style
		if ( in_array( 'italic', $styles_array ) ) {
			$font_styles .= "font-style: italic{$important}; ";
		} else {
			$font_styles .= "font-style: normal{$important}; ";
		}

		// Text-transform
		if ( in_array( 'uppercase', $styles_array ) ) {
			$font_styles .= "text-transform: uppercase{$important}; ";
		} else {
			$font_styles .= "text-transform: none{$important}; ";
		}

		// Text-decoration
		if ( in_array( 'underline', $styles_array ) ) {
			$font_styles .= "text-decoration: underline{$important}; ";
		} else {
			$font_styles .= "text-decoration: none{$important}; ";
		}
	}

	return esc_html( $font_styles );
}

/*
 * Adds color scheme class to the body tag
 */
function et_customizer_color_scheme_class( $body_class ) {
	$color_scheme        = et_get_option( 'color_schemes', 'none' );
	$color_scheme_prefix = 'et_color_scheme_';

	if ( 'none' !== $color_scheme ) $body_class[] = $color_scheme_prefix . $color_scheme;

	return $body_class;
}
add_filter( 'body_class', 'et_customizer_color_scheme_class' );

/*
 * Adds button class to the body tag
 */
function et_customizer_button_class( $body_class ) {
	$button_icon_placement = et_get_option( 'all_buttons_icon_placement', 'right' );
	$button_icon_on_hover = et_get_option( 'all_buttons_icon_hover', 'yes' );
	$button_use_icon = et_get_option( 'all_buttons_icon', 'yes' );
	$button_icon = et_get_option( 'all_buttons_selected_icon', '5' );

	if ( 'left' === $button_icon_placement ) {
		$body_class[] = 'et_button_left';
	}

	if ( 'no' === $button_icon_on_hover ) {
		$body_class[] = 'et_button_icon_visible';
	}

	if ( 'no' === $button_use_icon ) {
		$body_class[] = 'et_button_no_icon';
	}

	if ( '5' !== $button_icon ) {
		$body_class[] = 'et_button_custom_icon';
	}

	$body_class[] = 'et_pb_button_helper_class';

	return $body_class;
}
add_filter( 'body_class', 'et_customizer_button_class' );

function et_load_google_fonts_scripts() {
	$theme_version = et_get_theme_version();

	wp_enqueue_script( 'et_google_fonts', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.js', array( 'jquery' ), $theme_version, true );
}
add_action( 'customize_controls_print_footer_scripts', 'et_load_google_fonts_scripts' );

function et_load_google_fonts_styles() {
	$theme_version = et_get_theme_version();

	wp_enqueue_style( 'et_google_fonts_style', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.css', array(), $theme_version );
}
add_action( 'customize_controls_print_styles', 'et_load_google_fonts_styles' );

if ( ! function_exists( 'et_divi_post_meta' ) ) :
function et_divi_post_meta() {
	$postinfo = is_single() ? et_get_option( 'divi_postinfo2' ) : et_get_option( 'divi_postinfo1' );

	if ( $postinfo ) :
		echo '<p class="post-meta">';
		echo et_pb_postinfo_meta( $postinfo, et_get_option( 'divi_date_format', 'M j, Y' ), esc_html__( '0 comments', 'Divi' ), esc_html__( '1 comment', 'Divi' ), '% ' . esc_html__( 'comments', 'Divi' ) );
		echo '</p>';
	endif;
}
endif;

/**
 * Extract and return the first blockquote from content.
 */
if ( ! function_exists( 'et_get_blockquote_in_content' ) ) :
function et_get_blockquote_in_content() {
	global $more;
	$more_default = $more;
	$more = 1;

	remove_filter( 'the_content', 'et_remove_blockquote_from_content' );

	$content = apply_filters( 'the_content', get_the_content() );

	add_filter( 'the_content', 'et_remove_blockquote_from_content' );

	$more = $more_default;

	if ( preg_match( '/<blockquote>(.+?)<\/blockquote>/is', $content, $matches ) ) {
		return $matches[0];
	} else {
		return false;
	}
}
endif;

function et_remove_blockquote_from_content( $content ) {
	if ( 'quote' !== et_pb_post_format() ) {
		return $content;
	}

	$content = preg_replace( '/<blockquote>(.+?)<\/blockquote>/is', '', $content, 1 );

	return $content;
}
add_filter( 'the_content', 'et_remove_blockquote_from_content' );

if ( ! function_exists( 'et_get_link_url' ) ) :
function et_get_link_url() {
	if ( '' !== ( $link_url = get_post_meta( get_the_ID(), '_format_link_url', true ) ) ) {
		return $link_url;
	}

	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}
endif;

function et_video_embed_html( $video ) {
	if ( is_single() && 'video' === et_pb_post_format() ) {
		static $post_video_num = 0;

		$post_video_num++;

		// Hide first video in the post content on single video post page
		if ( 1 === $post_video_num ) {
			return '';
		}
	}

	return "<div class='et_post_video'>{$video}</div>";
}
add_filter( 'embed_oembed_html', 'et_video_embed_html' );

/**
 * Removes galleries on single gallery posts, since we display images from all
 * galleries on top of the page
 */
function et_delete_post_gallery( $content ) {
	if ( is_single() && is_main_query() && has_post_format( 'gallery' ) ) :
		$regex = get_shortcode_regex();
		preg_match_all( "/{$regex}/s", $content, $matches );

		// $matches[2] holds an array of shortcodes names in the post
		foreach ( $matches[2] as $key => $shortcode_match ) {
			if ( 'gallery' === $shortcode_match )
				$content = str_replace( $matches[0][$key], '', $content );
		}
	endif;

	return $content;
}
add_filter( 'the_content', 'et_delete_post_gallery' );

/*
 * Removes the first video shortcode from content on single pages since it is displayed
 * at the top of the page. This will also remove the video shortcode url from archive pages content
 */
function et_delete_post_video( $content ) {
	if ( has_post_format( 'video' ) ) :
		$regex = get_shortcode_regex();
		preg_match_all( "/{$regex}/s", $content, $matches );

		// $matches[2] holds an array of shortcodes names in the post
		foreach ( $matches[2] as $key => $shortcode_match ) {
			if ( 'video' === $shortcode_match ) {
				$content = str_replace( $matches[0][$key], '', $content );
				if ( is_single() && is_main_query() ) {
					break;
				}
			}
		}
	endif;

	return $content;
}

if ( ! function_exists( 'et_gallery_images' ) ) :
function et_gallery_images() {
	$output = $images_ids = '';

	if ( function_exists( 'get_post_galleries' ) ) {
		$galleries = get_post_galleries( get_the_ID(), false );

		if ( empty( $galleries ) ) return false;

		foreach ( $galleries as $gallery ) {
			if ( isset( $gallery['ids'] ) ) {
				// Grabs all attachments ids from one or multiple galleries in the post
				$images_ids .= ( '' !== $images_ids ? ',' : '' ) . $gallery['ids'];
			} else {
				$image_ids = false;

				// If user doesn't define ids of images on galleries, get attached media
				$attached_media = get_attached_media( 'image', get_the_id() );
			}
		}

		if ( $images_ids ) {
			$attachments_ids = explode( ',', $images_ids );
		} elseif ( isset( $attached_media ) && is_array( $attached_media ) && ! empty( $attached_media ) ) {
			$attachments_ids = wp_list_pluck( $attached_media, 'ID' );
		} else {
			$attachments_ids = false;
		}

		if ( ! $attachments_ids ) {
			// Print no gallery found message
			?>
			<div class="et_pb_module et_pb_slider et_pb_slider_fullwidth_off et_pb_bg_layout_light gallery-not-found">
				<div class="et_pb_slides">
					<div class="et_pb_slide et_pb_bg_layout_light et_pb_media_alignment_center et-pb-active-slide" style="background-color:#ffffff;">
						<div class="et_pb_container clearfix">
							<div class="et_pb_slide_description">
								<h2><?php _e( 'No Gallery Found', 'Divi' ); ?></h2>
								<div class="et_pb_slide_content">
									<p><?php _e( 'No items found', 'Divi' ); ?></p>
								</div>
							</div> <!-- .et_pb_slide_description -->
						</div> <!-- .et_pb_container -->
					</div> <!-- .et_pb_slide -->
				</div> <!-- .et_pb_slides -->
			</div>
			<?php
			return;
		}

		// Removes duplicate attachments ids
		$attachments_ids = array_unique( $attachments_ids );
	} else {
		$pattern = get_shortcode_regex();
		preg_match( "/$pattern/s", get_the_content(), $match );
		$atts = shortcode_parse_atts( $match[3] );

		if ( isset( $atts['ids'] ) )
			$attachments_ids = explode( ',', $atts['ids'] );
		else
			return false;
	}

	$slides = '';

	foreach ( $attachments_ids as $attachment_id ) {
		$attachment_attributes = wp_get_attachment_image_src( $attachment_id, 'et-pb-post-main-image-fullwidth' );
		$attachment_image = ! is_single() ? $attachment_attributes[0] : wp_get_attachment_image( $attachment_id, 'et-pb-portfolio-image' );

		if ( ! is_single() ) {
			$slides .= sprintf(
				'<div class="et_pb_slide" style="background: url(%1$s);"></div>',
				esc_attr( $attachment_image )
			);
		} else {
			$full_image = wp_get_attachment_image_src( $attachment_id, 'full' );
			$full_image_url = $full_image[0];
			$attachment = get_post( $attachment_id );

			$slides .= sprintf(
				'<li class="et_gallery_item">
					<a href="%1$s" title="%3$s">
						<span class="et_portfolio_image">
							%2$s
							<span class="et_overlay"></span>
						</span>
					</a>
				</li>',
				esc_url( $full_image_url ),
				$attachment_image,
				esc_attr( $attachment->post_title )
			);
		}
	}

	if ( ! is_single() ) {
		$output =
			'<div class="et_pb_slider et_pb_slider_fullwidth_off et_pb_gallery_post_type">
				<div class="et_pb_slides">
					%1$s
				</div>
			</div>';
	} else {
		$output =
			'<ul class="et_post_gallery clearfix">
				%1$s
			</ul>';
	}

	printf( $output, $slides );
}
endif;

if ( ! function_exists( 'et_get_first_video' ) ) :
function et_get_first_video() {
	$first_video  = '';
	$custom_fields = get_post_custom();
	$video_width  = (int) apply_filters( 'et_blog_video_width', 1080 );
	$video_height = (int) apply_filters( 'et_blog_video_height', 630 );

	foreach ( $custom_fields as $key => $custom_field ) {
		if ( 0 !== strpos( $key, '_oembed_' ) ) {
			continue;
		}

		$first_video = $custom_field[0];

		$first_video = preg_replace( '/<embed /', '<embed wmode="transparent" ', $first_video );
		$first_video = preg_replace( '/<\/object>/','<param name="wmode" value="transparent" /></object>', $first_video );

		$first_video = preg_replace( "/width=\"[0-9]*\"/", "width={$video_width}", $first_video );
		$first_video = preg_replace( "/height=\"[0-9]*\"/", "height={$video_height}", $first_video );

		break;
	}

	if ( '' === $first_video && has_shortcode( get_the_content(), 'video' )  ) {
		$regex = get_shortcode_regex();
		preg_match( "/{$regex}/s", get_the_content(), $match );

		$first_video = preg_replace( "/width=\"[0-9]*\"/", "width=\"{$video_width}\"", $match[0] );
		$first_video = preg_replace( "/height=\"[0-9]*\"/", "height=\"{$video_height}\"", $first_video );

		add_filter( 'the_content', 'et_delete_post_video' );

		$first_video = do_shortcode( et_pb_fix_shortcodes( $first_video ) );
	}

	return ( '' !== $first_video ) ? $first_video : false;
}
endif;

function et_divi_post_admin_scripts_styles( $hook ) {
	global $typenow;

	$theme_version = et_get_theme_version();

	if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) return;

	if ( ! isset( $typenow ) ) return;

	if ( in_array( $typenow, array( 'post' ) ) ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'et-admin-post-script', get_template_directory_uri() . '/js/admin_post_settings.js', array( 'jquery' ), $theme_version );
	}
}
add_action( 'admin_enqueue_scripts', 'et_divi_post_admin_scripts_styles' );

function et_password_form() {
	$pwbox_id = rand();

	$form_output = sprintf(
		'<div class="et_password_protected_form">
			<h1>%1$s</h1>
			<p>%2$s:</p>
			<form action="%3$s" method="post">
				<p><label for="%4$s">%5$s: </label><input name="post_password" id="%4$s" type="password" size="20" maxlength="20" /></p>
				<p><button type="submit" class="et_submit_button et_pb_button">%6$s</button></p>
			</form
		</div>',
		esc_html__( 'Password Protected', 'Divi' ),
		esc_html__( 'To view this protected post, enter the password below', 'Divi' ),
		esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ),
		esc_attr( 'pwbox-' . $pwbox_id ),
		esc_html__( 'Password', 'Divi' ),
		esc_html__( 'Submit', 'Divi' )
	);

	$output = sprintf(
		'<div class="et_pb_section et_section_regular">
			<div class="et_pb_row">
				<div class="et_pb_column et_pb_column_4_4">
					%1$s
				</div>
			</div>
		</div>',
		$form_output
	);

	return $output;
}
add_filter( 'the_password_form', 'et_password_form' );

function et_add_wp_version( $classes ) {
	global $wp_version;

	// add 'et-wp-pre-3_8' class if the current WordPress version is less than 3.8
	if ( version_compare( $wp_version, '3.7.2', '<=' ) ) {
		if ( 'body_class' === current_filter() )
			$classes[] = 'et-wp-pre-3_8';
		else
			$classes = 'et-wp-pre-3_8';
	} else {
		if ( 'admin_body_class' === current_filter() )
			$classes = 'et-wp-after-3_8';
	}

	return $classes;
}
add_filter( 'body_class', 'et_add_wp_version' );
add_filter( 'admin_body_class', 'et_add_wp_version' );

function et_layout_body_class( $classes ) {
	if ( 'rgba' == substr( et_get_option( 'primary_nav_bg', '#ffffff' ), 0, 4 ) && false === et_get_option( 'vertical_nav', false ) ) {
		$classes[] = 'et_transparent_nav';
	}

	// home-posts class is used by customizer > blog to work. It modifies post title and meta
	// of WP default layout (home, archive, single), but should not modify post title and meta of blog module (page as home)
	if ( in_array( 'home', $classes ) && ! in_array( 'page', $classes ) ) {
		$classes[] = 'home-posts';
	}

	if ( true === et_get_option( 'nav_fullwidth', false ) ) {
		if ( true === et_get_option( 'vertical_nav', false ) ) {
			$classes[] = 'et_fullwidth_nav_temp';
		} else {
			$classes[] = 'et_fullwidth_nav';
		}
	}

	if ( true === et_get_option( 'secondary_nav_fullwidth', false ) ) {
		$classes[] = 'et_fullwidth_secondary_nav';
	}

	if ( true === et_get_option( 'vertical_nav', false ) ) {
		$classes[] = 'et_vertical_nav';
	} else if ( 'on' === et_get_option( 'divi_fixed_nav', 'on' ) ) {
		$classes[] = 'et_fixed_nav';
	}

	if ( true === et_get_option( 'vertical_nav', false ) && 'on' === et_get_option( 'divi_fixed_nav', 'on' ) ) {
		$classes[] = 'et_vertical_fixed';
	}

	if ( true === et_get_option( 'boxed_layout', false ) ) {
		$classes[] = 'et_boxed_layout';
	}

	if ( true === et_get_option( 'hide_nav', false ) ) {
		$classes[] = 'et_hide_nav';
	} else {
		$classes[] = 'et_show_nav';
	}

	if ( true === et_get_option( 'hide_primary_logo', false ) ) {
		$classes[] = 'et_hide_primary_logo';
	}

	if ( true === et_get_option( 'hide_fixed_logo', false ) ) {
		$classes[] = 'et_hide_fixed_logo';
	}

	if ( true === et_get_option( 'hide_mobile_logo', false ) ) {
		$classes[] = 'et_hide_mobile_logo';
	}

	if ( true === et_get_option( 'cover_background', true ) ) {
		$classes[] = 'et_cover_background';
	}

	$et_secondary_nav_items = et_divi_get_top_nav_items();

	if ( $et_secondary_nav_items->top_info_defined ) {
		$classes[] = 'et_secondary_nav_enabled';
	}

	if ( $et_secondary_nav_items->two_info_panels ) {
		$classes[] = 'et_secondary_nav_two_panels';
	}

	if ( $et_secondary_nav_items->secondary_nav && ! ( $et_secondary_nav_items->contact_info_defined || $et_secondary_nav_items->show_header_social_icons ) ) {
		$classes[] = 'et_secondary_nav_only_menu';
	}

	if ( 'on' === get_post_meta( get_the_ID(), '_et_pb_side_nav', true ) && et_pb_is_pagebuilder_used( get_the_ID() ) ) {
		$classes[] = 'et_pb_side_nav_page';
	}

	if ( true === et_get_option( 'et_pb_sidebar-remove_border', false ) ) {
		$classes[] = 'et_pb_no_sidebar_vertical_divider';
	}

	if ( is_singular( array( 'post', 'page', 'project', 'product' ) ) && 'on' == get_post_meta( get_the_ID(), '_et_pb_post_hide_nav', true ) ) {
		$classes[] = 'et_hide_nav';
	}

	if ( ! et_get_option( 'use_sidebar_width', false ) ) {
		$classes[] = 'et_pb_gutter';
	}

	if ( stristr( $_SERVER['HTTP_USER_AGENT'],"mac") ) {
		$classes[] = 'osx';
	} elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"linux") ) {
		$classes[] = 'linux';
	} elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"windows") ) {
		$classes[] = 'windows';
	}

	$gutter_width = et_get_option( 'gutter_width', '3' );
	$classes[] = esc_attr( "et_pb_gutters{$gutter_width}" );

	$primary_dropdown_animation = et_get_option( 'primary_nav_dropdown_animation', 'fade' );
	$classes[] = esc_attr( "et_primary_nav_dropdown_animation_{$primary_dropdown_animation}" );

	$secondary_dropdown_animation = et_get_option( 'secondary_nav_dropdown_animation', 'fade' );
	$classes[] = esc_attr( "et_secondary_nav_dropdown_animation_{$secondary_dropdown_animation}" );

	$footer_columns = et_get_option( 'footer_columns', '4' );
	$classes[] = esc_attr( "et_pb_footer_columns{$footer_columns}" );

	$header_style = et_get_option( 'header_style', 'left' );
	$classes[] = esc_attr( "et_header_style_{$header_style}" );

	return $classes;
}
add_filter( 'body_class', 'et_layout_body_class' );

if ( ! function_exists( 'et_show_cart_total' ) ) {
	function et_show_cart_total( $args = array() ) {
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$defaults = array(
			'no_text' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		printf(
			'<a href="%1$s" class="et-cart-info">
				<span>%2$s</span>
			</a>',
			esc_url( WC()->cart->get_cart_url() ),
			( ! $args['no_text']
				? sprintf(
				__( '%1$s %2$s', 'Divi' ),
				esc_html( WC()->cart->get_cart_contents_count() ),
				( 1 === WC()->cart->get_cart_contents_count() ? __( 'Item', 'Divi' ) : __( 'Items', 'Divi' ) )
				)
				: ''
			)
		);
	}
}

if ( ! function_exists( 'et_divi_get_top_nav_items' ) ) {
	function et_divi_get_top_nav_items() {
		$items = new stdClass;

		$items->phone_number = et_get_option( 'phone_number' );

		$items->email = et_get_option( 'header_email' );

		$items->contact_info_defined = $items->phone_number || $items->email;

		$items->show_header_social_icons = et_get_option( 'show_header_social_icons', false );

		$items->secondary_nav = wp_nav_menu( array(
			'theme_location' => 'secondary-menu',
			'container'      => '',
			'fallback_cb'    => '',
			'menu_id'        => 'et-secondary-nav',
			'echo'           => false,
		) );

		$items->top_info_defined = $items->contact_info_defined || $items->show_header_social_icons || $items->secondary_nav;

		$items->two_info_panels = $items->contact_info_defined && ( $items->show_header_social_icons || $items->secondary_nav );

		return $items;
	}
}

function et_divi_activate_features(){
	/* activate shortcodes */
	require_once( get_template_directory() . '/epanel/shortcodes/shortcodes.php' );
}
add_action( 'init', 'et_divi_activate_features' );

require_once( get_template_directory() . '/et-pagebuilder/et-pagebuilder.php' );

function et_divi_sidebar_class( $classes ) {
	if ( et_pb_is_pagebuilder_used( get_the_ID() ) )
		$classes[] = 'et_pb_pagebuilder_layout';

	if ( is_single() || is_page() || ( class_exists( 'woocommerce' ) && is_product() ) )
		$page_layout = '' !== ( $layout = get_post_meta( get_the_ID(), '_et_pb_page_layout', true ) )
			? $layout
			: 'et_right_sidebar';

	if ( class_exists( 'woocommerce' ) && ( is_shop() || is_product() || is_product_category() || is_product_tag() ) ) {
		if ( is_shop() || is_tax() )
			$classes[] = et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' );
		if ( is_product() )
			$classes[] = $page_layout;
	}

	else if ( is_archive() || is_home() || is_search() || is_404() ) {
		$classes[] = 'et_right_sidebar';
	}

	else if ( is_singular( 'project' ) ) {
		if ( 'et_full_width_page' === $page_layout )
			$page_layout = 'et_right_sidebar et_full_width_portfolio_page';

		$classes[] = $page_layout;
	}

	else if ( is_single() || is_page() ) {
		$classes[] = $page_layout;
	}

	return $classes;
}
add_filter( 'body_class', 'et_divi_sidebar_class' );

function et_modify_shop_page_columns_num( $columns_num ) {
	if ( class_exists( 'woocommerce' ) && is_shop() ) {
		$columns_num = 'et_full_width_page' !== et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' )
			? 3
			: 4;
	}

	return $columns_num;
}
add_filter( 'loop_shop_columns', 'et_modify_shop_page_columns_num' );

// WooCommerce

global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' )
	add_action( 'init', 'et_divi_woocommerce_image_dimensions', 1 );

/**
 * Default values for WooCommerce images changed in version 1.3
 * Checks if WooCommerce image dimensions have been updated already.
 */
function et_divi_check_woocommerce_images() {
	if ( 'checked' === et_get_option( 'divi_1_3_images' ) ) return;

	et_divi_woocommerce_image_dimensions();
	et_update_option( 'divi_1_3_images', 'checked' );
}
add_action( 'admin_init', 'et_divi_check_woocommerce_images' );

function et_divi_woocommerce_image_dimensions() {
	$catalog = array(
		'width' 	=> '400',
		'height'	=> '400',
		'crop'		=> 1,
	);

	$single = array(
		'width' 	=> '510',
		'height'	=> '9999',
		'crop'		=> 0,
	);

	$thumbnail = array(
		'width' 	=> '157',
		'height'	=> '157',
		'crop'		=> 1,
	);

	update_option( 'shop_catalog_image_size', $catalog );
	update_option( 'shop_single_image_size', $single );
	update_option( 'shop_thumbnail_image_size', $thumbnail );
}

function woocommerce_template_loop_product_thumbnail() {
	printf( '<span class="et_shop_image">%1$s<span class="et_overlay"></span></span>',
		woocommerce_get_product_thumbnail()
	);
}

function et_review_gravatar_size( $size ) {
	return '80';
}
add_filter( 'woocommerce_review_gravatar_size', 'et_review_gravatar_size' );


function et_divi_output_content_wrapper() {
	echo '
		<div id="main-content">
			<div class="container">
				<div id="content-area" class="clearfix">
					<div id="left-area">';
}

function et_divi_output_content_wrapper_end() {
	echo '</div> <!-- #left-area -->';

	if (
		( is_product() && 'et_full_width_page' !== get_post_meta( get_the_ID(), '_et_pb_page_layout', true ) )
		||
		( ( is_shop() || is_product_category() || is_product_tag() ) && 'et_full_width_page' !== et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' ) )
	) {
		woocommerce_get_sidebar();
	}

	echo '
				</div> <!-- #content-area -->
			</div> <!-- .container -->
		</div> <!-- #main-content -->';
}

function et_aweber_authorization_option() {
	$theme_version = et_get_theme_version();

	wp_enqueue_script( 'divi-advanced-options', get_template_directory_uri() . '/js/advanced_options.js', array( 'jquery' ), $theme_version, true );
	wp_localize_script( 'divi-advanced-options', 'et_advanced_options', array(
		'et_load_nonce'            => wp_create_nonce( 'et_load_nonce' ),
		'aweber_connecting'        => __( 'Connecting...', 'Divi' ),
		'aweber_failed'            => __( 'Connection failed', 'Divi' ),
		'aweber_remove_connection' => __( 'Removing connection...', 'Divi' ),
		'aweber_done'              => __( 'Done', 'Divi' ),
	) );
	wp_enqueue_style( 'divi-advanced-options', get_template_directory_uri() . '/css/advanced_options.css', array(), $theme_version );

	$app_id = 'b17f3351';

	$aweber_auth_endpoint = 'https://auth.aweber.com/1.0/oauth/authorize_app/' . $app_id;

	$hide_style = ' style="display: none;"';

	$aweber_connection_established = et_get_option( 'divi_aweber_consumer_key', false ) && et_get_option( 'divi_aweber_consumer_secret', false ) && et_get_option( 'divi_aweber_access_key', false ) && et_get_option( 'divi_aweber_access_secret', false );

	$output = sprintf(
		'<div id="et_aweber_connection">
			<ul id="et_aweber_authorization"%4$s>
				<li>%1$s</li>
				<li>
					<p>%2$s</p>
					<p><textarea id="et_aweber_authentication_code" name="et_aweber_authentication_code"></textarea></p>

					<p><button class="et_make_connection button button-primary button-large">%3$s</button></p>
				</li>
			</ul>

			<div id="et_aweber_remove_connection"%5$s>
				<p>%6$s</p>
				<p><button class="et_remove_connection button button-primary button-large">%7$s</button></p>
			</div>
		</div>',
		sprintf( __( 'Step 1: <a href="%1$s" target="_blank">Generate authorization code</a>', 'Divi' ), esc_url( $aweber_auth_endpoint ) ),
		__( 'Step 2: Paste in the authorization code and click "Make a connection" button: ', 'Divi' ),
		__( 'Make a connection', 'Divi' ),
		( $aweber_connection_established ? $hide_style : ''  ),
		( ! $aweber_connection_established ? $hide_style : ''  ),
		__( 'Aweber is set up properly. You can remove connection here if you wish.', 'Divi' ),
		__( 'Remove the connection', 'Divi' )
	);

	echo $output;
}

function et_aweber_submit_authorization_code() {
	if ( ! wp_verify_nonce( $_POST['et_load_nonce'], 'et_load_nonce' ) ) {
		die( __( 'Nonce failed.', 'Divi' ) );
	}

	$et_authorization_code = $_POST['et_authorization_code'];

	if ( '' === $et_authorization_code ) {
		die( __( 'Authorization code is empty.', 'Divi' ) );
	}

	if ( ! class_exists( 'AWeberAPI' ) ) {
		require_once( get_template_directory() . '/includes/subscription/aweber/aweber_api.php' );
	}

	try {
		$auth = AWeberAPI::getDataFromAweberID( $et_authorization_code );

		if ( ! ( is_array( $auth ) && 4 === count( $auth ) ) ) {
			die ( __( 'Authorization code is invalid. Try regenerating it and paste in the new code.', 'Divi' ) );
		}

		list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = $auth;

		et_update_option( 'divi_aweber_consumer_key', $consumer_key );
		et_update_option( 'divi_aweber_consumer_secret', $consumer_secret );
		et_update_option( 'divi_aweber_access_key', $access_key );
		et_update_option( 'divi_aweber_access_secret', $access_secret );

		die( 'success' );
	} catch ( AWeberAPIException $exc ) {
		printf(
			'<p>%4$s.</p>
			<ul>
				<li>%5$s: %1$s</li>
				<li>%6$s: %2$s</li>
				<li>%7$s: %3$s</li>
			</ul>',
			esc_html( $exc->type ),
			esc_html( $exc->message ),
			esc_html( $exc->documentation_url ),
			esc_html__( 'Aweber API Exception', 'Divi' ),
			esc_html__( 'Type', 'Divi' ),
			esc_html__( 'Message', 'Divi' ),
			esc_html__( 'Documentation', 'Divi' )
		);
	}

	die();
}
add_action( 'wp_ajax_et_aweber_submit_authorization_code', 'et_aweber_submit_authorization_code' );

function et_aweber_remove_connection() {
	if ( ! wp_verify_nonce( $_POST['et_load_nonce'], 'et_load_nonce' ) ) {
		die( __( 'Nonce failed', 'Divi' ) );
	}

	et_delete_option( 'divi_aweber_consumer_key' );
	et_delete_option( 'divi_aweber_consumer_secret' );
	et_delete_option( 'divi_aweber_access_key' );
	et_delete_option( 'divi_aweber_access_secret' );

	die( 'success' );
}
add_action( 'wp_ajax_et_aweber_remove_connection', 'et_aweber_remove_connection' );

if ( ! function_exists( 'et_pb_get_audio_player' ) ) {
	function et_pb_get_audio_player(){
		$output = sprintf(
			'<div class="et_audio_container">
				%1$s
			</div> <!-- .et_audio_container -->',
			do_shortcode( '[audio]' )
		);

		return $output;
	}
}

if ( ! function_exists( 'et_divi_get_post_text_color' ) ) {
	function et_divi_get_post_text_color() {
		$text_color_class = '';

		$post_format = et_pb_post_format();

		if ( in_array( $post_format, array( 'audio', 'link', 'quote' ) ) ) {
			$text_color_class = ( $text_color = get_post_meta( get_the_ID(), '_et_post_bg_layout', true ) ) ? $text_color : 'light';
			$text_color_class = ' et_pb_text_color_' . $text_color_class;
		}

		return $text_color_class;
	}
}

if ( ! function_exists( 'et_divi_get_post_bg_inline_style' ) ) {
	function et_divi_get_post_bg_inline_style() {
		$inline_style = '';

		$post_id = get_the_ID();

		$post_use_bg_color = get_post_meta( $post_id, '_et_post_use_bg_color', true )
			? true
			: false;
		$post_bg_color  = ( $bg_color = get_post_meta( $post_id, '_et_post_bg_color', true ) ) && '' !== $bg_color
			? $bg_color
			: '#ffffff';

		if ( $post_use_bg_color ) {
			$inline_style = sprintf( ' style="background-color: %1$s;"', esc_html( $post_bg_color ) );
		}

		return $inline_style;
	}
}

/*
 * Displays post audio, quote and link post formats content
 */
if ( ! function_exists( 'et_divi_post_format_content' ) ) {
	function et_divi_post_format_content(){
		$post_format = et_pb_post_format();

		$text_color_class = et_divi_get_post_text_color();

		$inline_style = et_divi_get_post_bg_inline_style();

		switch ( $post_format ) {
			case 'audio' :
				printf(
					'<div class="et_audio_content%4$s"%5$s>
						<h2><a href="%3$s">%1$s</a></h2>
						%2$s
					</div> <!-- .et_audio_content -->',
					get_the_title(),
					et_pb_get_audio_player(),
					esc_url( get_permalink() ),
					esc_attr( $text_color_class ),
					$inline_style
				);

				break;
			case 'quote' :
				printf(
					'<div class="et_quote_content%4$s"%5$s>
						%1$s
						<a href="%2$s" class="et_quote_main_link">%3$s</a>
					</div> <!-- .et_quote_content -->',
					et_get_blockquote_in_content(),
					esc_url( get_permalink() ),
					__( 'Read more', 'Divi' ),
					esc_attr( $text_color_class ),
					$inline_style
				);

				break;
			case 'link' :
				printf(
					'<div class="et_link_content%5$s"%6$s>
						<h2><a href="%2$s">%1$s</a></h2>
						<a href="%3$s" class="et_link_main_url">%4$s</a>
					</div> <!-- .et_link_content -->',
					get_the_title(),
					esc_url( get_permalink() ),
					esc_url( et_get_link_url() ),
					esc_html( et_get_link_url() ),
					esc_attr( $text_color_class ),
					$inline_style
				);

				break;
		}
	}
}

if ( ! function_exists( 'et_pb_get_mailchimp_lists' ) ) :
function et_pb_get_mailchimp_lists() {
	$lists = array();

	$mailchimp_api_key = et_get_option( 'divi_mailchimp_api_key' );
	if ( empty( $mailchimp_api_key ) ) {
		return false;
	}

	if ( 'on' === et_get_option( 'divi_regenerate_mailchimp_lists', 'false' ) || false === ( $et_pb_mailchimp_lists = get_transient( 'et_pb_mailchimp_lists' ) ) ) {
		if ( ! class_exists( 'MailChimp' ) ) {
			require_once( get_template_directory() . '/includes/subscription/mailchimp/mailchimp.php' );
		}

		try {
			$mailchimp = new MailChimp( $mailchimp_api_key );
			$retval = $mailchimp->call('lists/list');
			if ( ! empty( $retval['data'] ) ) {
				foreach ( $retval['data'] as $list ) {
					$lists[$list['id']] = $list['name'];
				}
			}

			set_transient( 'et_pb_mailchimp_lists', $lists, 60*60*24 );
		} catch ( Exception $exc ) {
			$lists = $et_pb_mailchimp_lists;
		}

		return $lists;
	}
}
endif;

if ( ! function_exists( 'et_pb_get_aweber_account' ) ) :
function et_pb_get_aweber_account() {
	if ( ! class_exists( 'AWeberAPI' ) ) {
		require_once( get_template_directory() . '/includes/subscription/aweber/aweber_api.php' );
	}

	$consumer_key = et_get_option( 'divi_aweber_consumer_key' );
	$consumer_secret = et_get_option( 'divi_aweber_consumer_secret' );
	$access_key = et_get_option( 'divi_aweber_access_key' );
	$access_secret = et_get_option( 'divi_aweber_access_secret' );

	if ( ! empty( $consumer_key ) && ! empty( $consumer_secret ) && ! empty( $access_key ) && ! empty( $access_secret ) ) {
		try {
			// Aweber requires curl extension to be enabled
			if ( ! function_exists( 'curl_init' ) ) {
				return false;
			}

			$aweber = new AWeberAPI( $consumer_key, $consumer_secret );

			if ( ! $aweber ) {
				return false;
			}

			$account = $aweber->getAccount( $access_key, $access_secret );
		} catch ( Exception $exc ) {
			return false;
		}
	} else {
		return false;
	}

	return $account;
}
endif;

if ( ! function_exists( 'et_pb_get_aweber_lists' ) ) :
function et_pb_get_aweber_lists() {
	$lists = array();

	$account = et_pb_get_aweber_account();

	if ( ! $account ) {
		return false;
	}

	if ( 'on' === et_get_option( 'divi_regenerate_aweber_lists', 'false' ) || false === ( $et_pb_aweber_lists = get_transient( 'et_pb_aweber_lists' ) ) ) {

		if ( ! class_exists( 'AWeberAPI' ) ) {
			require_once( get_template_directory() . '/includes/subscription/aweber/aweber_api.php' );
		}

		$aweber_lists = $account->lists;

		if ( isset( $aweber_lists ) ) {
			foreach ( $aweber_lists as $list ) {
				$lists[$list->id] = $list->name;
			}
		}

		set_transient( 'et_pb_aweber_lists', $lists, 60*60*24 );
	} else {
		$lists = $et_pb_aweber_lists;
	}

	return $lists;
}
endif;

function et_pb_submit_subscribe_form() {
	if ( ! wp_verify_nonce( $_POST['et_load_nonce'], 'et_load_nonce' ) ) die( json_encode( array( 'error' => __( 'Configuration error', 'Divi' ) ) ) );

	$service = sanitize_text_field( $_POST['et_service'] );

	$list_id = sanitize_text_field( $_POST['et_list_id'] );

	$email = sanitize_email( $_POST['et_email'] );

	$firstname = sanitize_text_field( $_POST['et_firstname'] );

	if ( '' === $firstname ) die( json_encode( array( 'error' => __( 'Please enter first name', 'Divi' ) ) ) );

	if ( ! is_email( sanitize_email( $_POST['et_email'] ) ) ) die( json_encode( array( 'error' => __( 'Incorrect email', 'Divi' ) ) ) );

	if ( '' == $list_id ) die( json_encode( array( 'error' => __( 'Configuration error: List is not defined', 'Divi' ) ) ) );

	$success_message = __( '<h2 class="et_pb_subscribed">Subscribed - look for the confirmation email!</h2>', 'Divi' );

	switch ( $service ) {
		case 'mailchimp' :
			$lastname = sanitize_text_field( $_POST['et_lastname'] );
			$email = array( 'email' => $email );

			if ( ! class_exists( 'MailChimp' ) )
				require_once( get_template_directory() . '/includes/subscription/mailchimp/mailchimp.php' );

			$mailchimp_api_key = et_get_option( 'divi_mailchimp_api_key' );

			if ( '' === $mailchimp_api_key ) die( json_encode( array( 'error' => __( 'Configuration error: api key is not defined', 'Divi' ) ) ) );


				$mailchimp = new MailChimp( $mailchimp_api_key );

				$merge_vars = array(
					'FNAME' => $firstname,
					'LNAME' => $lastname,
				);

				$retval =  $mailchimp->call('lists/subscribe', array(
					'id'         => $list_id,
					'email'      => $email,
					'merge_vars' => $merge_vars,
				));

				if ( isset($retval['error']) ) {
					if ( '214' == $retval['code'] ) {
						$error_message = str_replace( 'Click here to update your profile.', '', $retval['error'] );
						$result = json_encode( array( 'success' => $error_message ) );
					} else {
						$result = json_encode( array( 'success' => $retval['error'] ) );
					}
				} else {
					$result = json_encode( array( 'success' => $success_message ) );
				}

			die( $result );
			break;
		case 'aweber' :
			if ( ! class_exists( 'AWeberAPI' ) ) {
				require_once( get_template_directory() . '/includes/subscription/aweber/aweber_api.php' );
			}

			$account = et_pb_get_aweber_account();

			if ( ! $account ) {
				die( json_encode( array( 'error' => __( 'Aweber: Wrong configuration data', 'Divi' ) ) ) );
			}

			try {
				$list_url = "/accounts/{$account->id}/lists/{$list_id}";
				$list = $account->loadFromUrl( $list_url );

				$new_subscriber = $list->subscribers->create(
					array(
						'email' => $email,
						'name'  => $firstname,
					)
				);

				die( json_encode( array( 'success' => $success_message ) ) );
			} catch ( Exception $exc ) {
				die( json_encode( array( 'error' => $exc->message ) ) );
			}

			break;
	}

	die();
}
add_action( 'wp_ajax_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );
add_action( 'wp_ajax_nopriv_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );

function et_add_divi_menu() {
	$core_page = add_menu_page( 'Divi', 'Divi', 'switch_themes', 'et_divi_options', 'et_build_epanel' );
	add_submenu_page( 'et_divi_options', __( 'Theme Options', 'Divi' ), __( 'Theme Options', 'Divi' ), 'manage_options', 'et_divi_options' );
	add_submenu_page( 'et_divi_options', __( 'Theme Customizer', 'Divi' ), __( 'Theme Customizer', 'Divi' ), 'manage_options', 'customize.php?et_customizer_option_set=theme' );
	add_submenu_page( 'et_divi_options', __( 'Module Customizer', 'Divi' ), __( 'Module Customizer', 'Divi' ), 'manage_options', 'customize.php?et_customizer_option_set=module' );
	add_submenu_page( 'et_divi_options', __( 'Divi Library', 'Divi' ), __( 'Divi Library', 'Divi' ), 'manage_options', 'edit.php?post_type=et_pb_layout' );

	add_action( "admin_print_scripts-{$core_page}", 'et_epanel_admin_js' );
	add_action( "admin_head-{$core_page}", 'et_epanel_css_admin');
	add_action( "admin_print_scripts-{$core_page}", 'et_epanel_media_upload_scripts');
	add_action( "admin_head-{$core_page}", 'et_epanel_media_upload_styles');
}
add_action('admin_menu', 'et_add_divi_menu');

function add_divi_customizer_admin_menu() {
	if ( current_user_can( 'customize' ) ) {
		global $wp_admin_bar;

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$customize_url = add_query_arg( 'url', urlencode( $current_url ), wp_customize_url() );

		$wp_admin_bar->add_menu( array(
			'parent' => 'appearance',
			'id'     => 'customize-divi-theme',
			'title'  => __( 'Theme Customizer' ),
			'href'   => $customize_url . '&et_customizer_option_set=theme',
			'meta'   => array(
				'class' => 'hide-if-no-customize',
			),
		) );

		$wp_admin_bar->add_menu( array(
			'parent' => 'appearance',
			'id'     => 'customize-divi-module',
			'title'  => __( 'Module Customizer' ),
			'href'   => $customize_url . '&et_customizer_option_set=module',
			'meta'   => array(
				'class' => 'hide-if-no-customize',
			),
		) );

		$wp_admin_bar->remove_menu( 'customize' );
	}
}

add_action( 'admin_bar_menu', 'add_divi_customizer_admin_menu', 999 );