<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Accept_Offer_Page' ) ) {

    /**
     * Model that houses the logic of accept offer page.
     *
     * Class TEO_Accept_Offer_Page
     */
    final class TEO_Accept_Offer_Page {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Accept_Offer_Page.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Accept_Offer_Page
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
         * Property that wraps the logic relating to offer schedule.
         *
         * @since 1.2.0
         * @access private
         * @var TEO_Offer_Schedule
         */
        private $_offer_schedule;




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
         * TEO_Accept_Offer_Page constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Accept_Offer_Page model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];
            $this->_offer_schedule   = $dependencies[ 'TEO_Offer_Schedule' ];

        }

        /**
         * Ensure that only one instance of TEO_Accept_Offer_Page is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Accept_Offer_Page model.
         * @return TEO_Accept_Offer_Page
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Execute callback for accept offer page.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         */
        public function execute_page_callback() {

            global $wp_query;

            $accept_offer_page_id = get_option( $this->_plugin_constants->OPTION_ACCEPT_OFFER_PAGE_ID() , false );

            if ( is_page( $accept_offer_page_id ) &&
                isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_OFFER_ID() ] ) &&
                isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_ORDER_ID() ] ) &&
                isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_EMAIL_TOKEN() ] ) ) {
                
                $offer_id        = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_OFFER_ID() ];
                $order_id        = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_ORDER_ID() ];
                $email_token     = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_EMAIL_TOKEN() ];
                $offer_recipient = TEO_Helper::get_offer_recipient( $order_id , $offer_id );

                global $wpdb;

                // Check if offer is still valid
                do_action( 'teo_check_offer_validity_via_accept_link' , $offer_id , $order_id , $email_token , $offer_recipient );
                
                
                // -------------------------------------------------------------------------------------------------
                // Set offer recipients data
                // -------------------------------------------------------------------------------------------------

                $response_datetime = date( 'Y-m-d H:i:s' , current_time( 'timestamp' ) );

                // Set the specific offer email data accordingly
                $wpdb->update(
                    $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                    array( 'response_status' => 'accepted' , 'response_datetime' => $response_datetime ) , array( 'email_token' => $email_token ) ,
                    array( '%s' , '%s' ) , array( '%s' )
                );
                
                // Set the offer recipient data accordingly
                // There will be cases multiple offer emails are sent as long as offer order has not yet converted
                // Prevent changing offer stats if it was already converted
                if ( !$offer_recipient->created_order_id || !TEO_Helper::check_order_if_converted( $offer_recipient->created_order_id ) ) {

                    $wpdb->update(
                        $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS(),
                        array( 'response_status' => 'accepted' , 'response_datetime' => $response_datetime ),
                        array( 'recipient_id' => $offer_recipient->recipient_id ),
                        array( '%s' , '%s' ),
                        array( '%d' )
                    );

                    // Sync the new data to the current recicipient variable we are using
                    $offer_recipient->response_status   = 'accepted';
                    $offer_recipient->response_datetime = $response_datetime;

                }

                
                // -------------------------------------------------------------------------------------------------
                // Maybe unschedule any remaining scheduled emails
                // -------------------------------------------------------------------------------------------------
                
                if ( get_option( $this->_plugin_constants->OPTION_ONLY_UNSCHED_REMAINING_EMAILS_ON_OFFER_CONVERSION() , false ) != 'yes' ) {

                    $offer_scheduled_emails = TEO_Helper::get_offer_scheduled_emails( $offer_recipient->recipient_id );

                    foreach ( $offer_scheduled_emails as $scheduled_email ) {

                        $this->_offer_schedule->unschedule_email_offer_for_customer( $offer_id , $order_id , $scheduled_email->template_id , $scheduled_email->email_token );

                        if ( $scheduled_email->email_token == $email_token || $scheduled_email->send_status != 'pending' )
                            continue;

                        $wpdb->update(
                            $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                            array( 'send_status' => 'cancelled' , 'response_status' => 'na' ),
                            array( 'email_token' => $scheduled_email->email_token ),
                            array( '%s' , '%s' ),
                            array( '%s' )
                        );

                    }

                }


                // -------------------------------------------------------------------------------------------------
                // Execute accept offer actions
                // -------------------------------------------------------------------------------------------------

                // Initialize cart session
                WC()->session->set_customer_session_cookie( true );

                if ( get_option( $this->_plugin_constants->OPTION_RETAIN_CART_CONTENTS_ON_OFFER_ACCEPT() , false ) != 'yes' ) {

                    WC()->cart->empty_cart();
                    WC()->cart->remove_coupons();

                }

                $accept_offer_actions = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_ACCEPT_ACTIONS() , true );
                if ( !is_array( $accept_offer_actions ) )
                    $accept_offer_actions = array();

                foreach ( $accept_offer_actions as $action_id => $offer_data )
                    do_action( 'teo_execute_' . $action_id . '_accept_offer_action' , $offer_data , $offer_id , $order_id , $email_token );

                // Set the proper cart session data
                WC()->cart->set_session();
                

                // -------------------------------------------------------------------------------------------------
                // Set cookie or session to indicate this is a TEO order
                // This session data is used later when creating an order for this cart
                // -------------------------------------------------------------------------------------------------

                $session_data = array(
                    'offer-id'    => $offer_id,
                    'order-id'    => $order_id,
                    'email-token' => $email_token
                );

                WC()->session->set( $this->_plugin_constants->SESSION_TIMED_EMAIL_OFFER_ORDER() , $session_data );


                // -------------------------------------------------------------------------------------------------
                // We half to do this to avoid infinite loop on applying the offer
                // If we don't redirect them to new cart url, if they refresh the current url (with the offer args)
                // It will again apply the accept offer action on reload
                // This is due to the fact that offer links ( accept and decline ) are not one time use anymore
                // -------------------------------------------------------------------------------------------------

                $cart_url = WC()->cart->get_cart_url();
                $url_args = array( 'offer-id' => $offer_id , 'offer-processed' => 1 );
                $cart_url = add_query_arg( $url_args , $cart_url );

                wp_safe_redirect( $cart_url );
                
            } elseif ( is_page( $accept_offer_page_id ) ) {

                if ( isset( $_GET[ 'offer-id' ] ) && isset( $_GET[ 'offer-processed' ] ) && $_GET[ 'offer-processed' ] ) {

                    // -------------------------------------------------------------------------------------------------
                    // Display notice that offer is accepted successfully
                    // -------------------------------------------------------------------------------------------------

                    if ( get_option( $this->_plugin_constants->OPTION_TURN_OFF_NOTICE_ON_OFFER_ACCEPTANCE() , false ) != 'yes' )
                        add_action( 'woocommerce_before_cart' , array( $this , 'print_accept_offer_success_message' ) );

                }
                
            }
            
        }

        /**
         * Print accept offer success message.
         *
         * @since 1.0.0
         * @access public
         */
        public function print_accept_offer_success_message() {

            if ( isset( $_GET[ 'offer-id' ] ) && isset( $_GET[ 'offer-processed' ] ) && $_GET[ 'offer-processed' ] ) {

                $offer_id       = $_GET[ 'offer-id' ];
                $notice_message = apply_filters( 'teo_accept_offer_success_message' , sprintf( __( '<b>"%1$s"</b> has been accepted!' , 'timed-email-offers' ) , get_the_title( $offer_id ) ) , $offer_id );
                
                wc_print_notice( $notice_message , 'success' );
                
            }

        }

        /**
         * When creating a new order, if the order is a result of accepting a teo offer, then link the order and the offer together.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $order_id
         * @param $posted_data
         */
        public function link_wc_order_and_teo_offer_on_order_creation( $order_id , $posted_data ) {

            $teo_order_session_data = WC()->session->get( $this->_plugin_constants->SESSION_TIMED_EMAIL_OFFER_ORDER() );

            if ( isset( $teo_order_session_data ) &&
                 isset( $teo_order_session_data[ 'offer-id' ] ) &&
                 isset( $teo_order_session_data[ 'order-id' ] ) &&
                 isset( $teo_order_session_data[ 'email-token' ] ) ) {

                $teo_offer_id    = $teo_order_session_data[ 'offer-id' ];
                $teo_order_id    = $teo_order_session_data[ 'order-id' ];
                $teo_email_token = $teo_order_session_data[ 'email-token' ];

                $offer_recipient = TEO_Helper::get_offer_recipient( $teo_order_id , $teo_offer_id );

                // There will be cases multiple offer emails are sent as long as offer order has not yet converted
                // If offer already is converted, then ignore the remaining actions of offer orders
                if ( $offer_recipient && !TEO_Helper::check_order_if_converted( $offer_recipient->created_order_id ) ) {

                    $order = wc_get_order( $order_id );

                    // Add offer id and offer recipient order id as meta to this order for easier mapping of order and offer later
                    update_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_OFFER_ID() , $teo_offer_id );
                    update_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_OFFER_RECIPIENT_ID() , $teo_order_id );
                    update_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_OFFER_EMAIL_TOKEN() , $teo_email_token );

                }

                WC()->session->set( $this->_plugin_constants->SESSION_TIMED_EMAIL_OFFER_ORDER() , null );

                do_action( 'teo_after_linking_offer_created_order_to_spawning_offer' , $teo_offer_id , $teo_order_id , $teo_email_token , $order_id , $posted_data );

            }

        }

        /**
         * When order that is linked to an offer is updated, update as well the offer's order metadata.
         * The reason we are setting/unsetting the order_id of an offer recpient and scheduled email is
         * there will be instances that an offer spawns multple orders. But based on our discussion, an offer
         * will only recognize one spawned order, and that is, the first spawned order that converted.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         * 
         * @param $order_id
         * @param $old_status
         * @param $new_status
         */
        public function update_offer_order_metadata( $order_id , $old_status , $new_status ) {

            $offer_id       = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_OFFER_ID() , true );
            $offer_order_id = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_OFFER_RECIPIENT_ID() , true );
            $email_token    = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_OFFER_EMAIL_TOKEN() , true );

            if ( $offer_id && $offer_order_id && $email_token ) {
                
                $offer_recipient_id = TEO_Helper::get_offer_recipient_id( $offer_order_id , $offer_id );
                
                if ( $offer_recipient_id ) {

                    global $wpdb;

                    if ( $new_status == 'completed' ) {

                        $wpdb->update(
                            $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                            array( 'created_order_id' => $order_id ) , array( 'email_token' => $email_token ),
                            array( '%d' ) , array( '%s' )
                        );

                        $wpdb->update(
                            $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS(),
                            array( 'created_order_id' => $order_id ) , array( 'recipient_id' => $offer_recipient_id ),
                            array( '%d' ) , array( '%d' )
                        );

                        $offer_scheduled_emails = TEO_Helper::get_offer_scheduled_emails( $offer_recipient_id );

                        // Unschedule any remaining unsent emails for this offer
                        foreach ( $offer_scheduled_emails as $scheduled_email ) {

                            $this->_offer_schedule->unschedule_email_offer_for_customer( $offer_id , $offer_order_id , $scheduled_email->template_id , $scheduled_email->email_token );

                            if ( $scheduled_email->email_token == $email_token || $scheduled_email->send_status != 'pending' )
                                continue;
                            
                            $wpdb->update(
                                $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                                array( 'send_status' => 'cancelled' , 'response_status' => 'na' ) , array( 'email_token' => $scheduled_email->email_token ),
                                array( '%s' , '%s' ) , array( '%s' )
                            );

                        }

                    } elseif ( $new_status != 'completed' ) {

                        $wpdb->update(
                            $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                            array( 'created_order_id' => NULL ) , array( 'email_token' => $email_token ),
                            array( '%d' ) , array( '%s' )
                        );

                        $wpdb->update(
                            $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS(),
                            array( 'created_order_id' => NULL ) , array( 'recipient_id' => $offer_recipient_id ),
                            array( '%d' ) , array( '%d' )
                        );

                    }

                }

            }

        }

        /**
         * Check offer validity.
         * 
         * @since 1.1.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         * 
         * @param $offer_id
         * @param $order_id
         * @param $email_token
         * @param $offer_recipient
         */
        public function check_offer_validity( $offer_id , $order_id , $email_token , $offer_recipient ) {

            $invalid_offer_page_id  = get_option( $this->_plugin_constants->OPTION_INVALID_OFFER_PAGE_ID() );
            $invalid_offer_page_url = get_permalink( $invalid_offer_page_id );

            if ( get_post_status( $offer_id ) != 'publish' || !$offer_recipient || TEO_Helper::check_order_if_converted( $offer_recipient->created_order_id ) ) {

                $error_args             = array( 'error-key' => 'unavailable-offer' , 'error-message' => urlencode( apply_filters( 'teo_unavailable_offer_accessed_via_offer_accept_link_error_message' , __( 'Sorry, this offer is no longer available' , 'timed-email-offers' ) ) ) );
                $invalid_offer_page_url = add_query_arg( $error_args , $invalid_offer_page_url );
                
                do_action( 'teo_unavailable_offer_accessed_via_offer_accept_link' , $offer_id , $order_id , $email_token , $error_args );
                wp_safe_redirect( $invalid_offer_page_url );
                exit;

            } else {

                $recipient_id          = TEO_Helper::get_offer_recipient_id( $order_id , $offer_id );
                $offer_scheduled_email = TEO_Helper::get_offer_scheduled_email( $email_token , $recipient_id );

                if ( !$offer_scheduled_email || $offer_scheduled_email->send_status != 'sent' ) {

                    // Invalid query vars or
                    // Scheduled email is not sent yet but is tried to be accepted manually by trying to go to the accept link directly

                    $error_args             = array( 'error-key' => 'invalid-offer-link' , 'error-message' => urlencode( apply_filters( 'teo_invalid_offer_accept_link_error_message' , __( 'This offer link is invalid' , 'timed-email-offers' ) ) ) );
                    $invalid_offer_page_url = add_query_arg( $error_args , $invalid_offer_page_url );
                    
                    do_action( 'teo_invalid_offer_accept_link' , $offer_id , $order_id , $email_token , $error_args );
                    wp_safe_redirect( $invalid_offer_page_url );
                    exit;

                }

            }
            
        }

    }

}
