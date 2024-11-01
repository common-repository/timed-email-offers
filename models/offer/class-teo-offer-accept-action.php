<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Offer_Accept_Action' ) ) {

    /**
     * Model that houses the logic of accept offer action.
     *
     * Class TEO_Offer_Accept_Action
     */
    final class TEO_Offer_Accept_Action {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Offer_Accept_Action.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Accept_Action
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

        /**
         * Property that houses the logic of the various helper functions related to the shop's coupons.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Coupon
         */
        private $_coupon;




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
         * TEO_Offer_Accept_Action constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Accept_Action model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];
            $this->_product          = $dependencies[ 'TEO_Product' ];
            $this->_coupon           = $dependencies[ 'TEO_Coupon' ];

        }

        /**
         * Ensure that only one instance of TEO_Offer_Accept_Action is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Accept_Action model.
         * @return TEO_Offer_Accept_Action
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Execute 'add-products-to-cart' accept offer action.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_data
         * @param $offer_id
         * @param $order_id
         * @param $email_token
         */
        public function execute_add_products_to_cart_accept_offer_action( $offer_data , $offer_id , $order_id , $email_token ) {

            $products_not_added_to_cart = array();

            foreach ( $offer_data as $product_data ) {

                $add_product_to_cart     = apply_filters( 'teo_add-products-to-cart_action_maybe_add_product_to_cart_flag' , true , $offer_data , $offer_id , $order_id , $email_token );
                $additional_product_data = apply_filters( 'teo_add_product_to_cart_additional_product_data' , ( isset( $product_data[ 'product-price' ] ) && $product_data[ 'product-price' ] ) ? array( 'teo_product_data' => $product_data ) : array() );

                if ( $add_product_to_cart ) {

                    if ( $product_data[ 'product-type' ] == 'simple' )
                        $cart_item_key = WC()->cart->add_to_cart( $product_data[ 'product-id' ] , $product_data[ 'product-quantity' ] , 0 , array() , $additional_product_data );
                    elseif ( $product_data[ 'product-type' ] == 'variable' ) {

                        $variation_info_arr = TEO_Helper::get_product_variations( array(
                            'variable_id'  => $product_data[ 'product-id' ],
                            'variation_id' => $product_data[ 'product-variation-id' ]
                        ) );

                        $variation_attr = isset( $variation_info_arr[ 0 ][ 'attributes' ][ $product_data[ 'product-variation-id' ] ] ) ? $variation_info_arr[ 0 ][ 'attributes' ][ $product_data[ 'product-variation-id' ] ] : array();

                        $cart_item_key = WC()->cart->add_to_cart( $product_data[ 'product-id' ] , $product_data[ 'product-quantity' ] , $product_data[ 'product-variation-id' ] , $variation_attr , $additional_product_data );

                    } else
                        $cart_item_key = apply_filters( 'teo_add-products-to-cart_action_add_' . $product_data[ 'product-type' ] . '_product_to_cart' , false , $product_data , $offer_id , $order_id , $email_token , $additional_product_data ); // For other unknown product types

                    if ( !$cart_item_key )
                        $products_not_added_to_cart[] = $product_data;

                }

            }

            if ( !empty( $products_not_added_to_cart ) )
                do_action( 'teo_add-products-to-cart_action_error_products_not_added_to_cart' , $products_not_added_to_cart , $offer_data );

        }

        /**
         * Execute 'apply-coupons-to-cart' accept offer action.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_data
         */
        public function execute_apply_coupons_to_cart_accept_offer_action( $offer_data , $offer_id , $order_id , $email_token ) {

            $coupons_not_applied_to_cart = array();

            foreach ( $offer_data as $coupon_data ) {

                $coupon_code = get_the_title( $coupon_data[ 'coupon-id' ] );

                if ( !WC()->cart->add_discount( $coupon_code ) )
                    $coupons_not_applied_to_cart[] = $coupon_data;

            }

            if ( !empty( $coupons_not_applied_to_cart ) )
                do_action( 'teo_apply-coupons-to-cart_action_error_coupons_not_applied_to_cart' , $coupons_not_applied_to_cart , $offer_data );

        }

        /**
         * Get new accept offer action markup.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $args
         * @return mixed
         */
        public function get_new_accept_offer_action_markup( $offer_id , $args ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-get_new_accept_offer_action_markup-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-get_new_accept_offer_action_markup-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( !is_array( $args ) || !isset( $args[ 'action_type' ] ) )
                return new WP_Error( 'teo-get_new_accept_offer_action_markup-invalid-args' , __( 'Invalid Args Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( $args[ 'action_type' ] == 'add-products-to-cart' ) {

                $all_products_select_options = $this->_product->get_products( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'product_url' => true ) , false );
                ob_start(); ?>

                <div id="add-products-to-cart-action-container" class="accept-offer-action" data-action-type="add-products-to-cart">

                    <div class="action-controls">
                        <a class="remove-action"><?php _e( 'Remove Action' , 'timed-email-offers' ); ?></a>
                    </div>

                    <div class="fields">

                        <div class="field-set product-filter-field-set">

                            <span class="meta" style="display: none !important;">
                                <span class="product-type"></span>
                            </span>

                            <label for="add-products-to-cart-filter"><?php _e( 'Add product to cart' , 'timed-email-offers' ); ?></label>
                            <select id="add-products-to-cart-filter" class="product-filter-control" style="min-width: 340px;" data-placeholder="<?php _e( 'Please select a product...' , 'timed-email-offers' ); ?>">
                                <?php echo $all_products_select_options; ?>
                            </select>

                        </div>

                        <div class="field-set product-quantity-field-set">
                            <label for="add-products-to-cart-quantity"><?php _e( 'Quantity' , 'timed-email-offers' ); ?></label>
                            <input type="number" id="add-products-to-cart-quantity" value="1" min="1">
                        </div>

                        <?php do_action( 'teo_add-products-to-cart_additional_controls' ); ?>

                        <div class="field-set button-field-set">
                            <input type="button" id="add-product-to-cart-btn" class="button button-primary" value="<?php _e( 'Add' , 'timed-email-offers' ); ?>">
                            <input type="button" id="add-product-to-cart-edit-button" class="button button-primary" value="<?php _e( 'Edit' , 'timed-email-offers' ); ?>">
                            <input type="button" id="cancel-add-product-cart-edit-button" class="button button-secondary" value="<?php _e( 'Cancel' , 'timed-email-offers' ); ?>">
                            <span class="spinner"></span>
                        </div>

                        <div style="clear: both; float: none; display: block;"></div>

                    </div>

                    <table id="add-products-to-cart-table" class="wp-list-table widefat fixed striped" cellspacing="0" width="100%">

                        <thead>
                            <tr>
                                <th class="product-heading"><?php _e( 'Product' , 'timed-email-offers' ); ?></th>
                                <th class="quantity-heading"><?php _e( 'Quantity' , 'timed-email-offers' ); ?></th>
                                <?php do_action( 'teo_add-products-to-cart_additional_column_heading_markup' ); ?>
                                <th class="controls-heading"></th>
                            </tr>
                        </thead>

                        <tbody class="the-list">
                            <tr class="no-items">
                                <td class="colspanchange" colspan="<?php echo $this->_plugin_constants->ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS(); ?>"><?php _e( 'No products added' , 'timed-email-offers' ); ?></td>
                            </tr>
                        </tbody>

                    </table>

                </div>

                <?php $mark_up = ob_get_clean();

                return $mark_up;

            } elseif ( $args[ 'action_type' ] == 'apply-coupons-to-cart' ) {

                $all_coupons_select_options = $this->_coupon->get_coupons( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'coupon_url' => true , 'coupon_type' => true , 'coupon_amount' => true ) , false );
                ob_start(); ?>

                <div id="apply-coupons-to-cart-action-container" class="accept-offer-action" data-action-type="apply-coupons-to-cart">

                    <div class="action-controls">
                        <a class="remove-action"><?php _e( 'Remove Action' , 'timed-email-offers' ); ?></a>
                    </div>

                    <div class="fields">

                        <div class="field-set coupons-filter-field-set">

                            <label for="apply-coupons-to-cart-filter"><?php _e( 'Apply coupon to cart' , 'timed-email-offers' ); ?></label>
                            <select id="apply-coupons-to-cart-filter" class="coupon-filter-control" style="min-width: 340px;" data-placeholder="<?php _e( 'Please select a coupon...' , 'timed-email-offers' ); ?>">
                                <?php echo $all_coupons_select_options; ?>
                            </select>

                        </div>

                        <?php do_action( 'teo_apply-coupons-to-cart_additional_controls' ); ?>

                        <div class="field-set button-field-set">
                            <input type="button" id="add-coupon-to-be-applied-to-cart-btn" class="button button-primary" value="<?php _e( 'Add' , 'timed-email-offers' ); ?>">
                            <input type="button" id="edit-coupon-to-be-applied-to-cart-btn" class="button button-primary" value="<?php _e( 'Edit' , 'timed-email-offers' ); ?>">
                            <input type="button" id="cancel-edit-coupon-to-be-applied-to-cart-btn" class="button button-secondary" value="<?php _e( 'Cancel' , 'timed-email-offers' ); ?>">
                            <span class="spinner"></span>
                        </div>

                        <div style="clear: both; float: none; display: block;"></div>

                    </div>

                    <table id="apply-coupons-to-cart-table" class="wp-list-table widefat fixed striped" cellspacing="0" width="100%">

                        <thead>
                            <tr>
                                <th class="coupon-heading"><?php _e( 'Coupon' , 'timed-email-offers' ); ?></th>
                                <th class="coupon-type-heading"><?php _e( 'Coupon Type' , 'timed-email-offers' ); ?></th>
                                <th class="coupon-amount-heading"><?php _e( 'Coupon Amount' , 'timed-email-offers' ); ?></th>
                                <?php do_action( 'teo_apply-coupons-to-cart_additional_column_heading_markup' ); ?>
                                <th class="controls-heading"></th>
                            </tr>
                        </thead>

                        <tbody class="the-list">
                            <tr class="no-items">
                                <td class="colspanchange" colspan="<?php echo $this->_plugin_constants->APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS(); ?>"><?php _e( 'No coupons added' , 'timed-email-offers' ); ?></td>
                            </tr>
                        </tbody>

                    </table>

                </div>

                <?php $mark_up = ob_get_clean();

                return $mark_up;

            } else {

                $mark_up = new WP_Error( 'teo-get_new_accept_offer_action_markup-unknown-error' , __( 'An unknown error occur when generating offer condition markup.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );
                return apply_filters( 'teo_' . $args[ 'action_type' ] . '_accept_offer_action_markup' , $mark_up , $offer_id , $args );

            }

        }

        /**
         * Generate product to add to cart entry markup.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function generate_product_to_add_entry_markup( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-generate_product_to_add_entry_markup-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-generate_product_to_add_entry_markup-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            $data = $this->validate_and_sanitize_product_to_add_entry_data( $data );

            if ( !$data )
                return new WP_Error( 'teo-generate_product_to_add_entry_markup-invalid-product-to-add-entry-data' , __( 'Invalid Product To Add Entry Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            ob_start(); ?>

            <tr>

                <td class="row-meta hidden">
                    <span class="product-type"><?php echo $data[ "product-type" ]; ?></span>
                    <span class="product-id"><?php echo $data[ "product-id" ]; ?></span>

                    <?php if ( isset( $data[ "product-variation-id" ] ) ) { ?>

                        <span class="product-variation-id"><?php echo $data[ "product-variation-id" ]; ?></span>

                    <?php } ?>

                    <span class="product-quantity"><?php echo $data[ "product-quantity" ]; ?></span>
                    <?php do_action( 'teo_add-products-to-cart_additional_meta_markup' ); ?>
                </td>

                <td class="product-text">

                    <?php $product_text = "";

                    if ( $data[ "product-type" ] == 'variable' ) {
                        // Variable Product

                        $variation_info_arr = TEO_Helper::get_product_variations( array(
                            'variable_id'  => $data[ 'product-id' ],
                            'variation_id' => $data[ "product-variation-id" ]
                        ) );

                        $product_text  = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';
                        $product_text .= '<div class="product-variation">' . $variation_info_arr[ 0 ][ 'text' ] . '</div>';

                    } else if ( $data[ "product-type" ] == 'simple' )
                        $product_text =  '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';
                    else
                        $product_text = apply_filters( 'teo_' . $data[ "product-type" ] . '_product_to_add_entry_text' , $product_text , $data );

                    echo  $product_text; ?>

                </td>

                <td class="product-quantity">
                    <?php echo $data[ "product-quantity" ]; ?>
                </td>

                <?php do_action( 'teo_add-products-to-cart_additional_column_data_markup' , $data ); ?>

                <td class="row-controls">
                    <span class="dashicons dashicons-edit edit-product"></span>
                    <span class="dashicons dashicons-no delete-product"></span>
                </td>

            </tr>

            <?php $mark_up = ob_get_clean();

            return $mark_up;

        }

        /**
         * Generate coupon to apply entry markup.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function generate_coupon_to_apply_entry_markup( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-generate_coupon_to_apply_entry_markup-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-generate_coupon_to_apply_entry_markup-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            $data = $this->validate_and_sanitize_coupon_to_apply_entry_data( $data );
            if ( !$data )
                return new WP_Error( 'teo-generate_coupon_to_apply_entry_markup-invalid-coupon-to-apply-entry-data' , __( 'Invalid Coupon To Apply Entry Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            $coupon_details = TEO_Helper::get_coupon_info( $data[ 'coupon-id' ] );
            $coupon_amount  = TEO_Helper::get_coupon_data( $coupon_details[ 'coupon_obj' ] , 'coupon_amount' );

            ob_start(); ?>

            <tr>

                <td class="row-meta hidden">
                    <span class="coupon-id"><?php echo $data[ 'coupon-id' ]; ?></span>
                    <?php do_action( 'teo_apply-coupons-to-cart_additional_meta_markup' ); ?>
                </td>
                <td class="coupon">
                    <?php $coupon_text = '<div class="coupon"><a href="' . $coupon_details[ 'coupon_url' ] . '" target="_blank">[ID : ' .  $data[ 'coupon-id' ] . '] ' . get_the_title( $data[ 'coupon-id' ] ) . '</a></div>';
                    $coupon_text = apply_filters( 'teo_coupon_to_apply_entry_text' , $coupon_text , $data , $coupon_details );
                    echo $coupon_text; ?>
                </td>
                <td class="coupon-type-text"><?php echo $coupon_details[ 'coupon_type_text' ]; ?></td>
                <td class="coupon-amount"><?php echo $coupon_amount; ?></td>

                <?php do_action( 'teo_apply-coupons-to-cart_additional_column_data_markup' , $data ); ?>

                <td class="row-controls">
                    <span class="dashicons dashicons-edit edit-coupon"></span>
                    <span class="dashicons dashicons-no delete-coupon"></span>
                </td>

            </tr>

            <?php $mark_up = ob_get_clean();

            return $mark_up;

        }

        /**
         * Save accept offer actions.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function save_accept_offer_actions( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-save_accept_offer_actions-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-save_accept_offer_actions-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( is_array( $data ) ) {

                $data = $this->validate_and_sanitize_accept_offer_actions_data( $data );
                if ( !$data )
                    return new WP_Error( 'teo-save_accept_offer_actions-invalid-accept-offer-actions-data' , __( 'Invalid Offer Accept Actions Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            } else
                $data = '';

            $data = apply_filters( 'before_save_accept_offer_actions' , $data , $offer_id );

            update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_ACCEPT_ACTIONS() , $data );

            return $data;

        }




        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

        /**
         * Validate and sanitize product to add accept offer action entry data.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $data
         * @return boolean
         */
        public function validate_and_sanitize_product_to_add_entry_data( $data ) {

            if ( is_array( $data ) &&
                 array_key_exists( 'product-type' , $data ) &&
                 array_key_exists( 'product-id' , $data ) && filter_var( $data[ 'product-id' ] , FILTER_VALIDATE_INT ) &&
                 array_key_exists( 'product-quantity' , $data ) && filter_var( $data[ 'product-quantity' ] , FILTER_VALIDATE_INT ) ) {

                if ( $data[ 'product-type' ] == 'variable' )
                    if ( !array_key_exists( 'product-variation-id' , $data ) || !filter_var( $data[ 'product-variation-id' ] , FILTER_VALIDATE_INT ) )
                        return false;

                $data = apply_filters( 'teo_additional_product_to_add_entry_data_validation' , $data );

                if ( $data ) {

                    $data[ 'product-type' ]     = filter_var( $data[ 'product-type' ] , FILTER_SANITIZE_STRING );
                    $data[ 'product-id' ]       = filter_var( $data[ 'product-id' ] , FILTER_SANITIZE_NUMBER_INT );
                    $data[ 'product-quantity' ] = filter_var( $data[ 'product-quantity' ] , FILTER_SANITIZE_NUMBER_INT );

                    if ( $data[ 'product-type' ] == 'variable' )
                        $data[ 'product-variation-id' ] = filter_var( $data[ 'product-variation-id' ] , FILTER_SANITIZE_NUMBER_INT );

                    $data = apply_filters( 'teo_additional_product_to_add_entry_data_sanitation' , $data );

                    return $data;

                } else
                    return false;

            } else
                return false;

        }

        /**
         * Validate and sanitize coupon to apply accept offer action entry data.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $data
         * @return boolean
         */
        public function validate_and_sanitize_coupon_to_apply_entry_data( $data ) {

            $coupon_types = wc_get_coupon_types();

            if ( is_array( $data ) &&
                 array_key_exists( 'coupon-id' , $data ) && filter_var( $data[ 'coupon-id' ] , FILTER_VALIDATE_INT ) &&
                 array_key_exists( 'coupon-url' , $data ) && filter_var( $data[ 'coupon-url' ] , FILTER_VALIDATE_URL ) &&
                 array_key_exists( 'coupon-type' , $data ) && array_key_exists( $data[ 'coupon-type' ] , $coupon_types ) &&
                 array_key_exists( 'coupon-type-text' , $data ) && in_array( $data[ 'coupon-type-text' ] , $coupon_types ) &&
                 array_key_exists( 'coupon-amount' , $data ) && filter_var( $data[ 'coupon-amount' ] , FILTER_VALIDATE_FLOAT ) ) {

                $data = apply_filters( 'teop_additional_coupon_to_apply_entry_data_validation' , $data );

                if ( $data ) {

                    $data[ 'coupon-id' ]        = filter_var( $data[ 'coupon-id' ] , FILTER_SANITIZE_NUMBER_INT );
                    $data[ 'coupon-url' ]       = filter_var( $data[ 'coupon-url' ] , FILTER_SANITIZE_URL );
                    $data[ 'coupon-type' ]      = filter_var( $data[ 'coupon-type' ] , FILTER_SANITIZE_STRING );
                    $data[ 'coupon-type-text' ] = filter_var( $data[ 'coupon-type-text' ] , FILTER_SANITIZE_STRING );
                    $data[ 'coupon-amount' ]    = filter_var( $data[ 'coupon-amount' ] , FILTER_SANITIZE_NUMBER_FLOAT );

                    $data = apply_filters( 'teop_additional_coupon_to_apply_entry_data_sanitation' , $data );

                    return $data;

                } else
                    return false;

            } else
                return false;

        }

        /**
         * Validate and sanitize accept offer actions data.
         *
         * @since $data
         * @access public
         *
         * @param $data
         * @return boolean
         */
        public function validate_and_sanitize_accept_offer_actions_data( $data ) {

            if ( is_array( $data ) ) {

                if ( isset( $data[ 'add-products-to-cart' ] ) ) {

                    if ( !is_array( $data[ 'add-products-to-cart' ] ) )
                        return false;

                    foreach ( $data[ 'add-products-to-cart' ] as $index => $product_to_add_data ) {

                        $data[ 'add-products-to-cart' ][ $index ] = $this->validate_and_sanitize_product_to_add_entry_data( $product_to_add_data );

                        if ( !$data[ 'add-products-to-cart' ][ $index ] )
                            return false;

                    }

                }

                if ( isset( $data[ 'apply-coupons-to-cart' ] ) ) {

                    if ( !is_array( $data[ 'apply-coupons-to-cart' ] ) )
                        return false;

                    foreach ( $data[ 'apply-coupons-to-cart' ] as $coupon_to_apply_data ) {

                        if ( !isset( $coupon_to_apply_data[ 'coupon-id' ] ) || !filter_var( $coupon_to_apply_data[ 'coupon-id' ] , FILTER_VALIDATE_INT ) )
                            return false;

                    }

                }

                $data = apply_filters( 'additional_accept_offer_actions_data_validation' , $data );

                if ( $data ) {

                    if ( isset( $data[ 'apply-coupons-to-cart' ] ) ) {

                        foreach ( $data[ 'apply-coupons-to-cart' ] as $index => $coupon_to_apply_data )
                            $data[ 'apply-coupons-to-cart' ][ $index ][ 'coupon-id' ] = filter_var( $coupon_to_apply_data[ 'coupon-id' ] , FILTER_SANITIZE_NUMBER_INT );

                    }

                    $data = apply_filters( 'additional_accept_offer_actions_data_sanitation' , $data );

                    return $data;

                } else
                    return false;

            } else
                return false;

        }

    }

}
