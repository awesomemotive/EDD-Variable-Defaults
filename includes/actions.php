<?php
/**
 * Actions
 *
 * @package     EDD\VariableDefaults\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Maybe override the edd_get_variable_prices filter
 *
 * @since       1.0.0
 * @param       mixed $prices array if set, string otherwise
 * @return      array $prices The variable prices
 */
function edd_variable_defaults_get_variable_prices( $prices ) {
    if( is_array( $prices ) && count( $prices ) > 0 ) {
        return $prices;
    }

    $defaults = get_posts(
        array(
            'posts_per_page'    => 99999,
            'post_type'         => 'variable-default',
            'post_status'       => 'publish'
        )
    );

    if( ! empty( $defaults ) ) {
        $prices = array();

        foreach( $defaults as $key => $price ) {
            $value = get_post_meta( $price->ID, '_edd_variable_default_price', true );

            $prices[$key] = array(
                'index'     => $key,
                'name'      => $price->post_title,
                'amount'    => $value
            );
        }
    }

    return $prices;
}
add_filter( 'edd_get_variable_prices', 'edd_variable_defaults_get_variable_prices' );
