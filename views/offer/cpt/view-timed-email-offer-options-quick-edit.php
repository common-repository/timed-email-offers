<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// makes sure that the nonce field is only printed once
// useful when ouputting multiple column/fields
static $printNonce = TRUE;
if ( $printNonce ) {

    $printNonce = FALSE;
    wp_nonce_field( 'teo_action_save_offer_options', 'teo_nonce_save_offer_options' );

}

do_action( 'teo_before_quick_edit_fields' , $column_name , $post_type );

switch ( $column_name ) {

    case 'offer_order':
    
        ?>

        <fieldset class="inline-edit-col-right inline-edit-<?php echo $column_name; ?>">
          <div class="inline-edit-col column-<?php echo $column_name; ?>">
            <label class="inline-edit-group">
                <span class="title"><?php _e( 'Offer Order', 'timed-email-offers' ); ?></span>
                <input type="number" id="offer-order" name="offer-order" step="1" min="0" value="">
            </label>
          </div>
        </fieldset>

        <?php

        break;

}

do_action( 'teo_after_quick_edit_fields' , $column_name , $post_type );