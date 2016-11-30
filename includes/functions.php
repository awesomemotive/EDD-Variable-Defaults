<?php
/**
 * Helper functions
 *
 * @package     EDD\VariableDefaults\Functions
 * @since       1.0.1
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Get an array of configured prices
 *
 * @since       1.0.1
 * @return      array $prices The configured prices
 */
function edd_variable_defaults_get_prices() {
	$prices = array();

	$posts = get_posts(
		array(
			'posts_per_page' => 99999,
			'post_type'      => 'variable-default',
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'orderby'        => 'meta_value_num',
			'meta_key'       => '_edd_variable_default_order'
		)
	);

	if ( ! empty( $posts ) ) {
		foreach ( $posts as $key => $post ) {
			$prices[ $post->ID ] = esc_html( $post->post_title );
		}
	}

	return $prices;
}
