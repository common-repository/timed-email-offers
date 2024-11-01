<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Offer_Decline_Action' ) ) {

    /**
     * Model that houses the logic of decline offer action.
     *
     * Class TEO_Offer_Decline_Action
     */
    final class TEO_Offer_Decline_Action {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Offer_Decline_Action.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_Decline_Action
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
         * TEO_Offer_Decline_Action constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Decline_Action model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];

        }

        /**
         * Ensure that only one instance of TEO_Offer_Decline_Action is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_Decline_Action model.
         * @return TEO_Offer_Decline_Action
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Execute 'do-nothing' decline offer action.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $decline_action
         * @param $offer_id
         * @param $order_id
         * @param $email_token
         */
        public function execute_do_nothing_decline_offer_action( $decline_action , $offer_id , $order_id , $email_token ) {

            // Do nothing... Surprise!

        }

        /**
         * Save decline offer actions.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function save_decline_offer_actions( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'teo-save_decline_offer_actions-invalid-operation' , __( 'Invalid Operation.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !TEO_Helper::current_user_authorized() )
                return new WP_Error( 'teo-save_decline_offer_actions-authorization-failed' , __( 'Authorization Failed.' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( is_array( $data ) ) {

                $data = $this->validate_and_sanitize_offer_decline_actions_data( $data );
                if ( !$data )
                    return new WP_Error( 'teo-save_decline_offer_actions-invalid-offer-decline-actions-data' , __( 'Invalid Offer Decline Actions Data' , 'timed-email-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );                    
                
            } else
                $data = '';
            
            $data = apply_filters( 'before_save_decline_offer_actions' , $data , $offer_id );

            update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_DECLINE_ACTIONS() , $data );

            return true;

        }


        

        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

        /**
         * Validate and sanitize offer decline actions data.
         * 
         * @since 1.0.0
         * @access public
         * 
         * @return boolean
         */
        public function validate_and_sanitize_offer_decline_actions_data( $data ) {
            
            $data = apply_filters( 'additional_offer_decline_actions_data_validation' , $data );

            if ( $data ) {

                if ( array_key_exists( 'do-nothing' , $data ) )
                    $data[ 'do-nothing' ] = null;

                $data = apply_filters( 'additional_offer_decline_actions_data_sanitation' , $data );

                return $data; // Nothing to sanitize

            } else
                return false;
            
        }

    }

}