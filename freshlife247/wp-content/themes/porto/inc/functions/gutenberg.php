<?php
/**
 * Global
 * 
 * @since 6.5.1 WooCommerce booking plugin not working porperly in its archive page.
 */
global $porto_settings;
if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) && ( ! empty( $porto_settings['enable-gfse'] ) && true == $porto_settings['enable-gfse'] ) ) {
	// Add block patterns
	require PORTO_LIB . '/block-patterns.php';
} else {
	// Disable Block Templates
	remove_theme_support( 'block-templates' );
	add_filter( 'get_block_templates', 'porto_remove_template_block', 20, 3 );

	if ( class_exists( 'WooCommerce' ) ) {
		add_filter( 'woocommerce_has_block_template', 'porto_remove_woocommerce_template', 20, 2 );
	}
	if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) ) {
		add_filter( 'should_load_separate_core_block_assets', '__return_false' );
	}
	if ( is_admin() ) {
		add_filter( 'add_menu_classes', 'porto_remove_template_menu', 20 );
		add_action( 'admin_bar_menu', 'porto_remove_site_edit_menu', 50 );
	}
}

/**
 * Remove Porto block template for Gutenberg Full Site Editing
 *
 * @since 6.5.0
 */
function porto_remove_template_block( $query_result, $query, $template_type ) {
	foreach ( $query_result as $index => $query ) {
		if ( false !== strpos( $query->id, 'porto//' ) ) {
			unset( $query_result[ $index ] );
		}
	}
	return $query_result;
}

/**
 * Remove WooCommerce Html Templates for non Gutenberg Full Site Editing
 *
 * @since 6.5.0
 */
function porto_remove_woocommerce_template( $has_template, $template_name ) {
	if ( 'single-product' == $template_name || 'archive-product' == $template_name || 'taxonomy-product_cat' == $template_name || 'taxonomy-product_tag' == $template_name ) {
		return false;
	}
	return $has_template;
}

if ( is_admin() ) {
	/**
	 * Remove Submenu item - Appearance/Editor
	 *
	 * @since 6.5.0
	 */
	function porto_remove_template_menu( $menu ) {
		global $submenu;
		if ( ! empty( $submenu['themes.php'] ) && ! empty( $submenu['themes.php'][6] ) ) {
			if ( 'site-editor.php' == $submenu['themes.php'][6][2] ) {
				unset( $submenu['themes.php'][6] );
			}
		}
		return $menu;
	}

	/**
	 * Remove Admin Submenu - Edit Site
	 *
	 * @since 6.5.0
	 */
	function porto_remove_site_edit_menu( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'site-editor' );
	}
}
