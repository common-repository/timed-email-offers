<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'Timed_Email_Offer' ) ) {

    /**
     * Class Timed_Email_Offer
     *
     * Model that houses the logic relating to timed email offer.
     *
     * @since 1.0.0
     */
    final class Timed_Email_Offer {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of Timed_Email_Offer.
         *
         * @since 1.0.0
         * @access private
         * @var Timed_Email_Offer
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
         * Property that wraps the logic of the plugin's mailer.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Mailer
         */
        private $_mailer;

        /**
         * Property that wraps the logic of 'timed_email_offer' cpt.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_CPT
         */
        private $_offer_cpt;

        /**
         * Property that wraps the logic of timed email offer conditions.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Condition
         */
        private $_offer_condition;

        /**
         * Property that wraps the logic of timed email offer recipients.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Recipient
         */
        private $_offer_recipient;

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
         * Timed_Email_Offer constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of Timed_Email_Offer model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants  = $dependencies[ 'TEO_Constants' ];
            $this->_mailer            = $dependencies[ 'TEO_Mailer' ];
            $this->_offer_cpt         = $dependencies[ 'TEO_Offer_CPT' ];
            $this->_offer_condition   = $dependencies[ 'TEO_Offer_Condition' ];
            $this->_offer_recipient   = $dependencies[ 'TEO_Offer_Recipient' ];
            $this->_offer_schedule    = $dependencies[ 'TEO_Offer_Schedule' ];

        }

        /**
         * Ensure that only one instance of Timed_Email_Offer is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of Timed_Email_Offer model.
         * @return Timed_Email_Offer
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }




        /*
        |--------------------------------------------------------------------------
        | Offers
        |--------------------------------------------------------------------------
        */

        /**
         * Remove all recipients for an offer when the offer is trashed.
         *
         * @since 1.1.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $offer_id
         */
        public function process_trashed_offer( $offer_id ) {

            if ( get_post_type( $offer_id ) == $this->_plugin_constants->OFFER_CPT_NAME() ) {

                global $wpdb;

                $query         = "SELECT recipient_id , order_id FROM " . $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() . " WHERE offer_id = $offer_id;";
                $query_results = $wpdb->get_results( $query );

                foreach ( $query_results as $result ) {

                    $this->_offer_schedule->unschedule_email_offers_for_customer( $result->recipient_id , $result->order_id , $offer_id );

                    $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS() , array( 'recipient_id' => $result->recipient_id ) , array( '%d' ) );
                    $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() ,  array( 'recipient_id' => $result->recipient_id ) , array( '%d' ) );
                    $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() , array( 'recipient_id' => $result->recipient_id ) , array( '%d' ) );

                }

            }

        }




        /*
        |--------------------------------------------------------------------------
        | Recipients
        |--------------------------------------------------------------------------
        */

        /**
         * Process completed orders. Add customer as recipient to an offer if it is qualified based on his/her order.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $order_id
         * @param $untrashed boolean If order came from being untrashed
         */
        public function process_completed_order( $order_id , $untrashed = false ) {

            $order    = wc_get_order( $order_id );
            $customer = TEO_Helper::get_order_user( $order_id );

            if ( $order ) {

                // Get all offers ( offer cpt )
                $timed_email_offers = apply_filters( 'teo_timed_email_offers_to_load' , TEO_Helper::get_all_timed_email_offers() );

                // Get blacklist
                $blacklist = get_option( $this->_plugin_constants->OPTION_BLACKLIST() , array() );

                // Check if customer isn't blacklisted
                if ( !array_key_exists( $customer->user_email , $blacklist ) ) {

                    // Check if order passes to any offer condition ( offer condition )
                    foreach ( $timed_email_offers as $offer ) {

                        $recipient_id = TEO_Helper::get_offer_recipient_id( $order_id , $offer->ID );
                        if ( $recipient_id )
                            continue; // Meaning, this order already have a record on this offer so skip this offer.

                        if ( $this->_offer_condition->check_offer_condition( $offer , $order , $customer ) ) {

                            // If passed create offer recipient ( offer recipient )
                            $this->_offer_recipient->create_offer_recipient( $offer , $order , $customer , $untrashed );

                            $allow_order_on_multiple_offers = apply_filters( 'teo_allow_order_linked_on_multiple_offers' , false );

                            if ( !$allow_order_on_multiple_offers )
                                break; // Currently only for one offer can an order be associated with, which offer gets satisfied first.

                        }

                    }

                }

            }

        }

        /**
         * Process orders that changed the status from completed to any other status.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $order_id
         */
        public function process_completed_order_changed_to_uncompleted_order( $order_id ) {

            $linked_offer_ids = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_LINKED_OFFER_IDS() , true );
            if ( !is_array( $linked_offer_ids ) )
                $linked_offer_ids = array();

            foreach ( $linked_offer_ids as $offer_id )
                $this->_offer_recipient->remove_offer_recipient( $order_id , $offer_id );

        }

        /**
         * Process completed orders that has been trashed.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $order_id
         */
        public function process_trashed_order( $order_id ) {

            if ( get_post_type( $order_id ) == 'shop_order' ) {

                $order = wc_get_order( $order_id );

                if ( $order && $order->post_status == 'wc-completed' ) {

                    $linked_offer_ids = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_LINKED_OFFER_IDS() , true );
                    if ( !is_array( $linked_offer_ids ) )
                        $linked_offer_ids = array();

                    foreach ( $linked_offer_ids as $offer_id )
                        $this->_offer_recipient->remove_offer_recipient( $order_id , $offer_id );

                }

            }

        }

        /**
         * Process trashed completed order that has been recovered.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $order_id
         */
        public function process_untrashed_order( $order_id ) {

            if ( get_post_type( $order_id ) == 'shop_order' ) {

                $order = wc_get_order( $order_id );

                if ( $order && $order->post_status == 'wc-completed' )
                    $this->process_completed_order( $order_id , true );

            }

        }

        /**
         * Send a specific scheduled email offer to a specific recipient.
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
        public function send_timed_email_offer( $offer_id , $order_id , $template_id , $email_token ) {

            // Get all offer templates
            $offer_templates = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , true );
            if ( !is_array( $offer_templates ) )
                $offer_templates = array();

            if ( !isset( $offer_templates[ $template_id ] ) ) {

                $error_message = "Offer scheduled email template id does not exist on the list of existing offer templates" .
                                 "offer_id    : " . $offer_id . "\n" .
                                 "order_id    : " . $order_id . "\n" .
                                 "template_id : " . $template_id . "\n" .
                                 "email_token : " . $email_token;

                TEO_Helper::debug_log( $error_message );

            } else {

                $customer       = TEO_Helper::get_order_user( $order_id );
                $offer_template = $offer_templates[ $template_id ];

                $filtered_email_subject = $this->_template_tags_processor( 'subject' , $offer_template[ 'subject' ] , $customer , $offer_id , $order_id , $email_token , $template_id );
                $filtered_email_heading = $this->_template_tags_processor( 'heading_text' , $offer_template[ 'heading-text' ] , $customer , $offer_id , $order_id , $email_token , $template_id );
                $filtered_email_content = $this->_template_tags_processor( 'email_content' , $offer_template[ 'message' ] , $customer , $offer_id , $order_id , $email_token , $template_id );

                $filtered_email_content .= '<br><img src="' . admin_url( 'admin-ajax.php' ) . '?action=record_offer_scheduled_email_view&offer_id=' . $offer_id . '&order_id=' . $order_id . '&email_token=' . $email_token . '" />';

                $email_sent = $this->_mailer->send_email( $customer->user_email , $filtered_email_subject , $filtered_email_content , $offer_template[ 'wrap-wc-header-footer' ] , $filtered_email_heading );

                if ( $email_sent ) {

                    global $wpdb;

                    $wpdb->update(
                        $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS(),
                        array( 'response_status' => 'idle' ) , array( 'offer_id' => $offer_id , 'order_id' => $order_id ) ,
                        array( '%s' ) , array( '%d' , '%d' )
                    );

                    $wpdb->update(
                        $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                        array( 'send_status' => 'sent' , 'response_status' => 'idle' ) , array( 'email_token' => $email_token ) ,
                        array( '%s' , '%s' ) , array( '%s' )
                    );

                } else {

                    $error_message = "Offer scheduled email failed to send\n" .
                                     "offer_id       : " . $offer_id . "\n" .
                                     "order_id       : " . $order_id . "\n" .
                                     "template_id    : " . $template_id . "\n" .
                                     "email_token    : " . $email_token . "\n" .
                                     "customer_email : " . $customer->user_email;

                    TEO_Helper::debug_log( $error_message );

                }

            }

        }

        /**
         * Decline an offer for a recipient if offer timeout period is reached.
         * Offer timeout period is the number of days after the last email of an offer to wait for a response from a recipient
         * before considering the offer as dormant or being ignored by the recipient therefore considering it as declined implicitly.
         *
         * @since 1.0.0
         * @since 1.2.0 Updated the code base to use the custom plugin tables.
         * @access public
         *
         * @param $offer_id
         * @param $order_id
         * @param $email_token
         */
        public function decline_timed_email_offer_on_timeout( $offer_id , $order_id , $email_token ) {

            global $wpdb;

            $recipient_id = TEO_Helper::get_offer_recipient_id( $order_id , $offer_id );

            if ( $recipient_id ) {

                // -------------------------------------------------------------------------------------------------
                // Set offer recipients data
                // -------------------------------------------------------------------------------------------------

                $current_datetime = date( 'Y-m-d H:i:s' , current_time( 'timestamp' ) );

                $wpdb->update(
                    $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                    array( 'response_status' => 'declined' , 'timeout' => 'yes' , 'timeout_datetime' => $current_datetime ),
                    array( 'email_token' => $email_token ),
                    array( '%s' , '%s' , '%s' ),
                    array( '%s' )
                );

                $wpdb->update(
                    $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS(),
                    array( 'response_status' => 'declined' , 'timeout' => 'yes' , 'timeout_datetime' => $current_datetime ),
                    array( 'recipient_id' => $recipient_id ),
                    array( '%s' , '%s' , '%s' ),
                    array( '%d' )
                );

                // -------------------------------------------------------------------------------------------------
                // Unschedule any remaining scheduled emails
                // -------------------------------------------------------------------------------------------------

                $offer_scheduled_emails = TEO_Helper::get_offer_scheduled_emails( $recipient_id );

                foreach ( $offer_scheduled_emails as $scheduled_email ) {

                    $this->_offer_schedule->unschedule_email_offer_for_customer( $offer_id , $order_id , $scheduled_email->template_id , $scheduled_email->email_token );

                    if ( $scheduled_email->email_token == $email_token || $scheduled_email->send_status != 'pending' )
                        continue;

                    $wpdb->update(
                        $$this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS(),
                        array( 'send_status' => 'cancelled' , 'response_status' => 'na' ),
                        array( 'email_token' => $scheduled_email->email_token ),
                        array( '%s' , '%s' ),
                        array( '%s' )
                    );

                }

                // -------------------------------------------------------------------------------------------------
                // Maybe execute decline offer actions
                // -------------------------------------------------------------------------------------------------

                $maybe_execute_offer_decline_actions = get_option( $this->_plugin_constants->OPTION_EXECUTE_DECLINE_ACTIONS_ON_TIMEOUT() , false );

                if ( $maybe_execute_offer_decline_actions == 'yes' ) {

                    $decline_offer_actions = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_DECLINE_ACTIONS() , true );
                    if ( !is_array( $decline_offer_actions ) )
                        $decline_offer_actions = array();

                    foreach ( $decline_offer_actions as $decline_action_key => $decline_action )
                        do_action( 'teo_execute_' . $decline_action_key . '_decline_offer_action' , $decline_action , $offer_id , $order_id , $email_token );

                }

            }

        }




        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

        /**
         * Process template tags on offer email content, subject and heading text.
         *
         * @since 1.0.0
         * @access private
         *
         * @param $consumer
         * @param $content
         * @param $customer
         * @param $offer_id
         * @param $order_id
         * @param $email_token
         * @param $template_id
         * @return mixed
         */
        private function _template_tags_processor( $consumer , $content , $customer , $offer_id , $order_id , $email_token , $template_id ) {

            $order = wc_get_order( $order_id );

            $customer_first_name   = $customer->first_name;
            $customer_last_name    = $customer->last_name;
            $order_url             = $order ? $order->get_checkout_order_received_url() : '';
            $order_total           = TEO_Helper::get_order_data( $order , 'order_total' );
            $order_amount          = $order ? html_entity_decode( strip_tags( wc_price( $order_total ) ) ) : '';
            $order_date            = TEO_Helper::get_order_data( $order , 'order_date' );
            $order_date            = $order ? date_i18n( get_option( 'date_format' ) , strtotime( $order_date ) ) : '';
            $accept_offer_url      = str_replace( array( 'http://' , 'https://' ) , '' , get_permalink( get_option( $this->_plugin_constants->OPTION_ACCEPT_OFFER_PAGE_ID() , false ) ) . '?offer-id=' . $offer_id . '&order-id=' . $order_id . '&email-token=' . $email_token );
            $decline_offer_url     = str_replace( array( 'http://' , 'https://' ) , '' , get_permalink( get_option( $this->_plugin_constants->OPTION_DECLINE_OFFER_PAGE_ID() , false ) ) . '?offer-id=' . $offer_id . '&order-id=' . $order_id . '&email-token=' . $email_token );
            $unsubscribe_offer_url = str_replace( array( 'http://' , 'https://' ) , '' , get_permalink( get_option( $this->_plugin_constants->OPTION_UNSUBSCRIBE_PAGE_ID() , false ) ) . '?offer-id=' . $offer_id . '&order-id=' . $order_id . '&email-token=' . $email_token );

            $find_replace = array();

            if ( $consumer == 'subject' ) {

                $find_replace = array(
                    '{recipient_first_name}' => $customer_first_name,
                    '{recipient_last_name}'  => $customer_last_name,
                    '{order_no}'             => $order_id,
                    '{order_amount}'         => $order_amount,
                    '{order_date}'           => $order_date
                );

                $find_replace = apply_filters( 'teo_offer_subject_template_tags' , $find_replace , $customer , $offer_id , $order_id , $email_token );

            } elseif ( $consumer == 'heading_text' ) {

                $find_replace = array(
                    '{recipient_first_name}' => $customer_first_name,
                    '{recipient_last_name}'  => $customer_last_name,
                    '{order_no}'             => $order_id,
                    '{order_amount}'         => $order_amount,
                    '{order_date}'           => $order_date
                );

                $find_replace = apply_filters( 'teo_offer_heading_text_template_tags' , $find_replace , $customer , $offer_id , $order_id , $email_token );

            } else {

                // email_content

                $find_replace = array(
                    '{recipient_first_name}'  => $customer_first_name,
                    '{recipient_last_name}'   => $customer_last_name,
                    '{order_no}'              => $order_id,
                    '{order_url}'             => $order_url,
                    '{order_amount}'          => $order_amount,
                    '{order_date}'            => $order_date,
                    '{accept_offer_url}'      => $accept_offer_url,
                    '{decline_offer_url}'     => $decline_offer_url,
                    '{unsubscribe_offer_url}' => $unsubscribe_offer_url
                );

                $find_replace = apply_filters( 'teo_offer_email_content_template_tags' , $find_replace , $customer , $offer_id , $order_id , $email_token );

            }

            $find_replace = apply_filters( 'teo_offer_template_tags' , $find_replace , $customer , $offer_id , $order_id , $email_token );

            foreach ( $find_replace as $find => $replace )
                $content = str_replace( $find , $replace , $content );

            return $content;

        }

        /**
         * Correct href with template tag url that has no protocol.
         *
         * @since 1.1.0
         * @access public
         *
		 * @param string $content        Default editor content.
		 * @param string $default_editor The default editor for the current user. Either 'html' or 'tinymce'.
         * @return string $content       Editor content
         */
        public function correct_href_with_no_protocol( $content , $default_editor ) {

            global $post;

            if ( $post && $post->post_type == $this->_plugin_constants->OFFER_CPT_NAME() ) {

                // Remove trailing forward slashes on offer links
                $content = str_replace( '{order_url}/' , '{order_url}' , $content );
                $content = str_replace( '{accept_offer_url}/' , '{accept_offer_url}' , $content );
                $content = str_replace( '{decline_offer_url}/' , '{decline_offer_url}' , $content );
                $content = str_replace( '{unsubscribe_offer_url}/' , '{unsubscribe_offer_url}' , $content );

                // Add protocol to offer links
                $content = str_replace( 'href="{order_url}"' , 'href="http://{order_url}"' , $content );
                $content = str_replace( 'href="{accept_offer_url}"' , 'href="http://{accept_offer_url}"' , $content );
                $content = str_replace( 'href="{decline_offer_url}"' , 'href="http://{decline_offer_url}"' , $content );
                $content = str_replace( 'href="{unsubscribe_offer_url}"' , 'href="http://{unsubscribe_offer_url}"' , $content );

            }

            return $content;

        }

    }

}
