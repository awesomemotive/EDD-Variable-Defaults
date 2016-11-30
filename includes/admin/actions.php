<?php
/**
 * Admin actions
 *
 * @package     EDD\VariableDefaults\Admin\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Save default variable price
 *
 * @since       1.0.0
 * @param       array $data
 * @return      void
 */
function edd_edit_default_variable_price( $data ) {
	if ( ! current_user_can( 'manage_shop_settings' ) ) {
		wp_die( __( 'You do not have permission to add variable prices', 'edd-variable-defaults' ), __( 'Error', 'edd-variable-defaults' ), array( 'response' => 401 ) );
	}

	if ( ! wp_verify_nonce( $data['edd-variable-defaults-nonce'], 'edd_variable_defaults_nonce' ) ) {
		wp_die( __( 'Nonce verification failed', 'edd-variable-defaults' ), __( 'Error', 'edd-variable-defaults' ), array( 'response' => 401 ) );
	}

	if ( empty( $data['name'] ) || empty( $data['price'] ) ) {
		echo '<div class="error settings-error"><p><strong>' . __( '"Name" and "Price" are both required fields!', 'edd-variable-defaults' ) . '</strong></p></div>';
		return;
	}

	$price_id = ( ! empty( $data['price-id'] ) ? absint( $data['price-id'] ) : false );
	$name     = esc_attr( $data['name'] );
	$value    = esc_attr( $data['price'] );
	$order    = esc_attr( $data['order'] );

	if ( ! $price_id ) {
		$price_id = wp_insert_post(
			array(
				'post_title'  => $name,
				'post_type'   => 'variable-default',
				'post_status' => 'publish'
			)
		);
	}

	update_post_meta( $price_id, '_edd_variable_default_price', edd_sanitize_amount( $value ) );
	update_post_meta( $price_id, '_edd_variable_default_order', absint( $order ) );

	wp_safe_redirect( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions' ) );
	exit;
}
add_action( 'edd_edit_default_variable_price', 'edd_edit_default_variable_price' );


/**
 * Delete default variable price
 *
 * @since       1.0.0
 * @param       array $data
 * @return      void
 */
function edd_delete_default_variable_price( $data ) {
	if ( ! current_user_can( 'manage_shop_settings' ) ) {
		wp_die( __( 'You do not have permission to delete variable prices', 'edd-variable-defaults' ), __( 'Error', 'edd-variable-defaults' ), array( 'response' => 401 ) );
	}

	if ( ! wp_verify_nonce( $data['_wpnonce'] ) ) {
		wp_die( __( 'Nonce verification failed', 'edd-variable-defaults' ), __( 'Error', 'edd-variable-defaults' ), array( 'response' => 401 ) );
	}

	if ( empty( $data['price-id'] ) || ! isset( $data['price-id'] ) ) {
		wp_die( __( 'No price ID provided', 'edd-variable-defaults' ), __( 'Error', 'edd-variable-defaults' ), array( 'response' => 409 ) );
	}

	wp_delete_post( $data['price-id'] );

	wp_safe_redirect( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions' ) );
	exit;
}
add_action( 'edd_delete_default_variable_price', 'edd_delete_default_variable_price' );
