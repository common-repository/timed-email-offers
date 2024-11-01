<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_WooCommerce_Product_Bundles' ) ) {

    /**
     * Class TEO_WooCommerce_Product_Bundles
     *
     * Model that houses the logic of integrating with WooCommerce Product Bundles plugin.
     *
     * @since 1.2.0
     */
    final class TEO_WooCommerce_Product_Bundles {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_WooCommerce_Product_Bundles.
         *
         * @since 1.2.0
         * @access private
         * @var TEO_WooCommerce_Product_Bundles
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
         * TEO_WooCommerce_Product_Bundles constructor.
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
         * Ensure that there is only one instance of TEO_WooCommerce_Product_Bundles is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.2.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return TEO_WooCommerce_Product_Bundles
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
         * @param array      $product_additional_data Array of product additional data
         * @param WC_Product $product                 WC_Product object.
         * @return array
         */
        public function get_product_additional_info( $product_additional_data , $product ) {

            $product_additional_data = array( 'product_type' => 'bundle' );

            return $product_additional_data;

        }

        // Offer Condition

        /**
         * Check whether to condition check product order item.
         * Ex. a simple product is a component of a bundle product, then that simple product should be excluded on offer condition check.
         *
         * @param boolean $process_product_order_item Flag that determines if an order item is a product bundle component.
         * @param array   $order_item                 Order item array
         * @param array   $order_items                Array of order items
         * @param array   $product_condition          Array of product conditions
         * @return boolean Returns False if order item is a product bundle component, True otherwise.
         */
        public function condition_check_product_order_item( $process_product_order_item , $order_item , $order_items , $product_condition ) {

            if ( isset( $order_item[ 'bundled_by' ] ) && $order_item[ 'bundled_by' ] )
                return false; // This product is a component of a bundle product
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
         * @return string Product text.
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
         * @param boolean $product_condition_attained Flag that determines if product condition is attained.
         * @param array   $product_condition          Array of product conditions.
         * @param array   $order_items                Array of order items.
         * @return boolean True if condition is attained, False otherwise.
         */
        public function product_condition_check( $product_condition_attained , $product_condition , $order_items ) {

            $product_id         = $product_condition[ 'product-id' ];
            $quantity_condition = $product_condition[ 'product-quantity-condition' ];
            $quantity           = $product_condition[ 'product-quantity' ];

            $product_in_order = false;

            foreach ( $order_items as $order_item ) {

                // Bundleception, bundle product component of another bundle product ( mind blown )
                $process_bundle_product_order_item = true;
                $process_bundle_product_order_item = apply_filters( 'teo_condition_check_bundle_product_order_item' , $process_bundle_product_order_item , $order_item , $order_items , $product_condition );

                if ( !$process_bundle_product_order_item )
                    continue;

                if ( $order_item[ 'product_id' ] == $product_id ) {

                    if ( !$product_in_order )
                        $product_in_order = true;

                    switch ( $quantity_condition ) {
                        case '=':

                            if ( $order_item[ 'quantity' ] == $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                        case '!=':

                            if ( $order_item[ 'quantity' ] != $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                        case '>':

                            if ( $order_item[ 'quantity' ] > $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                        case '<':

                            if ( $order_item[ 'quantity' ] < $quantity )
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

            $product_condition_attained = apply_filters( 'teo_bundle_product_additional_condition_check' , $product_condition_attained , $product_condition , $order_items );

            return $product_condition_attained;

        }


        // Offer Accept Action

        /**
         * Get bundle product to add in order entry text.
         *
         * @since 1.2.0
         * @access public
         *
         * @param string $product_text Product text.
         * @param array  $data         Array of product data.
         * @return string Product text.
         */
        public function product_to_add_entry_text( $product_text , $data ) {

            $product_id   = isset( $data[ 'product_id' ] ) ? $data[ 'product_id' ] : $data[ 'product-id' ];
            $product_text = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $product_id . "&action=edit" ) . '" target="_blank">[ID : ' . $product_id . '] ' . get_the_title( $product_id ) . '</a></div>';

            return $product_text;

        }

        /**
         * Add bundle product to order on executing offer accept action if necessary.
         * Limitation:
         * 1. For the current functionality admin must set min quantity for components of a bundle product.
         * 2. For variable product components, they must either provide default selected attributes for the variable product or
         * enable 'Override Default Selections' on the variable product component and provide overridden default selected attributes.
         *
         * These limitations must be meet for the code below to work properly ( Though I've set some basic safeguards ).
         * In the future release I'll be adding custom controls for bundle products that will address the limitations above.
         * Limitation above is present to save time.
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
         * @return mixed order item key on success, Boolean false on failure
         */
        public function accept_offer_action_add_bundle_product_to_cart( $cart_item_key , $product_data , $offer_id , $order_id , $email_token , $additional_product_data ) {

            $bundle_product = wc_get_product( $product_data[ 'product-id' ] );
            $bundled_items  = $bundle_product->get_bundled_items();

            foreach ( $bundled_items as $bundled_item ) {

                $item_id = $bundled_item->item_id;

                $_POST[ 'bundle_quantity_' . $item_id ] = $bundled_item->item_data[ 'quantity_min' ];

                if ( $bundled_item->product->product_type == 'variable' ) {

                    $variable_product_id          = $bundled_item->product->id;
                    $valid_default_variation_attr = true;

                    // There is a bug in WooCommerce Product Bundles where it allows saving of variable product component while enabling "Override Default Selections"
                    // But then not providing an overridden values. So we validate the default variation attributes here.
                    foreach ( $bundled_item->item_data[ 'default_variation_attributes' ] as $attr => $val ) {

                        if ( $val == '' ) {

                            $valid_default_variation_attr = false;
                            break;

                        }

                    }

                    if ( $bundled_item->item_data[ 'override_default_variation_attributes' ] == 'yes' && $valid_default_variation_attr ) {

                        // Product bundle saves attribute data value incorrectly
                        // Ex. value of Blue is saved as blue
                        $variation_data = TEO_Helper::get_variation_data_by_attributes( $variable_product_id , $bundled_item->item_data[ 'default_variation_attributes' ] );

                        if ( $variation_data ) {

                            foreach ( $variation_data[ 'attributes' ] as $attribute => $value )
                                $_POST[ 'bundle_attribute_' . $attribute . '_' . $item_id ] = $value;

                            $_POST[ 'bundle_variation_id_' . $item_id ] = $variation_data[ 'variation_id' ];

                        } else
                            wc_add_notice( sprintf( __( 'Please provide default selected attributes for the bundle component variable product %1$s' , 'timed-email-offers' ) , get_the_title( $variable_product_id ) ) , 'error' );

                    } else {

                        // We use the default attributes of the variable product component if there is any
                        $variable_product   = wc_get_product( $variable_product_id );
                        $default_attributes = $variable_product->get_variation_default_attributes();

                        // There are instances where the admin only supplly default values for partial set of attributes.
                        // We need them to supply defaults to all attributes, that's why we check if the 2 arrays below is the same length
                        // Coz unlike 'default_variation_attributes' where if an attribute has no value, it still add that attribute to the array with no value
                        // 'get_variation_default_attributes' just add on the array the attributes with value, disregarding the other ones with no default value.
                        if ( count( $default_attributes ) == count( $bundled_item->item_data[ 'default_variation_attributes' ] ) ) {

                            // Product bundle saves attribute data value incorrectly
                            // Ex. value of Blue is saved as blue
                            $variation_data = TEO_Helper::get_variation_data_by_attributes( $variable_product_id , $default_attributes );

                            if ( $variation_data ) {

                                foreach ( $variation_data[ 'attributes' ] as $attribute => $value )
                                    $_POST[ 'bundle_attribute_' . $attribute . '_' . $item_id ] = $value;

                                $_POST[ 'bundle_variation_id_' . $item_id ] = $variation_data[ 'variation_id' ];

                            } else
                                wc_add_notice( sprintf( __( 'Please provide default selected attributes for the bundle component variable product %1$s' , 'timed-email-offers' ) , $variable_product->get_title() ) , 'error' );

                        } else
                            wc_add_notice( sprintf( __( 'Please provide default selected attributes for the bundle component variable product %1$s' , 'timed-email-offers' ) , $variable_product->get_title() ) , 'error' );

                    }

                } // if ( $bundled_item->product->product_type == 'variable' )

            }

            $_POST[ 'quantity' ]    = $product_data[ 'product-quantity' ];
            $_POST[ 'add-to-cart' ] = $product_data[ 'product-id' ];

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

            // Get bundle product additional info
            add_filter( 'teo_get_bundle_product_additional_info' , array( $this , 'get_product_additional_info' ) , 10 , 2 );


            // [Offer Condition]

            // Check whether to condition check product order item
            add_filter( 'teo_condition_check_simple_product_order_item' , array( $this , 'condition_check_product_order_item' ) , 10 , 4 );
            add_filter( 'teo_condition_check_variable_product_order_item' , array( $this , 'condition_check_product_order_item' ) , 10 , 4 );
            add_filter( 'teo_condition_check_bundle_product_order_item' , array( $this , 'condition_check_product_order_item' ) , 10 , 4 );
            add_filter( 'teo_condition_check_composite_product_order_item' , array( $this , 'condition_check_product_order_item' ) , 10 , 4 );

            // Get quantity in order entry text for 'bundle' type product
            add_filter( 'teo_bundle_product_quantity_in_order_entry_text' , array( $this , 'product_quantity_in_order_entry_text' ) , 10 , 2 );

            // Check bundle product condition is meet
            add_filter( 'teo_bundle_product_type_condition_check' , array( $this , 'product_condition_check' ) , 10 , 3 );


            // [Offer Accept Action]

            // Get bundle product to add in order entry text
            add_filter( 'teo_bundle_product_to_add_entry_text' , array( $this , 'product_to_add_entry_text' ) , 10 , 2 );

            // Add bundle product to order on executing offer accept action if necessary
            add_filter( 'teo_add-products-to-cart_action_add_bundle_product_to_cart' , array( $this , 'accept_offer_action_add_bundle_product_to_cart' ) , 10 , 6 );

        }

    }

}
