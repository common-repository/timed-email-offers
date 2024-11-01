<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Offer_Schedule' ) ) {

    /**
     * Class TEO_Offer_Schedule
     *
     * Model that houses the logic relating to offer schedule.
     *
     * @since 1.0.0
     */
    final class TEO_Offer_Schedule {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Offer_Schedule.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Schedule
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
         * TEO_Offer_Schedule constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Schedule model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];

        }

        /**
         * Ensure that only one instance of TEO_Offer_Schedule is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Schedule model.
         * @return TEO_Offer_Schedule
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Determine the cron schedule. Returns unix timestamp.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $order
         * @param $schedule
         * @return string
         */
        public function get_cron_schedule_timestamp( $order , $schedule ) {

            $timestamp = array();

            // We use the modified_date here instead of completed_date
            // You see during the execution of the callbacks of this hook 'woocommerce_order_status_completed'
            // The completed_date is still holds the old completed date data
            // Meaning the new completed_date data ( which is the modified date ) is not yet set to the completed_date property of this order
            // So that's why the cron sched is way off coz we are using the old completed_date data on setting the cron
            // This makes sense when an order is set to completed, then set back to some other status, then set again as completed
            // Same as if a completed order is trashed, then untrashed
            $date_time = new DateTime( TEO_Helper::get_order_data( $order , 'modified_date' ) );

            $timestamp[ 'base_timestamp' ] = $date_time->format( 'U' );

            $date_time->add( new DateInterval( 'P' . $schedule . 'D' ) );

            $timestamp[ 'cron_timestamp' ] = $date_time->format( 'U' );

            return $timestamp;

        }

        /**
         * Schedule timed email offers for a recipient.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer
         * @param $order
         * @param $customer
         * @return array
         */
        public function schedule_email_offers_for_customer( $offer , $order , $customer ) {

            $scheduled_emails = array();

            do_action( 'teo_before_scheduling_offer_emails' , $offer , $order , $customer , $scheduled_emails );

            // Get all offer templates
            $offer_templates = get_post_meta( $offer->ID , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , true );
            if ( !is_array( $offer_templates ) )
                $offer_templates = array();

            $last_sched_token = '';
            $max_sched        = 0;
            $order_id         = TEO_Helper::get_order_data( $order , 'id' );

            foreach ( $offer_templates as $template_id => $template_data ) {

                // Determine the email schedule date time
                $timestamp = $this->get_cron_schedule_timestamp( $order , $template_data[ 'schedule' ] );

                // Set scheduled email
                $email_token = str_replace( '.' , '' , uniqid( 'teo_' , true ) );
                $args        = array( (int) $offer->ID , (int) $order_id , (int) $template_id , $email_token );

                // Returns null if event is scheduled
                $email_scheduled = wp_schedule_single_event( $timestamp[ 'cron_timestamp' ] , $this->_plugin_constants->CRON_HOOK_SEND_EMAIL_OFFER() , $args );

                if ( is_null( $email_scheduled ) ) {

                    $scheduled_emails[ $email_token ] = array(
                        'schedule_timestamp' => $timestamp,
                        'template_id'        => $template_id,
                        'send_status'        => 'pending',
                        'response_status'    => 'na'
                    );

                    if ( $template_data[ 'schedule' ] > $max_sched ) {

                        $max_sched        = $template_data[ 'schedule' ];
                        $last_sched_token = $email_token;

                    }

                } else {

                    $error_message = "wp_schedule_single_event Failed to execute, returns false\n" .
                                     "timestamp : " . $timestamp[ 'cron_timestamp' ] . "\n" .
                                     "hook      : " . $this->_plugin_constants->CRON_HOOK_SEND_EMAIL_OFFER() . "\n" .
                                     "args      : \n" . print_r( $args , true );

                    TEO_Helper::debug_log( $error_message );

                }

            }


            // -------------------------------------------------------------------------------------------------
            // Maybe schedule decline offer on timeout cron
            // -------------------------------------------------------------------------------------------------

            $offer_timeout_period = get_option( $this->_plugin_constants->OPTION_OFFER_TIMEOUT_PERIOD() , false );

            if ( $offer_timeout_period ) {

                // Add the max sched ( implicitly the last sched ) and the offer time out period
                $processed_offer_timeout_period = (int) ( $max_sched + $offer_timeout_period );

                // Get the offer timeout timestamp
                $timeout_timestamp = $this->get_cron_schedule_timestamp( $order , $processed_offer_timeout_period );

                // Construct the arguments
                $args = array( (int) $offer->ID , (int) $order_id , $last_sched_token );

                // Schedule the decline offer on timeout cron. Returns null if event is scheduled
                $offer_timeout_scheduled = wp_schedule_single_event( $timeout_timestamp[ 'cron_timestamp' ] , $this->_plugin_constants->CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT() , $args );

                if ( !is_null( $offer_timeout_scheduled ) ) {

                    $error_message = "wp_schedule_single_event Failed to execute, returns false\n" .
                                     "timestamp : " . $timeout_timestamp[ 'cron_timestamp' ] . "\n" .
                                     "hook      : " . $this->_plugin_constants->CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT() . "\n" .
                                     "args      : \n" . print_r( $args , true );

                    TEO_Helper::debug_log( $error_message );

                }

            }

            do_action( 'teo_after_scheduling_offer_emails' , $offer , $order , $customer , $scheduled_emails );

            return $scheduled_emails;

        }

        /**
         * Unschedule all scheduled timed email offers of a customer ( recipient ).
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $order_id
         * @param $offer_id
         * @param $offer_recipients
         */
        public function unschedule_email_offers_for_customer( $recipient_id , $order_id , $offer_id ) {

            global $wpdb;

            $offer_scheduled_emails = TEO_Helper::get_offer_scheduled_emails( $recipient_id );

            foreach ( $offer_scheduled_emails as $scheduled_email )
                $this->unschedule_email_offer_for_customer( $offer_id , $order_id , $scheduled_email->template_id , $scheduled_email->email_token );

        }

        /**
         * Unschedule specific scheduled timed email offer of a customer ( recipient ).
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $offer_id
         * @param $order_id
         * @param $template_id
         * @param $email_token
         */
        public function unschedule_email_offer_for_customer( $offer_id , $order_id , $template_id , $email_token ) {

            $customer = TEO_Helper::get_order_user( $order_id );
            $args     = array( (int) $offer_id , (int) $order_id , (int) $template_id , $email_token );

            $timestamp = wp_next_scheduled( $this->_plugin_constants->CRON_HOOK_SEND_EMAIL_OFFER() , $args );

            if ( $timestamp )
                wp_unschedule_event( $timestamp , $this->_plugin_constants->CRON_HOOK_SEND_EMAIL_OFFER() , $args );

            $this->unschedule_decline_offer_on_timeout_cron_for_customer( $offer_id , $order_id , $email_token );

        }

        /**
         * Unschedule the decline offer on timeout cron for a customer.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $order_id
         * @param $email_token
         */
        public function unschedule_decline_offer_on_timeout_cron_for_customer( $offer_id , $order_id , $email_token ) {

            $args = array( (int) $offer_id , (int) $order_id , $email_token );

            $timestamp = wp_next_scheduled( $this->_plugin_constants->CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT() , $args );

            if ( $timestamp )
                wp_unschedule_event( $timestamp , $this->_plugin_constants->CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT() , $args );

        }

    }

}
