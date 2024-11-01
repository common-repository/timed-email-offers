<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_WooCommerce_Composite_Products' ) ) {

    /**
     * Class TEO_WooCommerce_Composite_Products
     *
     * Model that houses the logic of integrating with WooCommerce Composite Products plugin.
     *
     * @since 1.2.0
     */
    final class TEO_WooCommerce_Composite_Products {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_WooCommerce_Composite_Products.
         *
         * @since 1.2.0
         * @access private
         * @var TEO_WooCommerce_Composite_Products
         */
        private static $_instance;

        /**
         * TEO_Constants instance. Holds various constants this class uses.
         *
         * @since 1.2.0
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
         * @since 1.2.0
         * @access public
         */
        public function __clone () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ) , '1.2.0' );

        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.2.0
         * @access public
         */
        public function __wakeup () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ) , '1.2.0' );

        }

        /**
         * TEO_WooCommerce_Composite_Products constructor.
         *
         * @since 1.2.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];

        }

        /**
         * Ensure that there is only one instance of TEO_WooCommerce_Composite_Products is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.2.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return TEO_WooCommerce_Composite_Products
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Get bundle product additional info.
         *
         * @since 1.2.0
         * @access public
         *
         * @param array      $product_additional_data Array of product additional data.
         * @param WC_Product $product                 WC_Product object.
         * @return array Array of product additional data.
         */
        public function get_product_additional_info( $product_additional_data , $product ) {

            $product_additional_data = array( 'product_type' => 'composite' );

            return $product_additional_data;

        }


        // Offer Condition

        /**
         * Check whether to condition check product cart item.
         * Ex. a simple product is a component of a composite product, then that simple product should be excluded on offer condition check.
         *
         * @param boolean $process_product_order_item Flag that determines if product is composite item.
         * @param array   $order_item                 WC_Order object.
         * @param array   $order_items                Array of WC_Order objects.
         * @param array   $product_condition          Array of product condition.
         * @return boolean Flag that determines if product is composite.
         */
        public function condition_check_product_order_item( $process_product_order_item , $order_item , $order_items , $product_condition ) {

            if ( isset( $order_item[ 'composite_item' ] ) && $order_item[ 'composite_item' ] )
                return false; // This product is a component of a composite product
            else
                return $process_product_order_item && true;

        }

        /**
         * Get quantity in order entry text for 'bundle' type product.
         *
         * @since 1.2.0
         * @access public
         *
         * @param string $product_text Product text.
         * @param array  $data         Array of product data.
         * @return text Product text.
         */
        public function product_quantity_in_order_entry_text( $product_text , $data ) {

            $product_text = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';

            return $product_text;

        }

        /**
         * Check bundle product condition is meet.
         *
         * @since 1.2.0
         * @access public
         *
         * @param boolean $product_condition_attained Flag that determines if product condition check.
         * @param array   $product_condition          Array of product condition.
         * @param array   $order_items                Array of order items.
         * @return boolean Flag that determines if product condition check.
         */
        public function product_condition_check( $product_condition_attained , $product_condition , $order_items ) {

            $product_id         = $product_condition[ 'product-id' ];
            $quantity_condition = $product_condition[ 'product-quantity-condition' ];
            $quantity           = $product_condition[ 'product-quantity' ];

            $product_in_order = false;

            foreach ( $order_items as $order_item ) {

                // Compoception, composite product component of another composite product ( mind blown )
                $process_composite_product_order_item = true;
                $process_composite_product_order_item = apply_filters( 'teo_condition_check_composite_product_order_item' , $process_composite_product_order_item , $order_item , $order_items , $product_condition );

                if ( !$process_composite_product_order_item )
                    continue;

                if ( $order_item[ 'product_id' ] == $product_id ) {

                    if ( !$product_in_order )
                        $product_in_order = true;

                    switch ( $quantity_condition ) {
                        case '=':

                            if ( $order_item[ 'qty' ] == $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                        case '!=':

                            if ( $order_item[ 'qty' ] != $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                        case '>':

                            if ( $order_item[ 'qty' ] > $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                        case '<':

                            if ( $order_item[ 'qty' ] < $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                    } // end switch

                    break; // No point in continuing with the loop

                }

            }

            if ( !$product_in_order )
                $product_condition_attained = false;

            $product_condition_attained = apply_filters( 'teo_composite_product_additional_condition_check' , $product_condition_attained , $product_condition , $order_items );

            return $product_condition_attained;

        }


        // Offer Accept Action

        /**
         * Get bundle product to add in cart entry text.
         *
         * @param string $product_text Product Text.
         * @param array  $data         Array of product data.
         * @return string Product text.
         */
        public function product_to_add_entry_text( $product_text , $data ) {

            $product_id   = isset( $data[ 'product_id' ] ) ? $data[ 'product_id' ] : $data[ 'product-id' ];
            $product_text = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $product_id . "&action=edit" ) . '" target="_blank">[ID : ' . $product_id . '] ' . get_the_title( $product_id ) . '</a></div>';

            return $product_text;

        }

        /**
         * Add composite product to cart on executing offer accept action if necessary.
         *
         * @since 1.2.0
         * @access public
         *
         * @param string $cart_item_key           Unique cart item key.
         * @param array  $product_data            Array product data.
         * @param int    $offer_id                Timed email offer id.
         * @param int    $order_id                Timed email offer order id that triggered this order.
         * @param string $email_token             Timed email offer scheduled email token.
         * @param array  $additional_product_data Array of product addtional data.
         * @return mixed Cart item key on success, Boolean false on failure
         */
        public function accept_offer_action_add_composite_product_to_cart( $cart_item_key , $product_data , $offer_id , $order_id , $email_token , $additional_product_data ) {

            // Initialize components of a composite product
            $composite_product     = wc_get_product( $product_data[ 'product-id' ] );
            $composite_data        = $composite_product->get_composite_data();
            $components_to_add     = array();
            $components_to_add_qty = array();

            foreach ( $composite_data as $composite ) {

                // Only add required components
                if ( $composite[ 'optional' ] == 'no' ) {

                    $components_to_add[ $composite[ 'component_id' ] ]     = $composite[ 'default_id' ]; // The actual product id
                    $components_to_add_qty[ $composite[ 'component_id' ] ] = $composite[ 'quantity_min' ]; // Minimum qty times the composite product qty

                    $product = wc_get_product( $composite[ 'default_id' ] );

                    if ( $product->product_type == 'variable' ) {

                        $default_attributes = $product->get_variation_default_attributes();

                        // There are instances where the admin only supplly default values for partial set of attributes.
                        // We need them to supply defaults to all attributes, that's why we check if the 2 arrays below is the same length.
                        // 'get_variation_default_attributes' just add on the array the attributes with value, disregarding the other ones with no default value.
                        // So we need to check againts the variable products attribute length the result array of 'get_variation_default_attributes'.
                        if ( count( $default_attributes ) == count( $product->get_attributes() ) ) {

                            $variation_data = TEO_Helper::get_variation_data_by_attributes( $composite[ 'default_id' ] , $default_attributes );

                            foreach ( $default_attributes as $attribute => $value ) {

                                $_POST[ 'wccp_attribute_' . $attribute ][ $composite[ 'component_id' ] ] = $value;
                                $_POST[ 'wccp_variation_id' ][ $composite[ 'component_id' ] ]            = $variation_data[ 'variation_id' ];

                            }

                        } else
                            wc_add_notice( sprintf( __( 'Please provide default selected attributes for the composite component variable product %1$s' , 'timed-email-offers' ) , $product->get_title() ) , 'error' );

                    }

                }

            }

            $components_to_add     = apply_filters( 'teo_components_to_add_for_composite_product' , $components_to_add , $composite_data , $composite_product , $product_data , $additional_product_data );
            $components_to_add_qty = apply_filters( 'teo_components_to_add_quantity_for_composite_product' , $components_to_add_qty , $composite_data , $composite_product, $product_data , $additional_product_data );

            if ( !empty( $components_to_add ) && !empty( $components_to_add_qty ) ) {

                $_POST[ 'wccp_component_selection' ] = $components_to_add;
                $_POST[ 'wccp_component_quantity' ]  = $components_to_add_qty;

            }

            $_POST[ 'quantity' ]    = $product_data[ 'product-quantity' ];
            $_POST[ 'add-to-cart' ] = $product_data[ 'product-id' ];

            // Add composite product
            $cart_item_key = WC()->cart->add_to_cart( $product_data[ 'product-id' ] , $product_data[ 'product-quantity' ] , 0 , array() , $additional_product_data );

            return $cart_item_key;

        }

        /**
         * Execute the model that integrates TEO with WooCommerce Product Composites plugin.
         *
         * @since 1.2.1
         * @access public
         */
        public function run() {

            // Get composite product additional info
            add_filter( 'teo_get_composite_product_additional_info' , array( $this , 'get_product_additional_info' ) , 10 , 2 );


            // [Offer Condition]

            // Check whether to condition check product order item
            add_filter( 'teo_condition_check_simple_product_order_item' , array( $this , 'condition_check_product_order_item' ) , 10 , 4 );
            add_filter( 'teo_condition_check_variable_product_order_item' , array( $this , 'condition_check_product_order_item' ) , 10 , 4 );
            add_filter( 'teo_condition_check_bundle_product_order_item' , array( $this , 'condition_check_product_order_item' ) , 10 , 4 );
            add_filter( 'teo_condition_check_composite_product_order_item' , array( $this , 'condition_check_product_order_item' ) , 10 , 4 );

            // Get quantity in order entry text for 'composite' type product
            add_filter( 'teo_composite_product_quantity_in_order_entry_text' , array( $this , 'product_quantity_in_order_entry_text' ) , 10 , 2 );

            // Check composite product condition is meet
            add_filter( 'teo_composite_product_type_condition_check' , array( $this , 'product_condition_check' ) , 10 , 3 );


            // [Offer Accept Action]

            // Get composite product to add in order entry text
            add_filter( 'teo_composite_product_to_add_entry_text' , array( $this , 'product_to_add_entry_text' ) , 10 , 2 );

            // Add composite product to cart on executing offer accept action if necessary
            add_filter( 'teo_add-products-to-cart_action_add_composite_product_to_cart' , array( $this , 'accept_offer_action_add_composite_product_to_cart' ) , 10 , 6 );

        }

    }

}
