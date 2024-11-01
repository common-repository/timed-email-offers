<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="timed-email-offer-templates-meta-box" class="teo-meta-box">

    <div class="meta" style="display: none !important;">
        <span class="offer-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'Setup the email templates for this offer:' , 'timed-email-offers' ); ?></h3>

    <p class="desc"><?php _e( "Each email template you define here will be sent to eligible customers on the given number of days after their Order is completed." , 'timed-email-offers' ); ?></p>

    <p class="desc"><?php _e( '<b>Note:</b> The content of actual emails may look slightly different here than it does in your customer\'s email client. Make use of the test function to see what each email will look like as a proper email.' , 'timed-email-offers' ); ?></p>

    <div id="offer-templates-controls">

        <span class="index" style="display: none !important;"></span>

        <div class="field-set">
            <label for="schedule"><?php _e( 'Schedule' , 'timed-email-offers' ); ?><span class="dashicons dashicons-editor-help tooltip right" data-tip="<?php _e( 'The number of days after the order is completed this email should be sent to the customer.' , 'timed-email-offers' ); ?>"></span></label>
            <input type="number" id="schedule" min="1" step="1" autocomplete="off"><span class="field-desc"><?php _e( 'Days after order is completed' , 'timed-email-offers' ); ?></span>
        </div>

        <div class="field-set">
            <input type="checkbox" id="wrap-with-wc-header-footer" autocomplete="off">
            <label for="wrap-with-wc-header-footer"><?php _e( 'Wrap with WC Header/Footer' , 'timed-email-offers' ); ?><span class="dashicons dashicons-editor-help tooltip right" data-tip="<?php _e( 'Wraps the email with the standard WooCommerce template email header and footer' , 'timed-email-offers' ); ?>"></span></label>
        </div>

        <div class="field-set heading-text-field-set">
            <label for="heading-text"><?php _e( 'Heading Text' , 'timed-email-offers' ); ?><span class="dashicons dashicons-editor-help tooltip right" data-tip="<?php _e( 'Add a heading line to the email header to grab your customer\'s attention.' , 'timed-email-offers' ); ?>"></span></label>
            <input type="text" id="heading-text" autocomplete="off">
        </div>

        <div class="field-set">
            <label for="schedule-subject"><?php _e( 'Subject' , 'timed-email-offers' ); ?><span class="dashicons dashicons-editor-help tooltip right" data-tip="<?php _e( 'The subject line of the email.' , 'timed-email-offers' ); ?>"></span></label>
            <input type="text" id="subject" autocomplete="off">
        </div>

        <div class="field-set">
            <label for="schedule-message"><?php _e( 'Email Content' , 'timed-email-offers' ); ?><span class="dashicons dashicons-editor-help tooltip right" data-tip="<?php _e( 'This is the body of the email – your chance to sell your customers on the offer!' , 'timed-email-offers' ); ?>"></span></label>
            
            <a id="show-template-legend"><?php _e( '+ Show Templates Tags' , 'timed-email-offers' ); ?></a>
            
            <div id="template-legend">
                <a id="hide-template-legend"><?php  _e( '- Hide Template Tags' , 'timed-email-offers' ); ?></a>
                <p><strong><?php _e( 'Template Tags Available' , 'timed-email-offers' ); ?></strong></p>
                <p><strong><?php _e( 'Subject/Heading:' , 'timed-email-offers' ); ?></strong><br>{recipient_first_name}, {recipient_last_name}, {order_no}, {order_amount}, {order_date}.</p>
                <p><strong><?php _e( 'Email Content:' , 'timed-email-offers' ); ?></strong><br>{recipient_first_name}, {recipient_last_name}, {order_no}, {order_url}, {order_amount}, {order_date}, {accept_offer_url}, {decline_offer_url}, {unsubscribe_offer_url}.</p>
            </div>

            <?php wp_editor( $default_email_template_content , 'message_body' , $editor_settings ); ?>
        </div>

        <div class="button-controls field-set">
            <span class="spinner"></span>
            <input type="button" id="add-template" class="button button-primary" value="<?php _e( 'Add Template' , 'timed-email-offers' ); ?>">
            <input type="button" id="edit-template" class="button button-primary" value="<?php _e( 'Edit Template' , 'timed-email-offers' ); ?>">
            <?php do_action( 'teo_schedules_additional_button_controls' ); ?>
            <input type="button" id="cancel-add-template" class="button button-secondary" value="<?php _e( 'Cancel' , 'timed-email-offers' ); ?>">
        </div>

    </div><!--#offer-templates-controls-->

    <input type="button" id="show-offer-templates-controls" class="button button-primary" value="<?php _e( 'Add New Template' , 'timed-email-offers' ); ?>">

    <table id="offer-templates" class="wp-list-table widefat fixed striped posts">

        <thead>
            <tr>
                <?php foreach ( $offer_templates_table_headings as $class => $text ) { ?>
                    <th class="<?php echo $class; ?>"><?php echo $text; ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <?php foreach ( $offer_templates_table_headings as $class => $text ) { ?>
                    <th class="<?php echo $class; ?>"><?php echo $text; ?></th>
                <?php } ?>
            </tr>
        </tfoot>

    </table><!--#offer-templates-->

    <div id="send-test-email-popup" class="white-popup mfp-hide">

        <h3><?php _e( 'Send Test Email' , 'timed-email-offers' ); ?></h3>
        <p class="desc"><?php _e( 'Send a test to see what your email template looks like. You can add multiple email addresses separated by commas.' , 'timed-email-offers' ); ?></p>

        <div class="meta" style="display: none !important;">
            <span class="template-index"></span>
            <?php do_action( 'teo_send_test_email_popup_meta' ); ?>
        </div>

        <div class="field-set">
            <label for="test-email-recipient"><?php _e( 'Test Email Recipient' , 'timed-email-offers' ); ?></label>
            <input type="email" id="test-email-recipient" autocomplete="off">
        </div>

        <?php do_action( 'teo_send_test_email_popup_additional_fields' ); ?>

        <div class="button-field-set field-set">
            <p id="sending-email-message" class="desc" style="margin-bottom: 0; text-align: center; font-size: 16px; font-weight:600; font-style: italic; color: green; display: none;"><?php _e( 'Sending Email...' , 'timed-email-offers' ); ?></p>
            <input type="button" id="send-test-email" class="button button-primary" value="<?php _e( 'Send Test Email' , 'timed-email-offers' ); ?>">
            <?php do_action( 'teo_send_test_email_popup_additional_button_controls' ); ?>
        </div>

    </div><!--#send-test-email-popup-->

</div><!--#timed-email-offer-templates-meta-box-->
