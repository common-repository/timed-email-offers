<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Constants' ) ) {

    /**
     * Class TEO_Constants
     *
     * Model that houses the various constants Timed Email Offers plugin utilizes.
     *
     * @since 1.0.0
     */
    final class TEO_Constants {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Constants.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Constants
         */
        private static $_instance;

        /**
         * Property that holds the plugin's main file directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_MAIN_PLUGIN_FILE_PATH;

        /**
         * Property that holds the plugin's root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_PLUGIN_DIR_PATH;

        /**
         * Property that holds the plugin's root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_PLUGIN_DIR_URL;

        /**
         * Property that holds the plugin's basename.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_PLUGIN_BASENAME;

        /**
         * Property that holds the plugin's unique token.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_TOKEN;

        /**
         * Property that holds the plugin's 'current' version.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_VERSION;

        /**
         * Property that holds the plugin's text domain. Used for internationalization.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_TEXT_DOMAIN;

        /**
         * Property that holds the 'css' root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_CSS_ROOT_URL;

        /**
         * Property that holds the 'images' root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_IMAGES_ROOT_URL;

        /**
         * Property that holds the 'js' root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_JS_ROOT_URL;

        /**
         * Property that holds the 'logs' root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_LOGS_ROOT_PATH;

        /**
         * Property that holds the 'models' root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_MODELS_ROOT_PATH;

        /**
         * Property that holds the path of the current theme overridden plugin template files.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_THEME_TEMPLATE_PATH;

        /**
         * Property that holds the 'views' root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_VIEWS_ROOT_PATH;

        /**
         * Property that holds the offer custom post type name.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OFFER_CPT_NAME;

        /**
         * Property that holds the offer custom post type meta boxes.
         * 
         * @since 1.1.0
         * @access private
         * @var array
         */
        private $_OFFER_CPT_META_BOXES;

        /**
         * Property that holds the array of user roles that are allowed to manage "Timed Email Offers" plugin.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_ROLES_ALLOWED_TO_MANAGE_TEO;

        /**
         * Property that holds the offer template table headings.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_OFFER_TEMPLATES_TABLE_TOTAL_HEADINGS;

        /**
         * Property that holds the types of timed email offer conditions.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_TIMED_EMAIL_OFFER_CONDITION_TYPES;

        /**
         * Property that holds the logic of either to show only basic configuration for the timed email offer condition options.
         *
         * @since 1.0.0
         * @access private
         * @var bool
         */
        private $_TIMED_EMAIL_OFFER_CONDITION_TYPES_SIMPLE_MODE;

        /**
         * Property that holds the product quantity in cart logic conditions ( = , != , > , < ).
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_LOGIC_CONDITIONS;

        /**
         * Property that holds the accept timed email offer action types.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_ACCEPT_TIMED_EMAIL_OFFER_ACTION_TYPES;

        /**
         * Property that holds the decline offer action types.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_DECLINE_OFFER_ACTION_TYPES;

        /**
         * Property that holds the option to either show basic configuration for the decline offer action types.
         *
         * @since 1.0.0
         * @access private
         * @var bool
         */
        private $_DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE;

        /**
         * Property that holds the various state of the recipient's offer response status.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_RECIPIENT_OFFER_RESPONSE_STATUS;

        /**
         * Property that holds the various state of the offer email status.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_OFFER_EMAIL_SEND_STATUS;

        /**
         * Property that holds the various state of the offer email response status.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_OFFER_EMAIL_RESPONSE_STATUS;

        /**
         * Property that holds the list of headings for the offer recipients table.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_OFFER_RECIPIENTS_TABLE_HEADINGS;

        /**
         * Property that holds the list of headings for the offer recipient popup data schedules table.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_OFFER_RECIPIENT_POPUP_SCHEDULED_EMAILS_TABLE_HEADINGS;

        /**
         * Property that holds the list of headings for the blacklist table headings.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_BLACKLIST_TABLE_HEADINGS;

        /**
         * Property that holds the list of blacklist types.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_BLACKLIST_TYPES;

        /**
         * Property that holds the total number columns for the product quantity in cart table.
         * Basically used for extensibility in the no product added entry on table. The colspan.
         *
         * @since 1.0.0
         * @access private
         * @var int
         */
        private $_PRODUCT_QUANTITY_IN_ORDER_TABLE_TOTAL_COLUMNS;

        /**
         * Property that holds the total number of columns for the accept offer action "Add products to cart" table.
         *
         * @since 1.0.0
         * @access private
         * @var int
         */
        private $_ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS;

        /**
         * Property that holds the total number of columns for the accept offer action "Apply coupons to cart" table.
         *
         * @since 1.0.0
         * @access private
         * @var int
         */
        private $_APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS;

        /**
         * Property that holds the total number of columns for the offer recipient popup data schedules table.
         *
         * @since 1.0.0
         * @access private
         * @var int
         */
        private $_OFFER_RECIPIENT_POPUP_SCHEDULES_TABLE_TOTAL_COLUMNS;


        /*
        |--------------------------------------------------------------------------
        | Mesc
        |--------------------------------------------------------------------------
        */

        /**
         * Default email template content.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_DEFAULT_EMAIL_TEMPLATE_CONTENT;

        /**
         * Mesc option that determines whether old cron jobs are updated to new one.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CRON_JOBS_UPDATED;


        /*
        |--------------------------------------------------------------------------
        | Custom Tables
        |--------------------------------------------------------------------------
        */

        /**
         * Offer recipients custom table name.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_RECIPIENTS;
        
        /**
         * Option that holds the current offer recipients custom table version.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_RECIPIENTS_VERSION;

        /**
         * Email schedules custom table name.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS;
        
        /**
         * Option that holds the current email schedules custom table version.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS_VERSION;
        
        /**
         * Offer email views logs custom table name.
         * 
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS;

        /**
         * Option that holds the current offer email views logs custom table version.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS_VERSION;

        /*
        |--------------------------------------------------------------------------
        | Post Meta
        |--------------------------------------------------------------------------
        */

        // 'timed_email_offer' cpt post meta

        /**
         * Property that holds the offer's schedules post meta.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_OFFER_TEMPLATES;

        /**
         * Property that holds offer's conditions post meta.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_OFFER_CONDITIONS;

        /**
         * Property that holds the post meta for the actions to take if an offer is accepted.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_OFFER_ACCEPT_ACTIONS;

        /**
         * Property that holds the post meta for the actions to take if an offer is declined.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_OFFER_DECLINE_ACTIONS;

        /**
         * Property that holds the post meta for the recipients of a timed email offer.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_OFFER_RECIPIENTS;

        /**
         * Property that holds the list of timed email offers id in which an order is attached with.
         * Post meta of an order.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_POST_META_ORDER_LINKED_OFFER_IDS;

        /**
         * Order post meta holding the offer id that this order is linked.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_POST_META_ORDER_OFFER_ID;

        /**
         * Order post meta holding the offer recipient id (order id) that this order is linked.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_POST_META_ORDER_OFFER_RECIPIENT_ID;

        /**
         * Order post meta holding the offer email token that this order is linked.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_POST_META_ORDER_OFFER_EMAIL_TOKEN;

        /**
         * Post meta holding offer order. Sort order.
         * 
         * @since 1.2.0
         * @access private
         *
         * @var string
         */
        private $_POST_META_OFFER_ORDER;


        /*
        |--------------------------------------------------------------------------
        | Options
        |--------------------------------------------------------------------------
        */

        // Cron Hooks

        /**
         * Property that holds the cron hook name for sending email offers.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_CRON_HOOK_SEND_EMAIL_OFFER;

        /**
         * Property that holds the cron hook name for declining offer for a recipient if offer timeout period is reached.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT;

        // Sessions

        /**
         * Property that holds the session key that specifies that this is a timed email offer order.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_SESSION_TIMED_EMAIL_OFFER_ORDER;

        // Pages Params

        /**
         * Property that holds the decline offer page offer id param.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_PAGE_PARAM_OFFER_ID;

        /**
         * Property that holds the decline offer page order id param.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_PAGE_PARAM_ORDER_ID;

        /**
         * Property that holds the decline offer page email token param.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_PAGE_PARAM_EMAIL_TOKEN;

        // Pages Options

        /**
         * Property that holds the decline offer page id option.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OPTION_DECLINE_OFFER_PAGE_ID;

        /**
         * Property that holds the unsubscribe page id option.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OPTION_UNSUBSCRIBE_PAGE_ID;

        /**
         * Property that holds the accept offer page id option.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OPTION_ACCEPT_OFFER_PAGE_ID;

        /**
         * Property that holds the invalid offer page id option.
         *
         * @since 1.1.0
         * @access private
         * @var string
         */
        private $_OPTION_INVALID_OFFER_PAGE_ID;

        // Accept Options

        /**
         * Property that holds the option to retain cart contents on offer accept.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OPTION_RETAIN_CART_CONTENTS_ON_OFFER_ACCEPT;

        /**
         * Property that holds the option to turn off notice when offer is successfully accepted.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OPTION_TURN_OFF_NOTICE_ON_OFFER_ACCEPTANCE;

        /**
         * Property that holds the option on whether to unschedule any remaining unsent email of an offer on offer accept or not.
         *
         * @since 1.1.0
         * @access private
         * @var string
         */
        private $_OPTION_ONLY_UNSCHED_REMAINING_EMAILS_ON_OFFER_CONVERSION;

        // Decline Options

        /**
         * Property that holds the option of the time out period of an offer.
         * Time out period is based on the send date time of the last email of an offer.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_OPTION_OFFER_TIMEOUT_PERIOD;

        /**
         * Property that holds the option whether to execute decline actions of an offer
         * when a recipient triggers a time out of the offer.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $_OPTION_EXECUTE_DECLINE_ACTIONS_ON_TIMEOUT;

        // Blacklist Options

        /**
         * Property that holds the option of blacklist of people from timed email offers.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OPTION_BLACKLIST;

        // Help Options

        /**
         * Property that holds the option of either cleaning up all plugin options upon plugin un-installation.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OPTION_CLEANUP_PLUGIN_OPTIONS;




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

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ) , '1.0.0' );

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
         * TEO_Constants constructor. Initialize various property values.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

            global $wpdb;

            // Paths
            $this->_MAIN_PLUGIN_FILE_PATH = WP_PLUGIN_DIR . '/timed-email-offers/timed-email-offers.php';

            $this->_PLUGIN_DIR_PATH = plugin_dir_path( $this->_MAIN_PLUGIN_FILE_PATH );
            $this->_PLUGIN_DIR_URL  = plugin_dir_url( $this->_MAIN_PLUGIN_FILE_PATH );
            $this->_PLUGIN_BASENAME = plugin_basename( dirname( $this->_MAIN_PLUGIN_FILE_PATH ) );

            $this->_CSS_ROOT_URL    = $this->_PLUGIN_DIR_URL . 'css/';
            $this->_IMAGES_ROOT_URL = $this->_PLUGIN_DIR_URL . 'images/';
            $this->_JS_ROOT_URL     = $this->_PLUGIN_DIR_URL . 'js/';

            $this->_LOGS_ROOT_PATH      = $this->_PLUGIN_DIR_PATH . 'logs/';
            $this->_MODELS_ROOT_PATH    = $this->_PLUGIN_DIR_PATH . 'models/';
            $this->_THEME_TEMPLATE_PATH = apply_filters( 'teo_theme_template_path' , 'timed-email-offers' );
            $this->_VIEWS_ROOT_PATH     = $this->_PLUGIN_DIR_PATH . 'views/';

            $this->_TOKEN       = 'teo'; // short for Timed Email Offers
            $this->_VERSION     = '1.2.2';
            $this->_TEXT_DOMAIN = 'timed-email-offers';

            $this->_OFFER_CPT_NAME = 'timed_email_offer';

            $this->_OFFER_CPT_META_BOXES = apply_filters( 'teo_offer_cpt_meta_boxes' , array(
                'timed-email-offer-templates' => array(
                    'title'    => __( 'Email Templates' , 'timed-email-offers' ),
                    'callback' => 'view_timed_email_offer_templates_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'high'
                ),
                'timed-email-offer-conditions' => array(
                    'title'    => __( 'Timed Email Offer Conditions' , 'timed-email-offers' ),
                    'callback' => 'view_timed_email_offer_conditions_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'high'
                ),
                'accept-timed-email-offer-actions' => array(
                    'title'    => __( 'Timed Email Offer Accept Actions' , 'timed-email-offers' ),
                    'callback' => 'view_accept_timed_email_offer_actions_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'high'
                ),
                'decline-timed-email-offer-actions' => array(
                    'title'    => __( 'Timed Email Offer Decline Actions' , 'timed-email-offers' ),
                    'callback' => 'view_decline_timed_email_offer_actions_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'high'
                ),
                'timed-email-offer-recipients' => array(
                    'title'    => __( 'Timed Email Offer Recipients' , 'timed-email-offers' ),
                    'callback' => 'view_timed_email_offer_recipients_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'low'
                ),
                'timed-email-offer-options' => array(
                    'title' => __( 'Offer Options' , 'timed-email-offers' ),
                    'callback' => 'view_timed_email_offer_options_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'side',
                    'priority' => 'low'
                ),
                'timed-email-offer-upgrade' => array(
                    'title'    => __( 'Premium Add-on' , 'timed-email-offers' ),
                    'callback' => 'view_timed_email_offer_upgrade_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'side',
                    'priority' => 'low'
                )
            ) );

            $this->_ROLES_ALLOWED_TO_MANAGE_TEO = apply_filters( 'teo_roles_allowed_to_manage_teo' , array( 'administrator' ) );

            $this->_OFFER_TEMPLATES_TABLE_TOTAL_HEADINGS = apply_filters( 'TEO_Offer_Template_table_total_headings' , array(
                'tid'             => __( 'TID' , 'timed-email-offers' ),
                'subject'         => __( 'Subject' , 'timed-email-offers' ),
                'schedule'        => __( 'Schedule' , 'timed-email-offers' ),
                'wrap-wc'         => __( 'WC Wrapper' , 'timed-email-offers' ),
                'heading-text'    => __( 'Heading Text' , 'timed-email-offers' ),
                'column-controls' => '',
            ) );

            $this->_TIMED_EMAIL_OFFER_CONDITION_TYPES = apply_filters( 'teo_offer_condition_types' , array(
                'product-quantity'   => __( 'Product Quantity In The Order' , 'timed-email-offers' ),
                'product-category'   => __( 'Product Category Exist In The Order' , 'timed-email-offers' ),
                'customer-user-role' => __( 'Customer User Role' , 'timed-email-offers' ),
                'has-ordered-before' => __( 'Has Ordered Before' , 'timed-email-offers' ),
                'order-subtotal'     => __( 'Order Subtotal' , 'timed-email-offers' ),
                'order-quantity'     => __( 'Order Quantity' , 'timed-email-offers' )
            ) );

            $this->_TIMED_EMAIL_OFFER_CONDITION_TYPES_SIMPLE_MODE = apply_filters( 'teo_offer_condition_types_simple_mode' , true );

            $this->_LOGIC_CONDITIONS = apply_filters( 'teo_logic_conditions' , array(
                "="  => __( 'EXACTLY' , 'timed-email-offers' ),
                "!=" => __( 'ANYTHING EXCEPT' , 'timed-email-offers' ),
                ">"  => __( 'MORE THAN' , 'timed-email-offers' ),
                "<"  => __( 'LESS THAN' , 'timed-email-offers' ),
            ) );

            $this->_ACCEPT_TIMED_EMAIL_OFFER_ACTION_TYPES = apply_filters( 'teo_accept_timed_email_offer_action_types' , array(
                'add-products-to-cart'  => __( 'Add Products To Cart' , 'timed-email-offers' ),
                'apply-coupons-to-cart' => __( 'Apply Coupons To Cart' , 'timed-email-offers' ),
            ) );

            $this->_DECLINE_OFFER_ACTION_TYPES = apply_filters( 'teo_decline_offer_action_types' , array(
                'do-nothing'           => __( 'Do Nothing' , 'timed-email-offers' ),
                'add-to-another-offer' => __( 'Add Them To Another Timed Email Offer' , 'timed-email-offers' )
            ) );

            $this->_DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE = apply_filters( 'teo_decline_offer_action_types_simple_mode' , true );

            $this->_RECIPIENT_OFFER_RESPONSE_STATUS = apply_filters( 'teo_recipient_offer_response_status' , array(
                'na'       => __( 'Pending Send' , 'timed-email-offers' ), // No action taken coz email not sent yet
                'idle'     => __( 'Awaiting Response' , 'timed-email-offers' ), // Email is sent but customer has not acted on it yet
                'accepted' => __( 'Accepted' , 'timed-email-offers' ),
                'declined' => __( 'Declined' , 'timed-email-offers' )
            ) );

            $this->_OFFER_EMAIL_SEND_STATUS = apply_filters( 'teo_offer_email_send_status' , array(
                'pending'   => __( 'Pending' , 'timed-email-offers' ),
                'sent'      => __( 'Sent' ,  'timed-email-offers' ),
                'cancelled' => __( 'Cancelled' , 'timed-email-offers' ),
                'failed'    => __( 'Failed' , 'timed-email-offers' )
            ) );

            $this->_OFFER_EMAIL_RESPONSE_STATUS = apply_filters( 'teo_offer_email_response_status' , array(
                'na'       => __( 'Pending Send' , 'timed-email-offers' ), // No action taken coz email not sent yet
                'idle'     => __( 'Awaiting Response' , 'timed-email-offers' ), // Email is sent but customer has not acted on it yet
                'accepted' => __( 'Accepted' , 'timed-email-offers' ),
                'declined' => __( 'Declined' , 'timed-email-offers' )
            ) );

            $this->_OFFER_RECIPIENTS_TABLE_HEADINGS = apply_filters( 'teo_offer_recipients_table_headings' , array(
                "recipient-name"                  => __( 'Name' , 'timed-email-offers' ),
                "recipient-email"                 => __( 'Email' , 'timed-email-offers' ),
                "recipient-order-no"              => __( 'Order No.' , 'timed-email-offers' ),
                "recipient-order-completed-date"  => __( 'Order Completed' , 'timed-email-offers' ),
                "recipient-offer-response-status" => __( 'Status' , 'timed-email-offers' ),
                "column-controls"                 => ''
            ) );

            $this->_OFFER_RECIPIENT_POPUP_SCHEDULED_EMAILS_TABLE_HEADINGS = apply_filters( 'teo_offer_recipient_popup_scheduled_emails_table_headings' , array(
                'tid'             => __( 'TID' , 'timed-email-offers' ),
                'schedule'        => __( 'Schedule' , 'timed-email-offers' ),
                'send-status'     => __( 'Send Status' , 'timed-email-offers' ),
                'response-status' => __( 'Response Status' , 'timed-email-offers' ),
                'column-controls' => '',
            ) );

            $this->_BLACKLIST_TABLE_HEADINGS = apply_filters( 'teo_blacklist_table_headings' , array(
                'email'           => __( 'Email' , 'timed-email-offers' ),
                'opt-out-date'    => __( 'Opt-out Date' , 'timed-email-offers' ),
                'type'            => __( 'Type' , 'timed-email-offers' ),
                'column-controls' => ''
            ) );

            $this->_BLACKLIST_TYPES = apply_filters( 'teo_' , array(
                'all'          => __( 'All' ,  'timed-email-offers' ),
                'unsubscribed' => __( 'Unsubscribed' , 'timed-email-offers' ),
                'manual'       => __( 'Manual' , 'timed-email-offers' )
            ) );

            $this->_PRODUCT_QUANTITY_IN_ORDER_TABLE_TOTAL_COLUMNS       = apply_filters( 'teo_product_quantity_in_order_table_total_columns' , 4 );
            $this->_ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS            = apply_filters( 'teo_add_products_to_cart_table_total_columns' , 3 );
            $this->_APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS           = apply_filters( 'teo_apply_coupons_to_cart_table_total_columns' , 4 );
            $this->_OFFER_RECIPIENT_POPUP_SCHEDULES_TABLE_TOTAL_COLUMNS = apply_filters( 'teo_offer_recipient_popup_schedules_table_total_columns' , 5 );


            // Mesc

            $this->_DEFAULT_EMAIL_TEMPLATE_CONTENT = apply_filters( 'teo_default_email_template_content' ,
                                                                    __( 'Hi {recipient_first_name},<br><br>' .
                                                                    'This is some example email content. Please personalize this to your offer.<br>' .
                                                                    'This is your big chance to sell your customer on what your offer is all about!<br><br>' .
                                                                    '<a href="http://{accept_offer_url}">Accept Offer</a><br><br>' .
                                                                    'Not interested? Click here to <a href="http://{decline_offer_url}">decline this offer</a>.<br><br>' .
                                                                    'Or stop receiving offers, click <a href="http://{unsubscribe_offer_url}">Unsubscribe</a>' , 'timed-email-offers' ) );
            
            $this->_CRON_JOBS_UPDATED = 'teo_cron_jobs_updated';

            
            // Custom tables names (without prefix) and versions.
            $this->_CUSTOM_TABLE_OFFER_RECIPIENTS               = $wpdb->prefix . 'teo_offer_recipients';
            $this->_CUSTOM_TABLE_OFFER_RECIPIENTS_VERSION       = 'teo_offer_recipients_table_version';
            $this->_CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS         = $wpdb->prefix . 'teo_offer_scheduled_emails';
            $this->_CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS_VERSION = 'teo_offer_scheduled_emails_table_version';
            $this->_CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS         = $wpdb->prefix . 'teo_offer_email_views_logs';
            $this->_CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS_VERSION = 'teo_offer_email_views_logs_table_version';


            // Post Meta

            // 'timed_email_offer' cpt post meta
            $this->_POST_META_OFFER_TEMPLATES          = 'teo_templates';
            $this->_POST_META_OFFER_CONDITIONS         = 'teo_conditions';
            $this->_POST_META_OFFER_ACCEPT_ACTIONS     = 'teo_accept_actions';
            $this->_POST_META_OFFER_DECLINE_ACTIONS    = 'teo_decline_actions';
            $this->_POST_META_OFFER_RECIPIENTS         = 'teo_recipients';
            $this->_POST_META_ORDER_LINKED_OFFER_IDS   = 'teo_order_offer_ids';
            $this->_POST_META_ORDER_OFFER_ID           = 'teo_order_offer_id';
            $this->_POST_META_ORDER_OFFER_RECIPIENT_ID = 'teo_order_offer_recipient_id';
            $this->_POST_META_ORDER_OFFER_EMAIL_TOKEN  = 'teo_order_offer_email_token';
            $this->_POST_META_OFFER_ORDER              = 'teo_offer_order';


            // Options

            // Cron Hooks
            $this->_CRON_HOOK_SEND_EMAIL_OFFER         = 'teo_send_email_offer';
            $this->_CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT = 'teo_decline_email_offer';

            // Session
            $this->_SESSION_TIMED_EMAIL_OFFER_ORDER = 'timed_email_offer_order';

            // Pages Params
            $this->_PAGE_PARAM_OFFER_ID    = 'offer-id';
            $this->_PAGE_PARAM_ORDER_ID    = 'order-id';
            $this->_PAGE_PARAM_EMAIL_TOKEN = 'email-token';

            // Pages Options

            $this->_OPTION_DECLINE_OFFER_PAGE_ID = 'teo_decline_offer_page_id';
            $this->_OPTION_UNSUBSCRIBE_PAGE_ID   = 'teo_unsubscribe_page_id';
            $this->_OPTION_ACCEPT_OFFER_PAGE_ID  = 'teo_accept_offer_page_id';
            $this->_OPTION_INVALID_OFFER_PAGE_ID = 'teo_invalid_offer_page_id';

            // Acceptance Options

            $this->_OPTION_RETAIN_CART_CONTENTS_ON_OFFER_ACCEPT              = 'teo_retain_cart_contents_on_offer_accept';
            $this->_OPTION_TURN_OFF_NOTICE_ON_OFFER_ACCEPTANCE               = 'teo_turn_off_notice_on_offer_acceptance';
            $this->_OPTION_ONLY_UNSCHED_REMAINING_EMAILS_ON_OFFER_CONVERSION = 'teo_only_unsched_remaining_emails_on_offer_convertion';

            // Decline Options

            $this->_OPTION_OFFER_TIMEOUT_PERIOD               = 'teo_offer_timeout_period';
            $this->_OPTION_EXECUTE_DECLINE_ACTIONS_ON_TIMEOUT = 'teo_execute_decline_actions_on_timeout';

            // Blacklist Options

            $this->_OPTION_BLACKLIST = 'teo_blacklist';

            // Help Options

            $this->_OPTION_CLEANUP_PLUGIN_OPTIONS = 'teo_cleanup_plugin_options';

        }

        /**
         * Ensure that there is only one instance of TEO_Constants is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @return TEO_Constants
         */
        public static function instance() {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self();

            return self::$_instance;

        }


        /*
        |--------------------------------------------------------------------------
        | Property Getters
        |--------------------------------------------------------------------------
        |
        | Getter functions to read properties of the class.
        | These properties serves as the constants consumed by the plugin.
        |
        */

        /**
         * Return _MAIN_PLUGIN_FILE_PATH. Property that holds the plugin's main file directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function MAIN_PLUGIN_FILE_PATH() {

            return $this->_MAIN_PLUGIN_FILE_PATH;

        }

        /**
         * Return _PLUGIN_DIR_PATH property. Property that holds the plugin's root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PLUGIN_DIR_PATH() {

            return $this->_PLUGIN_DIR_PATH;

        }

        /**
         * Return _PLUGIN_DIR_URL property. Property that holds the plugin's root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PLUGIN_DIR_URL() {

            return $this->_PLUGIN_DIR_URL;

        }

        /**
         * Return _PLUGIN_BASENAME property. Property that holds the plugin's basename.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PLUGIN_BASENAME() {

            return $this->_PLUGIN_BASENAME;

        }

        /**
         * Return _TOKEN property. Property that holds the plugin's unique token.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function TOKEN() {

            return $this->_TOKEN;

        }

        /**
         * Return _VERSION property. Property that holds the plugin's 'current' version.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function VERSION() {

            return $this->_VERSION;

        }

        /**
         * Return _TEXT_DOMAIN property. Property that holds the 'views' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function TEXT_DOMAIN() {

            return $this->_TEXT_DOMAIN;

        }

        /**
         * Return _CSS_ROOT_URL property. Property that holds the 'css' root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function CSS_ROOT_URL() {

            return $this->_CSS_ROOT_URL;

        }

        /**
         * Return _IMAGES_ROOT_URL property. Property that holds the 'images' root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function IMAGES_ROOT_URL() {

            return $this->_IMAGES_ROOT_URL;

        }

        /**
         * Return _JS_ROOT_URL property. Property that holds the 'js' root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function JS_ROOT_URL() {

            return $this->_JS_ROOT_URL;

        }

        /**
         * Return _LOGS_ROOT_PATH. Property that holds the 'logs' root directory path.
         *
         * @since 1.0.0
         * @access public
         * @var string
         */
        public function LOGS_ROOT_PATH() {

            return $this->_LOGS_ROOT_PATH;

        }

        /**
         * Return _MODELS_ROOT_PATH. Property that holds the 'models' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function MODELS_ROOT_PATH() {

            return $this->_MODELS_ROOT_PATH;

        }
        
        /**
         * Return _THEME_TEMPLATE_PATH. Property that holds the path of the current theme overridden plugin template files.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function THEME_TEMPLATE_PATH() {

            return $this->_THEME_TEMPLATE_PATH;

        }

        /**
         * Return _VIEWS_ROOT_PATH property. Property that holds the 'views' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function VIEWS_ROOT_PATH() {

            return $this->_VIEWS_ROOT_PATH;

        }

        /**
         * Return _OFFER_CPT_NAME. Property that holds the Offer custom post type name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OFFER_CPT_NAME() {

            return $this->_OFFER_CPT_NAME;

        }

        /**
         * Return _OFFER_CPT_META_BOXES. Property that holds the offer custom post type meta boxes.
         *
         * @since 1.1.0
         * @access public
         * 
         * @return array
         */
        public function OFFER_CPT_META_BOXES() {

            return $this->_OFFER_CPT_META_BOXES;

        }

        /**
         * Return _ROLES_ALLOWED_TO_MANAGE_TEO. Property that holds the array of user roles that are allowed to manage "Timed Email Offers" plugin.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function ROLES_ALLOWED_TO_MANAGE_TEO() {

            return $this->_ROLES_ALLOWED_TO_MANAGE_TEO;

        }

        /**
         * Return _OFFER_TEMPLATES_TABLE_TOTAL_HEADINGS. Property that holds the offer template table headings.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function OFFER_TEMPLATES_TABLE_TOTAL_HEADINGS() {

            return $this->_OFFER_TEMPLATES_TABLE_TOTAL_HEADINGS;

        }

        /**
         * Return _TIMED_EMAIL_OFFER_CONDITION_TYPES. Property that holds the types of timed email offer conditions.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function TIMED_EMAIL_OFFER_CONDITION_TYPES() {

            return $this->_TIMED_EMAIL_OFFER_CONDITION_TYPES;

        }

        /**
         * Return _TIMED_EMAIL_OFFER_CONDITION_TYPES_SIMPLE_MODE. Property that holds the logic of either to show only basic configuration for the timed email offer condition options.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool
         */
        public function TIMED_EMAIL_OFFER_CONDITION_TYPES_SIMPLE_MODE() {

            return $this->_TIMED_EMAIL_OFFER_CONDITION_TYPES_SIMPLE_MODE;

        }

        /**
         * Return _LOGIC_CONDITIONS. Property that holds the product quantity logic conditions ( = , != , > , < ).
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function LOGIC_CONDITIONS() {

            return $this->_LOGIC_CONDITIONS;

        }

        /**
         * Return _ACCEPT_TIMED_EMAIL_OFFER_ACTION_TYPES. Property that holds the accept timed email offer action types.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function ACCEPT_TIMED_EMAIL_OFFER_ACTION_TYPES() {

            return $this->_ACCEPT_TIMED_EMAIL_OFFER_ACTION_TYPES;

        }

        /**
         * Return _DECLINE_OFFER_ACTION_TYPES. Property that holds the decline offer action types.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function DECLINE_OFFER_ACTION_TYPES() {

            return $this->_DECLINE_OFFER_ACTION_TYPES;

        }

        /**
         * Return _DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE. Property that holds the option to either show basic configuration for the decline offer action types.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool
         */
        public function DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE() {

            return $this->_DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE;

        }

        /**
         * Return _RECIPIENT_OFFER_RESPONSE_STATUS. Property that holds the various state of the recipient's offer response status.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function RECIPIENT_OFFER_RESPONSE_STATUS() {

            return $this->_RECIPIENT_OFFER_RESPONSE_STATUS;

        }

        /**
         * Return _OFFER_EMAIL_SEND_STATUS. Property that holds the various state of the offer email status.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function OFFER_EMAIL_SEND_STATUS() {

            return $this->_OFFER_EMAIL_SEND_STATUS;

        }

        /**
         * Return _OFFER_EMAIL_RESPONSE_STATUS. Property that holds the various state of the offer email response status.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function OFFER_EMAIL_RESPONSE_STATUS() {

            return $this->_OFFER_EMAIL_RESPONSE_STATUS;

        }

        /**
         * Return _OFFER_RECIPIENTS_TABLE_HEADINGS. Property that holds the list of headings text for the offer recipients table.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function OFFER_RECIPIENTS_TABLE_HEADINGS() {

            return $this->_OFFER_RECIPIENTS_TABLE_HEADINGS;

        }

        /**
         * Return _OFFER_RECIPIENT_POPUP_SCHEDULED_EMAILS_TABLE_HEADINGS. Property that holds the list of headings test for the offer recipient popup data schedules table.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function OFFER_RECIPIENT_POPUP_SCHEDULED_EMAILS_TABLE_HEADINGS() {

            return $this->_OFFER_RECIPIENT_POPUP_SCHEDULED_EMAILS_TABLE_HEADINGS;

        }

        /**
         * Return _BLACKLIST_TABLE_HEADINGS. Property that holds the list of headings for the blacklist table headings.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function BLACKLIST_TABLE_HEADINGS() {

            return $this->_BLACKLIST_TABLE_HEADINGS;

        }

        /**
         * Return _BLACKLIST_TYPES. Property that holds the list of blacklist types.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function BLACKLIST_TYPES() {

            return $this->_BLACKLIST_TYPES;

        }

        /**
         * Return html markup for the column actions for the offer templates table.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         * @param $index
         * @return mixed
         */
        public function OFFER_TEMPLATES_TABLE_COLUMN_ACTIONS( $offer_id , $index ) {
            
            $column_controls = '<span data-offer-id="' . $offer_id . '" data-template-index="' . $index . '" class="dashicons dashicons-email-alt send-test-email" href="#send-test-email-popup" alt="' . __( 'Send Test Email' , 'timed-email-offers' ) . '" title="' . __( 'Send Test Email' , 'timed-email-offers' ) . '"></span>
                                <span data-offer-id="' . $offer_id . '" data-template-index="' . $index . '" class="dashicons dashicons-edit edit-offer-template" alt="' . __( 'Edit Template' , 'timed-email-offers' ) . '" title="' . __( 'Edit Template' , 'timed-email-offers' ) . '"></span>
                                <span data-offer-id="' . $offer_id . '" data-template-index="' . $index . '" class="dashicons dashicons-no delete-offer-template" alt="' . __( 'Delete Template' , 'timed-email-offers' ) . '" title="' . __( 'Delete Template' , 'timed-email-offers' ) . '"></span>';
                                
            return apply_filters( 'TEO_Offer_Template_table_column_actions' , $column_controls , $offer_id , $index );

        }

        /**
         * Return html markup for the column actions for the offer recipients table.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $offer_id
         * @param null $order_id
         * @return mixed
         */
        public function OFFER_RECIPIENTS_TABLE_COLUMN_ACTIONS( $offer_id = null , $order_id = null ) {

            // $meta_index is also known as the order id.

            $offer_id_text = !is_null( $offer_id ) ? $offer_id : '';
            $order_id_text = !is_null( $order_id ) ? $order_id : '';

            $column_controls = '<span class="dashicons dashicons-search view-recipient-details" data-offer-id="' . $offer_id_text . '" data-order-id="' . $order_id_text . '"></span>
                                <span class="dashicons dashicons-no delete-recipient" data-offer-id="' . $offer_id_text . '" data-order-id="' . $order_id_text . '"></span>';

            return apply_filters( 'teo_offer_recipients_table_column_actions' , $column_controls , $offer_id , $order_id );

        }

        /**
         * Return html markup for the column actions for the recipient scheduled emails table.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $offer_id
         * @param null $order_id
         * @param null $unique_email_token
         * @param null $send_status
         * @return mixed
         */
        public function OFFER_RECIPIENT_POPUP_SCHEDULED_EMAILS_TABLE_COLUMN_ACTIONS( $offer_id = null , $order_id = null , $unique_email_token = null , $send_status = null ) {

            $offer_id_text           = !is_null( $offer_id ) ? $offer_id : '';
            $order_id_text           = !is_null( $order_id ) ? $order_id : '';
            $unique_email_token_text = !is_null( $unique_email_token ) ? $unique_email_token : '';

            // We only allow deleting of a scheduled email that is not sent yet
            // Sent scheduled emails are retained for stats purposes
            // Consider the case where the scheduled email is sent, then is accepted, then we manually delete it here
            // Sent scheduled emails don't do harm if they are not removed
            $column_controls = '';
            if ( $send_status == 'pending' )
                $column_controls = '<span class="dashicons dashicons-no delete-recipient-schedule" data-offer-id="' . $offer_id_text . '" data-order-id="' . $order_id_text . '" data-unique-email-token="' . $unique_email_token_text . '"></span>';

            return apply_filters( 'teo_offer_recipient_scheduled_emails_table_column_actions' , $column_controls , $offer_id , $order_id , $unique_email_token , $send_status );

        }

        /**
         * Return html markup for the column actions for the blacklist table.
         *
         * @since 1.0.0
         * @access public
         *
         * @param null $blacklist_email
         * @param null $blacklist_type
         * @return mixed
         */
        public function BLACKLIST_TABLE_COLUMN_ACTIONS( $blacklist_email = null , $blacklist_type = null ) {

            $blacklist_email_text = !is_null( $blacklist_email ) ? $blacklist_email : '';
            $blacklist_type_text  = !is_null( $blacklist_type ) ? $blacklist_type : '';

            $column_controls = '<span class="dashicons dashicons-no remove-blacklist-item" data-blacklist-email="' . $blacklist_email_text . '" data-blacklist-type="' . $blacklist_type_text . '"></span>';

            return apply_filters( 'teo_blacklist_table_column_actions' , $column_controls , $blacklist_email , $blacklist_type );

        }

        /**
         * Return _PRODUCT_QUANTITY_IN_ORDER_TABLE_TOTAL_COLUMNS.
         * Property that holds the total number columns for the product quantity in cart table.
         * Basically used for extensibility in the no product added entry on table. The colspan.
         *
         * @since 1.0.0
         * @access public
         *
         * @return int
         */
        public function PRODUCT_QUANTITY_IN_ORDER_TABLE_TOTAL_COLUMNS() {

            return $this->_PRODUCT_QUANTITY_IN_ORDER_TABLE_TOTAL_COLUMNS;

        }

        /**
         * Return _ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS. Property that holds the total number of columns for the accept offer action "Add products to cart" table.
         *
         * @since 1.0.0
         * @access public
         *
         * @return int
         */
        public function ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS() {

            return $this->_ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS;

        }

        /**
         * Return _APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS. Property that holds the total number of columns for the accept offer action "Apply coupons to cart" table.
         *
         * @since 1.0.0
         * @access public
         *
         * @return int
         */
        public function APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS() {

            return $this->_APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS;

        }

        /**
         * Return _OFFER_RECIPIENT_POPUP_SCHEDULES_TABLE_TOTAL_COLUMNS. Property that holds the total number of columns for the offer recipient popup data schedules table.
         *
         * @since 1.0.0
         * @access public
         *
         * @return int
         */
        public function OFFER_RECIPIENT_POPUP_SCHEDULES_TABLE_TOTAL_COLUMNS() {

            return $this->_OFFER_RECIPIENT_POPUP_SCHEDULES_TABLE_TOTAL_COLUMNS;

        }


        /*
        |--------------------------------------------------------------------------
        | Mesc
        |--------------------------------------------------------------------------
        */

        /**
         * Return _DEFAULT_EMAIL_TEMPLATE_CONTENT. Default email template content.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function DEFAULT_EMAIL_TEMPLATE_CONTENT() {

            return $this->_DEFAULT_EMAIL_TEMPLATE_CONTENT;

        }
        
        /**
         * Return _CRON_JOBS_UPDATED. Mesc option that determines whether old cron jobs are updated to new one.
         *
         * @since 1.2.0
         * @access private
         *
         * @return string
         */
        public function CRON_JOBS_UPDATED() {

            return $this->_CRON_JOBS_UPDATED;

        }


        /*
        |--------------------------------------------------------------------------
        | Custom Tables
        |--------------------------------------------------------------------------
        */

        /**
         * Return _CUSTOM_TABLE_OFFER_RECIPIENTS. Offer recipients custom table name ( Without prefix ).
         *
         * @since 1.2.0
         * @access public
         * 
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_RECIPIENTS() {

            return $this->_CUSTOM_TABLE_OFFER_RECIPIENTS;

        }
        
        /**
         * Return _CUSTOM_TABLE_OFFER_RECIPIENTS_VERSION. Option that holds the current offer recipients custom table version.
         * 
         * @since 1.2.0
         * @access public
         * 
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_RECIPIENTS_VERSION() {

            return $this->_CUSTOM_TABLE_OFFER_RECIPIENTS_VERSION;

        }

        /**
         * Return _CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS. Email schedules custom table name ( Without prefix ).
         *
         * @since 1.2.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() {

            return $this->_CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS;

        }
        
        /**
         * Return _CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS_VERSION. Option that holds the current email schedules custom table version.
         *
         * @since 1.2.0
         * @access public
         * 
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS_VERSION() {

            return $this->_CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS_VERSION;

        }

        /**
         * Offer email views logs custom table name.
         * 
         * @since 1.2.0
         * @access public
         * 
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS() {

            return $this->_CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS;

        }

        /**
         * Option that holds the current offer email views logs custom table version.
         *
         * @since 1.2.0
         * @access public
         * 
         * @var string
         */
        public function CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS_VERSION() {

            return $this->_CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS_VERSION;

        }


        /*
        |--------------------------------------------------------------------------
        | Post Meta Property Getters
        |--------------------------------------------------------------------------
        */

        // 'timed_email_offer' cpt post meta

        /**
         * Return _POST_META_OFFER_TEMPLATES. Property that holds the offer's schedules post meta.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_OFFER_TEMPLATES() {

            return $this->_POST_META_OFFER_TEMPLATES;

        }

        /**
         * Return _POST_META_OFFER_CONDITIONS. Property that holds offer's conditions post meta.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_OFFER_CONDITIONS() {

            return $this->_POST_META_OFFER_CONDITIONS;

        }

        /**
         * Return _POST_META_OFFER_ACCEPT_ACTIONS. Property that holds the post meta for the actions to take if an offer is accepted.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_OFFER_ACCEPT_ACTIONS() {

            return $this->_POST_META_OFFER_ACCEPT_ACTIONS;

        }

        /**
         * Return _POST_META_OFFER_DECLINE_ACTIONS. Property that holds the post meta for the actions to take if an offer is declined.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_OFFER_DECLINE_ACTIONS() {

            return $this->_POST_META_OFFER_DECLINE_ACTIONS;

        }

        /**
         * Return _POST_META_OFFER_RECIPIENTS. Property that holds the post meta for the recipients of a timed email offer.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_OFFER_RECIPIENTS() {

            return $this->_POST_META_OFFER_RECIPIENTS;

        }

        /**
         * Return _POST_META_ORDER_LINKED_OFFER_IDS. Property that holds the list of timed email offers id in which an order is attached with.
         * Post meta of an order.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_ORDER_LINKED_OFFER_IDS() {

            return $this->_POST_META_ORDER_LINKED_OFFER_IDS;

        }

        /**
         * Return _POST_META_ORDER_OFFER_ID. Order post meta holding the offer id that this order is linked.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_ORDER_OFFER_ID() {

            return $this->_POST_META_ORDER_OFFER_ID;

        }

        /**
         * Return _POST_META_ORDER_OFFER_RECIPIENT_ID. Order post meta holding the offer recipient id (order id) that is order is linked.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_ORDER_OFFER_RECIPIENT_ID() {

            return $this->_POST_META_ORDER_OFFER_RECIPIENT_ID;

        }

        /**
         * Return _POST_META_ORDER_OFFER_EMAIL_TOKEN. Order post meta holding the offer email token that this order is linked.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_ORDER_OFFER_EMAIL_TOKEN() {

            return $this->_POST_META_ORDER_OFFER_EMAIL_TOKEN;

        }

        /**
         * Return _POST_META_OFFER_ORDER. Post meta holding offer order. Sort order.
         * 
         * @since 1.2.0
         * @access public
         *
         * @var string
         */
        public function POST_META_OFFER_ORDER() {

            return $this->_POST_META_OFFER_ORDER;

        }
        
        
        /*
        |--------------------------------------------------------------------------
        | Options
        |--------------------------------------------------------------------------
        */

        // Cron Hooks

        /**
         * Return _CRON_HOOK_SEND_EMAIL_OFFER. Property that holds the cron hook name for sending email offers.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function CRON_HOOK_SEND_EMAIL_OFFER() {

            return $this->_CRON_HOOK_SEND_EMAIL_OFFER;

        }

        /**
         * Return _CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT. Property that holds the cron hook name for declining offer for a recipient if offer timeout period is reached.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT() {

            return $this->_CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT;

        }

        // Sessions

        /**
         * Return _SESSION_TIMED_EMAIL_OFFER_ORDER. Property that holds the session key that specifies that this is a timed email offer order.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function SESSION_TIMED_EMAIL_OFFER_ORDER() {

            return $this->_SESSION_TIMED_EMAIL_OFFER_ORDER;

        }

        // Page Params

        /**
         * Return _PAGE_DECLINE_OFFER_PARAM_OFFER_ID. Property that holds the decline offer endpoint offer id param.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PAGE_PARAM_OFFER_ID() {

            return $this->_PAGE_PARAM_OFFER_ID;

        }

        /**
         * Return _PAGE_DECLINE_OFFER_PARAM_ORDER_ID. Property that holds the decline offer endpoint order id param.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PAGE_PARAM_ORDER_ID() {

            return $this->_PAGE_PARAM_ORDER_ID;

        }

        /**
         * Return _ENDPOINT_DECLINE_OFFER_PARAM_EMAIL_TOKEN. Property that holds the decline offer endpoint email token param.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PAGE_PARAM_EMAIL_TOKEN() {

            return $this->_PAGE_PARAM_EMAIL_TOKEN;

        }

        // Pages Options

        /**
         * Return _OPTION_DECLINE_OFFER_PAGE_ID. Property that holds the decline offer page id option.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_DECLINE_OFFER_PAGE_ID() {

            return $this->_OPTION_DECLINE_OFFER_PAGE_ID;

        }

        /**
         * Return _OPTION_UNSUBSCRIBE_PAGE_ID. Property that holds the unsubscribe page id option.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_UNSUBSCRIBE_PAGE_ID() {

            return $this->_OPTION_UNSUBSCRIBE_PAGE_ID;

        }

        /**
         * Return _OPTION_ACCEPT_OFFER_PAGE_ID. Property that holds the accept offer page id option.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_ACCEPT_OFFER_PAGE_ID() {

            return $this->_OPTION_ACCEPT_OFFER_PAGE_ID;

        }

        /**
         * Return _OPTION_INVALID_OFFER_PAGE_ID. Property that holds the invalid offer page id option.
         *
         * @since 1.1.0
         * @access public
         *
         * @return string
         */
        public function OPTION_INVALID_OFFER_PAGE_ID() {

            return $this->_OPTION_INVALID_OFFER_PAGE_ID;

        }
        
        // Acceptance Options

        /**
         * Return _OPTION_RETAIN_CART_CONTENTS_ON_OFFER_ACCEPT. Property that holds the option to retain cart contents on offer accept.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_RETAIN_CART_CONTENTS_ON_OFFER_ACCEPT() {

            return $this->_OPTION_RETAIN_CART_CONTENTS_ON_OFFER_ACCEPT;

        }

        /**
         * Return _OPTION_TURN_OFF_NOTICE_ON_OFFER_ACCEPTANCE. Property that holds the option to turn off notice when offer is successfully accepted.
         * 
         * @since 1.0.0
         * @access public
         * 
         * @return string
         */
        public function OPTION_TURN_OFF_NOTICE_ON_OFFER_ACCEPTANCE() {

            return $this->_OPTION_TURN_OFF_NOTICE_ON_OFFER_ACCEPTANCE;

        }

        /**
         * Return _OPTION_ONLY_UNSCHED_REMAINING_EMAILS_ON_OFFER_CONVERSION. Property that holds the option on whether to unschedule any remaining unsent email of an offer on offer accept or not.
         *
         * @since 1.1.0
         * @access public
         * 
         * @return string
         */
        public function OPTION_ONLY_UNSCHED_REMAINING_EMAILS_ON_OFFER_CONVERSION() {

            return $this->_OPTION_ONLY_UNSCHED_REMAINING_EMAILS_ON_OFFER_CONVERSION;

        }
        
        // Decline Options

        /**
         * Return _OPTION_OFFER_TIMEOUT_PERIOD.
         * Property that holds the option of the time out period of an offer.
         * Time out period is based on the send date time of the last email of an offer.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_OFFER_TIMEOUT_PERIOD() {

            return $this->_OPTION_OFFER_TIMEOUT_PERIOD;

        }

        /**
         * Return _OPTION_EXECUTE_DECLINE_ACTIONS_ON_TIMEOUT.
         * Property that holds the option whether to execute decline actions of an offer
         * when a recipient triggers a time out of the offer.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_EXECUTE_DECLINE_ACTIONS_ON_TIMEOUT() {

            return $this->_OPTION_EXECUTE_DECLINE_ACTIONS_ON_TIMEOUT;

        }

        // Blacklist Options

        /**
         * Return _OPTION_BLACKLIST. Property that holds the option of blacklist of people from timed email offers.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_BLACKLIST() {

            return $this->_OPTION_BLACKLIST;

        }

        // Help Options

        /**
         * Return _OPTION_CLEANUP_PLUGIN_OPTIONS. Property that holds the option of either cleaning up all plugin options upon plugin un-installation.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_CLEANUP_PLUGIN_OPTIONS() {

            return $this->_OPTION_CLEANUP_PLUGIN_OPTIONS;

        }

    }

}
