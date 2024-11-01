<?php if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

/**
 * Function that houses the code that cleans up the plugin on un-installation.
 *
 * @since 1.0.0
 */
function teo_plugin_cleanup() {

    include_once ( 'models/class-teo-constants.php' );

    $plugin_constants = TEO_Constants::instance();

    if ( get_option( $plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() ) == 'yes' ) {
        
        // Settings
        
        // Pages
        
        // We don't delete the pages that is associated with decline and unsubscribe page id
        delete_option( $plugin_constants->OPTION_DECLINE_OFFER_PAGE_ID() );
        delete_option( $plugin_constants->OPTION_UNSUBSCRIBE_PAGE_ID() );

        delete_option( $plugin_constants->OPTION_ACCEPT_OFFER_PAGE_ID() );
        
        // Acceptance
        delete_option( $plugin_constants->OPTION_RETAIN_CART_CONTENTS_ON_OFFER_ACCEPT() );
        
        // Decline
        delete_option( $plugin_constants->OPTION_OFFER_TIMEOUT_PERIOD() );
        delete_option( $plugin_constants->OPTION_EXECUTE_DECLINE_ACTIONS_ON_TIMEOUT() );
        
        // Blacklist
        delete_option( $plugin_constants->OPTION_BLACKLIST() );
        
        // Help

        // Delete the option that specifies the option whether to delete the options on plugin uninstall
        delete_option( $plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() );


        // Cron Jobs
        $scheduled_cron_jobs = _get_cron_array();
        
        foreach ( $scheduled_cron_jobs as $timestamp => $cron_jobs ) {
            foreach ( $cron_jobs as $hook => $dings ) {
                foreach ( $dings as $sig => $data ) {

                    // check if this is a hook we need to concern about
                    if ( $hook == $plugin_constants->CRON_HOOK_SEND_EMAIL_OFFER() )
                        wp_unschedule_event( $timestamp , $hook , $data[ 'args' ] );
                    elseif ( $hook == $plugin_constants->CRON_HOOK_DECLINE_OFFER_ON_TIMEOUT() )
                        wp_unschedule_event( $timestamp , $hook , $data[ 'args' ] );

                }
            }
        }

        delete_option( $plugin_constants->CRON_JOBS_UPDATED() );
        

        // Custom Plugin Tables
        global $wpdb;

        $wpdb->query( "DROP TABLE IF EXISTS " . $plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS() );
        $wpdb->query( "DROP TABLE IF EXISTS " . $plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS() );
        $wpdb->query( "DROP TABLE IF EXISTS " . $plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS() );

        delete_option( $plugin_constants->CUSTOM_TABLE_OFFER_RECIPIENTS_VERSION() );
        delete_option( $plugin_constants->CUSTOM_TABLE_OFFER_SCHEDULED_EMAILS_VERSION() );
        delete_option( $plugin_constants->CUSTOM_TABLE_OFFER_EMAIL_VIEWS_LOGS_VERSION() );

        flush_rewrite_rules();

    }

}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {

    global $wpdb;

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

    foreach ( $blog_ids as $blog_id ) {

        switch_to_blog( $blog_id );
        teo_plugin_cleanup();

    }

    restore_current_blog();

    return;

} else
    teo_plugin_cleanup();
