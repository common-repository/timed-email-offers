<?php

final class TEO_Offer_Entry_Guided_Tour {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    private static $_instance = null;

    const OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS = 'teo_offer_entry_guided_tour_status';
    const STATUS_OPEN                           = 'open';
    const STATUS_CLOSE                          = 'close';

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

        $this->urls = apply_filters( 'teo_offer_entry_guided_tour_pages' , array() );

        $tours_array = array(
            array(
                'id'    => 'offer_entry_guide_intro',
                'elem'  => '#toplevel_page_woocommerce ul li a.current',
                'html'  => __( '<h3>Congratulations, you just added your first offer!</h3>
                                <p>Would you like to learn how to configure it? It takes less than a minute and you\'ll then know exactly how to setup your first offer!</p>' , 'timed-email-offers' ),
                'prev'  => null,
                'next'  => '@offer_entry_guide_title',
                'edge'  => 'left',
                'align' => 'left'
            ),
            array(
                'id'    => 'offer_entry_guide_title',
                'elem'  => '#titlediv',
                'html'  => __( '<h3>First, give your Offer a name.</h3>
                                <p>This is used internally for you to identify the Offer in the system so make it something that describes what the Offer is all about.</p>' , 'timed-email-offers' ),
                'prev'  => '@offer_entry_guide_intro',
                'next'  => '@offer_entry_guide_schedules',
                'edge'  => 'top',
                'align' => 'center'
            ),
            array(
                'id'    => 'offer_entry_guide_schedules',
                'elem'  => '#offer-templates',
                'html'  => __( '<h3>Email Templates</h3>
                                <p>When a Customer becomes a Recipient of an offer, they are sent a schedule of emails to tell them about the offer. The Email Templates area lets you setup exactly what those emails look like and when they should be sent.</p>
                                <p>Click Add New Template now to add your first email template.</p>
                                <p>You can add as many or as few email templates to the list as you like. The day that you schedule each email is the number of days after the Order has been marked Completed in WooCommerce. The Recipients will be sent the emails on the schedule that you have defined.</p>' , 'timed-email-offers' ),
                'prev'  => '@offer_entry_guide_title',
                'next'  => '@offer_entry_guide_conditions',
                'edge'  => 'left',
                'align' => 'center'
            ),
            array(
                'id'    => 'offer_entry_guide_conditions',
                'elem'  => '#timed-email-offer-conditions',
                'html'  => __( '<h3>Sales Offer Conditions</h3>
                                <p>Here\'s the exciting part! You get to decide under what conditions a Customer will become a Recipient of your Offer. Conditions are based on what the customer just ordered.</p>
                                <p>There are no conditions set up yet, so to get started click on Add Condition Group and then click on Add Condition to add your first condition</p>
                                <p>Conditions can be grouped together for using the one Offer for multiple scenarios. It gets really powerful in the Premium add-on where you can add loads of different condition types.</p>' , 'timed-email-offers' ),
                'prev'  => '@offer_entry_guide_schedules',
                'next'  => '@offer_entry_guide_accept_actions',
                'edge'  => 'left',
                'align' => 'center'
            ),
            array(
                'id'    => 'offer_entry_guide_accept_actions',
                'elem'  => '#accept-timed-email-offer-actions',
                'html'  => __( '<h3>Offer Accept Actions</h3>
                                <p>If the customer accepts your Offer by clicking the Accept link in your email, you can have the Offer automatically apply Products and Coupons to the cart for them.</p>
                                <p>Likewise you can remove certain Products and Coupons should they not be included in the offer.</p>
                                <p>This will make the whole Offer process seem completely smooth and painless from the customer\'s perspective. They don\'t have to lift a finger!</p>' , 'timed-email-offers' ),
                'prev'  => '@offer_entry_guide_conditions',
                'next'  => '@offer_entry_guide_decline_actions',
                'edge'  => 'left',
                'align' => 'center'
            ),
            array(
                'id'    => 'offer_entry_guide_decline_actions',
                'elem'  => '#decline-timed-email-offer-actions',
                'html'  => __( '<h3>Offer Decline Actions</h3>
                                <p>If the customer declines your Offer, you can also have the plugin perform certain actions.</p>
                                <p>In the Premium version, you can redirect them to a specific page or product or even add them to another Offer.</p>' , 'timed-email-offers' ),
                'prev'  => '@offer_entry_guide_accept_actions',
                'next'  => '@offer_entry_guide_recipients',
                'edge'  => 'left',
                'align' => 'center'
            )
        );

        if ( in_array( 'timed-email-offers-premium/timed-email-offers-premium.php' , apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

            $tours_array[] = array(
                'id'    => 'offer_entry_guide_recipients',
                'elem'  => '#offer-recipients-table',
                'html'  => __( '<h3>Recipients</h3>
                                <p>This is the list of all the Recipients associated with this Offer. A Customer becomes a Recipient as long as they aren\'t currently receiving another Offer already and they also pass all of the Offer Conditions above.</p>
                                <p>You can filter the Recipients by their status for some quick reporting.</p>' , 'timed-email-offers' ),
                'prev'  => '@offer_entry_guide_decline_actions',
                'next'  => null,
                'edge'  => 'left',
                'align' => 'center'
            );

        } else {

            $tours_array[] = array(
                'id'    => 'offer_entry_guide_recipients',
                'elem'  => '#offer-recipients-table',
                'html'  => __( '<h3>Recipients</h3>
                                <p>This is the list of all the Recipients associated with this Offer. A Customer becomes a Recipient as long as they aren\'t currently receiving another Offer already and they also pass all of the Offer Conditions above.</p>
                                <p>You can filter the Recipients by their status for some quick reporting.</p>' , 'timed-email-offers' ),
                'prev'  => '@offer_entry_guide_decline_actions',
                'next'  => '@offer_entry_guide_plugin_upgrade',
                'edge'  => 'left',
                'align' => 'center'
            );

            $tours_array[] = array(
                'id'    => 'offer_entry_guide_plugin_upgrade',
                'elem'  => '#timed-email-offer-upgrade',
                'html'  => sprintf( __( '<h3>Premium Upsell</h3>
                                         <p>This concludes the guide. You are now ready to setup your first offer!</p>
                                         <p>Want to unlock all of the extra features you see here? The Premium add-on will unlock all this and more. We\'re adding new features all the time!</p>
                                         <p><a href="%1$s" target="_blank">Check out the Premium version now &rarr;</a></p>' , 'timed-email-offers' ) , 'https://marketingsuiteplugin.com/knowledge-base/timed-email-offers/?utm_source=TEO&utm_medium=Settings%20Help&utm_campaign=TEO' ),
                'prev'  => '@offer_entry_guide_recipients',
                'next'  => null,
                'edge'  => 'right',
                'align' => 'center'
            );

        }
        
        $this->screens = apply_filters( 'teo_offer_entry_guided_tours' , array( 'timed_email_offer' => $tours_array ) );

    }

    /**
     * Get the only instance of the class.
     *
     * @since 1.2.0
     * @access public
     *
     * @return TEO_Offer_Entry_Guided_Tour
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

        if ( get_option( self::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS ) === false )
            update_option( self::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS , self::STATUS_OPEN );

    }

    /**
     * Terminate guided tour options.
     *
     * @since 1.2.0
     * @access public
     */
    public function terminate_guided_tour_options() {

        delete_option( self::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS );

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
     * Close offer entry guided tour.
     *
     * @since 1.2.0
     * @access public
     */
    public function teo_close_offer_entry_guided_tour() {

        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {

            if ( !check_ajax_referer( 'teo-close-offer-entry-guided-tour' , 'nonce' , false ) )
                wp_die( __( 'Security Check Failed' , 'timed-email-offers' ) );

            update_option( self::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS , self::STATUS_CLOSE );

            wp_send_json_success();

        } else
            wp_die( __( 'Invalid AJAX Call' , 'timed-email-offers' ) );

    }

} // end class
