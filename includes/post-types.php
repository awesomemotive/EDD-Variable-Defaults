<?php
/**
 * Post type functions
 *
 * @package     EDD\ConditionalEmails
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register the prices post type
 *
 * @since       1.0.0
 * @return      void
 */
function edd_variable_defaults_register_cpt() {
	$labels = array(
		'name'               => _x( 'Variable Prices', 'post type general name', 'edd-variable-defaults' ),
		'singular_name'      => _x( 'Variable Price', 'post type singular name', 'edd-variable-defaults' ),
		'add_new'            => __( 'Add New', 'edd-variable-defaults' ),
		'add_new_item'       => __( 'Add New Variable Price', 'edd-variable-defaults' ),
		'edit_item'          => __( 'Edit Variable Price', 'edd-variable-defaults' ),
		'new_item'           => __( 'New Variable Price', 'edd-variable-defaults' ),
		'all_items'          => __( 'All Variable Prices', 'edd-variable-defaults' ),
		'view_item'          => __( 'View Variable Price', 'edd-variable-defaults' ),
		'search_items'       => __( 'Search Variable Prices', 'edd-variable-defaults' ),
		'not_found'          => __( 'No Variable Prices found', 'edd-variable-defaults' ),
		'not_found_in_trash' => __( 'No Variable Prices found in Trash', 'edd-variable-defaults' ),
		'menu_name'          => __( 'Variable Prices', 'edd-variable-defaults' )
	);

	$args = array(
		'labels'       => apply_filters( 'edd_variable_defaults_labels', $labels ),
		'public'       => false,
		'show_in_menu' => false,
		'query_var'    => false,
		'hierarchical' => false,
		'supports'     => apply_filters( 'edd_variable_defaults_supports', array( 'title' ) ),
		'can_export'   => true
	);

	register_post_type( 'variable-default', $args );
}
add_action( 'init', 'edd_variable_defaults_register_cpt', 1 );
