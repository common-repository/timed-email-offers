<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Coupon' ) ) {

    /**
     * Class TEO_Coupon
     *
     * Model that houses the logic of the various helper functions related to the shop's coupons.
     *
     * @since 1.0.0
     */
    final class TEO_Coupon {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Coupon.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Coupon
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
         * TEO_Coupon constructor.
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
         * Ensure that there is only one instance of TEO_Coupon is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return TEO_Coupon
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Get shop coupons.
         * This function is available only for plugin authorized roles.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $args
         * @return mixed
         */
        public function get_coupons( $args ) {

            if ( !is_array( $args ) )
                return new WP_Error( 'teo-get_coupons-function-invalid-args' , __( 'Function "get_coupons" requires an $args argument in array format.' , 'timed-email-offers' ) , $args );
            
            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-get_coupons-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , $args );

            $limit    = array_key_exists( 'limit' , $args ) ? $args[ 'limit' ] : null;
            $order_by = array_key_exists( 'order_by' , $args ) ? $args[ 'order_by' ] : 'DESC';

            $coupons = TEO_Helper::get_all_coupons( $limit , $order_by );

            if ( array_key_exists( 'return_format' , $args  ) ) {

                switch ( $args[ 'return_format' ] ) {

                    case 'select_option':

                        if ( isset( $args[ 'add_empty_option' ] ) && $args[ 'add_empty_option' ] ) {

                            $empty_option_text = isset( $args[ 'empty_option_text' ] ) ? $args[ 'empty_option_text' ] : __( '--Select Product--' , 'timed-email-offers' );
                            $return_coupons    = "<option value=''>" . $empty_option_text . "</option>";

                        } else
                            $return_coupons = "";

                        $get_coupon_url    = isset( $args[ 'coupon_url' ] ) && $args[ 'coupon_url' ];
                        $get_coupon_type   = isset( $args[ 'coupon_type' ] ) && $args[ 'coupon_type' ];
                        $get_coupon_amount = isset( $args[ 'coupon_amount' ] ) && $args[ 'coupon_amount' ];

                        $coupon_types = wc_get_coupon_types();

                        foreach ( $coupons as $coupon ) {

                            $coupon_url_attr    = $get_coupon_url ? 'data-coupon-url="' . home_url( "/wp-admin/post.php?post=" . $coupon->ID . "&action=edit" ) . '"' : '';
                            $coupon_amount_attr = $get_coupon_amount ? 'data-coupon-amount="' . get_post_meta( $coupon->ID , 'coupon_amount' , true ) . '"' : '';

                            $coupon_type_attr = '';
                            if ( $get_coupon_type ) {

                                $coupon_type = get_post_meta( $coupon->ID , 'discount_type' , true );
                                $coupon_type_attr = 'data-coupon-type="' . $coupon_type . '" data-coupon-type-text="' . $coupon_types[ $coupon_type ] . '"';

                            }

                            $return_coupons .= '<option value="' . $coupon->ID . '" ' . $coupon_url_attr . ' ' . $coupon_type_attr . ' ' . $coupon_amount_attr . '>[ID : ' . $coupon->ID . '] ' . $coupon->post_title . '</option>';

                        }

                        break;

                    case 'raw':

                        $return_coupons = $coupons;

                        break;

                    default:
                        return new WP_Error( 'teo-get_coupons-function-unsupported-return_format' , __( 'Unsupported "return_format" in the $args argument.' , 'timed-email-offers' ) , $args );

                }

            } else
                return new WP_Error( 'teo-get_coupons-function-missing-return_format-key-in-args' , __( 'Missing "return_format" key in the $args argument.' , 'timed-email-offers' ) , $args );
            
            return $return_coupons;

        }

    }

}
