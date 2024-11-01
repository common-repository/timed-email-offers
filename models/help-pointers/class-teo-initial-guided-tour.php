<?php

final class TEO_Initial_Guided_Tour {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    private static $_instance = null;

    const OPTION_INITIAL_GUIDED_TOUR_STATUS = 'teo_initial_guided_tour_status';
    const STATUS_OPEN                       = 'open';
    const STATUS_CLOSE                      = 'close';

    private $urls;
    private $screens;




    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Cloning is forbidden.
     *
     * @since 1.2.0
     * @access public
     */
    public function __clone () {

        _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ) , '1.2.0' );

    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.2.0
     * @access public
     */
    public function __wakeup () {

        _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'timed-email-offers' ) , '1.2.0' );

    }

    /**
     * TEO_Initial_Guided_Tour constructor.
     *
     * @since 1.2.0
     * @access public
     */
    private function __construct() {

        $this->urls = apply_filters( 'teo_initial_guided_tour_pages' , array(
            'plugin-listing'  => admin_url( 'plugins.php' ),
            'product-listing' => admin_url( 'edit.php?post_type=product' ),
            'teo-settings'  => admin_url( 'admin.php?page=wc-settings&tab=teo_settings' ),
            'teo-listing'   => admin_url( 'edit.php?post_type=timed_email_offer' )
        ) );

        $this->screens = apply_filters( 'teo_initial_guided_tours' , array(
            'plugins' => array(
                'elem'  => '#toplevel_page_woocommerce',
                'html'  => __( '<h3>Welcome to Timed Email Offers!</h3>
                                <p>Would you like to go on a guided tour of the plugin? Takes less than 30 seconds.</p>' , 'timed-email-offers' ),
                'prev'  => null,
                'next'  => $this->urls[ 'product-listing' ],
                'edge'  => 'left',
                'align' => 'left'
            ),
            'edit-product' => array(
                'elem'  => '#menu-posts-product .wp-has-current-submenu',
                'html'  => __( '<h3>Timed Email Offers is made for emailing offers to customers after they have purchased in order to attract them back to your store. You can qualify customers to be a recipient of an offer based on what they ordered last time.</h3>
                                <p>Eg. If they ordered a pair of running shoes last time, you might send them a Timed Email Offer promoting a discount on a runner’s pack containing breathable socks, a special running strap and a heart rate monitor.</p>
                                <p>It’s great to use for both up-sells (offers that get your customers to buy a higher priced item) or cross-sells (offers that promote related items). Both are great strategies for encouraging customers to come back and spend more.</p>' ),
                'prev'  => $this->urls[ 'plugin-listing' ],
                'next'  => $this->urls[ 'teo-settings' ],
                'edge'  => 'left',
                'align' => 'left'
            ),
            'woocommerce_page_wc-settings' => array(
                'elem'  => '.nav-tab-active',
                'html'  => __( '<h3>This is the settings area where you can configure important plugin options that affect the way your offers are run.</h3>
                                <p>You can come back here anytime after the tour to configure these settings.</p>' , 'timed-email-offers' ),
                'prev'  => $this->urls[ 'product-listing' ],
                'next'  => $this->urls[ 'teo-listing' ],
                'edge'  => 'top',
                'align' => 'left'
            ),
            'edit-timed_email_offer' => array(
                'elem'  => '#toplevel_page_woocommerce ul li a.current',
                'html'  => sprintf( __( '<h3>This is the Offers list which shows you what Offers you currently have running in your store.</h3>
                                         <p>An offer consists of a list of emails to send on a schedule, a set of conditions to judge if a customer should receive the offer and a few other configurable options.</p>
                                         <p>Offers in Draft mode are not sent to customers. Use the draft mode to edit your Offer like you would a product that isn\'t quite ready put on your store.</p>
                                         <p>This concludes the tour. Click on the button below to add your first offer:</p>
                                         <p><a id="teo-add-first-offer" href="%1$s" class="button button-primary">Add My First Offer</a></p>' , 'timed-email-offers' ) , admin_url( 'post-new.php?post_type=timed_email_offer' ) ),
                'prev'  => $this->urls[ 'teo-settings' ],
                'next'  => null,
                'edge'  => 'left',
                'align' => 'left'
            )
        ) );

    }

    /**
     * Get the only instance of the class.
     *
     * @since 1.2.0
     * @access public
     *
     * @return TEO_Initial_Guided_Tour
     */
    public static function instance() {

        if ( !self::$_instance )
            self::$_instance = new self();

        return self::$_instance;

    }

    /**
     * Get current screen.
     * 
     * @since 1.2.0
     * @access public
     */
    public function get_current_screen() {

        $screen = get_current_screen();

        if ( !empty( $this->screens[ $screen->id ] ) )
            return $this->screens[ $screen->id ];

        return false;

    }

    /**
     * Initialize guided tour options.
     *
     * @since 1.2.0
     * @access public
     */
    public function initialize_guided_tour_options() {

        if ( get_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS ) === false )
            update_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS , self::STATUS_OPEN );
        
    }

    /**
     * Terminate guided tour options.
     *
     * @since 1.2.0
     * @access public
     */
    public function terminate_guided_tour_options() {
        
        delete_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS );

    }

    /**
     * Get screens with registered guide.
     * 
     * @since 1.2.0
     * @access public
     */
    public function get_screens() {

        return $this->screens;

    }

    /**
     * Close initial guided tour.
     * 
     * @since 1.2.0
     * @access public
     */
    public function teo_close_initial_guided_tour() {

        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {

            if ( !check_ajax_referer( 'teo-close-initial-guided-tour' , 'nonce' , false ) )
                wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

            update_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS , self::STATUS_CLOSE );

            wp_send_json_success();

        } else
            wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );
        
    }

} // end class