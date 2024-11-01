<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Mailer' ) ) {

    /**
     * Class TEO_Mailer
     *
     * Model that houses the logic relating to plugin's mailer.
     *
     * @since 1.0.0
     */
    final class TEO_Mailer {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Mailer.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Mailer
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
         * TEO_Mailer constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Mailer model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];

        }

        /**
         * Ensure that only one instance of TEO_Mailer is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Mailer model.
         * @return TEO_Mailer
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Get email from name.
         *
         * @since 1.0.0
         * @access private
         *
         * @return string
         */
        private function _get_from_name() {

            $from_name = trim( get_option( "woocommerce_email_from_name" ) );

            if ( !$from_name )
                $from_name = get_bloginfo( 'name' );

            return apply_filters( 'TEO_Mailer_from_name' , $from_name );

        }

        /**
         * Get from email.
         *
         * @since 1.0.0
         * @access private
         *
         * @return string
         */
        private function _get_from_email() {

            $fromEmail = trim( get_option( "woocommerce_email_from_address" ) );

            if ( !$fromEmail )
                $fromEmail = get_option( 'admin_email' );

            return apply_filters( 'wwlc_filter_from_email' , $fromEmail );

        }

        /**
         * Construct email headers.
         *
         * @param $from_name
         * @param $from_email
         * @param array $cc
         * @param array $bcc
         * @return array
         *
         * @since 1.3.0
         */
        private function _construct_email_header( $from_name , $from_email , $cc = array() , $bcc = array() ) {

            $headers[] = 'From: ' . $from_name  . ' <' . $from_email . '>';

            if ( is_array( $cc ) )
                foreach ( $cc as $c )
                    $headers[] = 'Cc: ' . $c;

            if ( is_array( $bcc ) )
                foreach ( $bcc as $bc )
                    $headers[] = 'Bcc: ' . $bc;

            $headers[] = 'Content-Type: text/html; charset=' . get_option( 'blog_charset' );

            return $headers;

        }

        /**
         * Send email.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $recipients
         * @param $subject
         * @param $message
         * @param $wrap_message
         * @param string $heading_text
         * @param string $attachments
         * @param null $from_name
         * @param null $from_email
         * @param null $cc
         * @param null $bcc
         * @return boolean
         */
        public function send_email( $recipients , $subject , $message , $wrap_message , $heading_text = "" , $attachments = "" , $from_name = null , $from_email = null , $cc = null , $bcc = null ) {

            $mailer = WC()->mailer();

            if ( is_null( $from_name ) )
                $from_name = $this->_get_from_name();

            if ( is_null( $from_email ) )
                $from_email = $this->_get_from_email();

            $headers = $this->_construct_email_header( $from_name , $from_email , $cc , $bcc );

            if ( $wrap_message == 'yes' ) {

                if ( $heading_text == "" )
                    $message = $mailer->wrap_message( $subject , $message );
                else
                    $message = $mailer->wrap_message( $heading_text , $message );

            }

            if ( !TEO_Helper::is_plugin_active( 'timed-email-offers-premium/timed-email-offers-premium.php' ) ) {

                $message .= '<br><br><br><br>';
                $message .= sprintf( __( '<em>Powered By <a href="%1$s" target="_blank">Timed Email Offers - Marketing Suite</a><em>' , 'timed-email-offers-premium' ) , 'https://marketingsuiteplugin.com/product/timed-email-offers/?utm_source=TEO&utm_medium=Powered%20By&utm_campaign=TEO' );

            }

            return $mailer->send( $recipients , $subject , $message , $headers , $attachments );

        }

    }

}