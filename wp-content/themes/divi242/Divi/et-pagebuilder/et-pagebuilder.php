<?php

define( 'ET_BUILDER_THEME', true );
function et_setup_builder() {
	define( 'ET_BUILDER_DIR', get_template_directory() . '/includes/builder/' );
	define( 'ET_BUILDER_URI', get_template_directory_uri() . '/includes/builder' );
	define( 'ET_BUILDER_LAYOUT_POST_TYPE', 'et_pb_layout' );
	load_theme_textdomain( 'et_builder', ET_BUILDER_DIR . 'languages' );
	require ET_BUILDER_DIR . 'framework.php';
}
add_action( 'init', 'et_setup_builder' );