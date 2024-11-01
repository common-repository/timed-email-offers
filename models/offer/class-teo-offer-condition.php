<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Offer_Condition' ) ) {

    /**
     * Class TEO_Offer_Condition
     *
     * Model that houses the logic relating to offer conditions.
     *
     * @since 1.0.0
     */
    final class TEO_Offer_Condition {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Offer_Condition.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Condition
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

        /**
         * Property that houses the logic of the various helper functions related to the shop's products.
         * 
         * @since 1.0.0
         * @access private
         * @var TEO_Product
         */
        private $_product;




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

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ), '1.0.0' );

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
         * TEO_Offer_Condition constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Condition model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];
            $this->_product          = $dependencies[ 'TEO_Product' ];

        }

        /**
         * Ensure that only one instance of TEO_Offer_Condition is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Condition model.
         * @return TEO_Offer_Condition
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Generate offer condition group markup.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $args
         * @return mixed
         */
        public function generate_offer_condition_group_markup( $offer_id , $args ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-generate_offer_condition_group_markup-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-generate_offer_condition_group_markup-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );
            
            $offer_condition_types             = $this->_plugin_constants->TIMED_EMAIL_OFFER_CONDITION_TYPES();
            $offer_condition_types_simple_mode = $this->_plugin_constants->TIMED_EMAIL_OFFER_CONDITION_TYPES_SIMPLE_MODE();

            ob_start();

            if ( isset( $args[ 'show-condition-group-logic' ] ) ) { ?>

                <div class="offer-condition-group-logic">

                    <div class="controls">

                        <select class="condition-group-logic">
                            <option value="and"><?php _e( 'AND' , 'timed-email-offers' ); ?></option>
                            <option value="or"><?php _e( 'OR' , 'timed-email-offers' ); ?></option>
                        </select>

                    </div>

                </div>

            <?php } ?>

            <div class="offer-condition-group">

                <div class="offer-condition-group-actions">
                    <?php do_action( 'teo_offer_condition_group_additional_actions' ); ?>
                    <a class="remove-condition-group"><?php _e( 'Remove Condition Group' , 'timed-email-offers' ); ?></a>
                </div>

                <div class="empty-condition-group-container">
                    <p class="empty-condition-group-message"><?php _e( 'Empty Condition Group. Click <b>"Add Condition"</b> button to add condition.' , 'timed-email-offers' ); ?></p>
                </div>

                <div class="offer-condition-controls">

                    <div class="controls">

                        <?php do_action( 'teo_offer_condition_group_additional_controls' ); ?>

                        <select class="condition-types">
                            <?php foreach ( $offer_condition_types as $key => $text ) {

                                $disabled = '';
                                if ( $offer_condition_types_simple_mode && $key != 'product-quantity' ) {

                                    $disabled = 'disabled="disabled"';
                                    $text     = sprintf( __( '%1$s (PREMIUM)' , 'timed-email-offers' ) , $text );

                                } ?>

                                <option value="<?php echo $key; ?>" <?php echo $disabled; ?>><?php echo $text; ?></option>

                            <?php } ?>
                        </select>

                        <input type="button" class="show-add-condition-controls button button-secondary" value="<?php _e( 'Add Condition' , 'timed-email-offers' ); ?>">
                        <input type="button" class="add-condition button button-primary" value="<?php _e( 'Add' , 'timed-email-offers' ); ?>">
                        <input type="button" class="hide-add-condition-controls button button-secondary" value="<?php _e( 'Cancel' , 'timed-email-offers' ); ?>">

                        <span class="spinner"></span>

                    </div>

                </div>

            </div>

            <?php $mark_up = ob_get_clean();

            return $mark_up;

        }

        /**
         * Get new offer condition markup.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $args
         * @return mixed
         */
        public function generate_offer_condition_markup( $offer_id , $args ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-generate_offer_condition_markup-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-generate_offer_condition_markup-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );            
            
            if ( !is_array( $args ) || !isset( $args[ 'condition-type' ] ) )
                return new WP_Error( 'teo-generate_offer_condition_markup-invalid-args' , __( 'Invalid Args Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );
            
            $mark_up = new WP_Error( 'teo-generate_offer_condition_markup-unknown-error' , __( 'An unknown error occur when generating offer condition markup.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );
            return apply_filters( 'teo_' . $args[ 'condition-type' ] . '_offer_condition_markup' , $mark_up , $offer_id , $args );
            
        }

        /**
         * Save timed email offer conditions.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function save_timed_email_offer_conditions( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-save_timed_email_offer_conditions-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-save_timed_email_offer_conditions-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );            

            // $data here is condition_groups

            if ( is_array( $data ) ) {

                foreach ( $data as $i => $condition_group ) {

                    foreach ( $condition_group as $ii => $conditions ) {
                        
                        foreach ( $conditions as $iii => $condition ) {

                            if ( !isset( $condition[ 'condition-type' ] ) )
                                return new WP_Error( 'teo-save_timed_email_offer_conditions-invalid-offer-condition' , __( 'Invalid Offer Condition' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

                            if ( $condition[ 'condition-type' ]  == 'product-quantity' ) {

                                if ( !isset( $condition[ 'product-conditions' ] ) )
                                    return new WP_Error( 'teo-save_timed_email_offer_conditions-invalid-offer-condition' , __( 'Invalid Offer Condition' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );
                                
                                foreach ( $condition[ 'product-conditions' ] as $iiii => $product_condition_data ) {

                                    $data[ $i ][ $ii ][ $iii ][ 'product-conditions' ][ $iiii ] = $this->validate_and_sanitize_product_quantity_in_order_entry_data( $product_condition_data );
                                    if ( !$data[ $i ][ $ii ][ $iii ][ 'product-conditions' ][ $iiii ] )
                                        return new WP_Error( 'teo-generate_product_quantity_in_order_entry_markup-invalid-product-quantity-in-order-entry-data' , __( 'Invalid Product Quantity In Order Entry Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );                
                                    
                                }

                            } else {
                                
                                // Other Condition Types

                                $result_data = apply_filters( 'teo_' . $condition[ 'condition-type' ] . '_offer_condition_validation_and_sanitation' , $condition );

                                if ( is_wp_error( $result_data ) )
                                    return $result_data;
                                else
                                    $data[ $i ][ $ii ][ $iii ] = $result_data;
                                
                            }

                        }

                    }

                }

            } else
                $data = '';
            
            $data = apply_filters( 'teo_save_timed_email_offer_conditions' , $data , $offer_id );

            update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_CONDITIONS() , $data );

            return $data;

        }

        /**
         * Check if conditions of an offer are all successfully attained.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer
         * @param $order
         * @param $customer
         * @return boolean True if all conditions are attained, False otherwise
         */
        public function check_offer_condition( $offer , $order , $customer ) {

            $offer_conditions = get_post_meta( $offer->ID , $this->_plugin_constants->POST_META_OFFER_CONDITIONS() , true );
            if ( !is_array( $offer_conditions ) )
                $offer_conditions = array();

            if ( !empty( $offer_conditions ) ) {

                // Retrieve order contents
                $order_items = $order->get_items();

                $condition_groups_attained = true;

                foreach ( $offer_conditions as $condition_group ) {

                    $condition_group_logic = isset( $condition_group[ 'condition-group-logic' ] ) ? $condition_group[ 'condition-group-logic' ] : null;
                    $conditions_attained   = true;

                    foreach( $condition_group[ 'conditions' ] as $condition ) {

                        $condition_logic = isset( $condition[ 'condition-logic' ] ) ? $condition[ 'condition-logic' ] : null;
                        $conditions_attained = apply_filters( 'teo_check_' . $condition[ 'condition-type' ] . '_offer_condition' , $conditions_attained , $condition , $condition_logic , $order_items , $offer , $order , $customer );                        
                        
                    }

                    if ( $condition_group_logic ) {

                        if ( $condition_group_logic == 'and' )
                            $condition_groups_attained = $condition_groups_attained && $conditions_attained;
                        elseif ( $condition_group_logic == 'or' )
                            $condition_groups_attained = $condition_groups_attained || $conditions_attained;

                    } else
                        $condition_groups_attained = $conditions_attained;

                }

                $timed_email_offer_conditions_attained = $condition_groups_attained;

            } else
                $timed_email_offer_conditions_attained = true;

            return apply_filters( 'teo_timed_email_offer_conditions_attained' , $timed_email_offer_conditions_attained , $offer , $order , $customer );

        }




        /*
        |--------------------------------------------------------------------------
        | Product Quantity
        |--------------------------------------------------------------------------
        */

        /**
         * Get [product-quantity] offer condition markup.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $mark_up
         * @param $offer_id
         * @param $args
         */
        public function product_quantity_offer_condition_markup( $mark_up , $offer_id , $args ) {

            $all_products_select_options          = $this->_product->get_products( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'product_url' => true ) );
            $logic_conditions                     = $this->_plugin_constants->LOGIC_CONDITIONS();
            $product_conditions                   = array();
            $views_path                           = $this->_plugin_constants->VIEWS_ROOT_PATH();
            $product_in_order_table_total_columns = $this->_plugin_constants->PRODUCT_QUANTITY_IN_ORDER_TABLE_TOTAL_COLUMNS();

            $show_condition_logic = isset( $args[ 'show-condition-logic' ] );
            $condition_logic_val  = "";

            ob_start();

            include ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/offer-conditions/view-product-quantity-in-order.php' );
            
            return ob_get_clean();

        }

        /**
         * Render [product-quantity] offer condition markup.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $condition
         */
        public function render_product_quantity_offer_condition_markup( $condition ) {

            $all_products_select_options          = $this->_product->get_products( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'product_url' => true ) );
            $logic_conditions                     = $this->_plugin_constants->LOGIC_CONDITIONS();
            $product_conditions                   = $condition[ 'product-conditions' ];
            $views_path                           = $this->_plugin_constants->VIEWS_ROOT_PATH();
            $product_in_order_table_total_columns = $this->_plugin_constants->PRODUCT_QUANTITY_IN_ORDER_TABLE_TOTAL_COLUMNS();

            if ( isset( $condition[ 'condition-logic' ] ) ) {

                $show_condition_logic = true;
                $condition_logic_val  = $condition[ 'condition-logic' ];

            } else
                $show_condition_logic = false;

            ob_start();

            include ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/offer-conditions/view-product-quantity-in-order.php' );
            
            echo ob_get_clean();

        }

        /**
         * Generate markup for product quantity in order entry.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function generate_product_quantity_in_order_entry_markup( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-generate_product_quantity_in_order_entry_markup-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );
            
            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-generate_product_quantity_in_order_entry_markup-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );
            
            $data = $this->validate_and_sanitize_product_quantity_in_order_entry_data( $data );

            if ( !$data )
                return new WP_Error( 'teo-generate_product_quantity_in_order_entry_markup-invalid-product-quantity-in-order-entry-data' , __( 'Invalid Product Quantity In Order Entry Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );                
            
            $logic_conditions = $this->_plugin_constants->LOGIC_CONDITIONS();

            ob_start(); 

            include ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/offer-conditions/view-product-to-check-in-order-entry.php' );

            return ob_get_clean();
            
        }

        /**
         * Check [product-quantity] offer condition.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $conditions_attained
         * @param $condition
         * @param $condition_logic
         * @param $order_items
         * @param $offer
         * @param $order
         * @param $customer
         * @return boolean
         */
        public function check_product_quantity_offer_condition( $conditions_attained , $condition , $condition_logic , $order_items , $offer , $order , $customer ) {

            $product_condition_attained = true;

            if ( empty( $order_items ) )
                $product_condition_attained = false; // Empty cart meaning condition automatically fails

            foreach ( $condition[ 'product-conditions' ] as $product_condition ) {

                if ( $product_condition_attained === false )
                    break; // No point in continuing. We are using && here and if this variable is false then we will always gets false

                $product_id         = $product_condition[ 'product-id' ];
                $quantity_condition = $product_condition[ 'product-quantity-condition' ];
                $quantity           = $product_condition[ 'product-quantity' ];

                if ( ( $quantity_condition == '<' && $quantity <= 1 ) || ( $quantity_condition == '=' && $quantity == 0 ) ) {

                    // Product not in cart condition.
                    // Meaning this product must not be in the cart

                    $product_in_order = false;

                    if ( $product_condition[ 'product-type' ] == 'simple' ) {

                        foreach ( $order_items as $order_item ) {

                            if ( $order_item[ 'product_id' ] == $product_id ) {
                                $product_in_order = true;
                                break;
                            }

                        }

                    } elseif ( $product_condition[ 'product-type' ] == 'variable' ) {

                        $variations = $product_condition[ 'product-variation-id' ];

                        if ( in_array( 'any' , $variations ) ) {

                            foreach ( $order_items as $order_item ) {

                                if ( $order_item[ 'product_id' ] == $product_id ) {
                                    $product_in_order = true;
                                    break;
                                }

                            }

                        } else {

                            foreach ( $order_items as $order_item ) {

                                if ( in_array( $order_item[ 'variation_id' ] , $variations ) ) {
                                    $product_in_order = true;
                                    break;
                                }

                            }

                        }

                    } else
                        $product_in_order = apply_filters( 'teo_check_if_product_is_in_order_' . $product_condition[ 'product-type' ] , $product_in_order , $order_items );

                    if ( !$product_in_order )
                        $product_condition_attained = $product_condition_attained && true;
                    else
                        $product_condition_attained = $product_condition_attained && false;

                    continue;

                } else {

                    if ( $product_condition[ 'product-type' ] == 'simple' ) {

                        $product_in_order = false;

                        foreach ( $order_items as $order_item ) {

                            $process_simple_product_order_item = true;
                            $process_simple_product_order_item = apply_filters( 'teo_condition_check_simple_product_order_item' , $process_simple_product_order_item , $order_item , $order_items , $product_condition );

                            if ( !$process_simple_product_order_item )
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

                    } elseif ( $product_condition[ 'product-type' ] == 'variable' ) {

                        $variations = $product_condition[ 'product-variation-id' ];

                        if ( in_array( 'any' , $variations ) ) { // Any variation

                            // Get total variable product quantity in cart
                            // Sum up the variations of that variable product in cart to get the total
                            $variable_product_total_quantity_in_cart = array();
                            foreach ( $order_items as $order_item ) {

                                $process_variable_product_order_item = true;
                                $process_variable_product_order_item = apply_filters( 'teo_condition_check_variable_product_order_item' , $process_variable_product_order_item , $order_item , $order_items , $product_condition );

                                if ( !$process_variable_product_order_item )
                                    continue;

                                if ( isset( $order_item[ 'variation_id' ] ) && $order_item[ 'variation_id' ] ) {

                                    if ( isset( $variable_product_total_quantity_in_cart[ $order_item[ 'product_id' ] ] ) )
                                        $variable_product_total_quantity_in_cart[ $order_item[ 'product_id' ] ] += ( int ) $order_item[ 'qty' ];
                                    else
                                        $variable_product_total_quantity_in_cart[ $order_item[ 'product_id' ] ] = ( int ) $order_item[ 'qty' ];

                                }

                            }

                        } else { // Specified specific variations

                            // Get the total quantity of a variable product in cart dependent on what variations are specified.
                            $variable_product_total_quantity_in_cart = array();
                            foreach ( $order_items as $order_item ) {

                                $process_variable_product_order_item = true;
                                $process_variable_product_order_item = apply_filters( 'teo_condition_check_variable_product_order_item' , $process_variable_product_order_item , $order_item , $order_items , $product_condition );

                                if ( !$process_variable_product_order_item )
                                    continue;

                                if ( isset( $order_item[ 'variation_id' ] ) && $order_item[ 'variation_id' ] && in_array( $order_item[ 'variation_id' ] , $variations ) ) {

                                    if ( isset( $variable_product_total_quantity_in_cart[ $order_item[ 'product_id' ] ] ) )
                                        $variable_product_total_quantity_in_cart[ $order_item[ 'product_id' ] ] += ( int ) $order_item[ 'qty' ];
                                    else
                                        $variable_product_total_quantity_in_cart[ $order_item[ 'product_id' ] ] = ( int ) $order_item[ 'qty' ];

                                }

                            }

                        }

                        switch ( $quantity_condition ) {

                            case '=':

                                if ( isset( $variable_product_total_quantity_in_cart[ $product_id ] ) && $variable_product_total_quantity_in_cart[ $product_id ] == $quantity )
                                    $product_condition_attained = $product_condition_attained && true;
                                else
                                    $product_condition_attained = $product_condition_attained && false;
                                break;

                            case '!=':

                                if ( isset( $variable_product_total_quantity_in_cart[ $product_id ] ) && $variable_product_total_quantity_in_cart[ $product_id ] != $quantity )
                                    $product_condition_attained = $product_condition_attained && true;
                                else
                                    $product_condition_attained = $product_condition_attained && false;
                                break;

                            case '>':

                                if ( isset( $variable_product_total_quantity_in_cart[ $product_id ] ) && $variable_product_total_quantity_in_cart[ $product_id ] > $quantity )
                                    $product_condition_attained = $product_condition_attained && true;
                                else
                                    $product_condition_attained = $product_condition_attained && false;
                                break;

                            case '<':

                                if ( isset( $variable_product_total_quantity_in_cart[ $product_id ] ) && $variable_product_total_quantity_in_cart[ $product_id ] < $quantity )
                                    $product_condition_attained = $product_condition_attained && true;
                                else
                                    $product_condition_attained = $product_condition_attained && false;
                                break;

                        } // end switch

                    } else
                        $product_condition_attained = apply_filters( 'teo_' . $product_condition[ 'product-type' ] . '_product_type_condition_check' , $product_condition_attained , $product_condition , $order_items ); // For extensibility of non-woocommerce native product types

                }

            }

            if ( $condition_logic ) {

                if ( $condition_logic == 'and' )
                    $conditions_attained = $conditions_attained && $product_condition_attained;
                elseif ( $condition_logic == 'or' )
                    $conditions_attained = $conditions_attained || $product_condition_attained;
            
            } else
                $conditions_attained = $product_condition_attained;
            
            return $conditions_attained;
            
        }




        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

        /**
         * Validate and sanitize [product-quantity-in-order] condtion entry data.
         *
         * @since 1.0.0
         * @access public
         *
         * @return boolean
         */
        public function validate_and_sanitize_product_quantity_in_order_entry_data( $data ) {
            
            if ( is_array( $data ) &&
                 array_key_exists( 'product-type' , $data ) && 
                 array_key_exists( 'product-id' , $data ) && filter_var( $data[ 'product-id' ] , FILTER_VALIDATE_INT ) && 
                 array_key_exists( 'product-quantity-condition' , $data ) && array_key_exists( $data[ 'product-quantity-condition' ] , $this->_plugin_constants->LOGIC_CONDITIONS() ) &&
                 array_key_exists( 'product-quantity' , $data ) && filter_var( $data[ 'product-quantity' ] , FILTER_VALIDATE_INT ) !== false ) {

                if ( $data[ 'product-type' ] == 'variable' && ( !array_key_exists( 'product-variation-id' , $data ) || !is_array( $data[ 'product-variation-id' ] ) ) )
                    return false;

                $data = apply_filters( 'teo_additional_product_quantity_in_order_entry_data_validation' , $data );

                if ( $data ) {

                    $data[ 'product-type' ]     = filter_var( $data[ 'product-type' ] , FILTER_SANITIZE_STRING );
                    $data[ 'product-id' ]       = filter_var( $data[ 'product-id' ] , FILTER_SANITIZE_NUMBER_INT );
                    $data[ 'product-quantity' ] = filter_var( $data[ 'product-quantity' ] , FILTER_SANITIZE_NUMBER_INT );

                    if ( $data[ 'product-type' ] == 'variable' ) {

                        foreach ( $data[ 'product-variation-id' ] as $index => $variation_id ) {

                            if ( $variation_id == 'any' )
                                continue;

                            $data[ 'product-variation-id' ][ $index ] = filter_var( $variation_id , FILTER_SANITIZE_NUMBER_INT );

                        }

                    }

                    $data = apply_filters( 'teo_additional_product_quantity_in_order_entry_data_sanitation' , $data );

                    return $data;

                } else
                    return false;
                
            } else
                return false;

        }

    }

}
