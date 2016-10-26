<?php

if ( ! function_exists( 'et_builder_add_main_elements' ) ) :
function et_builder_add_main_elements() {
	require ET_BUILDER_DIR . 'main-structure-elements.php';
	require ET_BUILDER_DIR . 'main-modules.php';
}
endif;

if ( ! function_exists( 'et_builder_load_framework' ) ) :
function et_builder_load_framework() {
	$action_hook = is_admin() ? 'wp_loaded' : 'wp';

	require ET_BUILDER_DIR . 'layouts.php';
	require ET_BUILDER_DIR . 'class-et-builder-element.php';
	require ET_BUILDER_DIR . 'functions.php';
	require ET_BUILDER_DIR . 'class-et-global-settings.php';

	add_action( $action_hook, 'et_builder_init_global_settings' );
	add_action( $action_hook, 'et_builder_add_main_elements' );
}
endif;

et_builder_load_framework();