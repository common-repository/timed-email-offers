<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_AJAX_Interfaces' ) ) {

    /**
     * Property that wraps the ajax interfaces of the plugin.
     *
     * Class TEO_AJAX_Interfaces
     */
    final class TEO_AJAX_Interfaces {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Single main instance of Timed Email Offers plugin.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_AJAX_Interfaces
         */
        private static $_instance;

        /**
         * Property that holds various constants utilized throughout the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Constants
         */
        private $_plugin_constants;

        /**
         * Property that houses the logic of the various helper functions related to the shop's product.
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

        /**
         * Property that wraps the logic of timed email offer templates.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Template
         */
        private $_offer_template;

        /**
         * Property that wraps the logic of timed email offer conditions.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Condition
         */
        private $_offer_condition;

        /**
         * Property that wraps the logic of accept offer actions.
         *
         * @since 1.0.0
         * @access private
         *
         * @var TEO_Offer_Accept_Action
         */
        private $_accept_offer_action;

        /**
         * Property that wraps the logic of decline offer actions.
         *
         * @since 1.0.0
         * @access private
         *
         * @var TEO_Offer_Decline_Action
         */
        private $_decline_offer_action;

        /**
         * Property that wraps the logic of timed email offer recipients.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Recipient
         */
        private $_offer_recipient;

        /**
         * Property that wraps the logic of unsubscribe page.
         *
         * @since 1.0.0
         * @access private
         *
         * @var TEO_Unsubscribe_Page
         */
        private $_unsubscribe_page;

        


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
        public function __clone() {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ) , '1.0.0' );

        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.0.0
         * @access public
         */
        public function __wakeup() {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ) , '1.0.0' );

        }

        /**
         * TEO_AJAX_Interfaces constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants     = $dependencies[ 'TEO_Constants' ];
            $this->_product              = $dependencies[ 'TEO_Product' ];
            $this->_coupon               = $dependencies[ 'TEO_Coupon' ];
            $this->_offer_template       = $dependencies[ 'TEO_Offer_Template' ];
            $this->_offer_condition      = $dependencies[ 'TEO_Offer_Condition' ];
            $this->_accept_offer_action  = $dependencies[ 'TEO_Offer_Accept_Action' ];
            $this->_decline_offer_action = $dependencies[ 'TEO_Offer_Decline_Action' ];
            $this->_offer_recipient      = $dependencies[ 'TEO_Offer_Recipient' ];
            $this->_unsubscribe_page     = $dependencies[ 'TEO_Unsubscribe_Page' ];

        }

        /**
         * Ensure that there is only one instance of TEO_AJAX_Interfaces is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return TEO_AJAX_Interfaces
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }




        /*
        |--------------------------------------------------------------------------
        | AJAX Callbacks
        |--------------------------------------------------------------------------
        */

        // Products

        /**
         * Get all products.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_get_products() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-get-products' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $args = $_POST[ 'args' ];

                $products = $this->_product->get_products( $args );

                if ( is_wp_error( $products ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => __( 'Failed to get products.' , 'timed-email-offers' )
                    );

                } else {

                    $response = array(
                        'status'   => 'success',
                        'products' => $products
                    );
                    
                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Get additional info about a product.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_get_product_additional_info() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-get-product-additional-info' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $product_id = filter_var( $_POST[ 'product_id' ] , FILTER_SANITIZE_NUMBER_INT );

                $product_additional_info = $this->_product->get_product_additional_info( $product_id );

                if ( is_wp_error( $product_additional_info ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => __( 'Failed to get product additional info.' , 'timed-email-offers' )
                    );

                } else {

                    $response = array(
                        'status'       => 'success',
                        'product_data' => $product_additional_info
                    );

                }          

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();


            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Get shop coupons.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_get_coupons() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-get-coupons' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $args = $_POST[ 'args' ];

                $coupons = $this->coupon->get_coupons( $args );

                if ( is_wp_error( $coupons ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => __( 'Failed to get coupons.' , 'timed-email-offers' )
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'coupons' => $coupons
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }


        

        /*
        |--------------------------------------------------------------------------
        | Offer Templates
        |--------------------------------------------------------------------------
        */

        /**
         * Get timed email offers email templates.
         * Data compatible with datatables.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $offer_id
         * @param bool|true $ajax_call
         * @return array
         */
        public function teo_get_offer_email_templates() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-get-offer-email-templates' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_REQUEST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );

                $response = $this->_offer_template->get_offer_email_templates( $offer_id );

                if ( is_wp_error( $response ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => __( 'Failed to retrieve offer email templates.' , 'timed-email-offers' )
                    );

                }
                
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Add offer template.
         *
         * @since 1.0.0
         * @access private
         */
        public function teo_add_offer_template() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-add-offer-template' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id      = filter_var( $_REQUEST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );;
                $template_data = apply_filters( 'teo_add_offer_template_data' , $_POST[ 'template_data' ] );

                $new_index = $this->_offer_template->add_offer_template( $offer_id , $template_data );
                
                if ( is_wp_error( $new_index ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $new_index->get_error_message()
                    );

                } else {

                    $response = array(
                        'status' => 'success',
                        'index'  => $new_index
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Get offer template info.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_get_offer_template_info() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-get-offer-template-info' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $index    = filter_var( $_POST[ 'index' ] , FILTER_SANITIZE_NUMBER_INT );

                $template_info = $this->_offer_template->get_offer_template_info( $offer_id , $index );

                if ( is_wp_error( $template_info ) ) {

                    $response = array(
                        'status' => 'fail',
                        'error_message' => $template_info->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'        => 'success',
                        'template_data' => $template_info
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Edit offer template.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_edit_offer_template() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-edit-offer-template' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id      = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $index         = filter_var( $_POST[ 'index' ] , FILTER_SANITIZE_NUMBER_INT );
                $template_data = apply_filters( 'teo_edit_offer_template_data' , $_POST[ 'template_data' ] );

                $result = $this->_offer_template->edit_offer_template( $offer_id , $index , $template_data );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = array( 'status' => 'success' );

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );

        }

        /**
         * Delete offer template.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_delete_offer_template() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-delete-offer-template' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $index    = filter_var( $_POST[ 'index' ] , FILTER_SANITIZE_NUMBER_INT );

                $result = $this->_offer_template->delete_offer_template( $offer_id , $index );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = array( 'status' => 'success' );

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Send test email for a specific offer template.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_send_offer_test_email() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-send-offer-test-email' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );
                
                $offer_id        = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $template_index  = filter_var( $_POST[ 'template_index' ] , FILTER_SANITIZE_NUMBER_INT );
                $recipient_email = filter_var( $_POST[ 'recipient_email' ] , FILTER_SANITIZE_EMAIL );

                $result = $this->_offer_template->send_offer_test_email( $offer_id , $template_index , $recipient_email );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = array( 'status' => 'success' );

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }




        /*
        |--------------------------------------------------------------------------
        | Offer Conditions
        |--------------------------------------------------------------------------
        */

        /**
         * Generate offer condition group markup.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_generate_offer_condition_group_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-generate-offer-condition-group-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );
                
                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $args     = apply_filters( 'teo_offer_condition_group_args' , $_POST[ 'args' ] , $offer_id );

                $mark_up = $this->_offer_condition->generate_offer_condition_group_markup( $offer_id , $args );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Get new offer condition markup.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_generate_offer_condition_markup() {
            
            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-generate-offer-condition-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $args     = apply_filters( 'teo_offer_condition_args' , $_POST[ 'args' ] , $offer_id );

                $mark_up = $this->_offer_condition->generate_offer_condition_markup( $offer_id , $args );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Generate markup for product quantity in order entry.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_generate_product_quantity_in_order_entry_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-generate-product-quantity-in-order-entry-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );
                
                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $data     = apply_filters( 'teo_product_quantity_in_cart_entry_data' , $_POST[ 'data' ] );

                $mark_up = $this->_offer_condition->generate_product_quantity_in_order_entry_markup( $offer_id , $data );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Save timed email offer conditions.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_save_timed_email_offer_conditions() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-save-timed-email-offer-conditions' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );
                
                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $data     = apply_filters( 'teo_save_sales_offer_conditions_data' , $_POST[ 'data' ] , $offer_id );

                $conditions_data = $this->_offer_condition->save_timed_email_offer_conditions( $offer_id , $data );

                if ( is_wp_error( $conditions_data ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $conditions_data->get_error_message()
                    );

                } else {

                    $response = array(
                        'status' => 'success',
                        'data'   => $conditions_data
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }




        /*
        |--------------------------------------------------------------------------
        | Offer Accept Actions
        |--------------------------------------------------------------------------
        */

        /**
         * Get new accept offer action markup.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_get_new_accept_offer_action_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-get-new-accept-offer-action-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $args     = $_POST[ 'args' ];

                $mark_up = $this->_accept_offer_action->get_new_accept_offer_action_markup( $offer_id , $args );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Generate product to add to cart entry markup.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_generate_product_to_add_entry_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-generate-product-to-add-entry-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );
                
                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $data     = apply_filters( 'teo_product_to_add_entry_data' , $_POST[ 'data' ] );

                $mark_up = $this->_accept_offer_action->generate_product_to_add_entry_markup( $offer_id , $data );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Generate coupon to apply entry markup.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $offer_id
         * @param null $data
         * @param bool|true $ajax_call
         * @return array
         */
        public function teo_generate_coupon_to_apply_entry_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-generate-coupon-to-apply-entry-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $data     = apply_filters( 'teo_coupon_to_apply_entry_data' , $_POST[ 'data' ] );

                $mark_up  = $this->_accept_offer_action->generate_coupon_to_apply_entry_markup( $offer_id , $data );

                if ( is_wp_error( $mark_up ) ) {
                    
                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );

        }

        /**
         * Save accept offer actions.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_save_accept_offer_actions() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-save-accept-offer-actions' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $data     = apply_filters( 'teo_save_accept_offer_actions_data' , $_POST[ 'data' ] , $offer_id );

                $action_data = $this->_accept_offer_action->save_accept_offer_actions( $offer_id , $data );

                if ( is_wp_error( $action_data ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $action_data->get_error_message()
                    );

                } else {

                    $response = array(
                        'status' => 'success',
                        'data'   => $action_data
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }
        
        
        
        
        /*
        |--------------------------------------------------------------------------
        | Offer Decline Actions
        |--------------------------------------------------------------------------
        */

        /**
         * Save decline offer actions.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_save_decline_offer_actions() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-save-decline-offer-actions' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );
                
                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $data     = apply_filters( 'teo_save_decline_offer_actions_data' , $_POST[ 'data' ] , $offer_id );

                $result = $this->_decline_offer_action->save_decline_offer_actions( $offer_id , $data );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = array( 'status' => 'success' );
                
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }




        /*
        |--------------------------------------------------------------------------
        | Offer Recipients
        |--------------------------------------------------------------------------
        */

        /**
         * Return offer recipients in format that is compatible with datatables library requires.
         * This data is then in turn populated to the offer recipients datatables on the admin.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_get_offer_recipients() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-get-offer-recipients' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );

                $data = $this->_offer_recipient->get_offer_recipients( $offer_id );

                if ( is_wp_error( $data ) ) {

                    $data = array(
                        'status'        => 'fail',
                        'error_message' => $data->get_error_message()
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $data );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }
        
        /**
         * Generate offer recipient data popup markup.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_generate_offer_recipient_data_popup_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-generate-offer-recipient-data-popup-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $order_id = filter_var( $_POST[ 'order_id' ] , FILTER_SANITIZE_NUMBER_INT );

                $mark_up = $this->_offer_recipient->generate_offer_recipient_data_popup_markup( $offer_id , $order_id );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status' => 'success',
                        'markup' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );

        }

        /**
         * Remove specific scheduled email from an offer recipient.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_remove_recipient_scheduled_email() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-remove-recipient-scheduled-email' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id           = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $order_id           = filter_var( $_POST[ 'order_id' ] , FILTER_SANITIZE_NUMBER_INT ); // int on the cron arg
                $unique_email_token = filter_var( $_POST[ 'unique_email_token' ] , FILTER_SANITIZE_STRING );

                $result = $this->_offer_recipient->remove_recipient_scheduled_email( $offer_id , $order_id , $unique_email_token );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = array( 'status' => 'success' );
                
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Remove recipient from offer.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_remove_recipient_from_offer() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-remove-recipient-from-offer' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $offer_id = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $order_id = filter_var( $_POST[ 'order_id' ] , FILTER_SANITIZE_NUMBER_INT ); // int on the cron arg

                $result = $this->_offer_recipient->remove_recipient_from_offer( $offer_id , $order_id );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'success',
                        'error_message' => $result->get_error_message()
                    );

                } else 
                    $response = array( 'status' => 'success' );
                
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }




        /*
        |--------------------------------------------------------------------------
        | Unsubscribe
        |--------------------------------------------------------------------------
        */

        /**
         * Get blacklist.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_get_blacklist() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-get-blacklist' , 'ajax-nonce' , false ) )
                    wp_die( 'Security Check Failed' , 'timed-email-offers' );

                $blacklist_type = filter_var( $_POST[ 'blacklist_type' ] , FILTER_SANITIZE_STRING );

                $data = $this->_unsubscribe_page->get_blacklist( $blacklist_type );

                if ( is_wp_error( $data ) ) {

                    $data = array(
                        'status'        => 'fail',
                        'error_message' => $data->get_error_message()
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $data );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Manually opt-out email.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_manually_opt_out_email() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-manually-opt-out-email' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

                $email = filter_var( $_POST[ 'email' ] , FILTER_SANITIZE_EMAIL );

                $result = $this->_unsubscribe_page->manually_opt_out_email( $email );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = array( 'status' => 'success' );

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }

        /**
         * Un opt-out email from blacklist.
         *
         * @since 1.0.0
         * @access public
         */
        public function teo_un_opt_out_email() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'teo-un-opt-out-email' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );
                
                $email = filter_var( $_POST[ 'email' ] , FILTER_SANITIZE_EMAIL );

                $result = $this->_unsubscribe_page->un_opt_out_email( $email );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );
                    
                } else
                    $response = array( 'status' => 'success' );
                
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
            
        }




        /*
        |--------------------------------------------------------------------------
        | Email Tracking
        |--------------------------------------------------------------------------
        */

        /**
         * Record offer scheduled email view.
         *
         * @since 1.2.0
         * @access public
         */
        public function record_offer_scheduled_email_view() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( filter_var( $_REQUEST[ 'offer_id' ] , FILTER_VALIDATE_INT ) && filter_var( $_REQUEST[ 'order_id' ] , FILTER_VALIDATE_INT ) && $_REQUEST[ 'email_token' ] ) {

                    // Extract the data                    
                    $offer_id         = filter_var( $_REQUEST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                    $order_id         = filter_var( $_REQUEST[ 'order_id' ] , FILTER_SANITIZE_NUMBER_INT );
                    $email_token      = filter_var( $_REQUEST[ 'email_token' ] , FILTER_SANITIZE_STRING );
                    $current_datetime = date( 'Y-m-d H:i:s' , current_time( 'timestamp' ) );
                    $recipient_id     = TEO_Helper::get_offer_recipient_id( $order_id , $offer_id );

                    if ( $recipient_id ) { // Meaning record exists, proceed

                        // Log the data
                        global $wpdb;

                        $wpdb->insert(
                            $this->_plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS(),
                            array( 'email_token' => $email_token , 'recipient_id' => $recipient_id , 'view_datetime' => $current_datetime ),
                            array( '%s' , '%d' , '%s' )
                        );

                    }

                }

                // Push out image
                header( 'Content-Type: image/png' );
                
                if ( ini_get( 'zlib.output_compression' ) )
                    ini_set( 'zlib.output_compression' , 'Off' );
                
                header( 'Pragma: public' ); // required
                header( 'Expires: 0' ); // no cache
                header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
                header( 'Cache-Control: private' , false );
                header( 'Content-Disposition: attachment; filename="blank.png"' );
                header( 'Content-Transfer-Encoding: binary' );
                header( 'Content-Length: ' . filesize( $this->_plugin_constants->PLUGIN_DIR_PATH() . 'images/blank.png' ) );	// provide file size
                readfile( $this->_plugin_constants->PLUGIN_DIR_PATH() . 'images/blank.png' ); // push it out
                exit();

            }

        }

    }

}