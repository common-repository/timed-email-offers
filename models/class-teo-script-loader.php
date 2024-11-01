<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Script_Loader' ) ) {

    /**
     * Class TEO_Script_Loader
     *
     * Model that houses the logic of loading various js and css scripts Timed Email Offers plugin utilizes.
     *
     * @since 1.0.0
     */
    final class TEO_Script_Loader {

        /**
         * Property that holds the single main instance of TEO_Script_Loader.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Script_Loader
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
         * TEO_Script_Loader constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants        = $dependencies[ 'TEO_Constants' ];
            $this->_initial_guided_tour     = $dependencies[ 'TEO_Initial_Guided_Tour' ];
            $this->_offer_entry_guided_tour = $dependencies[ 'TEO_Offer_Entry_Guided_Tour' ];

        }

        /**
         * Ensure that there is only one instance of TEO_Script_Loader is loaded or can be loaded.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return TEO_Script_Loader
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Load backend js and css scripts.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $handle Unique identifier of the current backend page.
         */
        public function load_backend_scripts( $handle ) {

            $screen = get_current_screen();

            $post_type = get_post_type();
            if ( !$post_type && isset( $_GET[ 'post_type' ] ) )
                $post_type = $_GET[ 'post_type' ];

            if ( ( $handle == 'post-new.php' || $handle == 'post.php' ) && $post_type == $this->_plugin_constants->OFFER_CPT_NAME() ) {
                // 'timed_email_offer' cpt new post and edit single post page

                wp_enqueue_style( 'teo_datatables_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/data-tables/datatables.min.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'teo_vex_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'teo_vex-theme-plain_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex-theme-plain.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'teo_chosen_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/chosen_js/chosen.min.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'teo_magnific-popup_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'teo_timed-email-offer-cpt_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'offer/cpt/timed-email-offer-cpt.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

                wp_enqueue_script( 'teo_datatables_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/data-tables/datatables.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_vex_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/js/vex.combined.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_chosen_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/chosen_js/chosen.jquery.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_magnific-popup_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_timed-email-offers-templates-datatables-config_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/timed-email-offers-templates-datatables-config.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_timed-email-offer-templates_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/timed-email-offer-templates.js' , array( 'jquery' , 'jquery-tiptip' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_timed-email-offer-conditions_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/timed-email-offer-conditions.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_timed-email-offer-accept-actions_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/timed-email-offer-accept-actions.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_timed-email-offer-decline-actions_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/timed-email-offer-decline-actions.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_timed-email-offer-recipients-datatables-config_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/timed-email-offer-recipients-datatables-config.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_timed-email-offer-recipients_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/timed-email-offer-recipients.js' , array( 'jquery' , 'teo_timed-email-offer-recipients-datatables-config_js' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'teo_timed-email-offer-cpt_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/timed-email-offer-cpt.js' , array( 'jquery' , 'teo_timed-email-offer-conditions_js' , 'teo_timed-email-offer-accept-actions_js' , 'teo_timed-email-offer-decline-actions_js' ) , $this->_plugin_constants->VERSION() , true );


                wp_localize_script( 'teo_timed-email-offers-templates-datatables-config_js' , 'templates_datatables_config_param' , array(
                    'nonce_get_offer_email_templates' => wp_create_nonce( 'teo-get-offer-email-templates' ),
                    'i18n_no_email_templates'         => __( 'No Email Templates' , 'timed-email-offers' )
                ) );

                wp_localize_script( 'teo_timed-email-offer-templates_js' , 'timed_email_offer_templates_params' , array(
                    'nonce_add_offer_template'            => wp_create_nonce( 'teo-add-offer-template' ),
                    'nonce_delete_offer_template'         => wp_create_nonce( 'teo-delete-offer-template' ),
                    'nonce_get_offer_template_info'       => wp_create_nonce( 'teo-get-offer-template-info' ),
                    'nonce_edit_offer_template'           => wp_create_nonce( 'teo-edit-offer-template' ),
                    'nonce_send_offer_test_email'         => wp_create_nonce( 'teo-send-offer-test-email' ),
                    'i18n_please_specify_schedule'        => __( 'Please specify the schedule' , 'timed-email-offers' ),
                    'i18n_please_specify_subject'         => __( 'Please specify email subject' , 'timed-email-offers' ),
                    'i18n_please_specify_message'         => __( 'Please specify email message' , 'timed-email-offers' ),
                    'i18n_please_fill_form_properly'      => __( 'Please fill the template form properly' , 'timed-email-offers' ),
                    'i18n_fail_add_template'              => __( 'Failed to add offer template' , 'timed-email-offers' ),
                    'i18n_fail_delete_template'           => __( 'Failed to delete offer template' , 'timed-email-offers' ),
                    'i18n_no_template_set'                => __( 'No template Set' , 'timed-email-offers' ),
                    'i18n_confirm_delete_template'        => __( "<b>Confirm Action</b><br>Are you sure to delete this email template?<br> <b>Warning:</b> If this email template is associated to any existing recipient's scheduled email, those scheduled emails will be unscheduled." , 'timed-email-offers' ),
                    'i18n_fail_retrieve_template_info'    => __( 'Failed to retrieve offer template info' , 'timed-email-offers' ),
                    'i18n_fail_edit_template'             => __( 'Failed to edit offer template' , 'timed-email-offers' ),
                    'i18n_missing_template_index'         => __( 'Missing offer template index' , 'timed-email-offers' ),
                    'i18n_please_provide_email_recipient' => __( 'Please provide email recipient' , 'timed-email-offers' ),
                    'i18n_fill_test_email_form_properly'  => __( 'Please fill the form properly' , 'timed-email-offers' ),
                    'i18n_test_email_sent'                => __( 'Test email sent' , 'timed-email-offers' ),
                    'i18n_fail_send_test_email'           => __( 'Failed to send test email' , 'timed-email-offers' ),
                    'i18n_please_specify_heading_text'    => __( 'Please specify heading text' , 'timed-email-offers' ),
                    'i18n_default_email_template_content' => $this->_plugin_constants->DEFAULT_EMAIL_TEMPLATE_CONTENT(),
                    'i18n_days_after_order_completed'     => __( ' Days after order is completed' , 'timed-email-offers' ),
                    'i18n_schedule_must_be_greater_zero'  => __( 'Please input a value more than zero for the schedule field' , 'timed-email-offers' )
                ) );

                wp_localize_script( 'teo_timed-email-offer-conditions_js' , 'timed_email_offer_conditions_params' , array(
                    'product_quantity_conditions'                                => $this->_plugin_constants->LOGIC_CONDITIONS(),
                    'product_quantity_table_total_columns'                       => $this->_plugin_constants->PRODUCT_QUANTITY_IN_ORDER_TABLE_TOTAL_COLUMNS(),
                    'nonce_generate_offer_condition_group_markup'                => wp_create_nonce( 'teo-generate-offer-condition-group-markup' ),
                    'nonce_generate_offer_condition_markup'                      => wp_create_nonce( 'teo-generate-offer-condition-markup' ),
                    'nonce_get_product_additional_info'                          => wp_create_nonce( 'teo-get-product-additional-info' ),
                    'nonce_generate_product_quantity_in_order_entry_markup'      => wp_create_nonce( 'teo-generate-product-quantity-in-order-entry-markup' ),
                    'nonce_save_timed_email_offer_conditions'                    => wp_create_nonce( 'teo-save-timed-email-offer-conditions' ),
                    'i18n_failed_generate_condition_markup'                      => __( 'Failed to generate offer condition group markup' , 'timed-email-offers' ),
                    'i18n_confirm_remove_condition_group'                        => __( 'Are you sure you want to remove this condition group?' , 'timed-email-offers' ),
                    'i18n_no_condition_set'                                      => __( 'No conditions are set for this offer yet. This means that all customers will receive this Offer on completion of their Order.<br/>Click <b>"Add Condition Group"</b> and <b>"Add Condition"</b> buttons to add some conditions.' , 'timed-email-offers' ),
                    'i18n_please_fill_form_properly'                             => __( 'Please fill the form properly' , 'timed-email-offers' ),
                    'i18n_confirm_remove_condition'                              => __( 'Are you sure you want to remove this condition?' , 'timed-email-offers' ),
                    'i18n_empty_condition_group'                                 => __( 'Empty Condition Group. Click <b>"Add Condition"</b> button to add condition.' , 'timed-email-offers' ),
                    'i18n_please_select_variation'                               => __( 'Please select a variation...' , 'timed-email-offers' ),
                    'i18n_any_variation'                                         => __( 'Any Variation' , 'timed-email-offers' ),
                    'i18n_product_variations'                                    => __( 'Product variations' , 'timed-email-offers' ),
                    'i18n_failed_retrieve_product_data'                          => __( 'Failed to retrieve product additional data' , 'timed-email-offers' ),
                    'i18n_please_select_product'                                 => __( 'Please select product' , 'timed-email-offers' ),
                    'i18n_please_select_product_variation'                       => __( 'Please select product variation' , 'timed-email-offers' ),
                    'i18n_please_select_product_quantity_condition'              => __( 'Please select product quantity condition' , 'timed-email-offers' ),
                    'i18n_please_select_product_quantity'                        => __( 'Please select product quantity' , 'timed-email-offers' ),
                    'i18n_failed_generate_product_quantity_in_cart_entry_markup' => __( 'Failed to generate product quantity in cart entry markup' , 'timed-email-offers' ),
                    'i18n_confirm_remove_product'                                => __( 'Are you sure you want to remove this product?' , 'timed-email-offers' ),
                    'i18n_no_products_added'                                     => __( 'No products added' , 'timed-email-offers' ),
                    'i18n_success_save_conditions'                               => __( 'Successfully saved timed email offer conditions' , 'timed-email-offers' ),
                    'i18n_failed_save_conditions'                                => __( 'Failed to save timed email offer conditions' , 'timed-email-offers' ),
                    'i18n_failed_generate_offer_condition_markup'                => __( 'Failed to generate new timed email offer condition markup' , 'timed-email-offers' ),
                    'i18n_please_specify_offer_condition_type'                   => __( 'Please specify offer condition type' , 'timed-email-offers' )
                ) );

                wp_localize_script( 'teo_timed-email-offer-accept-actions_js' , 'accept_timed_email_offer_actions_params' , array(
                    'add_product_table_total_columns'             => $this->_plugin_constants->ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS(),
                    'apply_coupon_table_total_columns'            => $this->_plugin_constants->APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS(),
                    'nonce_get_new_accept_offer_action_markup'    => wp_create_nonce( 'teo-get-new-accept-offer-action-markup' ),
                    'nonce_get_product_additional_info'           => wp_create_nonce( 'teo-get-product-additional-info' ),
                    'nonce_generate_product_to_add_entry_markup'  => wp_create_nonce( 'teo-generate-product-to-add-entry-markup' ),
                    'nonce_generate_coupon_to_apply_entry_markup' => wp_create_nonce( 'teo-generate-coupon-to-apply-entry-markup' ),
                    'nonce_save_accept_offer_actions'             => wp_create_nonce( 'teo-save-accept-offer-actions' ),
                    'i18n_failed_add_accept_offer_action'         => __( 'Failed to add new accept offer action' , 'timed-email-offers' ),
                    'i18n_select_variation'                       => __( 'Please select a variation...' , 'timed-email-offers' ),
                    'i18n_product_variations'                     => __( 'Product variations' , 'timed-email-offers' ),
                    'i18n_failed_retrieve_product_data'           => __( 'Failed to retrieve product additional data' , 'timed-email-offers' ),
                    'i18n_select_product_to_add'                  => __( 'Please select a product to add' , 'timed-email-offers' ),
                    'i18n_specify_product_quantity'               => __( 'Please specify a product quantity to add' , 'timed-email-offers' ),
                    'i18n_failed_generate_product_entry_markup'   => __( 'Failed to generate product to add entry mark up' , 'timed-email-offers' ),
                    'i18n_fill_form_properly'                     => __( 'Please fill the form properly' , 'timed-email-offers' ),
                    'i18n_confirm_delete_product'                 => __( 'Are you sure to delete this product?' , 'timed-email-offers' ),
                    'i18n_no_products_found'                      => __( 'No products added' , 'timed-email-offers' ),
                    'i18n_failed_generate_coupon_entry_markup'    => __( 'Failed to generate coupon to apply entry markup' , 'timed-email-offers' ),
                    'i18n_confirm_delete_coupon'                  => __( 'Are you sure to remove this coupon?' , 'timed-email-offers' ),
                    'i18n_no_coupons_added'                       => __( 'No coupons added' , 'timed-email-offers' ),
                    'i18n_confirm_delete_action'                  => __( 'Are you sure to delete this accept offer action?' , 'timed-email-offers' ),
                    'i18n_no_action_to_take'                      => __( '<p class="no-actions">Take the customer to the site and then ...<br/>There\'s currently no acceptance actions defined. Click <b>"Add Action"</b> to define what should happen after clicking Accept in an email.</p>' , 'timed-email-offers' ),
                    'i18n_successfully_saved_action'              => __( 'Successfully saved accept offer actions' , 'timed-email-offers' ),
                    'i18n_failed_save_action'                     => __( 'Failed to save accept offer actions' , 'timed-email-offers' ),
                    'i18n_select_product_variation_to_add'        => __( 'Please select a product variation to add' , 'timed-email-offers' ),
                    'i18n_select_coupon_to_apply'                 => __( 'Please select a coupon to apply' , 'timed-email-offers' ),
                    'i18n_select_action_to_execute'               => __( 'Please select an action to execute' , 'timed-email-offers' )
                ) );

                wp_localize_script( 'teo_timed-email-offer-decline-actions_js' , 'decline_timed_email_offer_actions_params' , array(
                    'nonce_save_decline_offer_actions'        => wp_create_nonce( 'teo-save-decline-offer-actions' ),
                    'i18n_please_fill_form_properly'          => __( 'Please fill the form properly' , 'timed-email-offers' ),
                    'i18n_success_save_decline_offer_actions' => __( 'Successfully saved decline offer actions' , 'timed-email-offers' ),
                    'i18n_failed_save_decline_offer_actions'  => __( 'Failed to save decline offer actions' , 'timed-email-offers' )
                ) );

                wp_localize_script( 'teo_timed-email-offer-recipients-datatables-config_js' , 'offer_recipients_datatables_config_params' , array(
                    'nonce_get_offer_recipients' => wp_create_nonce( 'teo-get-offer-recipients' ),
                    'i18n_no_recipients'         => __( 'No Recipients' , 'timed-email-offers' )
                ) );

                wp_localize_script( 'teo_timed-email-offer-recipients_js' , 'timed_email_offer_recipients_params' , array(
                    'nonce_generate_offer_recipient_data_popup_markup' => wp_create_nonce( 'teo-generate-offer-recipient-data-popup-markup' ),
                    'nonce_remove_recipient_scheduled_email'           => wp_create_nonce( 'teo-remove-recipient-scheduled-email' ),
                    'nonce_remove_recipient_from_offer'                => wp_create_nonce( 'teo-remove-recipient-from-offer' ),
                    'recipient_schedules_table_total_columns'          => $this->_plugin_constants->OFFER_RECIPIENT_POPUP_SCHEDULES_TABLE_TOTAL_COLUMNS(),
                    'i18n_confirm_remove_scheduled_email'              => __( 'Are you sure you want to remove this scheduled email?' , 'timed-email-offers' ),
                    'i18n_no_scheduled_emails'                         => __( 'No Scheduled Emails' , 'timed-email-offers' ),
                    'i18n_failed_remove_scheduled_email'               => __( 'Failed to remove scheduled email' , 'timed-email-offers' ),
                    'i18n_failed_retrieving_recipient_data'            => __( 'Failed retrieving recipient data.' , 'timed-email-offers' ),
                    'i18n_confirm_remove_recipient'                    => __( 'Are you sure to want to remove this recipient?' , 'timed-email-offers' ),
                    'i18n_failed_remove_offer_recipient'               => __( 'Failed to remove offer recipient' , 'timed-email-offers' )
                ) );

            } elseif ( $handle == 'edit.php' && $post_type == $this->_plugin_constants->OFFER_CPT_NAME() ) {
                
                // Quick edit
                wp_enqueue_script( 'teo_plugin-quick-edit_js' , $this->_plugin_constants->JS_ROOT_URL() . 'admin/plugin-quick-edit.js' , array() , $this->_plugin_constants->VERSION() , true );

            } elseif ( in_array( $screen->id , array( 'woocommerce_page_wc-settings' ) ) && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'teo_settings' ) {

                // Settings

                if ( isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'teo_setting_decline_section' ) {

                    // Decline Section
                    wp_enqueue_script( 'teo_timed-email-offer-decline-section-options_js' , $this->_plugin_constants->JS_ROOT_URL() . 'settings/decline/timed-email-offer-decline-section-options.js' , array() , $this->_plugin_constants->VERSION() , 'all' );

                } elseif ( isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] == 'teo_setting_blacklist_section' ) {

                    // Blacklist Section

                    wp_enqueue_style( 'teo_datatables_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/data-tables/datatables.min.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                    wp_enqueue_style( 'teo_vex_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                    wp_enqueue_style( 'teo_vex-theme-plain_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex-theme-plain.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                    wp_enqueue_style( 'teo_timed-email-offer-blacklist_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'settings/blacklist/timed-email-offer-blacklist.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

                    wp_enqueue_script( 'teo_datatables_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/data-tables/datatables.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                    wp_enqueue_script( 'teo_vex_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/js/vex.combined.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                    wp_enqueue_script( 'teo_timed-email-offer-blacklist-datatable-config_js' , $this->_plugin_constants->JS_ROOT_URL() . 'settings/blacklist/timed-email-offer-blacklist-datatable-config.js' , array() , $this->_plugin_constants->VERSION() , 'all' );
                    wp_enqueue_script( 'teo_timed-email-offer-blacklist_js' , $this->_plugin_constants->JS_ROOT_URL() . 'settings/blacklist/timed-email-offer-blacklist.js' , array() , $this->_plugin_constants->VERSION() , 'all' );

                    wp_localize_script( 'teo_timed-email-offer-blacklist-datatable-config_js' , 'blacklist_datatable_config_params' , array(
                        'nonce_get_blacklist'        => wp_create_nonce( 'teo-get-blacklist' ),
                        'i18n_no_blacklisted_emails' => __( 'No Blacklisted Emails' , 'timed-email-offers' )
                    ) );

                    wp_localize_script( 'teo_timed-email-offer-blacklist_js' , 'blacklist_params' , array(
                        'nonce_manually_opt_out_email' => wp_create_nonce( 'teo-manually-opt-out-email' ),
                        'nonce_un_opt_out_email'       => wp_create_nonce( 'teo-un-opt-out-email' ),
                        'i18n_input_valid_email'       => __( 'Please fill a valid email address' , 'timed-email-offers' ),
                        'i18n_confirm_blacklist_email' => __( '<b>Are you sure to blacklist this email?</b><br/>Executing this means removing any existing entry of this user with this email from the list of recipients of all Timed Email Offers.' , 'timed-email-offers' ),
                        'i18n_failed_opt_out_email'    => __( 'Failed to opt-out email' , 'timed-email-offers' ),
                        'i18n_confirm_opt_out_email'   => __( 'Are you sure you wish to remove this email from the blacklist? This means the customer will be eligible to receive timed email offers again.' , 'timed-email-offers' ),
                        'i18n_warning_opt_out_email'   => __( '<b>Warning:</b> This is an unsubscribed email.' , 'timed-email-offers' ),
                        'i18n_failed_opt_out_email'    => __( 'Failed to opt-out email' , 'timed-email-offers' )
                    ) );

                }

            }

            // Help Pointers
            if ( get_option( TEO_Initial_Guided_Tour::OPTION_INITIAL_GUIDED_TOUR_STATUS , false ) == TEO_Initial_Guided_Tour::STATUS_OPEN && array_key_exists( $screen->id , $this->_initial_guided_tour->get_screens() ) ) {

                wp_enqueue_style( 'teo_plugin-guided-tour_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'admin/plugin-guided-tour.css' , array( 'wp-pointer' ) , $this->_plugin_constants->VERSION() , 'all' );

                wp_enqueue_script( 'teo_plugin-initial-guided-tour_js' , $this->_plugin_constants->JS_ROOT_URL() . 'admin/plugin-initial-guided-tour.js' , array( 'wp-pointer' , 'thickbox' ) , $this->_plugin_constants->VERSION() , true );

                wp_localize_script( 'teo_plugin-initial-guided-tour_js' , 'teo_initial_guided_tour_params', array(
                    'actions' => array( 'close_tour' => 'teo_close_initial_guided_tour' ),
                    'nonces'  => array( 'close_tour' => wp_create_nonce( 'teo-close-initial-guided-tour' ) ),
                    'screen'  => $this->_initial_guided_tour->get_current_screen(),
                    'height'  => 640,
                    'width'   => 640,
                    'texts'   => array(
                                    'btn_prev_tour'  => __( 'Previous' , 'timed-email-offers' ),
                                    'btn_next_tour'  => __( 'Next' , 'timed-email-offers' ),
                                    'btn_close_tour' => __( 'Close' , 'timed-email-offers' ),
                                    'btn_start_tour' => __( 'Start Tour' , 'timed-email-offers' )
                                ),
                    'urls'    => array( 'ajax' => admin_url( 'admin-ajax.php' ) ),
                    'post'    => isset( $post ) && isset( $post->ID ) ? $post->ID : 0
                ) );

            }

            if ( get_option( TEO_Offer_Entry_Guided_Tour::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS , false ) == TEO_Offer_Entry_Guided_Tour::STATUS_OPEN && array_key_exists( $screen->id , $this->_offer_entry_guided_tour->get_screens() ) ) {

                wp_enqueue_style( 'teo_plugin-guided-tour_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'admin/plugin-guided-tour.css' , array( 'wp-pointer' ) , $this->_plugin_constants->VERSION() , 'all' );

                wp_enqueue_script( 'teo_plugin-offer-entry-guided-tour_js' , $this->_plugin_constants->JS_ROOT_URL() . 'admin/plugin-offer-entry-guided-tour.js' , array( 'wp-pointer' , 'thickbox' ) , $this->_plugin_constants->VERSION() , true );

                wp_localize_script( 'teo_plugin-offer-entry-guided-tour_js' , 'teo_offer_entry_guided_tour_params', array(
                    'actions' => array( 'close_tour' => 'teo_close_offer_entry_guided_tour' ),
                    'nonces'  => array( 'close_tour' => wp_create_nonce( 'teo-close-offer-entry-guided-tour' ) ),
                    'screen'  => $this->_offer_entry_guided_tour->get_current_screen(),
                    'height'  => 640,
                    'width'   => 640,
                    'texts'   => array(
                                    'btn_prev_tour'  => __( 'Previous' , 'just-in-time-sales-offers' ),
                                    'btn_next_tour'  => __( 'Next' , 'just-in-time-sales-offers' ),
                                    'btn_close_tour' => __( 'Close' , 'just-in-time-sales-offers' ),
                                    'btn_start_tour' => __( 'Start Tour' , 'just-in-time-sales-offers' )
                                ),
                    'urls'    => array( 'ajax' => admin_url( 'admin-ajax.php' ) ),
                    'post'    => isset( $post ) && isset( $post->ID ) ? $post->ID : 0
                ) );


            }

        }

        /**
         * Load frontend js and css scripts.
         *
         * @since 1.0.0
         * @access public
         */
        public function load_frontend_scripts() {

            global $post, $wp;

        }

    }

}
