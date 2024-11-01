<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'TEO_Offer_CPT' ) ) {

    /**
     * Class TEO_Offer_CPT
     *
     * Model that houses the logic relating to Offer CPT.
     *
     * @since 1.0.0
     */
    final class TEO_Offer_CPT {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of TEO_Offer_CPT.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Offer_CPT
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
         * Property that houses the logic of the various helper functions related to the shop's products.
         *
         * @since 1.0.0
         * @access public
         * @var TEO_Product
         */
        private $_product;

        /**
         * Property that houses the logic of the various helper functions related to the shop's coupons.
         *
         * @since 1.0.0
         * @access private
         * @var TEO_Coupon
         */
        private $_coupon;




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
         * TEO_Offer_CPT constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_CPT model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'TEO_Constants' ];
            $this->_product          = $dependencies[ 'TEO_Product' ];
            $this->_coupon           = $dependencies[ 'TEO_Coupon' ];

        }

        /**
         * Ensure that only one instance of TEO_Offer_CPT is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of TEO_Offer_CPT model.
         * @return TEO_Offer_CPT
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Register 'timed_email_offer' custom post type.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_timed_email_offer_cpt() {

            $labels = array(
                'name'                => __( 'Timed Email Offers' , 'timed-email-offers' ),
                'singular_name'       => __( 'Timed Email Offer' , 'timed-email-offers' ),
                'menu_name'           => __( 'Timed Email Offer' , 'timed-email-offers' ),
                'parent_item_colon'   => __( 'Parent Timed Email Offer' , 'timed-email-offers' ),
                'all_items'           => __( 'Timed Email Offers' , 'timed-email-offers' ),
                'view_item'           => __( 'View Timed Email Offer' , 'timed-email-offers' ),
                'add_new_item'        => __( 'Add Timed Email Offer' , 'timed-email-offers' ),
                'add_new'             => __( 'New Timed Email Offer' , 'timed-email-offers' ),
                'edit_item'           => __( 'Edit Timed Email Offer' , 'timed-email-offers' ),
                'update_item'         => __( 'Update Timed Email Offer' , 'timed-email-offers' ),
                'search_items'        => __( 'Search Timed Email Offers' , 'timed-email-offers' ),
                'not_found'           => __( 'No Timed Email Offer found' , 'timed-email-offers' ),
                'not_found_in_trash'  => __( 'No Timed Email Offers found in Trash' , 'timed-email-offers' ),
            );

            $args = array(
                'label'               => __( 'Timed Email Offers' , 'timed-email-offers' ),
                'description'         => __( 'Timed Email Offer Information Pages' , 'timed-email-offers' ),
                'labels'              => $labels,
                'supports'            => array( 'title' ),
                'taxonomies'          => array(),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                //'show_in_menu'        => true,
                //'show_in_menu'        => 'edit.php?post_type=shop_order',
                'show_in_menu'        => 'woocommerce',
                'show_in_json'        => false,
                'query_var'           => true,
                'rewrite'             => array(),
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => true,
                'menu_position'       => 26,
                'menu_icon'           => 'dashicons-forms',
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capability_type'     => 'post'
            );

            $args = apply_filters( 'teo_offer_cpt_args' , $args );

            register_post_type( $this->_plugin_constants->OFFER_CPT_NAME() , $args );

        }

        /**
         * Register 'timed_email_offer' cpt meta boxes.
         *
         * @since 1.0.0
         * @since 1.1.0 Refactor code base for adding cpt meta boxes.
         * @access public
         */
        public function register_timed_email_offer_cpt_custom_meta_boxes() {

            foreach ( $this->_plugin_constants->OFFER_CPT_META_BOXES() as $id => $data ) {

                $callback = is_array( $data[ 'callback' ] ) ? $data[ 'callback' ] : array( $this , $data[ 'callback' ] );

                add_meta_box(
                    $id,
                    $data[ 'title' ],
                    $callback,
                    $data[ 'cpt' ],
                    $data[ 'context' ],
                    $data[ 'priority' ]
                );

            }

        }

        /**
         * Save 'timed_email_offer' cpt entry.
         *
         * @since 1.2.0
         * @access public
         *
         * @param $post_id
         */
        public function save_post( $post_id ) {

            // On manual click of 'update' , 'publish' or 'save draft' button, execute code inside the if statement
            if ( $this->_valid_save_post_action( $post_id ) ) {

                // Check offer options
                if ( isset( $_POST[ 'teo_nonce_save_offer_options' ] ) && wp_verify_nonce( $_POST[ 'teo_nonce_save_offer_options' ] , 'teo_action_save_offer_options' ) ) {

                    if ( isset( $_POST[ 'offer-order' ] ) && ( filter_var( $_POST[ 'offer-order' ] , FILTER_VALIDATE_INT ) || $_POST[ 'offer-order' ] == '' ) ) {

                        $offer_order = $_POST[ 'offer-order' ] != '' ? ( int ) $_POST[ 'offer-order' ] : '';

                        update_post_meta( $post_id , $this->_plugin_constants->POST_META_OFFER_ORDER() , $offer_order );

                    } else
                        update_post_meta( $post_id , $this->_plugin_constants->POST_META_OFFER_ORDER() , '' );

                }

            }

        }




        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        /**
         * Timed email offer templates meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_timed_email_offer_templates_meta_box() {

            global $post;

            $default_email_template_content = $this->_plugin_constants->DEFAULT_EMAIL_TEMPLATE_CONTENT();
            $offer_templates_table_headings = $this->_plugin_constants->OFFER_TEMPLATES_TABLE_TOTAL_HEADINGS();

            $editor_settings = array(
                                    'textarea_rows' => 20,
                                    'wpautop'       => true,
                                    'tinymce'       => array( 'height' => 200 )
                                );

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-timed-email-offer-templates-meta-box.php' );

        }

        /**
         * Timed email offer conditions meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_timed_email_offer_conditions_meta_box() {

            global $post;

            // Get timed email offer conditions
            $timed_email_offer_conditions = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_OFFER_CONDITIONS() , true );
            if ( !is_array( $timed_email_offer_conditions ) )
                $timed_email_offer_conditions = array();

            // Get timed email offer condition types and if it is in simple mode
            $offer_condition_types             = $this->_plugin_constants->TIMED_EMAIL_OFFER_CONDITION_TYPES();
            $offer_condition_types_simple_mode = $this->_plugin_constants->TIMED_EMAIL_OFFER_CONDITION_TYPES_SIMPLE_MODE();

            // Get product options
            $all_products_select_options = $this->_product->get_products( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'product_url' => true ) );

            // Get logic conditions ( = , > , < , != )
            $product_conditions = $this->_plugin_constants->LOGIC_CONDITIONS();

            // Get table total column
            $product_quantity_in_cart_table_total_columns = $this->_plugin_constants->PRODUCT_QUANTITY_IN_ORDER_TABLE_TOTAL_COLUMNS();

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-timed-email-offer-conditions-meta-box.php' );

        }

        /**
         * Accept timed email offer actions meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_accept_timed_email_offer_actions_meta_box() {

            global $post;

            // Get product options
            $all_products_select_options = $this->_product->get_products( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'product_url' => true ) , false );

            // Get coupon options
            $all_coupons_select_options = $this->_coupon->get_coupons( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'coupon_url' => true , 'coupon_type' => true , 'coupon_amount' => true ) , false );

            // Get timed email offer action types
            $accept_offer_action_types = $this->_plugin_constants->ACCEPT_TIMED_EMAIL_OFFER_ACTION_TYPES();

            // Get timed email offer accept actions
            $accept_offer_actions = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_OFFER_ACCEPT_ACTIONS() , true );
            if ( !is_array( $accept_offer_actions ) )
                $accept_offer_actions = array();

            // Get table total columns
            $add_products_to_cart_table_total_columns  = $this->_plugin_constants->ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS();
            $apply_coupons_to_cart_table_total_columns = $this->_plugin_constants->APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS();

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-accept-timed-email-offer-actions-meta-box.php' );

        }

        /**
         * Decline timed email offer actions meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_decline_timed_email_offer_actions_meta_box() {

            global $post;

            $decline_offer_action_types = $this->_plugin_constants->DECLINE_OFFER_ACTION_TYPES();
            $decline_offer_action_types_simple_mode = $this->_plugin_constants->DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE();

            $decline_offer_action = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_OFFER_DECLINE_ACTIONS() , true );
            if ( !is_array( $decline_offer_action ) )
                $decline_offer_action = array();

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-decline-timed-email-offer-actions-meta-box.php' );

        }

        /**
         * Timed email offer recipients meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_timed_email_offer_recipients_meta_box() {

            global $post;

            $recipient_offer_response_status = $this->_plugin_constants->RECIPIENT_OFFER_RESPONSE_STATUS();
            $offer_recipients_table_headings = $this->_plugin_constants->OFFER_RECIPIENTS_TABLE_HEADINGS();

            $offer_recipients = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_OFFER_RECIPIENTS() , true );
            if ( !$offer_recipients )
                $offer_recipients = array();

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-timed-email-offer-recipients-meta-box.php' );

        }

        /**
         * Timed email offer options meta box.
         *
         * @since 1.2.0
         * @access public
         */
        public function view_timed_email_offer_options_meta_box() {

            global $post;

            $offer_order = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_OFFER_ORDER() , true );

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-timed-email-offer-options-meta-box.php' );

        }

        /**
         * Timed email offer upgrade meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_timed_email_offer_upgrade_meta_box() {

            $banner_img_url = $this->_plugin_constants->IMAGES_ROOT_URL() . 'teo-premium-upsell-edit-screen.png';

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-timed-email-offer-upgrade-meta-box.php' );

        }

        /**
         * Timed email offer options quick edit field
         *
         * @since 1.2.1
         * @access public
         *
         * @param $column_name string
         * @param $post_type string
         */
        public function view_timed_email_offer_options_quick_edit( $column_name, $post_type ) {

            if ( $this->_plugin_constants->OFFER_CPT_NAME() != $post_type )
        		return;

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-timed-email-offer-options-quick-edit.php' );
            
        }




        /*
        |--------------------------------------------------------------------------
        | CPT entry custom columns
        |--------------------------------------------------------------------------
        */

        /**
         * Add 'offer order' cpt listing custom field.
         *
         * @since 1.2.0
         * @access public
         *
         * @param $columns array CPT listing columns array.
         * @return array Modified CPT listing columns array.
         */
        public function add_offer_listing_custom_columns( $columns ) {

            $all_keys    = array_keys( $columns );
            $title_index = array_search( 'title' , $all_keys );

            $new_columns_array = array_slice( $columns , 0 , $title_index + 1 , true ) +
                apply_filters( 'teo_offer_listing_custom_columns' , array( 'offer_order' => __( 'Offer Order' , 'timed-email-offers' ) ) , $columns ) +
                array_slice( $columns , $title_index + 1 , NULL , true );

            return $new_columns_array;

        }

        /**
         * Add value to 'offer order' cpt listing custom field.
         *
         * @since 1.2.0
         * @access public
         *
         * @param $columns array CPT listing columns array.
         * @param $post_id int/string Post Id.
         */
        public function add_offer_listing_custom_columns_data( $column , $post_id ) {

            switch ( $column ) {

                case 'offer_order':

                    $offer_order = get_post_meta( $post_id , $this->_plugin_constants->POST_META_OFFER_ORDER() , true ); ?>

                    <div class="offer_page"><?php echo $offer_order; ?></div>

                    <?php

                    break;

                default :
                    break;

            }

            do_action( 'teo_offer_listing_custom_columns_data' , $column , $post_id );

        }

        /**
         * Mark custom columns that are  as sortable accordingly.
         *
         * @since 1.2.0
         * @access public
         *
         * @param array $sortable_columns Array of sortable custom columns.
         * @return array Modified array of sortable custom columns.
         */
        public function mark_offer_custom_columns_as_sortable( $sortable_columns ) {

            $sortable_columns[ 'offer_order' ] = $this->_plugin_constants->POST_META_OFFER_ORDER();
            $sortable_columns = apply_filters( 'teo_mark_offer_custom_columns_as_sortable' , $sortable_columns );

            return $sortable_columns;

        }

        /**
         * Sort custom columns.
         * The reason why we are using 'posts_clauses' instead of 'pre_get_posts' is that post meta are treated as strings.
         * Therefore sorting post meta that is expected to be int via 'pre_get_posts' won't work coz it will treat it as string so sorting is wrong.
         * Ex. the sorting of 1,3,10 and 8 will be 1, 10, 3 , 8.
         * Therefore we need to alter the sql itself.
         *
         * @since 1.2.0
         * @access public
         *
         * @param array    $pieces Array of pieces of the query.
         * @param WP_Query $query  WP_Query object.
         * @return array Modified array of pieces of the query.
         */
        public function sort_offer_custom_columns( $pieces , $query ) {

            global $wpdb;

            // We only want our code to run in the main WP query and if an orderby query variable is designated.
            if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {

                // Get the order query variable - ASC or DESC
                $order = strtoupper( $query->get( 'order' ) );

                // Make sure the order setting qualifies. If not, set default as ASC
                if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) )
                    $order = 'ASC';

                switch( $orderby ) {

                    case $this->_plugin_constants->POST_META_OFFER_ORDER():

                        // We have to join the postmeta table to include our offer order in the query
                        $pieces[ 'join' ] .= " LEFT JOIN $wpdb->postmeta wp_meta_offer_order ON wp_meta_offer_order.post_id = {$wpdb->posts}.ID AND wp_meta_offer_order.meta_key = '" . $this->_plugin_constants->POST_META_OFFER_ORDER() . "'";

                        // Then tell the query to order by our offer order (ABS converts value to int)
                        $pieces[ 'orderby' ] = "ABS( wp_meta_offer_order.meta_value ) $order, " . $pieces[ 'orderby' ];

                        break;

                }

            }

            $pieces = apply_filters( 'teo_sort_offer_custom_columns' , $pieces , $query );

            return $pieces;

        }




        /*
        |--------------------------------------------------------------------------
        | Utilities
        |--------------------------------------------------------------------------
        */

        /**
         * Check validity of a save post action.
         *
         * @since 1.0.0
         * @access private
         *
         * @param $post_id
         * @return bool
         */
        private function _valid_save_post_action( $post_id ) {

            if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || !current_user_can( 'edit_page' , $post_id ) || get_post_type( $post_id ) != $this->_plugin_constants->OFFER_CPT_NAME() || empty( $_POST ) )
                return false;
            else
                return true;

        }

    }

}
