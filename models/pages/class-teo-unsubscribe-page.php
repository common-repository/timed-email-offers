<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Unsubscribe_Page' ) ) {

    /**
     * Model that houses the logic of unsubscribe page.
     *
     * Class TEO_Unsubscribe_Page
     */
    final class TEO_Unsubscribe_Page {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Unsubscribe_Page.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Unsubscribe_Page
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
         * Property that houses the logic of timed email offer recipients.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Recipient
         */
        private $_offer_recipient;




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
         * TEO_Unsubscribe_Page constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Unsubscribe_Page model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];
            $this->_offer_recipient  = $dependencies[ 'TEO_Offer_Recipient' ];

        }

        /**
         * Ensure that only one instance of TEO_Unsubscribe_Page is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Unsubscribe_Page model.
         * @return TEO_Unsubscribe_Page
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Execute callback for unsubscribe offer page.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         */
        public function execute_page_callback() {

            global $wp_query;

            $unsubscribe_page_id = get_option( $this->_plugin_constants->OPTION_UNSUBSCRIBE_PAGE_ID() , false );

            if ( is_page( $unsubscribe_page_id ) &&
                isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_OFFER_ID() ] ) &&
                isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_ORDER_ID() ] ) &&
                isset( $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_EMAIL_TOKEN() ] ) ) {

                $offer_id        = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_OFFER_ID() ];
                $order_id        = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_ORDER_ID() ];
                $email_token     = $wp_query->query_vars[ $this->_plugin_constants->PAGE_PARAM_EMAIL_TOKEN() ];
                $offer_recipient = TEO_Helper::get_offer_recipient( $order_id , $offer_id );

                // Check if offer is still valid
                do_action( 'teo_check_offer_validity_via_unsubscribe_link' , $offer_id , $order_id , $email_token , $offer_recipient );

                // Black list user
                $blacklist             = get_option( $this->_plugin_constants->OPTION_BLACKLIST() , array() );
                $customer              = TEO_Helper::get_order_user( $order_id );
                $recipient_id          = TEO_Helper::get_offer_recipient_id( $order_id , $offer_id );
                $offer_scheduled_email = TEO_Helper::get_offer_scheduled_email( $email_token , $recipient_id );

                do_action( 'teo_before_blacklist_customer' , $customer , $offer_recipient , $offer_scheduled_email );

                if ( $offer_scheduled_email ) {

                    // Add recipient to the blacklist
                    $blacklist[ $customer->user_email ] = array(
                        'blacklist_type'      => 'unsubscribed',
                        'blacklist_timestamp' => current_time( 'timestamp' ),
                        'offer_id'            => $offer_id,
                        'order_id'            => $order_id,
                        'template_id'         => $offer_scheduled_email->template_id,
                        'customer'            => $customer
                    );

                    update_option( $this->_plugin_constants->OPTION_BLACKLIST() , $blacklist );

                }

                do_action( 'teo_after_blacklist_customer' , $customer , $offer_recipient , $offer_scheduled_email );

                // Remove recipient from all offers it is associated with ( There will be instances where 1 recipient is associated to multiple offers )
                $offers = TEO_Helper::get_all_timed_email_offers();
                foreach ( $offers as $offer )
                    $this->_offer_recipient->remove_offer_recipient( $order_id , $offer->ID );

            } elseif ( is_page( $unsubscribe_page_id ) )
                wp_redirect( get_home_url() ); // Meaning unsubscribed page but lacks required query vars

        }

        /**
         * Get blacklist.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $blacklist_type
         * @return mixed
         */
        public function get_blacklist( $blacklist_type ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-get_blacklist-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'blacklist_type' => $blacklist_type ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-get_blacklist-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'blacklist_type' => $blacklist_type ) );

            $blacklist       = get_option( $this->_plugin_constants->OPTION_BLACKLIST() , array() );
            $blacklist_types = $this->_plugin_constants->BLACKLIST_TYPES();

            $data = array(
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => array()
            );

            foreach ( $blacklist as $email => $bl ) {

                if ( $blacklist_type != 'all' && $blacklist_type != $bl[ 'blacklist_type' ] )
                    continue;

                $d = array(
                    $email,
                    $bl[ 'blacklist_timestamp' ],
                    $blacklist_types[ $bl[ 'blacklist_type' ] ],
                    $this->_plugin_constants->BLACKLIST_TABLE_COLUMN_ACTIONS( $email , $bl[ 'blacklist_type' ] )
                );

                $d = apply_filters( 'teo_blacklist_table_item_data' , $d , $blacklist_type );

                $data[ 'data' ][] = $d;
                $data[ 'recordsTotal' ]++;
                $data[ 'recordsFiltered' ]++;

            }

            // Sort survey questions
            usort( $data[ 'data' ] , array( 'TEO_Helper' , 'sort_datatables_data' ) );

            // Length and Paging
            $data[ 'data' ] = array_slice( $data[ 'data' ] , $_REQUEST[ 'start' ] , $_REQUEST[ 'length' ] );

            // Finalize data
            // Make date timestamp consumable by mere mortals
            foreach ( $data[ 'data' ] as $index => $d )
                $data[ 'data' ][ $index ][ 1 ] = date( 'Y-m-d H:i:s A' , $d[ 1 ] );

            $data = apply_filters( 'teo_finalize_blacklist_table_data' , $data , $blacklist_type );

            return $data;

        }

        /**
         * Manually opt-out email.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $email
         * @return mixed
         */
        public function manually_opt_out_email( $email ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-manually_opt_out_email-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'email' => $email ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-manually_opt_out_email-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'email' => $email ) );

            if ( !filter_var( $email , FILTER_VALIDATE_EMAIL ) )
                return new WP_Errors( 'teo-manually_opt_out_email-invalid-email' , __( 'Invalid Email' , 'timed-email-offers' ) , array( 'email' => $email ) );

            $blacklist = get_option( $this->_plugin_constants->OPTION_BLACKLIST() , array() );
            $offers    = TEO_Helper::get_all_timed_email_offers();

            foreach ( $offers as $offer )
                $this->_offer_recipient->remove_offer_recipient_by_email( $email , $offer->ID );

            $blacklist[ $email ] = array(
                'blacklist_type'      => 'manual',
                'blacklist_timestamp' => current_time( 'timestamp' )
            );

            update_option( $this->_plugin_constants->OPTION_BLACKLIST() , $blacklist );

            return true;

        }

        /**
         * Un opt-out email from blacklist.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $email
         * @return array
         */
        public function un_opt_out_email( $email ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-un_opt_out_email-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'email' => $email ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-un_opt_out_email-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'email' => $email ) );

            if ( !filter_var( $email , FILTER_VALIDATE_EMAIL ) )
                return new WP_Errors( 'teo-un_opt_out_email-invalid-email' , __( 'Invalid Email' , 'timed-email-offers' ) , array( 'email' => $email ) );

            $blacklist = get_option( $this->_plugin_constants->OPTION_BLACKLIST() , array() );

            if ( !array_key_exists( $email , $blacklist ) )
                return new WP_Error( 'teo-un_opt_out_email-email-to-opt-out-not-exist' , __( 'The email you wish to opt-out does not exist on blacklist' , 'timed-email-offers' ) , array( 'email' => $email ) );
            else {

                unset( $blacklist[ $email ] );
                update_option( $this->_plugin_constants->OPTION_BLACKLIST() , $blacklist );

                return true;

            }

        }

        /**
         * Check offer validity.
         *
         * @since 1.1.0
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

                $error_args             = array( 'error-key' => 'unavailable-offer' , 'error-message' => urlencode( apply_filters( 'teo_unavailable_offer_accessed_via_offer_unsubscribe_link_error_message' , __( 'Sorry, this offer is no longer available' , 'timed-email-offers' ) ) ) );
                $invalid_offer_page_url = add_query_arg( $error_args , $invalid_offer_page_url );

                do_action( 'teo_unavailable_offer_accessed_via_offer_unsubscribe_link' , $offer_id , $order_id , $email_token , $error_args );
                wp_safe_redirect( $invalid_offer_page_url );
                exit;

            } else {

                $recipient_id          = TEO_Helper::get_offer_recipient_id( $order_id , $offer_id );
                $offer_scheduled_email = TEO_Helper::get_offer_scheduled_email( $email_token , $recipient_id );

                if ( !$offer_scheduled_email || $offer_scheduled_email->send_status != 'sent' ) {

                    // Invalid query vars or
                    // Scheduled email is not sent yet but is tried to be declined manually by trying to go to the declined link directly

                    $error_args             = array( 'error-key' => 'invalid-offer-link' , 'error-message' => urlencode( apply_filters( 'teo_invalid_offer_unsubscribe_link_error_message' , __( 'This offer link is invalid' , 'timed-email-offers' ) ) ) );
                    $invalid_offer_page_url = add_query_arg( $error_args , $invalid_offer_page_url );

                    do_action( 'teo_invalid_offer_unsubscribe_link' , $offer_id , $order_id , $email_token , $error_args );
                    wp_safe_redirect( $invalid_offer_page_url );
                    exit;

                }

            }

        }

    }

}
