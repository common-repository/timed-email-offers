<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Decline_Offer_Page' ) ) {

    /**
     * Model that houses the logic of decline offer page.
     *
     * Class TEO_Decline_Offer_Page
     */
    final class TEO_Decline_Offer_Page {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Decline_Offer_Page.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Decline_Offer_Page
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
         * TEO_Decline_Offer_Page constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Decline_Offer_Page model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];
            $this->_offer_schedule   = $dependencies[ 'TEO_Offer_Schedule' ];

        }

        /**
         * Ensure that only one instance of TEO_Decline_Offer_Page is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Decline_Offer_Page model.
         * @return TEO_Decline_Offer_Page
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Execute callback for decline offer page.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         */
        public function execute_page_callback() {

            global $wp_query;

            $decline_offer_page_id = get_option( $this->_plugin_constants->OPTION_DECLINE_OFFER_PAGE_ID() , false );

            if ( is_page( $decline_offer_page_id ) &&
                 isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_OFFER_ID() ] ) &&
                 isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_ORDER_ID() ] ) &&
                 isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_EMAIL_TOKEN() ] ) ) {

                global $wpdb;

                $offer_id        = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_OFFER_ID() ];
                $order_id        = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_ORDER_ID() ];
                $email_token     = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_EMAIL_TOKEN() ];
                $offer_recipient = TEO_Helper::get_offer_recipient( $order_id , $offer_id );
                
                // Check if offer is still valid
                do_action( 'teo_check_offer_validity_via_decline_link' , $offer_id , $order_id , $email_token , $offer_recipient );


                // -------------------------------------------------------------------------------------------------
                // Set offer recipients data
                // -------------------------------------------------------------------------------------------------

                $response_datetime = date( 'Y-m-d H:i:s' , current_time( 'timestamp' ) );

                // Set the specific offer email data accordingly
                $wpdb->update(
                    $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                    array( 'response_status' => 'declined' , 'response_datetime' => $response_datetime , 'created_order_id' => NULL ) , array( 'email_token' => $email_token ),
                    array( '%s' , '%s' , '%d' ) , array( '%s' )
                );
                
                // Set recipient's response to this offer as 'declined'
                $wpdb->update(
                    $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS(),
                    array( 'response_status' => 'declined' , 'response_datetime' => $response_datetime , 'created_order_id' => NULL ) , array( 'recipient_id' => $offer_recipient->recipient_id ),
                    array( '%s' , '%s' , '%d' ) , array( '%d' )
                );
                
                
                // -------------------------------------------------------------------------------------------------
                // Unschedule any remaining scheduled emails
                // -------------------------------------------------------------------------------------------------

                $offer_scheduled_emails = TEO_Helper::get_offer_scheduled_emails( $offer_recipient->recipient_id );

                foreach ( $offer_scheduled_emails as $scheduled_email ) {

                    $this->_offer_schedule->unschedule_email_offer_for_customer( $offer_id , $order_id , $scheduled_email->template_id , $scheduled_email->email_token );

                    if ( $scheduled_email->email_token == $email_token || $scheduled_email->send_status != 'pending' )
                        continue;
                    
                    $wpdb->update(
                        $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                        array( 'send_status' => 'cancelled' , 'response_status' => 'na' ) , array( 'email_token' => $scheduled_email->email_token ) ,
                        array( '%s' , '%s' ) , array( '%s' )
                    );

                }


                // -------------------------------------------------------------------------------------------------
                // Execute decline offer actions
                // -------------------------------------------------------------------------------------------------

                $decline_offer_actions = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_DECLINE_ACTIONS() , true );
                if ( !is_array( $decline_offer_actions ) )
                    $decline_offer_actions = array();

                foreach ( $decline_offer_actions as $decline_action_key => $decline_action )
                    do_action( 'teo_execute_' . $decline_action_key . '_decline_offer_action' , $decline_action , $offer_id , $order_id , $email_token );

            } elseif ( is_page( $decline_offer_page_id ) )
                wp_redirect( get_home_url() ); // Meaning decline offer page but lacks required query vars
            
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

                $error_args             = array( 'error-key' => 'unavailable-offer' , 'error-message' => urlencode( apply_filters( 'teo_unavailable_offer_accessed_via_offer_decline_link_error_message' , __( 'Sorry, this offer is no longer available' , 'timed-email-offers' ) ) ) );
                $invalid_offer_page_url = add_query_arg( $error_args , $invalid_offer_page_url );
                
                do_action( 'teo_unavailable_offer_accessed_via_offer_decline_link' , $offer_id , $order_id , $email_token , $error_args );
                wp_safe_redirect( $invalid_offer_page_url );
                exit;

            } else {

                $recipient_id          = TEO_Helper::get_offer_recipient_id( $order_id , $offer_id );
                $offer_scheduled_email = TEO_Helper::get_offer_scheduled_email( $email_token , $recipient_id );

                if ( !$offer_scheduled_email || $offer_scheduled_email->send_status != 'sent' ) {

                    // Invalid query vars or
                    // Scheduled email is not sent yet but is tried to be declined manually by trying to go to the declined link directly

                    $error_args             = array( 'error-key' => 'invalid-offer-link' , 'error-message' => urlencode( apply_filters( 'teo_invalid_offer_decline_link_error_message' , __( 'This offer link is invalid' , 'timed-email-offers' ) ) ) );
                    $invalid_offer_page_url = add_query_arg( $error_args , $invalid_offer_page_url );
                    
                    do_action( 'teo_invalid_offer_decline_link' , $offer_id , $order_id , $email_token , $error_args );
                    wp_safe_redirect( $invalid_offer_page_url );
                    exit;

                }

            }

        }
        



        /*
        |--------------------------------------------------------------------------
        | Shortcodes
        |--------------------------------------------------------------------------
        */

        /**
         * Declined offer title shortcode callback.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $atts
         * @param $content
         * @return mixed
         */
        public function sc_teo_decline_offer_title( $atts , $content ) {

            global $wp_query;

            if ( isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_OFFER_ID() ] ) ) {

                $offer_id    = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_OFFER_ID() ];
                $offer_title = get_the_title( $offer_id );
                return $offer_title;

            }

        }

    }

}
