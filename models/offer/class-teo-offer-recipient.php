<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Offer_Recipient' ) ) {

    /**
     * Class TEO_Offer_Recipient
     *
     * Model that houses the logic relating to offer recipients.
     *
     * @since 1.0.0
     */
    final class TEO_Offer_Recipient {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Offer_Recipient.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Recipient
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
         * TEO_Offer_Recipient constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Recipient model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];
            $this->_offer_schedule   = $dependencies[ 'TEO_Offer_Schedule' ];

        }

        /**
         * Ensure that only one instance of TEO_Offer_Recipient is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Recipient model.
         * @return TEO_Offer_Recipient
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Create a recipient to a timed email offer.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $offer
         * @param $order
         * @param $customer
         * @param $untrashed boolean If order came from being untrashed
         */
        public function create_offer_recipient( $offer , $order , $customer , $untrashed = false ) {

            // Set email cron for this recipient as per offer templates
            // Add recipient to offer
            $scheduled_emails = $this->_offer_schedule->schedule_email_offers_for_customer( $offer , $order , $customer );
            $order_id         = TEO_Helper::get_order_data( $order , 'id' );

            if ( !empty( $scheduled_emails ) ) {

                do_action( 'teo_before_recipient_added_to_offer' , $offer , $order , $customer , $untrashed );

                // Add customer as recipient

                global $wpdb;

                if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() . "'" ) && $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() . "'" ) ) {

                    $offer_recipients_row_data = array(
                                                    'order_id'        => $order_id,
                                                    'offer_id'        => $offer->ID,
                                                    'customer_email'  => $customer->user_email,
                                                    'untrashed'       => $untrashed ? 1 : 0,
                                                    'response_status' => 'na'
                                                );

                    $wpdb->insert( $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() , $offer_recipients_row_data );

                    if ( !empty( $scheduled_emails ) ) {

                        $recipient_id = $wpdb->insert_id; // Latest recipient_id inserted. Auto-increment field.

                        foreach ( $scheduled_emails as $email_token => $schedule_email ) {

                            $schedules_emails_row_data = array(
                                                            'email_token'     => $email_token,
                                                            'recipient_id'    => $recipient_id,
                                                            'base_datetime'   => date( 'Y-m-d H:i:s' , $schedule_email[ 'schedule_timestamp' ][ 'base_timestamp' ] ),
                                                            'cron_datetime'   => date( 'Y-m-d H:i:s' , $schedule_email[ 'schedule_timestamp' ][ 'cron_timestamp' ] ),
                                                            'template_id'     => $schedule_email[ 'template_id' ],
                                                            'send_status'     => $schedule_email[ 'send_status' ],
                                                            'response_status' => $schedule_email[ 'response_status' ]
                                                        );

                            $wpdb->insert( $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() , $schedules_emails_row_data );

                        }

                    }

                }

                // Record the timed email offer id as a meta of the current order
                $order_linked_offer_ids = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_LINKED_OFFER_IDS() , true );
                if ( !is_array( $order_linked_offer_ids ) )
                    $order_linked_offer_ids = array();

                $order_linked_offer_ids[] = $offer->ID;

                update_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_LINKED_OFFER_IDS() , $order_linked_offer_ids );

                do_action( 'teo_after_recipient_added_to_offer' , $offer , $order , $customer , $untrashed );

            } else {

                $error_message = "Failed to add customer as a timed email offer recipient. There are no offer scheduled emails set.\n" .
                                 "offer_id       : " . $offer->ID . "\n" .
                                 "order_id       : " . $order_id . "\n" .
                                 "customer_email : " . $customer->user_email;

                TEO_Helper::debug_log( $error_message );

            }

        }

        /**
         * Remove recipient from a timed email offer.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $order_id
         * @param $offer_id
         */
        public function remove_offer_recipient( $order_id , $offer_id ) {

            global $wpdb;

            // Get recipient id
            $recipient_id = TEO_Helper::get_offer_recipient_id( $order_id , $offer_id );

            if ( $recipient_id ) {

                // Unschedule all scheduled timed email offers for this recipient ( person behind this order ).
                $this->_offer_schedule->unschedule_email_offers_for_customer( $recipient_id , $order_id , $offer_id );

                // Remove recipient ( person behind the current order ) from teo list of recipients.
                $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS() , array( 'recipient_id' => $recipient_id ) , array( '%d' ) );
                $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() ,  array( 'recipient_id' => $recipient_id ) , array( '%d' ) );
                $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() , array( 'recipient_id' => $recipient_id ) , array( '%d' ) );

                // Remove the offer id from the list of offer ids that this current order is linked with.
                $order_linked_offer_ids = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_LINKED_OFFER_IDS() , true );
                if ( !is_array( $order_linked_offer_ids ) )
                    $order_linked_offer_ids = array();

                $index = array_search( $offer_id , $order_linked_offer_ids );

                if ( $index !== false )
                    unset( $order_linked_offer_ids[ $index ] );

                update_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_LINKED_OFFER_IDS() , $order_linked_offer_ids );

            }

        }

        /**
         * Remove offer recipient by email.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $email
         * @param $offer_id
         */
        public function remove_offer_recipient_by_email( $email , $offer_id ) {

            $orders = TEO_Helper::get_all_orders_from_customer_via_email( $email );

            if ( is_array( $orders ) ) {

                foreach ( $orders as $order )
                    $this->remove_offer_recipient( $order->ID , $offer_id );

            }

        }

        /**
         * Return offer recipients in format that is compatible with datatables library requires.
         * This data is then in turn populated to the offer recipients datatables on the admin.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $offer_id
         * @return mixed
         */
        public function get_offer_recipients( $offer_id ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-get_offer_recipients-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-get_offer_recipients-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id ) );

            $response_status_filter          = isset( $_POST[ 'response_status_filter' ] ) ? $_POST[ 'response_status_filter' ] : 'na'; // Show pending if not set
            $offer_recipients                = TEO_Helper::get_offer_recipients_by_offer_id( $offer_id , $response_status_filter );
            $recipient_offer_response_status = $this->_plugin_constants->RECIPIENT_OFFER_RESPONSE_STATUS();

            $data = array(
                        'recordsTotal'    => 0,
                        'recordsFiltered' => 0,
                        'data'            => array()
                    );

            foreach ( $offer_recipients as $recipient ) {

                $order          = wc_get_order( $recipient->order_id );
                $customer_email = $recipient->customer_email;
                $completed_date = TEO_Helper::get_order_data( $order , 'completed_date' );

                $order_completed_date_time = new DateTime( $completed_date );
                $order_completed_timestamp = $order_completed_date_time->format( 'U' );

                $first_name = TEO_Helper::get_order_data( $order , 'billing_first_name' );
                $last_name  = TEO_Helper::get_order_data( $order , 'billing_last_name' );

                $d = array(
                    $first_name . ' ' . $last_name,
                    $customer_email,
                    $recipient->order_id,
                    $order_completed_timestamp,
                    $recipient_offer_response_status[ $recipient->response_status ],
                    $this->_plugin_constants->OFFER_RECIPIENTS_TABLE_COLUMN_ACTIONS( $offer_id , $recipient->order_id )
                );

                $d = apply_filters( 'teo_offer_recipients_table_item_data' , $d , $offer_id , $recipient );

                $data[ 'data' ][] = $d;
                $data[ 'recordsTotal' ]++;
                $data[ 'recordsFiltered' ]++;

            }

            // Sort survey questions
            usort( $data[ 'data' ] , array( 'TEO_Helper' , 'sort_datatables_data') );

            // Length and Paging
            $data[ 'data' ] = array_slice( $data[ 'data' ] , $_REQUEST[ 'start' ] , $_REQUEST[ 'length' ] );

            // Finalize data
            foreach ( $data[ 'data' ] as $index => $d ) {

                // Make add link to order id
                $data[ 'data' ][ $index ][ 2 ] = '<a target="_blank" href="' . admin_url( 'post.php?post=' . $d[ 2 ] . '&action=edit' ) . '">' . $d[ 2 ] . '</a>';

                // Make date timestamp consumable by mere mortals
                $data[ 'data' ][ $index ][ 3 ] = date( 'Y-m-d H:i:s A' , $d[ 3 ] );

            }

            $data = apply_filters( 'teo_finalize_offer_recipients_table_data' , $data , $offer_id );

            return $data;

        }

        /**
         * Generate offer recipient data popup markup.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $offer_id
         * @param $order_id
         * @return mixed
         */
        public function generate_offer_recipient_data_popup_markup( $offer_id , $order_id ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-generate_offer_recipient_data_popup_markup-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'order_id' => $order_id ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-generate_offer_recipient_data_popup_markup-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'order_id' => $order_id ) );

            $constants       = TEO_Constants::instance();
            $offer_recipient = TEO_Helper::get_offer_recipient( $order_id , $offer_id );

            if ( $offer_recipient ) {

                $offer_email_send_status     = $this->_plugin_constants->OFFER_EMAIL_SEND_STATUS();
                $offer_email_response_status = $this->_plugin_constants->OFFER_EMAIL_RESPONSE_STATUS();

                $customer               = TEO_Helper::get_order_user( $order_id );
                $offer_scheduled_emails = TEO_Helper::get_offer_scheduled_emails( $offer_recipient->recipient_id );

                ob_start();

                include ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-timed-email-offer-recipient-data-popup.php' );

                $mark_up = ob_get_clean();

                return $mark_up;

            } else
                return new WP_Error( 'teo-generate_offer_recipient_data_popup_markup-failed-retrieving-recipient-data' , __( 'Failed retrieving recipient data. Recipient does not exist.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'order_id' => $order_id ) );

        }

        /**
         * Remove specific scheduled email from an offer recipient.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $offer_id
         * @param $order_id int|string Order Id
         * @param $unique_email_token
         * @return mixed
         */
        public function remove_recipient_scheduled_email( $offer_id , $order_id , $unique_email_token ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-remove_recipient_scheduled_email-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'order_id' => $order_id , 'unique_email_token' => $unique_email_token ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-remove_recipient_scheduled_email-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'order_id' => $order_id , 'unique_email_token' => $unique_email_token ) );

            $recipient_id          = TEO_Helper::get_offer_recipient_id( $order_id , $offer_id );
            $offer_scheduled_email = TEO_Helper::get_offer_scheduled_email( $unique_email_token , $recipient_id );

            if ( !$offer_scheduled_email )
                return new WP_Error( 'teo-remove_recipient_scheduled_email-recipient-schedule-to-remove-not-exist' , __( "The scheduled email you wish to remove does not exist on the recipient's list scheduled emails." , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'order_id' => $order_id , 'unique_email_token' => $unique_email_token ) );
            else {

                global $wpdb;

                $this->_offer_schedule->unschedule_email_offer_for_customer( $offer_id , $order_id , $offer_scheduled_email->template_id , $unique_email_token );
                $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS() , array( 'email_token' => $unique_email_token , 'recipient_id' => $recipient_id ) , array( '%s' , '%d' ) );
                $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS()  , array( 'email_token' => $unique_email_token , 'recipient_id' => $recipient_id ) , array( '%s' , '%d' ) );

                return true;

            }

        }

        /**
         * Remove recipient from offer.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $order_id int|string Order Id
         * @return mixed
         */
        public function remove_recipient_from_offer( $offer_id , $order_id ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-remove_recipient_from_offer-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'order_id' => $order_id ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-remove_recipient_from_offer-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'order_id' => $order_id ) );

            $this->remove_offer_recipient( $order_id , $offer_id );

            return true;

        }

    }

}
