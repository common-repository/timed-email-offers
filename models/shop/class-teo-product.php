<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Product' ) ) {

    /**
     * Class TEO_Product
     *
     * Model that houses the logic of the various helper functions related to the shop's products.
     *
     * @since 1.0.0
     */
    final class TEO_Product {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Product.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Product
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
         * TEO_Product constructor.
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
         * Ensure that there is only one instance of TEO_Product is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return TEO_Product
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Get all products.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $args
         * @return mixed
         */
        public function get_products( $args ) {

            if ( !is_array( $args ) )
                return new WP_Error( 'teo-get_products-function-invalid-args' , __( 'Function "get_products" requires an $args argument in array format.' , 'timed-email-offers' ) , $args );
            
            $limit    = array_key_exists( 'limit' , $args ) ? $args[ 'limit' ] : null;
            $order_by = array_key_exists( 'order_by' , $args ) ? $args[ 'order_by' ] : 'DESC';

            $products = TEO_Helper::get_all_products( $limit , $order_by );

            if ( array_key_exists( 'return_format' , $args  ) ) {

                switch ( $args[ 'return_format' ] ) {

                    case 'select_option':

                        if ( isset( $args[ 'add_empty_option' ] ) && $args[ 'add_empty_option' ] ) {

                            $empty_option_text = isset( $args[ 'empty_option_text' ] ) ? $args[ 'empty_option_text' ] : __( '--Select Product--' , 'timed-email-offers' );
                            $return_products   = "<option value=''>" . $empty_option_text . "</option>";

                        } else
                            $return_products = "";

                        $get_product_url = isset( $args[ 'product_url' ] ) && $args[ 'product_url' ];

                        foreach ( $products as $product ) {

                            $product_url_attr = $get_product_url ? 'data-product-url="' . home_url( "/wp-admin/post.php?post=" .$product->ID . "&action=edit" ) . '"' : '';
                            $return_products .= '<option value="' . $product->ID . '" ' . $product_url_attr . '>[ID : ' . $product->ID . '] ' . $product->post_title . '</option>';

                        }

                        return $return_products;

                        break;

                    case 'raw':
                        return $products;
                        break;

                    default:
                        return new WP_Error( 'teo-get_products-function-unsupported-return_format' , __( 'Unsupported "return_format" in the $args argument.' , 'timed-email-offers' ) , $args );

                }

            } else
                return new WP_Error( 'teo-get_products-function-missing-return_format-key-in-args' , __( 'Missing "return_format" key in the $args argument.' , 'timed-email-offers' ) , $args );
            
        }

        /**
         * Get additional info about a product.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $product_id
         * @return mixed
         */
        public function get_product_additional_info( $product_id ) {

            $product = wc_get_product( $product_id );
            $product_additional_data = array();

            switch ( $product->get_type() ) {

                case 'simple':
                    $product_additional_data = array( 'product_type' => 'simple' );
                    break;

                case 'variable':

                    $product_additional_data = array(
                        'product_type'       => 'variable',
                        'product_variations' => TEO_Helper::get_product_variations( array( 'product' => $product ) )
                    );

                    break;

                default:
                    $product_additional_data = apply_filters( 'teo_get_' . $product->get_type() . '_product_additional_info' , $product_additional_data , $product );
                    break;

            }

            return $product_additional_data;

        }

    }

}
