<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'TEO_Settings' ) ) {

    /**
     * Class TEO_Settings
     *
     * Integrate into WooCommerce settings page and initialize Timed Email Offers settings page.
     * We do it in traditional way ( none singleton pattern ) for full compatibility with woocommerce
     * settings page integration requirements.
     *
     * @since 1.0.0
     */
    class TEO_Settings extends WC_Settings_Page {

        /**
         * teo_Constants instance. Holds various constants this class uses.
         *
         * @since 1.0.0
         * @access private
         * @var teo_Constants
         */
        private $_plugin_constants;

        /**
         * TEO_Settings constructor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

            $this->_plugin_constants = TEO_Constants::instance(); // Not dependency injection but this is safe as TEO_Constants class is already loaded.

            $this->id    = 'teo_settings';
            $this->label = __( 'Timed Email Offers' , 'timed-email-offers' );

            add_filter( 'woocommerce_settings_tabs_array' , array( $this , 'add_settings_page' ) , 30 ); // 30 so it is after the API tab
            add_action( 'woocommerce_settings_' . $this->id , array( $this , 'output' ) );
            add_action( 'woocommerce_settings_save_' . $this->id , array( $this , 'save' ) );
            add_action( 'woocommerce_sections_' . $this->id , array( $this , 'output_sections' ) );

            // Custom settings fields
            add_action( 'woocommerce_admin_field_teo_help_resources_controls' , array( $this , 'render_teo_help_resources_controls' ) );
            add_action( 'woocommerce_admin_field_teo_blacklist_table_control' , array( $this , 'render_teo_blacklist_table_control' ) );
            add_action( 'woocommerce_admin_field_teo_upgrade_banner_controls' , array( $this , 'render_teo_upgrade_banner_controls' ) );
            add_action( 'woocommerce_admin_field_teo_checkbox_custom' , array( $this, 'teo_custom_checkbox' ) );

            // Save custom settings fields
            add_filter( 'woocommerce_admin_settings_sanitize_option' , array( $this, 'teo_custom_checkbox_save' ) , 10 , 3 );

            do_action( 'teo_settings_construct' );

        }

        /**
         * Get sections.
         *
         * @return array
         * @since 1.0.0
         */
        public function get_sections() {

            $sections = array(
                ''                               => __( 'General' , 'timed-email-offers' ),
                'teo_setting_acceptance_section' => __( 'Acceptance' , 'timed-email-offers' ),
                'teo_setting_decline_section'    => __( 'Decline' , 'timed-email-offers' ),
                'teo_setting_blacklist_section'  => __( 'Blacklist' , 'timed-email-offers' ),
                'teo_setting_help_section'       => __( 'Help' , 'timed-email-offers' )
            );

            return apply_filters( 'woocommerce_get_sections_' . $this->id , $sections );

        }

        /**
         * Output the settings.
         *
         * @since 1.0.0
         */
        public function output() {

            global $current_section;

            $settings = $this->get_settings( $current_section );
            WC_Admin_Settings::output_fields( $settings );

        }

        /**
         * Save settings.
         *
         * @since 1.0.0
         */
        public function save() {

            global $current_section;

            $settings = $this->get_settings( $current_section );

            do_action( 'teo_before_save_settings' , $settings );

            WC_Admin_Settings::save_fields( $settings );

            do_action( 'teo_after_save_settings' , $settings );

        }

        /**
         * Get settings array.
         *
         * @param string $current_section
         *
         * @return mixed
         * @since 1.0.0
         */
        public function get_settings( $current_section = '' ) {

            if ( $current_section == 'teo_setting_help_section' ) {

                // Help Section Options
                $settings = apply_filters( 'teo_setting_help_section_options' , $this->_get_help_section_options() );

            } elseif ( $current_section == 'teo_setting_acceptance_section' ) {

                // Acceptance Section Options
                $settings = apply_filters( 'teo_setting_acceptance_section_options' , $this->_get_acceptance_section_options() );

            } elseif ( $current_section == 'teo_setting_decline_section' ) {

                // Decline Sections Options
                $settings = apply_filters( 'teo_setting_decline_section_options' , $this->_get_decline_section_options() );

            } elseif ( $current_section == 'teo_setting_blacklist_section' ) {

                // Blacklist Section Options
                $settings = apply_filters( 'teo_setting_blacklist_section_options' , $this->_get_blacklist_section_options() );

            } else {

                // General Section Options
                $settings = apply_filters( 'teo_setting_general_section_options' , $this->_get_general_section_options() );

            }

            return apply_filters( 'woocommerce_get_settings_' . $this->id , $settings , $current_section );

        }




        /*
         |--------------------------------------------------------------------------------------------------------------
         | Section Settings
         |--------------------------------------------------------------------------------------------------------------
         */

        /**
         * Get general section options.
         *
         * @since 1.0.0
         * @access private
         *
         * @return array
         */
        private function _get_general_section_options() {

            // Get all pages
            $all_pages     = TEO_Helper::get_all_site_pages();
            $all_pages_arr = array();

            foreach ( $all_pages as $page )
                $all_pages_arr[ $page->ID ] = $page->post_title;

            return array(

                array(
                    'title' => __( 'General Options', 'timed-email-offers' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'teo_general_main_title'
                ),

                array(
                    'name'  =>  '',
                    'type'  =>  'teo_upgrade_banner_controls',
                    'desc'  =>  '',
                    'id'    =>  'teo_upgrade_banner',
                ),

                array(
                    'title'             => __( 'Decline Offer Page' , 'timed-email-offers' ),
                    'type'              => 'select',
                    'desc'              => __( 'The page that is where the recipient is sent when he/she clicks the decline offer link' , 'timed-email-offers' ),
                    'default'           => '',
                    'desc_tip'          => true,
                    'id'                => $this->_plugin_constants->OPTION_DECLINE_OFFER_PAGE_ID(),
                    'class'             => 'chosen_select',
                    'css'	            => 'min-width: 350px',
                    'custom_attributes' => array( 'autocomplete' => 'off' ),
                    'options'           => $all_pages_arr
                ),

                array(
                    'title'             => __( 'Unsubscribe Page' , 'timed-email-offers' ),
                    'type'              => 'select',
                    'desc'              => __( 'The page that is where the recipient is sent when he/she clicks the unsubscribe link' , 'timed-email-offers' ),
                    'default'           => '',
                    'desc_tip'          => true,
                    'id'                => $this->_plugin_constants->OPTION_UNSUBSCRIBE_PAGE_ID(),
                    'class'             => 'chosen_select',
                    'css'	            => 'min-width: 350px',
                    'custom_attributes' => array( 'autocomplete' => 'off' ),
                    'options'           => $all_pages_arr
                ),

                array(
                    'title'             => __( 'Invalid Offer Page' , 'timed-email-offers' ),
                    'type'              => 'select',
                    'desc'              => __( 'The page where customers are sent if they click a link leading to an offer that is no longer valid.' , 'timed-email-offers' ),
                    'default'           => '',
                    'desc_tip'          => true,
                    'id'                => $this->_plugin_constants->OPTION_INVALID_OFFER_PAGE_ID(),
                    'class'             => 'chosen_select',
                    'css'	            => 'min-width: 350px',
                    'custom_attributes' => array( 'autocomplete' => 'off' ),
                    'options'           => $all_pages_arr
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'teo_general_sectionend'
                )

            );

        }

        /**
         * Get acceptance section options.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        private function _get_acceptance_section_options() {

            return array(

                array(
                    'title' => __( 'Acceptance Options', 'timed-email-offers' ),
                    'type'  => 'title',
                    'desc'  => __( 'Options influencing the behavior of accepting offers.' , 'timed-email-offers' ),
                    'id'    => 'teo_acceptance_main_title'
                ),

                array(
                    'title' => __( 'Retain Cart Contents On Offer Accept' , 'timed-email-offers' ),
                    'type'  => 'teo_checkbox_custom',
                    'desc'  => __( 'Do not clear cart contents before applying offer accept actions.' , 'timed-email-offers' ),
                    'id'    => $this->_plugin_constants->OPTION_RETAIN_CART_CONTENTS_ON_OFFER_ACCEPT(),
                    'desc_tip'  =>  __( 'Decides whether to clear the contents of the shopping cart prior to applying the Accept Actions of an offer after a customer clicks on the Accept link in an offer email.', 'timed-email-offers' )
                ),

                array(
                    'title' => __( 'Turn Off Notice On Offer Acceptance' , 'timed-email-offers' ),
                    'type'  => 'teo_checkbox_custom',
                    'desc'  => __( "Don't show notice to customer about the offer being accepted." , 'timed-email-offers' ),
                    'id'    => $this->_plugin_constants->OPTION_TURN_OFF_NOTICE_ON_OFFER_ACCEPTANCE(),
                    'desc_tip'  =>  __( 'A customer will see a WooCommerce success notice when an offer after clicking on the Accept link in an offer email and landing on the website. This setting turns this notice off when enabled.', 'timed-email-offers' )
                ),

                array(
                    'title' => __( 'Unschedule pending emails on conversion instead of acceptance' , 'timed-email-offers' ),
                    'type'  => 'teo_checkbox_custom',
                    'desc'  => __( 'Instead of unscheduling any remaining emails to be sent for the offer after the recipient clicks an "Accept" link the system will keep sending scheduled emails until an order is actually made.' , 'timed-email-offers' ),
                    'id'    => $this->_plugin_constants->OPTION_ONLY_UNSCHED_REMAINING_EMAILS_ON_OFFER_CONVERSION(),
                    'desc_tip'  =>  __( 'When a customer clicks on the Accept link in an offer email, all remaining emails that are scheduled to be sent for that offer will be unscheduled. If this setting is enabled, the emails will remain scheduled until the customer actually pays for an order instead.', 'timed-email-offers' )
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'teo_acceptance_sectionend'
                )

            );

        }

        /**
         * Get decline section options.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        private function _get_decline_section_options() {

            return array(

                array(
                    'title' => __( 'Decline Options', 'timed-email-offers' ),
                    'type'  => 'title',
                    'desc'  => __( 'Options relating to declining offers.' , 'timed-email-offers' ),
                    'id'    => 'teo_decline_main_title'
                ),

                array(
                    'title'    => __( 'Offer Timeout Period' , 'timed-email-offers' ),
                    'type'     => 'number',
                    'desc'     => __( 'days after the final email is sent', 'timed-email-offers' ),
                    'desc_tip' => __( 'The number of days after the final email of an offer is sent and still no response from the recipient, consider the offer as declined. Leave empty to disable time out period.' , 'timed-email-offers' ),
                    'id'       => $this->_plugin_constants->OPTION_OFFER_TIMEOUT_PERIOD()
                ),

                array(
                    'title' => __( 'Execute Declined Actions On Timeout' , 'timed-email-offers' ),
                    'type'  => 'checkbox',
                    'desc'  => __( 'Execute decline actions of an offer if it times out' , 'timed-email-offers' ),
                    'id'    => $this->_plugin_constants->OPTION_EXECUTE_DECLINE_ACTIONS_ON_TIMEOUT(),
                    'desc_tip'  =>  __( "If enabled, when an offer reaches the Offer Timeout Period before the customer clicks on an Accept link in one of the offer's emails, the system will execute the offer's Declined Actions.", 'timed-email-offers' ),
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'teo_decline_sectionend'
                )

            );

        }

        /**
         * Get blacklist section options.
         *
         * @since 1.0.0
         * @access private
         *
         * @return array
         */
        private function _get_blacklist_section_options() {

            return array(

                array(
                    'title' => __( 'Blacklist Options', 'timed-email-offers' ),
                    'type'  => 'title',
                    'desc'  => 'List of people that is opted out from Timed Email Offers.',
                    'id'    => 'teo_blacklist_main_title'
                ),

                array(
                    'type' => 'teo_blacklist_table_control'
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'teo_blacklist_sectionend'
                )

            );

        }

        /**
         * Get help section options
         *
         * @since 1.0.0
         * @access private
         *
         * @return array
         */
        private function _get_help_section_options() {

            return array(

                array(
                    'title' => __( 'Help Options' , 'timed-email-offers' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'teo_help_main_title'
                ),

                array(
                    'name'  =>  '',
                    'type'  =>  'teo_help_resources_controls',
                    'desc'  =>  '',
                    'id'    =>  'teo_help_resources',
                ),

                array(
                    'title' => __( 'Clean up plugin options on un-installation' , 'timed-email-offers' ),
                    'type'  => 'checkbox',
                    'desc'  => __( 'If checked, removes all plugin options when this plugin is uninstalled. <b>Warning:</b> This process is irreversible' , 'timed-email-offers' ),
                    'id'    => $this->_plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS()
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'teo_help_sectionend'
                )

            );

        }




        /*
         |--------------------------------------------------------------------------------------------------------------
         | Custom Settings Fields
         |--------------------------------------------------------------------------------------------------------------
         */

        /**
         * Render help resources controls.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $value
         */
        public function render_teo_help_resources_controls( $value ) {
            ?>

            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for=""><?php _e( 'Knowledge Base' , 'timed-email-offers' ); ?></label>
                </th>
                <td class="forminp forminp-<?php echo sanitize_title( $value[ 'type' ] ); ?>">
                    <?php echo sprintf( __( 'Looking for documentation? Please see our growing <a href="%1$s" target="_blank">Knowledge Base</a>' , 'timed-email-offers' ) , "https://marketingsuiteplugin.com/knowledge-base/timed-email-offers/?utm_source=TEO&utm_medium=Settings%20Help&utm_campaign=TEO" ); ?>
                </td>
            </tr>

            <?php
        }

        /**
         * Render blacklist table control.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $value
         */
        public function render_teo_blacklist_table_control( $value ) {
            ?>

            <div id="add-blacklist-manually-controls">

                <h4><?php _e( 'Manually Opt-out Email' , 'timed-email-offers' ); ?></h4>

                <div class="field-set">

                    <label for="blacklist-email"><?php _e( 'Email' , 'timed-email-offers' ); ?></label>
                    <input type="email" id="blacklist-email" autocomplete="off">

                </div>

                <div class="field-set button-field-set">

                    <input type="button" id="manually-blacklist-email" class="button button-primary" value="<?php _e( 'Blacklist Email' , 'timed-email-offers' ); ?>">
                    <span class="spinner"></span>

                </div>

            </div><!-- #add-blacklist-manually-controls -->

            <hr>

            <div id="blacklist-controls">

                <label for="blacklist-type-filter"><?php _e( 'Blacklist Type:' , 'timed-email-offers' ); ?></label>

                <select id="blacklist-type-filter">
                    <?php foreach ( $this->_plugin_constants->BLACKLIST_TYPES() as $key => $text ) { ?>
                        <option value="<?php echo $key; ?>"><?php echo $text; ?></option>
                    <?php } ?>
                </select>

            </div><!-- #blacklist-controls -->

            <table id="blacklist-table" class="wp-list-table widefat fixed striped posts" cellspacing="0" width="100%">

                <thead>
                    <tr>
                        <?php foreach ( $this->_plugin_constants->BLACKLIST_TABLE_HEADINGS() as $key => $text ) { ?>
                            <th class="<?php echo $key; ?>"><?php echo $text; ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <?php foreach ( $this->_plugin_constants->BLACKLIST_TABLE_HEADINGS() as $key => $text ) { ?>
                            <th class="<?php echo $key; ?>"><?php echo $text; ?></th>
                        <?php } ?>
                    </tr>
                </tfoot>

            </table><!-- #blacklist-table -->

            <?php
        }

        /**
         * Render upgrade banner for TEO.
         *
         * @param $value
         *
         * @since 1.0.0
         */
        public function render_teo_upgrade_banner_controls( $value ) {
            ?>

            <tr valign="top">
                <th scope="row" class="titledesc" colspan="2">
                    <a style="outline: none; display: inline-block;" target="_blank" href="https://marketingsuiteplugin.com/product/timed-email-offers/?utm_source=TEO&utm_medium=Premium%20Settings&utm_campaign=TEO"><img style="outline: none; border: 0;" src="<?php echo $this->_plugin_constants->IMAGES_ROOT_URL() . 'teo-premium-upsell-settings.png'; ?>" alt="<?php _e( 'Timed Email Offers Premium' , 'time-email-offers' ); ?>"/></a>
                </th>
            </tr>

            <?php
        }

        /**
         * Render custom checkbox setting
         *
         * @since 1.2.1
         * @access public
         *
         * @param array $value
         */
        public function teo_custom_checkbox( $value ){

            $option_value = WC_Admin_Settings::get_option( $value[ 'id' ] , $value[ 'default' ] ); ?>
                
                <tr valign="top" class="teo-checkbox">
                    <th scope="row" class="titledesc">
                        <b><?php echo $value[ 'title' ]; ?></b>
                        <?php echo wc_help_tip( $value[ 'desc_tip' ], true ); ?>
                    </th>
                    <td class="forminp forminp-checkbox">
                        <label for="<?php echo $value[ 'id' ]; ?>">
                            <input name="<?php echo $value[ 'id' ]; ?>" id="<?php echo $value[ 'id' ]; ?>" type="checkbox" class="" value="1" <?php checked( $option_value , 'yes' ); ?>>
                            <?php echo $value[ 'desc' ]; ?>
                        </label>
                    </td>
                </tr>

            <?php

        }

        /**
         * Save custom checkbox setting
         *
         * @since 1.2.1
         * @access public
         *
         * @param array $value     Default value to return
         * @param array $option    Setting field option
         * @param array $raw_value Raw value from $_POST
         * @return string 'yes' or 'no' string.
         */
        public function teo_custom_checkbox_save( $value , $option , $raw_value ) {

            if ( $option[ 'type' ] !== 'teo_checkbox_custom' )
                return $value;

            return is_null( $raw_value ) ? 'no' : 'yes';
            
        }

    }

}

return new TEO_Settings();
