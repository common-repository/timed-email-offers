<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="decline-timed-email-offer-actions-meta-box" class="teo-meta-box">

    <div class="meta" style="display: none !important;">
        <span class="offer-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'If offer is declined do the following: ' , 'timed-email-offers' ); ?></h3>

    <p><?php _e( '<b>Note:</b> When a recipient declines an offer, he/she will no longer received any email relating with this offer. All his/her scheduled emails for this offer will be removed.</b>' , 'timed-email-offers' ); ?></p>

    <select id="decline-offer-action-types" autocomplete="off">
        <?php foreach ( $decline_offer_action_types as $key => $text ) {

            $disabled = "";
            $selected = "";
            if ( $decline_offer_action_types_simple_mode && $key != 'do-nothing' ) {

                $disabled = 'disabled="disabled"';
                $text     = sprintf( __( '%1$s (PREMIUM)' , 'timed-email-offers' ) , $text );

            } elseif ( isset( $decline_offer_action[ $key ] ) )
                $selected = 'selected="selected"'; ?>

            <option value="<?php echo $key; ?>" <?php echo $disabled; ?> <?php echo $selected; ?>><?php echo $text; ?></option>

        <?php } ?>
    </select>

    <div id="additional-decline-offer-action-type-options">
        <?php do_action( 'teo_additional_decline_offer_action_type_options' , $decline_offer_action ); ?>
    </div>

    <div id="decline-offer-action-controls">

        <?php do_action( 'teo_decline_offer_action_additional_controls' ); ?>

        <input type="button" id="save-decline-offer-actions-btn" class="button button-primary" value="<?php _e( 'Save Actions' , 'timed-email-offers' ); ?>">
        <span class="spinner"></span>

    </div><!--#decline-offer-action-controls-->

</div>
