<?php
/**
 * Plugin Name: Timed Email Offers
 * Plugin URI: https://marketingsuiteplugin.com
 * Description: Timed and Personalized Email Marketing for WooCommerce.
 * Version: 1.2.2
 * Author: Rymera Web Co
 * Author URI: https://rymera.com.au
 * Requires at least: 4.4.2
 * Tested up to: 4.6.1
 *
 * Text Domain: timed-email-offers
 * Domain Path: /languages/
 *
 * @package Timed_Email_Offers
 * @category Core
 * @author Rymera Web Co
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'Timed_Email_Offers' ) ) {

    /**
     * Timed Email Offers plugin main class.
     *
     * This serves as the plugin's main Controller.
     *
     * @since 1.0.0.
     */
    final class Timed_Email_Offers {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Single main instance of Timed Email Offers plugin.
         *
         * @since 1.0.0
         * @access private
         * @var Timed_Email_Offers
         */
        private static $_instance;


        /*
        |--------------------------------------------------------------------------
        | Model Properties.
        |--------------------------------------------------------------------------
        |
        | These properties are instances of various models Events Manager
        | Seat Manager  utilizes. These models handles the logic of the
        | various aspects of the plugin. Ex. Internationalization, loading of
        | various scripts, booting the plugin, and other various business logic.
        |
        */

        /**
         * Property that holds various constants utilized throughout the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Constants
         */
        public $constants;

        /**
         * Property that wraps the logic of Internationalization.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_I18n
         */
        public $i18n;

        /**
         * Property that wraps the logic of loading js and css scripts the plugin utilizes.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Script_Loader
         */
        public $script_loader;

        /**
         * Property that wraps the logic of the plugin's mailer.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Mailer
         */
        public $mailer;

        /**
         * Property that wraps the logic of booting up and shutting down the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Bootstrap
         */
        public $bootstrap;


        /*
        |--------------------------------------------------------------------------
        | Help Pointers
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the plugin initial guided tour help pointers.
         *
         * @since 1.2.0
         * @access public
         * @var TEO_Initial_Guided_Tour
         */
        public $initial_guided_tour;

        /**
         * Property that holds the plugin offer entry guided tour help pointers.
         *
         * @since 1.2.0
         * @access public
         * @var TEO_Offer_Entry_Guided_Tour
         */
        public $offer_entry_guided_tour;


        /*
        |--------------------------------------------------------------------------
        | Shop
        |--------------------------------------------------------------------------
        */

        /**
         * Property that houses the logic of the various helper functions related to the shop's products.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Product
         */
        public $product;

        /**
         * Property that houses the logic of the various helper functions related to the shop's coupons.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Coupon
         */
        public $coupon;


        /*
        |--------------------------------------------------------------------------
        | Offer Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that wraps the logic of handling matters relating to invalid offers.
         *
         * @since 1.1.0
         * @access public
         * @var TEO_Invalid_Offer
         */
        public $invalid_offer;

        /**
         * Property that wraps the logic of 'timed_email_offer' cpt.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Offer_CPT
         */
        public $offer_cpt;

        /**
         * Property that wraps the logic of timed email offer conditions.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Offer_Condition
         */
        public $offer_condition;

        /**
         * Property that wraps the logic of timed email offer templates.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Offer_Template
         */
        public $offer_template;

        /**
         * Property that wraps the logic relating to offer schedule.
         *
         * @since 1.2.0
         * @access public
         * @var TEO_Offer_Schedule
         */
        public $offer_schedule;

        /**
         * Property that wraps the logic of timed email offer recipients.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Offer_Recipient
         */
        public $offer_recipient;

        /**
         * Property that wraps the logic of a timed email offer.
         *
         * @since 1.0.0
         * @access public
         * @var Timed_Email_Offer
         */
        public $timed_email_offer;


        /*
        |--------------------------------------------------------------------------
        | Pages
        |--------------------------------------------------------------------------
        */

        /**
         * Property that wraps the logic of decline offer page.
         *
         * @since 1.0.0
         * @access public
         *
         * @var TEO_Decline_Offer_Page
         */
        public $decline_offer_page;

        /**
         * Property that wraps the logic of unsubscribe page.
         *
         * @since 1.0.0
         * @access public
         *
         * @var TEO_Unsubscribe_Page
         */
        public $unsubscribe_page;

        /**
         * Property that wraps the logic of accept offer page.
         *
         * @since 1.0.0
         * @access public
         *
         * @var TEO_Accept_Offer_Page
         */
        public $accept_offer_page;


        /*
        |--------------------------------------------------------------------------
        | Offer Actions
        |--------------------------------------------------------------------------
        */

        /**
         * Property that wraps the logic of accept offer actions.
         *
         * @since 1.0.0
         * @access public
         *
         * @var TEO_Offer_Accept_Action
         */
        public $accept_offer_action;

        /**
         * Property that wraps the logic of decline offer actions.
         *
         * @since 1.0.0
         * @access public
         *
         * @var TEO_Offer_Decline_Action
         */
        public $decline_offer_action;


        /*
        |--------------------------------------------------------------------------
        | Plugin Integrations
        |--------------------------------------------------------------------------
        */

        // WooCommerce Product Bundles

        /**
         * Property that houses the logic of integrating with WooCommerce Product Bundles plugin.
         *
         * @since 1.1.0
         * @access public
         * @var TEO_WooCommerce_Product_Bundles
         */
        public $wc_product_bundles;

        // WooCommerce Composite Products

        /**
         * Property that houses the logic of integrating  with WooCommerce Composite Products plugin.
         *
         * @since 1.1.0
         * @access public
         * @var TEO_WooCommerce_Composite_Products
         */
        public $wc_composite_products;


        /*
        |--------------------------------------------------------------------------
        | AJAX Interfaces
        |--------------------------------------------------------------------------
        */

        /**
         * Property that wraps the logic of ajax interfaces of the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_AJAX_Interfaces
         */
        public $ajax_interface;


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
         * Timed_Email_Offers constructor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

            register_deactivation_hook( __FILE__ , array( $this , 'general_deactivation_code' ) );  

            if ( $this->_check_plugin_dependencies() ) {

                $this->_load_dependencies();
                $this->_init();
                $this->_exe();

            } else {

                // Display notice that plugin dependency ( WooCommerce ) is not present.
                add_action( 'admin_notices' , array( $this , 'missing_plugin_dependencies_notice' ) );

            }

        }

        /**
         * Ensure that only one instance of Timed Email Offers is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @return Timed_Email_Offers
         */
        public static function instance() {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self();

            return self::$_instance;

        }

        /**
         * General code base to be always executed on plugin deactivation.
         *
         * @since 1.2.1
         * @access public
         *
         * @param boolean $network_wide Flag that determines if the plugin is activated network wide.
         */
        public function general_deactivation_code( $network_wide ) {

            global $wpdb;

            // check if it is a multisite network
            if ( is_multisite() ) {

                // check if the plugin has been activated on the network or on a single site
                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        delete_option( 'teo_activation_code_triggered' );

                    }

                    restore_current_blog();

                } else {

                    // activated on a single site, in a multi-site
                    delete_option( 'teo_activation_code_triggered' );

                }

            } else {

                // activated on a single site
                delete_option( 'teo_activation_code_triggered' );

            }

        }

        /**
         * Check for plugin dependencies of Timed Email Offers plugin.
         *
         * @since 1.0.0
         * @access private
         *
         * @return bool
         */
        private function _check_plugin_dependencies() {

            // Makes sure the plugin is defined before trying to use it
            if ( !function_exists( 'is_plugin_active' ) )
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            return is_plugin_active( 'woocommerce/woocommerce.php' );

        }

        /**
         * Add notice to notify users that a required plugin dependency of Timed Email Offers plugin is missing.
         *
         * @since 1.0.0
         * @access public
         */
        public function missing_plugin_dependencies_notice() {

            $plugin_file = 'woocommerce/woocommerce.php';
            $sptFile = trailingslashit( WP_PLUGIN_DIR ) . plugin_basename( $plugin_file );

            $sptInstallText = '<a href="' . wp_nonce_url( 'update.php?action=install-plugin&plugin=woocommerce', 'install-plugin_woocommerce' ) . '">' . __( 'Click here to install from WordPress.org repo &rarr;' , 'timed-email-offers' ) . '</a>';
            if ( file_exists( $sptFile ) )
                $sptInstallText = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;s', 'activate-plugin_' . $plugin_file ) . '" title="' . __( 'Activate this plugin' , 'timed-email-offers' ) . '" class="edit">' . __( 'Click here to activate &rarr;' , 'timed-email-offers' ) . '</a>'; ?>

            <div class="error">
                <p>
                    <?php _e( '<b>Timed Email Offers</b> plugin missing dependency.<br/><br/>Please ensure you have the <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> plugin installed and activated.<br/>' , 'timed-email-offers' ); ?>
                    <?php echo $sptInstallText; ?>
                </p>
            </div>

            <?php

        }

        /**
         * Load controllers that handles various business logic of the plugin.
         *
         * @since 1.0.0
         * @access private
         */
        private function _load_dependencies() {

            include_once ( 'models/class-teo-constants.php' );
            include_once ( 'models/class-teo-helper.php' );
            include_once ( 'models/class-teo-i18n.php' );
            include_once ( 'models/class-teo-script-loader.php' );
            include_once ( 'models/class-teo-bootstrap.php' );
            include_once ( 'models/class-teo-mailer.php' );

            // Help Pointers
            include_once ( 'models/help-pointers/class-teo-initial-guided-tour.php' );
            include_once ( 'models/help-pointers/class-teo-offer-entry-guided-tour.php' );

            // Shop
            include_once ( 'models/shop/class-teo-product.php' );
            include_once ( 'models/shop/class-teo-coupon.php' );

            // Offer
            include_once ( 'models/offer/class-teo-invalid-offer.php' );
            include_once ( 'models/offer/class-teo-offer-cpt.php' );
            include_once ( 'models/offer/class-teo-offer-condition.php' );
            include_once ( 'models/offer/class-teo-offer-template.php' );
            include_once ( 'models/offer/class-teo-offer-schedule.php' );
            include_once ( 'models/offer/class-teo-offer-recipient.php' );
            include_once ( 'models/offer/class-timed-email-offer.php' );

            // Pages
            include_once( 'models/pages/class-teo-decline-offer-page.php' );
            include_once( 'models/pages/class-teo-unsubscribe-page.php' );
            include_once( 'models/pages/class-teo-accept-offer-page.php' );

            // Offer Actions
            include_once( 'models/offer/class-teo-offer-accept-action.php' );
            include_once( 'models/offer/class-teo-offer-decline-action.php' );

            // Plugin Integrations

            // WooCommerce Product Bundles
            include_once ( 'models/plugin-integrations/woocommerce-product-bundles/class-teo-woocommerce-product-bundles.php' );
            include_once ( 'models/plugin-integrations/woocommerce-composite-products/class-teo-woocommerce-composite-products.php' );

            //  AJAX Interfaces
            include_once ( 'models/class-teo-ajax-interfaces.php' );

        }

        /**
         * Initialize the plugin.
         *
         * Initialize various property values and instantiate controller properties.
         *
         * @since 1.0.0
         * @access private
         */
        private function _init() {

            /*
             * Note: We are using "Dependency Injection" to inject anything a specific controller requires in order
             * for it to perform its job. This makes models decoupled and is very modular.
             */

            $this->constants = TEO_Constants::instance();
            $common_deps     = array( 'TEO_Constants' => $this->constants );

            $this->i18n          = TEO_I18n::instance( $common_deps );
            $this->mailer        = TEO_Mailer::instance( $common_deps );

            // Help Pointers
            $this->initial_guided_tour     = TEO_Initial_Guided_Tour::instance( $common_deps );
            $this->offer_entry_guided_tour = TEO_Offer_Entry_Guided_Tour::instance( $common_deps );

            $this->script_loader = TEO_Script_Loader::instance( array(
                                        'TEO_Constants'               => $this->constants,
                                        'TEO_Initial_Guided_Tour'     => $this->initial_guided_tour,
                                        'TEO_Offer_Entry_Guided_Tour' => $this->offer_entry_guided_tour
                                    ) );

            // Shop
            $this->product = TEO_Product::instance( $common_deps );
            $this->coupon  = TEO_Coupon::instance( $common_deps );

            // Offer
            $this->invalid_offer   = TEO_Invalid_Offer::instance( $common_deps );
            $this->offer_cpt       = TEO_Offer_CPT::instance( array(
                                                                'TEO_Constants' => $this->constants,
                                                                'TEO_Product'   => $this->product,
                                                                'TEO_Coupon'    => $this->coupon
                                                            ) );
            $this->offer_condition = TEO_Offer_Condition::instance( array(
                                                            'TEO_Constants' => $this->constants,
                                                            'TEO_Product'   => $this->product
                                                        ) );
            $this->offer_schedule  = TEO_Offer_Schedule::instance( $common_deps );
            $this->offer_template  = TEO_Offer_Template::instance( array(
                                                                    'TEO_Constants'      => $this->constants,
                                                                    'TEO_Offer_Schedule' => $this->offer_schedule
                                                                ) );
            $this->offer_recipient = TEO_Offer_Recipient::instance( array(
                                                                    'TEO_Constants'      => $this->constants,
                                                                    'TEO_Offer_Schedule' => $this->offer_schedule
                                                                ) );

            // Timed Email Offer
            $this->timed_email_offer = Timed_Email_Offer::instance( array(
                                                                    'TEO_Constants'       => $this->constants,
                                                                    'TEO_Mailer'          => $this->mailer,
                                                                    'TEO_Offer_CPT'       => $this->offer_cpt,
                                                                    'TEO_Offer_Condition' => $this->offer_condition,
                                                                    'TEO_Offer_Recipient' => $this->offer_recipient,
                                                                    'TEO_Offer_Schedule'  => $this->offer_schedule
                                                                ) );

            // Pages
            $this->decline_offer_page = TEO_Decline_Offer_Page::instance( array(
                                                                            'TEO_Constants'      => $this->constants,
                                                                            'TEO_Offer_Schedule' => $this->offer_schedule
                                                                        ) );
            $this->unsubscribe_page = TEO_Unsubscribe_Page::instance( array(
                                                                        'TEO_Constants'       => $this->constants,
                                                                        'TEO_Offer_Recipient' => $this->offer_recipient
                                                                    ) );
            $this->accept_offer_page = TEO_Accept_Offer_Page::instance( array(
                                                                            'TEO_Constants'      => $this->constants,
                                                                            'TEO_Offer_Schedule' => $this->offer_schedule
                                                                        ) );

            // Offer Actions
            $this->accept_offer_action  = TEO_Offer_Accept_Action::instance( array(
                                                                                'TEO_Constants' => $this->constants,
                                                                                'TEO_Product'   => $this->product,
                                                                                'TEO_Coupon'    => $this->coupon
                                                                            ) );
            $this->decline_offer_action = TEO_Offer_Decline_Action::instance( $common_deps );

            // Plugin Integrations

            // WooCommerce Product Bundles
            $this->wc_product_bundles = TEO_WooCommerce_Product_Bundles::instance( $common_deps );

            // WooCommerce Composite Products
            $this->wc_composite_products = TEO_WooCommerce_Composite_Products::instance( $common_deps );

            // AJAX Interfaces
            $this->ajax_interface = TEO_AJAX_Interfaces::instance( array(
                                                                    'TEO_Constants'            => $this->constants,
                                                                    'TEO_Product'              => $this->product,
                                                                    'TEO_Coupon'               => $this->coupon,
                                                                    'TEO_Offer_Template'       => $this->offer_template,
                                                                    'TEO_Offer_Condition'      => $this->offer_condition,
                                                                    'TEO_Offer_Accept_Action'  => $this->accept_offer_action,
                                                                    'TEO_Offer_Decline_Action' => $this->decline_offer_action,
                                                                    'TEO_Offer_Recipient'      => $this->offer_recipient,
                                                                    'TEO_Unsubscribe_Page'     => $this->unsubscribe_page
                                                                ) );

            // Bootstrap
            $this->bootstrap = TEO_Bootstrap::instance( array(
                                                            'TEO_Constants'               => $this->constants,
                                                            'TEO_Initial_Guided_Tour'     => $this->initial_guided_tour,
                                                            'TEO_Offer_Entry_Guided_Tour' => $this->offer_entry_guided_tour,
                                                            'TEO_Offer_CPT'               => $this->offer_cpt
                                                        ) );

        }

        /**
         * Run the plugin. This is the main "method controller", this is where the various processes
         * are being routed to the appropriate models to handle them.
         *
         * @since 1.0.0
         * @access private
         */
        private function _exe() {

            /*
            |--------------------------------------------------------------------------
            | Internationalization
            |--------------------------------------------------------------------------
            */
            add_action( 'plugins_loaded' , array( $this->i18n , 'load_plugin_textdomain' ) );


            /*
            |--------------------------------------------------------------------------
            | Bootstrap
            |--------------------------------------------------------------------------
            */
            register_activation_hook( __FILE__ , array( $this->bootstrap , 'activate_plugin' ) );
            register_deactivation_hook( __FILE__ , array( $this->bootstrap , 'deactivate_plugin' ) );

            // Execute plugin initialization ( plugin activation ) on every newly created site in a multi site set up
            add_action( 'wpmu_new_blog' , array( $this->bootstrap , 'new_mu_site_init' ) , 10 , 6 );

            add_action( 'init' , array( $this->bootstrap , 'initialize' ) );
            add_action( 'init' , array( $this , 'register_ajax_handlers' ) );


            /*
            |--------------------------------------------------------------------------
            | Load JS and CSS Scripts
            |--------------------------------------------------------------------------
            */
            add_action( 'admin_enqueue_scripts' , array( $this->script_loader , 'load_backend_scripts' ) , 10 , 1 );
            add_action( 'wp_enqueue_scripts' , array( $this->script_loader , 'load_frontend_scripts' ) );


            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */

            // Register Settings Page
            add_filter( "woocommerce_get_settings_pages" , array( $this->bootstrap , 'initialize_plugin_settings_page' ) , 10 , 1 );


            /*
            |--------------------------------------------------------------------------
            | WP Integration
            |--------------------------------------------------------------------------
            */

            // Add settings link to plugin action links
            add_filter( 'plugin_action_links' , array( $this->bootstrap , 'plugin_settings_action_link' ) , 10 , 2 );


            /*
            |--------------------------------------------------------------------------
            | Offers
            |--------------------------------------------------------------------------
            */

            // CPT
            add_action( 'add_meta_boxes' , array( $this->offer_cpt , 'register_timed_email_offer_cpt_custom_meta_boxes' ) );
            add_action( 'quick_edit_custom_box', array( $this->offer_cpt , 'view_timed_email_offer_options_quick_edit' ), 10, 2 );

            // Remove all recipients for an offer when the offer is trashed
            add_action( 'wp_trash_post' , array( $this->timed_email_offer , 'process_trashed_offer' ) , 10 , 1 );

            // Save additional cpt fields
            add_action( 'save_post' , array( $this->offer_cpt , 'save_post' ) , 10 , 1 );

            // CPT Entry Listing Custom Columns
            add_filter( 'manage_timed_email_offer_posts_columns' , array( $this->offer_cpt , 'add_offer_listing_custom_columns' ) , 10 , 1 );
            add_action( 'manage_timed_email_offer_posts_custom_column' , array( $this->offer_cpt , 'add_offer_listing_custom_columns_data' ) , 10 , 2 );
            add_filter( 'manage_edit-timed_email_offer_sortable_columns', array( $this->offer_cpt , 'mark_offer_custom_columns_as_sortable' ) , 10  , 1 );
            add_filter( 'posts_clauses' , array( $this->offer_cpt , 'sort_offer_custom_columns' ) , 10  , 2 );


            /*
            |--------------------------------------------------------------------------
            | Email Templates
            |--------------------------------------------------------------------------
            */

            // Correct href with template tag url that has no protocol.
            add_filter( 'the_editor_content' , array( $this->timed_email_offer , 'correct_href_with_no_protocol' ) , 10 , 2 );


            /*
            |--------------------------------------------------------------------------
            | Offers Conditions
            |--------------------------------------------------------------------------
            */

            // Product In Order
            add_filter( 'teo_product-quantity_offer_condition_markup' , array( $this->offer_condition , 'product_quantity_offer_condition_markup' ) , 10 , 3 );
            add_action( 'teo_render_product-quantity_offer_condition_markup' , array( $this->offer_condition , 'render_product_quantity_offer_condition_markup' ) , 10 , 1 );
            add_filter( 'teo_check_product-quantity_offer_condition' , array( $this->offer_condition , 'check_product_quantity_offer_condition' ) , 10 , 7 );


            /*
            |--------------------------------------------------------------------------
            | Recipients
            |--------------------------------------------------------------------------
            */

            // Process completed order. Add customer as recipient to an offer if it is qualified based on his/her order
            add_action( 'woocommerce_order_status_completed' , array( $this->timed_email_offer , 'process_completed_order' ) , 10 , 1 );

            // Remove TEO entries for completed orders that later changed to none-completed status
            add_action( 'woocommerce_order_status_completed_to_on-hold' , array( $this->timed_email_offer , 'process_completed_order_changed_to_uncompleted_order' ) , 10 , 1 );
            add_action( 'woocommerce_order_status_completed_to_failed' , array( $this->timed_email_offer , 'process_completed_order_changed_to_uncompleted_order' ) , 10 , 1 );
            add_action( 'woocommerce_order_status_completed_to_cancelled' , array( $this->timed_email_offer , 'process_completed_order_changed_to_uncompleted_order' )  , 10 , 1);
            add_action( 'woocommerce_order_status_completed_to_processing' , array( $this->timed_email_offer , 'process_completed_order_changed_to_uncompleted_order' ) , 10 , 1 );
            add_action( 'woocommerce_order_status_completed_to_refunded' , array( $this->timed_email_offer , 'process_completed_order_changed_to_uncompleted_order' ) , 10 , 1 );
            add_action( 'woocommerce_order_status_completed_to_pending' , array( $this->timed_email_offer , 'process_completed_order_changed_to_uncompleted_order' ) , 10 , 1 );

            // Remove TEO entries for completed order that has been trashed
            add_action( 'wp_trash_post' , array( $this->timed_email_offer , 'process_trashed_order' ) , 10 , 1 );

            // Re add completed order as a recipient to applicable offers if untrashed
            add_action( 'untrashed_post' , array( $this->timed_email_offer , 'process_untrashed_order' ) , 10 , 1 );


            /*
            |--------------------------------------------------------------------------
            | Pages
            |--------------------------------------------------------------------------
            */

            // Shortcode for displaying invalid offer error message
            add_shortcode( 'teo-invalid-offer-error-message' , array( $this->invalid_offer , 'sc_invalid_offer_error_message' ) , 10 , 2 );

            // Register TEO page params
            add_filter( 'query_vars' , array( $this->bootstrap , 'add_page_query_vars' ) , 10 , 1 );

            // Accept offer page
            add_action( 'template_redirect' , array( $this->accept_offer_page , 'execute_page_callback' ) );
            add_action( 'teo_check_offer_validity_via_accept_link' , array( $this->accept_offer_page , 'check_offer_validity' ) , 10 , 4 );
            add_action( 'woocommerce_checkout_order_processed' , array( $this->accept_offer_page , 'link_wc_order_and_teo_offer_on_order_creation' ) , 10 , 2 );
            add_action( 'woocommerce_order_status_changed' , array( $this->accept_offer_page , 'update_offer_order_metadata' ) , 10 , 3 );

            // Decline offer page
            add_shortcode( 'teo_decline_offer_title' , array( $this->decline_offer_page , 'sc_teo_decline_offer_title' ) , 10 , 2 );
            add_action( 'template_redirect' , array( $this->decline_offer_page , 'execute_page_callback' ) );
            add_action( 'teo_check_offer_validity_via_decline_link' , array( $this->decline_offer_page , 'check_offer_validity' ) , 10 , 4 );

            // Unsubscribe page
            add_action( 'template_redirect' , array( $this->unsubscribe_page , 'execute_page_callback' ) );
            add_action( 'teo_check_offer_validity_via_unsubscribe_link' , array( $this->unsubscribe_page , 'check_offer_validity' ) , 10 , 4 );


            /*
            |--------------------------------------------------------------------------
            | Offer Actions
            |--------------------------------------------------------------------------
            */

            // Offer accept actions
            add_action( 'teo_execute_add-products-to-cart_accept_offer_action' , array( $this->accept_offer_action , 'execute_add_products_to_cart_accept_offer_action' ) , 10 , 4 );
            add_action( 'teo_execute_apply-coupons-to-cart_accept_offer_action' , array( $this->accept_offer_action , 'execute_apply_coupons_to_cart_accept_offer_action' ) , 10 , 4 );

            // Offer decline actions
            add_action( 'teo_execute_do-nothing_decline_offer_action' , array( $this->decline_offer_action , 'execute_do_nothing_decline_offer_action' ) , 10 , 4 );


            /*
            |--------------------------------------------------------------------------
            | Third Party Plugin Integrations
            |--------------------------------------------------------------------------
            */

            // WooCommerce Product Bundles
            $this->wc_product_bundles->run();

            // WooCommerce Composite Products
            $this->wc_composite_products->run();


            /*
            |--------------------------------------------------------------------------
            | Cron
            |--------------------------------------------------------------------------
            */

            // Send specific scheduled email offer to specific recipient
            add_action( $this->constants->CRON_HOOK_SEND_EMAIL_OFFER() , array( $this->timed_email_offer , 'send_timed_email_offer' ) , 10 , 4 );

            // Decline offer for a recipient if offer timeout is reached
            add_action( $this->constants->CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT() , array( $this->timed_email_offer , 'decline_timed_email_offer_on_timeout' ) , 10 , 3 );

        }

        /**
         * Register the various ajax interfaces the plugin exposes. This is the main controller for ajax interfaces.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_ajax_handlers() {

            // Plugin Help Pointers
            add_action( 'wp_ajax_teo_close_initial_guided_tour' , array( $this->initial_guided_tour , 'teo_close_initial_guided_tour' ) );
            add_action( 'wp_ajax_teo_close_offer_entry_guided_tour' , array( $this->offer_entry_guided_tour , 'teo_close_offer_entry_guided_tour' ) );

            // Products
            add_action( 'wp_ajax_teo_get_products' , array( $this->ajax_interface , 'teo_get_products' ) );
            add_action( 'wp_ajax_teo_get_product_additional_info' , array( $this->ajax_interface , 'teo_get_product_additional_info' ) );
            add_action( 'wp_ajax_teo_get_coupons' , array( $this->ajax_interface , 'teo_get_coupons' ) );

            // Offer Templates
            add_action( 'wp_ajax_teo_get_offer_email_templates' , array( $this->ajax_interface , 'teo_get_offer_email_templates' ) );
            add_action( 'wp_ajax_teo_add_offer_template' , array( $this->ajax_interface , 'teo_add_offer_template' ) );
            add_action( 'wp_ajax_teo_get_offer_template_info' , array( $this->ajax_interface , 'teo_get_offer_template_info' ) );
            add_action( 'wp_ajax_teo_edit_offer_template' , array( $this->ajax_interface , 'teo_edit_offer_template' ) );
            add_action( 'wp_ajax_teo_delete_offer_template' , array( $this->ajax_interface , 'teo_delete_offer_template' ) );
            add_action( 'wp_ajax_teo_send_offer_test_email' , array( $this->ajax_interface , 'teo_send_offer_test_email' ) );

            // Offer Conditions
            add_action( 'wp_ajax_teo_generate_offer_condition_group_markup' , array( $this->ajax_interface , 'teo_generate_offer_condition_group_markup' ) );
            add_action( 'wp_ajax_teo_generate_offer_condition_markup' , array( $this->ajax_interface , 'teo_generate_offer_condition_markup' ) );
            add_action( 'wp_ajax_teo_generate_product_quantity_in_order_entry_markup' , array( $this->ajax_interface , 'teo_generate_product_quantity_in_order_entry_markup' ) );
            add_action( 'wp_ajax_teo_save_timed_email_offer_conditions' , array( $this->ajax_interface , 'teo_save_timed_email_offer_conditions' ) );

            // Offer Accept Actions
            add_action( 'wp_ajax_teo_get_new_accept_offer_action_markup' , array( $this->ajax_interface , 'teo_get_new_accept_offer_action_markup' ) );
            add_action( 'wp_ajax_teo_generate_product_to_add_entry_markup' , array( $this->ajax_interface , 'teo_generate_product_to_add_entry_markup' ) );
            add_action( 'wp_ajax_teo_generate_coupon_to_apply_entry_markup' , array( $this->ajax_interface , 'teo_generate_coupon_to_apply_entry_markup' ) );
            add_action( 'wp_ajax_teo_save_accept_offer_actions' , array( $this->ajax_interface , 'teo_save_accept_offer_actions' ) );

            // Offer Decline Actions
            add_action( 'wp_ajax_teo_save_decline_offer_actions' , array( $this->ajax_interface , 'teo_save_decline_offer_actions' ) );

            // Offer Recipients
            add_action( 'wp_ajax_teo_get_offer_recipients' , array( $this->ajax_interface , 'teo_get_offer_recipients' ) );
            add_action( 'wp_ajax_teo_generate_offer_recipient_data_popup_markup' , array( $this->ajax_interface , 'teo_generate_offer_recipient_data_popup_markup' ) );
            add_action( 'wp_ajax_teo_remove_recipient_scheduled_email' , array( $this->ajax_interface , 'teo_remove_recipient_scheduled_email' ) );
            add_action( 'wp_ajax_teo_remove_recipient_from_offer' , array( $this->ajax_interface , 'teo_remove_recipient_from_offer' ) );

            // Unsubscribe
            add_action( 'wp_ajax_teo_get_blacklist' , array( $this->ajax_interface , 'teo_get_blacklist' ) );
            add_action( 'wp_ajax_teo_manually_opt_out_email' , array( $this->ajax_interface , 'teo_manually_opt_out_email' ) );
            add_action( 'wp_ajax_teo_un_opt_out_email' , array( $this->ajax_interface , 'teo_un_opt_out_email' ) );

            // Record offer scheduled email view
            add_action( 'wp_ajax_record_offer_scheduled_email_view' , array( $this->ajax_interface , 'record_offer_scheduled_email_view' ) );
            add_action( 'wp_ajax_nopriv_record_offer_scheduled_email_view' , array( $this->ajax_interface , 'record_offer_scheduled_email_view' ) );

        }

    }

}

/**
 * Main instance of Timed Email Offers.
 *
 * Returns the main instance of Timed Email Offers to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return Timed_Email_Offers
 */
function TEO() {
    return Timed_Email_Offers::instance();
}

// Global for backwards compatibility.
$GLOBALS[ 'teo' ] = TEO();
