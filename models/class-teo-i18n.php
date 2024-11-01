<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_I18n' ) ) {

    /**
     * Class TEO_I18n
     *
     * Model that houses the logic of internationalizing Timed Email Offers plugin.
     *
     * @since 1.0.0
     */
    final class TEO_I18n {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_I18n.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_I18n
         */
        private static $_instance;

        /**
         * TEO_Constants instance. Holds various constants this class uses.
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
         * TEO_I18n constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];

        }

        /**
         * Ensure that there is only one instance of TEO_I18n is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return TEO_I18n
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Load plugin text domain.
         *
         * @since 1.0.0
         * @access public
         */
        public function load_plugin_textdomain() {

            load_plugin_textdomain( $this->_plugin_constants->TEXT_DOMAIN() , false , $this->_plugin_constants->PLUGIN_BASENAME() . '/languages' );

        }

    }

}
