<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Invalid_Offer' ) ) {

    /**
     * Class TEO_Invalid_Offer
     *
     * Model that houses the logic of handling matters relating to invalid offers.
     *
     * @since 1.0.0
     */
    final class TEO_Invalid_Offer {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Invalid_Offer.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Invalid_Offer
         */
        private static $_instance;

        /**
         * Property that holds various constants utilized throughout the plugin.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Constants
         */
        private $_plugin_constants;




        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
        */

        /**
         * Cloning is forbidden.
         *
         * @since 1.0.0
         * @access public
         */
        public function __clone () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ) , '1.0.0' );

        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.0.0
         * @access public
         */
        public function __wakeup () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ) , '1.0.0' );

        }

        /**
         * TEO_Invalid_Offer constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Invalid_Offer model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];

        }

        /**
         * Ensure that only one instance of TEO_Invalid_Offer is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Invalid_Offer model.
         * @return TEO_Invalid_Offer
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }
        



        /*
        |--------------------------------------------------------------------------
        | Shortcodes
        |--------------------------------------------------------------------------
        */

        /**
         * Get invalid offer error message.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $atts
         * @param $content
         * @return string
         */
        public function sc_invalid_offer_error_message( $atts , $content ) {

            return isset( $_GET[ 'error-message' ] ) ? $_GET[ 'error-message' ] : '';
            
        }

    }

}