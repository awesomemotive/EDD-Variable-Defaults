<?php
/**
 * Plugin Name:     Easy Digital Downloads - Variable Defaults
 * Plugin URI:      https://easydigitaldownloads.com/extensions/variable-defaults
 * Description:     Setup default variable prices for EDD
 * Version:         1.0.1
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     edd-variable-defaults
 *
 * @package         EDD\VariableDefaults
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 * @copyright       Copyright (c) 2014, Daniel J Griffiths
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


if( ! class_exists( 'EDD_Variable_Defaults' ) ) {


    /**
     * Main EDD_Variable_Defaults class
     *
     * @since       1.0.0
     */
    class EDD_Variable_Defaults {


        /**
         * @var         EDD_Variable_Defaults $instance The one true EDD_Variable_Defaults
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      self::$instance The one true EDD_Variable_Defaults
         */
        public static function instance() {
            if( ! self::$instance ) {
                self::$instance = new EDD_Variable_Defaults();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_VARIABLE_DEFAULTS_VER', '1.0.1' );

            // Plugin path
            define( 'EDD_VARIABLE_DEFAULTS_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_VARIABLE_DEFAULTS_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            require_once EDD_VARIABLE_DEFAULTS_DIR . 'includes/actions.php';
            require_once EDD_VARIABLE_DEFAULTS_DIR . 'includes/functions.php';
            require_once EDD_VARIABLE_DEFAULTS_DIR . 'includes/post-types.php';
            
            if( is_admin() ) {
                require_once EDD_VARIABLE_DEFAULTS_DIR . 'includes/admin/actions.php';
                require_once EDD_VARIABLE_DEFAULTS_DIR . 'includes/admin/settings.php';
                require_once EDD_VARIABLE_DEFAULTS_DIR . 'includes/admin/pages.php';
            }
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            add_action( 'admin_init', array( $this, 'maybe_upgrade' ) );
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            $lang_dir = apply_filters( 'edd_variable_defaults_lang_dir', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), '' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-variable-defaults', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-variable-defaults/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-variable-defaults/ folder
                load_textdomain( 'edd-variable-defaults', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-variable-defaults/languages/ folder
                load_textdomain( 'edd-variable-defaults', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-variable-defaults', false, $lang_dir );
            }
        }


        /**
         * Ensure the order value is set on old installs
         *
         * @since       1.0.1
         * @return      void
         */
        public function maybe_upgrade() {
            $upgraded = get_option( 'edd_variable_defaults_v101_upgraded' );

            if( ! $upgraded ) {
                $posts = get_posts(
                    array(
                        'posts_per_page'    => 99999,
                        'post_type'         => 'variable-default',
                        'post_status'       => 'publish'
                    )
                );

                if( ! empty( $posts ) ) {
                    foreach( $posts as $key => $post ) {
                        $default = get_post_meta( $post->ID, '_edd_variable_default_order', true );

                        if( ! $default ) {
                            update_post_meta( $post->ID, '_edd_variable_default_order', '0' );
                        }
                    }
                }

                update_option( 'edd_variable_defaults_v101_upgraded', '1' );
            }
        }
    }
}


/**
 * The main function responsible for returning the one true EDD_Variable_Defaults
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      EDD_Variable_Defaults The one true EDD_Variable_Defaults
 */
function edd_variable_defaults_load() {
    if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
        if( ! class_exists( 'EDD_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }

        $activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();

        return EDD_Variable_Defaults::instance();
    } else {
        return EDD_Variable_Defaults::instance();
    }
}
add_action( 'plugins_loaded', 'edd_variable_defaults_load' );
