<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Bootstrap' ) ) {

    /**
     * Class TEO_Bootstrap
     *
     * Model that houses the logic of booting up (activating) and shutting down (deactivating) Timed Email Offers plugin.
     *
     * @since 1.0.0
     */
    final class TEO_Bootstrap {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Bootstrap.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Bootstrap
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
         * Property that holds the plugin initial guided tour help pointers.
         *
         * @since 1.2.0
         * @access private
         * @var TEO_Initial_Guided_Tour
         */
        private $_initial_guided_tour;

        /**
         * Property that holds the plugin offer entry guided tour help pointers.
         *
         * @since 1.2.0
         * @access private
         * @var TEO_Offer_Entry_Guided_Tour
         */
        private $_offer_entry_guided_tour;

        /**
         * Property that wraps the logic of timed email offer custom post type.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_CPT
         */
        private $_offer_cpt;




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
         * TEO_Bootstrap constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Bootstrap model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants        = $dependencies[ 'TEO_Constants' ];
            $this->_initial_guided_tour     = $dependencies[ 'TEO_Initial_Guided_Tour' ];
            $this->_offer_entry_guided_tour = $dependencies[ 'TEO_Offer_Entry_Guided_Tour' ];
            $this->_offer_cpt               = $dependencies[ 'TEO_Offer_CPT' ];

        }

        /**
         * Ensure that only one instance of TEO_Bootstrap is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Bootstrap model.
         * @return TEO_Bootstrap
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Method that houses the logic relating to activating Timed Email Offers plugin.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $network_wide
         */
        public function activate_plugin( $network_wide ) {

            global $wpdb;

            if ( is_multisite() ) {

                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        $this->_activate_plugin( $blog_id );

                    }

                    restore_current_blog();

                } else {

                    // activated on a single site, in a multi-site
                    $this->_activate_plugin( $wpdb->blogid );

                }

            } else {

                // activated on a single site
                $this->_activate_plugin( $wpdb->blogid );

            }

        }

        /**
         * Method to initialize a newly created site in a multi site set up.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $blog_id
         * @param $user_id
         * @param $domain
         * @param $path
         * @param $site_id
         * @param $meta
         */
        public function new_mu_site_init( $blog_id , $user_id , $domain , $path , $site_id , $meta ) {

            if ( is_plugin_active_for_network( 'timed-email-offers/timed-email-offers.php' ) ) {

                switch_to_blog( $blog_id );
                $this->_activate_plugin( $blog_id );
                restore_current_blog();

            }

        }

        /**
         * Create plugin custom tables.
         *
         * @since 1.2.0
         * @access private
         */
        private function _create_custom_tables() {

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            $latest_offer_recipients_table_version       = '1.0.0';
            $latest_email_schedules_table_version        = '1.0.0';
            $latest_offer_email_views_logs_table_version = '1.0.0';

            if ( $latest_offer_recipients_table_version != get_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS_VERSION() ) ) {

                // Create offer recipients table

                $offer_views_table_sql = "CREATE TABLE " . $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() . " (
                                          recipient_id INT NOT NULL AUTO_INCREMENT,
                                          order_id INT NOT NULL,
                                          offer_id INT NOT NULL,
                                          customer_email VARCHAR(100) NOT NULL,
                                          untrashed BOOLEAN DEFAULT 0 NOT NULL,
                                          response_status VARCHAR(50) NULL,
                                          response_datetime DATETIME NULL,
                                          timeout VARCHAR(50) NULL,
                                          timeout_datetime DATETIME NULL,
                                          created_order_id INT NULL,
                                          PRIMARY KEY  (recipient_id),
                                          UNIQUE KEY unique_offer_order_pair (offer_id,order_id)
                                          ) $charset_collate;";

                dbDelta( $offer_views_table_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS_VERSION() , $latest_offer_recipients_table_version );

            }

            if ( $latest_email_schedules_table_version  != get_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS_VERSION() ) ) {

                // Create email schedules table

                $email_schedules_table_sql = "CREATE TABLE " . $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() . " (
                                              email_token VARCHAR(50) NOT NULL,
                                              recipient_id INT NOT NULL,
                                              base_datetime DATETIME NOT NULL,
                                              cron_datetime DATETIME NOT NULL,
                                              template_id INT NOT NULL,
                                              send_status VARCHAR(50) NOT NULL,
                                              response_status VARCHAR(50) NULL,
                                              response_datetime DATETIME NULL,
                                              timeout VARCHAR(50) NULL,
                                              timeout_datetime DATETIME NULL,
                                              created_order_id INT NULL,
                                              PRIMARY KEY  (email_token,recipient_id)
                                              ) $charset_collate;";

                dbDelta( $email_schedules_table_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS_VERSION() , $latest_email_schedules_table_version );

            }

            if ( $latest_offer_email_views_logs_table_version != get_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS_VERSION() ) ) {

                // Create offer email logs table

                $offer_email_emails_views_logs_sql = "CREATE TABLE " . $this->_plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS() . " (
                                                      id INT NOT NULL AUTO_INCREMENT,
                                                      email_token VARCHAR(50) NOT NULL,
                                                      recipient_id INT NOT NULL,
                                                      view_datetime DATETIME NOT NULL,
                                                      PRIMARY KEY  (id)
                                                      ) $charset_collate;";

                dbDelta( $offer_email_emails_views_logs_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS_VERSION() , $latest_offer_email_views_logs_table_version );

            }

        }

        /**
         * Migrate offer recipients data from post meta to custom tables.
         * TODO: Remove this on future versions.
         *
         * @since 1.2.0
         * @access private
         */
        private function _migrate_offer_recipients_data_to_custom_tables() {

            global $wpdb;

            $teo_offers = TEO_Helper::get_all_timed_email_offers_legacy();

            // Migrate offer recipients data
            if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() . "'" ) ) {

                foreach ( $teo_offers as $teo_offer ) {

                    $offer_recipients = get_post_meta( $teo_offer->ID , $this->_plugin_constants->POST_META_OFFER_RECIPIENTS() , true );
                    if ( !is_array( $offer_recipients ) )
                        $offer_recipients = array();

                    foreach ( $offer_recipients as $order_id => $offer_recipient ) {

                        $offer_recipients_row_data = array(
                                                        'order_id'       => $order_id,
                                                        'offer_id'       => $teo_offer->ID,
                                                        'customer_email' => $offer_recipient[ 'customer' ]->user_email,
                                                        'untrashed'      => $offer_recipient[ 'untrashed' ] ? 1 : 0
                                                    );

                        if ( isset( $offer_recipient[ 'response_status' ] ) )
                            $offer_recipients_row_data[ 'response_status' ] = $offer_recipient[ 'response_status' ];

                        if ( isset( $offer_recipient[ 'response_timestamp' ] ) )
                            $offer_recipients_row_data[ 'response_datetime' ] = date( 'Y-m-d H:i:s' , $offer_recipient[ 'response_timestamp' ] );

                        if ( isset( $offer_recipient[ 'timeout' ] ) )
                            $offer_recipients_row_data[ 'timeout' ] = $offer_recipient[ 'timeout' ];

                        if ( isset( $offer_recipient[ 'timeout_timestamp' ] ) )
                            $offer_recipients_row_data[ 'timeout_datetime' ] = date( 'Y-m-d H:i:s' , $offer_recipient[ 'timeout_timestamp' ] );

                        if ( isset( $offer_recipient[ 'order_id' ] ) )
                            $offer_recipients_row_data[ 'created_order_id' ] = $offer_recipient[ 'order_id' ];

                        $wpdb->insert( $this->_plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() , $offer_recipients_row_data );

                        // Migrate schedules emails data
                        if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() . "'" ) && !empty( $offer_recipient[ 'scheduled_emails' ] ) ) {

                            $recipient_id = $wpdb->insert_id; // Latest recipient_id inserted. Auto-increment field.

                            foreach ( $offer_recipient[ 'scheduled_emails' ] as $email_token => $schedule_email ) {

                                $schedules_emails_row_data = array(
                                                                'email_token'   => $email_token,
                                                                'recipient_id'  => $recipient_id,
                                                                'base_datetime' => date( 'Y-m-d H:i:s' , $schedule_email[ 'schedule_timestamp' ][ 'base_timestamp' ] ),
                                                                'cron_datetime' => date( 'Y-m-d H:i:s' , $schedule_email[ 'schedule_timestamp' ][ 'cron_timestamp' ] ),
                                                                'template_id'   => $schedule_email[ 'template_id' ],
                                                                'send_status'   => $schedule_email[ 'send_status' ]
                                                            );

                                if ( isset( $schedule_email[ 'response_status' ] ) )
                                    $schedules_emails_row_data[ 'response_status' ] = $schedule_email[ 'response_status' ];

                                if ( isset( $schedule_email[ 'response_timestamp' ] ) )
                                    $schedules_emails_row_data[ 'response_datetime' ] = date( 'Y-m-d H:i:s' , $schedule_email[ 'response_timestamp' ] );

                                if ( isset( $schedule_email[ 'timeout' ] ) )
                                    $schedules_emails_row_data[ 'timeout' ] = $schedule_email[ 'timeout' ];

                                if ( isset( $schedule_email[ 'timeout_timestamp' ] ) )
                                    $schedules_emails_row_data[ 'timeout_datetime' ] = date( 'Y-m-d H:i:s' , $schedule_email[ 'timeout_timestamp' ] );

                                if ( isset( $schedule_email[ 'order_id' ] ) )
                                    $schedules_emails_row_data[ 'created_order_id' ] = $schedule_email[ 'order_id' ];

                                $wpdb->insert( $this->_plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() , $schedules_emails_row_data );

                            }

                        }

                    }

                    delete_post_meta( $teo_offer->ID , $this->_plugin_constants->POST_META_OFFER_RECIPIENTS() );

                    // Set initial value for post meta as empty string
                    update_post_meta( $teo_offer->ID , $this->_plugin_constants->POST_META_OFFER_ORDER() , '' );

                } // foreach ( $teo_offers as $teo_offer )

            }

        }

        /**
         * Update existing teo offer crons.
         * TODO: Remove this on future versions.
         *
         * @since 1.2.0
         * @access public
         */
        private function _update_existing_cron_jobs() {

            if ( get_option( $this->_plugin_constants->CRON_JOBS_UPDATED() , false ) != 'yes' ) {

                $scheduled_cron_jobs = _get_cron_array();

                foreach ( $scheduled_cron_jobs as $timestamp => $cron_jobs ) {
                    foreach ( $cron_jobs as $hook => $dings ) {
                        foreach ( $dings as $sig => $data ) {

                            // check if this is a hook we need to concern about
                            if ( $hook == $this->_plugin_constants->CRON_HOOK_SEND_EMAIL_OFFER() && count( $data[ 'args' ] ) == 5 ) {

                                wp_unschedule_event( $timestamp , $hook , $data[ 'args' ] );

                                // Old args
                                // $args = array( $offer->ID , $template_id , $order->id , $customer , $unique_email_token );
                                // We dont need the customer be on the cron arg
                                // We add proper data typing to cron args
                                // We re-arrange the cron args to make it have more sense

                                $new_args = array(
                                                (int) $data[ 'args' ][ 0 ], // Offer Id
                                                (int) $data[ 'args' ][ 2 ], // Order Id
                                                (int) $data[ 'args' ][ 1 ], // Template Id
                                                $data[ 'args' ][ 4 ]        // Email token
                                            );

                                wp_schedule_single_event( $timestamp , $hook , $new_args );

                            } elseif ( $hook ==  $this->_plugin_constants->CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT() ) {

                                // Old args
                                // $args = array( $offer->ID , $order->id , $last_sched_token );
                                // We add proper data typing to cron args

                                wp_unschedule_event( $timestamp , $hook , $data[ 'args' ] );

                                $new_args = array(
                                                (int) $data[ 'args' ][ 0 ], // Offer Id
                                                (int) $data[ 'args' ][ 1 ], // Order Id
                                                $data[ 'args' ][ 2 ]        // Email token
                                            );

                                wp_schedule_single_event( $timestamp , $hook , $new_args );

                            }

                        }
                    }
                }

                update_option( $this->_plugin_constants->CRON_JOBS_UPDATED() , 'yes' );

            }

        }

        /**
         * Initialize plugin options.
         *
         * @since 1.2.0
         * @access private
         */
        private function _initialize_plugin_options() {

            // Set initial value of 'no' for the option that sets the option that specify whether to delete the options on plugin uninstall
            if ( !get_option( $this->_plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() , false ) )
                update_option( $this->_plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() , 'no' );
            
            // Set initial value of 30 for the offer timeout option
            // Only when user didn't intentionally set the option as empty string
            if ( !get_option( $this->_plugin_constants->OPTION_OFFER_TIMEOUT_PERIOD() , false ) === false )
                update_option( $this->_plugin_constants->OPTION_OFFER_TIMEOUT_PERIOD() , 30 );

        }

        /**
         * Initialize decline offer page.
         *
         * @since 1.2.0
         * @access private
         */
        private function _initialize_decline_offer_page() {

            // Create decline offer page
            $decline_offer_page_id = get_option( $this->_plugin_constants->OPTION_DECLINE_OFFER_PAGE_ID() , false );

            if ( !$decline_offer_page_id || get_post_status( $decline_offer_page_id ) != 'publish' ) {

                $decline_offer_page_id = wp_insert_post( array(
                                            'post_title'   => __( 'Offer Declined' , 'timed-email-offers' ),
                                            'post_name'    => 'teo-decline',
                                            'post_content' => __( 'You have successfully decline the offer <b>"[teo_decline_offer_title]"</b>' , 'timed-email-offers' ),
                                            'post_type'    => 'page',
                                            'post_status'  => 'publish'
                                        ) , true );

                if ( !is_wp_error( $decline_offer_page_id ) )
                    update_option( $this->_plugin_constants->OPTION_DECLINE_OFFER_PAGE_ID() , $decline_offer_page_id );

            }

        }

        /**
         * Initialize unsubscribe offer page.
         *
         * @since 1.2.0
         * @access private
         */
        private function _initialize_unsubscribe_offer_page() {

            // Create unsubscribe page
            $unsubscribe_page_id = get_option( $this->_plugin_constants->OPTION_UNSUBSCRIBE_PAGE_ID() , false );

            if ( !$unsubscribe_page_id || get_post_status( $unsubscribe_page_id ) != 'publish' ) {

                $unsubscribe_page_id = wp_insert_post( array(
                                            'post_title'   => __( 'Unsubscribed From Offers' , 'timed-email-offers' ),
                                            'post_name'    => 'teo-unsubscribe',
                                            'post_content' => __( 'You have successfully unsubscribe to all Timed Email Offers of this site.' , 'timed-email-offers' ),
                                            'post_type'    => 'page',
                                            'post_status'  => 'publish'
                                        ) , true );

                if ( !is_wp_error( $unsubscribe_page_id ) )
                    update_option( $this->_plugin_constants->OPTION_UNSUBSCRIBE_PAGE_ID() , $unsubscribe_page_id );

            }

        }

        /**
         * Initialize accept offer page.
         *
         * @since 1.2.0
         * @access private
         */
        private function _initialize_accept_offer_page() {

            // Set accept offer page
            $accept_offer_page_id = get_option( $this->_plugin_constants->OPTION_ACCEPT_OFFER_PAGE_ID() , false );

            if ( !$accept_offer_page_id || get_post_status( $accept_offer_page_id ) != 'publish' ) {

                $cart_page_id = TEO_Helper::get_woocommerce_page_id( 'cart' );

                if ( $cart_page_id )
                    update_option( $this->_plugin_constants->OPTION_ACCEPT_OFFER_PAGE_ID() , $cart_page_id );

            }

        }

        /**
         * Initialize invalid offer page.
         *
         * @since 1.2.0
         * @access private
         */
        private function _initialize_invalid_offer_page() {

            // Set invalid offer page
            $invalid_offer_page_id = get_option( $this->_plugin_constants->OPTION_INVALID_OFFER_PAGE_ID() , false );

            if ( !$invalid_offer_page_id || get_post_status( $invalid_offer_page_id ) != 'publish' ) {

                $invalid_offer_page_id = wp_insert_post( array(
                    'post_title'   => __( 'Invalid Offer' , 'timed-email-offers' ),
                    'post_name'    => 'teo-invalid-offer',
                    'post_content' => __( '[teo-invalid-offer-error-message]' , 'timed-email-offers' ),
                    'post_type'    => 'page',
                    'post_status'  => 'publish'
                ) , true );

                if ( !is_wp_error( $invalid_offer_page_id ) )
                    update_option( $this->_plugin_constants->OPTION_INVALID_OFFER_PAGE_ID() , $invalid_offer_page_id );

            }

        }

        /**
         * Actual function that houses the code to execute on plugin activation.
         *
         * @since 1.0.0
         * @since 1.2.0 Create plugin custom tables and migrate existing data to custom tables and refactor code base.
         * @access private
         *
         * @param $blogid
         */
        private function _activate_plugin( $blogid ) {

            // Register 'timed_email_offer' cpt.
            $this->_offer_cpt->register_timed_email_offer_cpt();

            // Create plugin custom tables and migrate existing data to custom tables.
            $this->_create_custom_tables();
            $this->_migrate_offer_recipients_data_to_custom_tables();

            // Update existing cron jobs
            $this->_update_existing_cron_jobs();

            // Initialize plugin options.
            $this->_initialize_plugin_options();

            // Initialize decline offer page.
            $this->_initialize_decline_offer_page();

            // Initialize unsubscribe offer page.
            $this->_initialize_unsubscribe_offer_page();

            // Initialize accept offer page.
            $this->_initialize_accept_offer_page();

            // Initialize invalid offer page.
            $this->_initialize_invalid_offer_page();

            // Help pointers
            $this->_initial_guided_tour->initialize_guided_tour_options();
            $this->_offer_entry_guided_tour->initialize_guided_tour_options();

            flush_rewrite_rules();

        }

        /**
         * Method that houses the logic relating to deactivating Timed Email Offers plugin.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $network_wide
         */
        public function deactivate_plugin( $network_wide ) {

            global $wpdb;

            // check if it is a multisite network
            if ( is_multisite() ) {

                // check if the plugin has been activated on the network or on a single site
                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        $this->_deactivate_plugin( $wpdb->blogid );

                    }

                    restore_current_blog();

                } else {

                    // activated on a single site, in a multi-site
                    $this->_deactivate_plugin( $wpdb->blogid );

                }

            } else {

                // activated on a single site
                $this->_deactivate_plugin( $wpdb->blogid );

            }

        }

        /**
         * Actual method that houses the code to execute on plugin deactivation.
         *
         * @since 1.0.0
         * @access private
         *
         * @param $blogid
         */
        private function _deactivate_plugin( $blogid ) {

            $this->_initial_guided_tour->terminate_guided_tour_options();
            $this->_offer_entry_guided_tour->terminate_guided_tour_options();

            flush_rewrite_rules();

        }

        /**
         * Method that houses codes to be executed on init hook.
         *
         * @since 1.0.0
         * @access public
         */
        public function initialize() {

            if ( get_option( 'teo_activation_code_triggered' , false ) !== 'yes' ) {

                if ( ! function_exists( 'is_plugin_active_for_network' ) )
                    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

                $network_wide = is_plugin_active_for_network( 'timed-email-offers/timed-email-offers.php' );
                $this->activate_plugin( $network_wide );

            }

            $this->_offer_cpt->register_timed_email_offer_cpt();

        }

        /**
         * Initialize the plugin's settings page. Integrate to WooCommerce settings.
         *
         * @since 1.0.0
         * @access public
         *
		 * @param $settings
		 * @param $models_root_path
         * @return array
         */
        public function initialize_plugin_settings_page( $settings , $models_root_path = null ) {

            if ( is_null( $models_root_path ) )
                $models_root_path = $this->_plugin_constants->MODELS_ROOT_PATH();

            $settings[] = include( $models_root_path . "class-teo-settings.php" );
            return $settings;

        }

        /**
         * Register TEO pages query params.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $vars
         * @return array
         */
        public function  add_page_query_vars( $vars ) {

            $vars[] = $this->_plugin_constants->PAGE_PARAM_OFFER_ID();
            $vars[] = $this->_plugin_constants->PAGE_PARAM_ORDER_ID();
            $vars[] = $this->_plugin_constants->PAGE_PARAM_EMAIL_TOKEN();

            return $vars;

        }




        /*
        |--------------------------------------------------------------------------
        | WP Integration
        |--------------------------------------------------------------------------
        */

        /**
         * Add settings link to plugin actions links.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $links
         * @param $file
         * @return array
         */
        public function plugin_settings_action_link( $links , $file ) {

            if ( $file == $this->_plugin_constants->PLUGIN_BASENAME() . '/timed-email-offers.php' ) {

                $settings_link = '<a href="admin.php?page=wc-settings&tab=teo_settings">' . __( 'Settings' , 'time-sales-offers' ) . '</a>';
                array_unshift( $links , $settings_link );

            }

            return $links;

        }

    }

}