<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="recipient-data-<?php echo $offer_id; ?>-<?php echo $order_id; ?>" class="recipient-data-popup white-popup" data-offer-id="<?php echo $offer_id; ?>" data-meta-index="<?php echo $order_id; ?>">

    <h2><?php _e( 'Offer Recipient Details' , 'timed-email-offers' ); ?></h2>

    <section class="customer-data">

        <h3><?php _e( 'Customer' , 'timed-email-offers' ); ?></h3>

        <?php do_action( 'teo_recipient_popup_before_customer_data' , $customer , $offer_recipient , $offer_id , $order_id );

        if ( $customer->ID ) { ?>

            <?php do_action( 'teo_recipient_popup_before_registered_customer_data' , $customer , $offer_recipient , $offer_id , $order_id ); ?>

            <p class="customer-full-name"><label><?php _e( 'Full Name:' , 'timed-email-offers' ); ?></label><span><a href="<?php echo home_url( '/wp-admin/user-edit.php?user_id=' . $customer->ID ); ?>" target="_blank"><?php echo $customer->first_name . ' ' . $customer->last_name; ?></a></span></p>
            <p class="customer-email"><label><?php _e( 'Email:' , 'timed-email-offers' ); ?></label><span><?php echo $customer->user_email; ?></span></p>

            <?php do_action( 'teo_recipient_popup_after_registered_customer_data' , $customer , $offer_recipient , $offer_id , $order_id ); ?>

        <?php } else { ?>

            <?php do_action( 'teo_recipient_popup_before_guest_customer_data' , $customer , $offer_recipient , $offer_id , $order_id ); ?>

            <p class="customer-status"><label><?php _e( 'Status:' , 'timed-email-offers' ); ?></label><span class="customer-guest"><?php _e( 'Guest Customer' , 'timed-email-offers' ); ?></span></p>
            <p class="customer-full-name"><label><?php _e( 'Full Name:' , 'timed-email-offers' ); ?></label><span><?php echo $customer->first_name . ' ' . $customer->last_name; ?></span></p>
            <p class="customer-email"><label><?php _e( 'Email:' , 'timed-email-offers' ); ?></label><span><?php echo $customer->user_email; ?></span></p>

            <?php do_action( 'teo_recipient_popup_after_guest_customer_data' , $customer , $offer_recipient , $offer_id , $order_id ); ?>

        <?php } ?>

        <?php do_action( 'teo_recipient_popup_after_customer_data' , $customer , $offer_recipient , $offer_id , $order_id ); ?>

    </section>

    <section class="order-data">

        <h3><?php _e( 'Order' , 'timed-email-offers' ); ?></h3>

        <?php do_action( 'teo_recipient_popup_before_order_data' , $offer_recipient , $offer_id , $order_id ); ?>

        <p class="order-id"><label><?php _e( 'Order ID:' , 'timed-email-offers' ); ?></label><span><a href="<?php echo home_url( '/wp-admin/post.php?post=' . $order_id . '&action=edit' ); ?>" target="_blank"><?php echo $order_id; ?></a></span></p>

        <?php do_action( 'teo_recipient_popup_after_order_data' , $offer_recipient , $offer_id , $order_id ); ?>

    </section>

    <section class="scheduled-emails-data">

        <h3><?php _e( 'Scheduled Emails' , 'timed-email-offer' ); ?></h3>

        <?php do_action( 'teo_recipient_popup_before_schedules_data' , $offer_scheduled_emails , $offer_recipient , $offer_id , $order_id ); ?>

        <table class="recipient-schedules wp-list-table widefat fixed striped" cellspacing="0" width="100%">

            <thead>
                <tr>
                    <?php foreach ( $constants->OFFER_RECIPIENT_POPUP_SCHEDULED_EMAILS_TABLE_HEADINGS() as $class => $text ) { ?>
                        <th class="<?php echo $class; ?>"><?php echo $text; ?></th>
                    <?php } ?>
                </tr>
            </thead>

            <tbody>
                <?php if ( !empty( $offer_scheduled_emails ) ) {

                    foreach ( $offer_scheduled_emails as $sched_email ) {
                        
                        // This is intentional, we need to save on db without am/pm, and we need to save the actual date in mysql format, not the timestamp
                        // On ui on front end we need to show am/pm
                        $cron_datetime = date( 'Y-m-d H:i:s A' , strtotime( $sched_email->cron_datetime ) ); ?>

                        <tr>
                            <?php do_action( 'teo_recipient_popup_before_schedule_item' , $sched_email , $offer_recipient , $offer_id , $order_id ); ?>
                            <td class="tid"><?php echo 'T' . $sched_email->template_id; ?></td>
                            <td class="schedule-date"><?php echo $cron_datetime; ?></td>
                            <td class="send-status"><?php echo $offer_email_send_status[ $sched_email->send_status ]; ?></td>
                            <td class="response-status"><?php echo $offer_email_response_status[ $sched_email->response_status ]; ?></td>
                            <td class="column-controls"><?php echo $constants->OFFER_RECIPIENT_POPUP_SCHEDULED_EMAILS_TABLE_COLUMN_ACTIONS( $offer_id , $order_id , $sched_email->email_token , $sched_email->send_status ); ?></td>
                            <?php do_action( 'teo_recipient_popup_after_schedule_item' , $sched_email , $offer_recipient , $offer_id , $order_id ); ?>
                        </tr>

                    <?php }

                } else { ?>

                    <tr class="no-scheduled-emails">
                        <td colspan="<?php echo $constants->OFFER_RECIPIENT_POPUP_SCHEDULES_TABLE_TOTAL_COLUMNS(); ?>"><?php _e( 'No Scheduled Emails' , 'timed-email-offers' ); ?></td>
                    </tr>

                <?php } ?>
            </tbody>

            <tfoot>
                <tr>
                    <?php foreach ( $constants->OFFER_RECIPIENT_POPUP_SCHEDULED_EMAILS_TABLE_HEADINGS() as $class => $text ) { ?>
                        <th class="<?php echo $class; ?>"><?php echo $text; ?></th>
                    <?php } ?>
                </tr>
            </tfoot>

        </table>

        <?php do_action( 'teo_recipient_popup_after_schedules_data' , $offer_scheduled_emails , $offer_recipient , $offer_id , $order_id ); ?>

    </section>

</div>
