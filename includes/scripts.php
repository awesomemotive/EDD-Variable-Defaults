<?php
/**
 * Scripts
 *
 * @package     EDD\ConditionalEmails\Scripts
 * @scripts     1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function edd_variable_defaults_admin_scripts( $hook ) {
    if( $hook == 'download_page_edd-conditional-email' ) {
        wp_enqueue_style( 'edd-variable-defaults', EDD_VARIABLE_DEFAULTS_URL . 'assets/css/admin.css', array(), EDD_VARIABLE_DEFAULTS_VER );
    }
}
add_action( 'admin_enqueue_scripts', 'edd_variable_defaults_admin_scripts', 100 );
