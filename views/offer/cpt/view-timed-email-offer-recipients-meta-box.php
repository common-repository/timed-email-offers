<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="timed-email-offer-recipients-meta-box" class="teo-cpt-meta-box">

    <div class="meta" style="display: none !important;">
        <span class="offer-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'Offer Recipients' , 'timed-email-offers' ); ?></h3>

    <div id="custom-datatable-filters">

        <label for="recipient-response-status-filter"><?php _e( 'Response Status:' , 'timed-email-offers' ); ?></label>
        <select id="recipient-response-status-filter" autocomplete="off">
            <option value="all"><?php _e( 'All' , 'timed-email-offers' ); ?></option>

            <?php foreach ( $recipient_offer_response_status as $key => $text ) {
                $selected = $key === 'na' ? 'selected="selected"' : ''; ?>
                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $text; ?></option>
            <?php } ?>
        </select>

    </div>

    <table id="offer-recipients-table" class="wp-list-table widefat fixed striped" cellspacing="0" width="100%">
        <thead>
            <tr>
                <?php foreach ( $offer_recipients_table_headings as $class => $text ) { ?>
                    <th class="<?php echo $class; ?>"><?php echo $text; ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <?php foreach ( $offer_recipients_table_headings as $class => $text ) { ?>
                    <th class="<?php echo $class; ?>"><?php echo $text; ?></th>
                <?php } ?>
            </tr>
        </tfoot>
    </table>

</div><!--#timed-email-offer-recipients-meta-box-->