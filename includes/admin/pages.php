<?php
/**
 * Admin pages
 *
 * @package     EDD\VariableDefaults\Admin\Pages
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add admin pages
 *
 * @since       1.0.0
 * @return      void
 */
function edd_variable_defaults_admin_pages() {
    add_submenu_page( null, __( 'Default Variable Prices', 'edd-variable-defaults' ), __( 'Default Variable Prices', 'edd-variable-defaults' ), 'manage_shop_settings', 'edd-variable-defaults', 'edd_variable_defaults_render_edit' );
}
add_action( 'admin_menu', 'edd_variable_defaults_admin_pages', 10 );
