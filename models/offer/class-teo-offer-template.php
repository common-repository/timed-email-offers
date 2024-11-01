<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Offer_Template' ) ) {

    /**
     * Class TEO_Offer_Template
     *
     * Model that houses the logic relating to offer template.
     *
     * @since 1.0.0
     */
    final class TEO_Offer_Template {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Offer_Template.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Template
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
         * TEO_Offer_Template constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Template model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];
            $this->_offer_schedule   = $dependencies[ 'TEO_Offer_Schedule' ];

        }

        /**
         * Ensure that only one instance of TEO_Offer_Template is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Template model.
         * @return TEO_Offer_Template
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }
        
        /**
         * Get timed email offers email templates.
         * Data compatible with datatables.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @return mixed
         */
        public function get_offer_email_templates( $offer_id ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-get_offer_email_templates-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-get_offer_email_templates-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id ) );
            
            $offer_templates = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , true );
            if ( !is_array( $offer_templates ) )
                $offer_templates = array();

            $data = array(
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => array()
            );

            foreach ( $offer_templates as $index => $template ) {

                $d = array(
                    $index,
                    $template[ 'subject' ],
                    $template[ 'schedule' ],
                    $template[ 'wrap-wc-header-footer' ],
                    $template[ 'heading-text' ],
                    $this->_plugin_constants->OFFER_TEMPLATES_TABLE_COLUMN_ACTIONS( $offer_id , $index )
                );

                $d = apply_filters( 'TEO_Offer_Template_table_item_data' , $d , $offer_id , $index , $template );

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

                // Make TID
                $data[ 'data' ][ $index ][ 0 ] = 'T' . $d[ 0 ];

                // Trim subject text
                $subject_text = substr( $d[ 1 ] , 0 , 56 );
                if ( $subject_text != $d[ 1 ] )
                    $subject_text .= "...";

                $data[ 'data' ][ $index ][ 1 ] = $subject_text;

                // Make scheduled consumable by mere mortals
                $data[ 'data' ][ $index ][ 2 ] = sprintf( _n( '%1$s Day after order is completed' , '%1$s Days after order is completed' ,  $d[ 2 ] , 'timed-email-offers' ) ,  $d[ 2 ] );

                // Trim heading text
                $heading_text = $d[ 4 ];
                if ( $heading_text != "" ) {

                    $heading_text = substr( $d[ 4 ] , 0 , 56 );
                    if ( $heading_text != $d[ 4 ] )
                        $heading_text .= "...";

                };

                $data[ 'data' ][ $index ][ 4 ] = $heading_text;

            }

            $data = apply_filters( 'teo_finalize_offer_templates_table_data' , $data , $offer_id );

            return $data;

        }

        /**
         * Add offer template.
         *
         * @since 1.0.0
         * @access private
         *
         * @param $offer_id
         * @param $template_data
         * @return mixed
         */
        public function add_offer_template( $offer_id , $template_data ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-add_offer_template-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'template_data' => $template_data ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-add_offer_template-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'template_data' => $template_data ) );

            $template_data = $this->validate_and_sanitize_template_data( $template_data );
            if ( !$template_data )
                return new WP_Error( 'teo-add_offer_template-invalid-template-data' , __( 'Invalid Template Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'template_data' => $template_data )  );
            
            $offer_templates = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , true );

            if ( !is_array( $offer_templates ) )
                $offer_templates = array();
            
            // Check if new template has duplicate schedule
            $has_dup_sched = false;
            foreach ( $offer_templates as $offer_template ) {
                
                if ( $offer_template[ 'schedule' ] == $template_data[ 'schedule' ] ) {

                    $has_dup_sched = true;
                    break;

                }

            }
            
            if ( $has_dup_sched )
                return new WP_Error( 'teo-add_offer_template-add-offer-template-results-dup-entry' , __( "<b>Please fill the template form properly</b><br> The <b>schedule</b> you've chosen is already taken by another Email Template, please change to another day before saving." , "timed-email-offers" ) , array( 'offer_id' => $offer_id , 'template_data' => $template_data ) );
            else {

                $offer_templates[] = $template_data;

                update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , $offer_templates );

                // Construct new row markup
                end( $offer_templates );
                $new_index = key( $offer_templates );

                return $new_index;

            }

        }

        /**
         * Get offer template info.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $index
         * @return mixed
         */
        public function get_offer_template_info( $offer_id , $index ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-get_offer_template_info-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index ) );
            
            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-get_offer_template_info-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index ) );
            
            $offer_templates = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , true );
            if ( !is_array( $offer_templates ) )
                $offer_templates = array();
            
            if ( !array_key_exists( $index , $offer_templates ) )
                return new WP_Error( 'teo-get_offer_template_info-offer-info' , __( 'The offer template you wish to get info does not exist on record.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index ) );
            else 
                return $offer_templates[ $index ];
            
        }

        /**
         * Edit offer template.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $index
         * @param $template_data
         * @return array
         */
        public function edit_offer_template( $offer_id , $index , $template_data ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-edit_offer_template-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index , 'template_data' => $template_data ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-edit_offer_template-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index , 'template_data' => $template_data ) );            
            
            $template_data = $this->validate_and_sanitize_template_data( $template_data );
            if ( !$template_data )
                return new WP_Error( 'teo-edit_offer_template-invalid-template-data' , __( 'Invalid Template Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'template_data' => $template_data )  );
            
            $offer_templates = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , true );
            if ( !is_array( $offer_templates ) )
                $offer_templates = array();

            if ( !array_key_exists( $index , $offer_templates ) )
                return new WP_Error( 'teo-edit_offer_template-offer-template-to-edit-not-exist' , __( 'The offer template you wish to edit does not exist on record.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index , 'template_data' => $template_data ) );                
            else {

                // Check if new template has duplicate schedule
                $has_dup_sched = false;
                foreach ( $offer_templates as $idx => $offer_template ) {
                    
                    if ( $idx == $index ) continue;

                    if ( $offer_template[ 'schedule' ] == $template_data[ 'schedule' ] ) {

                        $has_dup_sched = true;
                        break;

                    }

                }
                
                if ( $has_dup_sched )
                    return new WP_Error( 'teo-edit_offer_template-edit-offer-template-results-dup-entry' , __( "<b>Please fill the template form properly</b><br> The <b>schedule</b> you've chosen is already taken by another Email Template, please change to another day before saving." , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index , 'template_data' => $template_data ) );                
                else {

                    $offer_templates[ $index ] = $template_data;
                    update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , $offer_templates );
                    return true;

                }

            }

        }

        /**
         * Delete offer template.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $index
         * @return mixed
         */
        public function delete_offer_template( $offer_id , $index ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-delete_offer_template-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-delete_offer_template-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index ) );
            
            $offer_templates = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , true );
            if ( !is_array( $offer_templates ) )
                $offer_templates = array();

            if ( !array_key_exists( $index , $offer_templates ) )
                return new WP_Error( 'teo-delete_offer_template-template-to-delete-not-exist' , __( 'The offer template you wish to delete does not exist on record.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'index' => $index ) );                
            else {

                global $wpdb;
                
                $query         = "SELECT recipient_id , order_id FROM " . $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() . " WHERE offer_id = $offer_id;";
                $query_results = $wpdb->get_results( $query );

                foreach ( $query_results as $result ) {

                    // Get scheduled emails
                    $query        = "SELECT email_token FROM " . $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() . " WHERE recipient_id = $result->recipient_id AND template_id = $index;";
                    $email_tokens = $wpdb->get_results( $query );

                    foreach ( $email_tokens as $email_token )
                        $this->_offer_schedule->unschedule_email_offer_for_customer( $offer_id , $result->order_id , $index , $email_token->email_token );
                    
                    $recipient_id = TEO_Helper::get_offer_recipient_id( $result->order_id , $offer_id );
                    
                    $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS() , array( 'email_token' => $email_token->email_token , 'recipient_id' => $recipient_id ) , array( '%s' , '%d' ) );
                    
                }

                $wpdb->delete( $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() ,  array( 'template_id' => $index ), array( '%d' ) );
                
                // Delete the template
                unset( $offer_templates[ $index ] );
                update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_TEMPLATES() , $offer_templates );

                return true;

            }

        }

        /**
         * Send test email for a specific offer template.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $template_index
         * @param $recipient_email
         * @return mixed
         */
        public function send_offer_test_email( $offer_id , $template_index , $recipient_email ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-send_offer_test_email-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'template_index' => $template_index , 'recipient_email' => $recipient_email ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-send_offer_test_email-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'template_index' => $template_index , 'recipient_email' => $recipient_email ) );            

            if ( !filter_var( $recipient_email , FILTER_VALIDATE_EMAIL ) )
                return new WP_Error( 'teo-send_offer_test_email-invalid-recipient-email' , __( 'Invalid Recipient Email' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'template_index' => $template_index , 'recipient_email' => $recipient_email ) );

            $constants = TEO_Constants::instance();
            $template  = $this->get_offer_template_info( $offer_id , $template_index );
            
            if ( is_wp_error( $template ) )
                return new WP_Error( 'teo-send_offer_test_email-email-to-send-test-not-exist' , __( 'The template you wish to send a test email does not exist on record.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'template_index' => $template_index , 'recipient_email' => $recipient_email ) );                            
            else {

                $mailer = TEO_Mailer::instance( array( 'TEO_Constants' => $constants ) );
                $bcc    = apply_filters( 'teo_send_test_email_bcc' , array() );

                if ( is_array( $bcc ) && !empty( $bcc ) )
                    $email_sent = $mailer->send_email( $recipient_email , $template[ 'subject' ] , $template[ 'message' ] , $template[ 'wrap-wc-header-footer' ] , $template[ 'heading-text' ] , "" , null , null , null , $bcc );
                else
                    $email_sent = $mailer->send_email( $recipient_email , $template[ 'subject' ] , $template[ 'message' ] , $template[ 'wrap-wc-header-footer' ] , $template[ 'heading-text' ] );

                if ( !$email_sent )
                    return new WP_Error( 'teo-send_offer_test_email-send-test-email-fail' , __( 'Failed to send test email.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'template_index' => $template_index , 'recipient_email' => $recipient_email ) );                            
                else
                    return true;

            }

        }




        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

        /**
         * Validate and sanitize template data.
         * 
         * @since 1.0.0
         * @access public
         * 
         * @param $template_data
         * @return boolean
         */
        public function validate_and_sanitize_template_data( $template_data ) {
            
            if ( is_array( $template_data ) && 
                 array_key_exists( 'schedule' , $template_data ) && array_key_exists( 'schedule-text' , $template_data ) && 
                 array_key_exists( 'subject' , $template_data ) && array_key_exists( 'message' , $template_data ) && 
                 array_key_exists( 'wrap-wc-header-footer' , $template_data ) && in_array( $template_data[ 'wrap-wc-header-footer' ] , array( 'yes' , 'no' ) ) &&
                 array_key_exists( 'heading-text' , $template_data ) ) {

                $template_data = apply_filters( 'teo_additional_template_data_validation' , $template_data );

                if ( $template_data ) {

                    $template_data[ 'schedule' ]      = filter_var( trim( $template_data[ 'schedule' ] ) , FILTER_SANITIZE_NUMBER_INT );
                    $template_data[ 'schedule-text' ] = filter_var( trim( $template_data[ 'schedule-text' ] ) , FILTER_SANITIZE_STRING );
                    $template_data[ 'subject' ]       = filter_var( trim( $template_data[ 'subject' ] ) , FILTER_SANITIZE_STRING );
                    $template_data[ 'heading-text' ]  = filter_var( trim( $template_data[ 'heading-text' ] ) , FILTER_SANITIZE_STRING );
                    $template_data[ 'message' ]       = wp_kses_post( $template_data[ 'message' ] );

                    return apply_filters( 'teo_additional_template_data_sanitation' , $template_data );

                } else
                    return false;
                
            } else
                return false;
            
        }

    }

}
