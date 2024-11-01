<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="timed-email-offer-options-meta-box" class="teo-meta-box">

    <div id="offer-order-field-set" class="field-set">

        <label for="offer-order"><?php _e( 'Order Number:' , 'timed-email-offers' ); ?></label>
        <input type="number" id="offer-order" name="offer-order" step="1" min="0" value="<?php echo $offer_order; ?>">
        <p class="desc"><?php _e( '(Optional) Set an order number to adjust the priority of this Offer. Offers with the lowest sort order will be given highest priority.' , 'timed-email-offers' ); ?></p>

    </div>

    <?php wp_nonce_field( 'teo_action_save_offer_options' , 'teo_nonce_save_offer_options' ); ?>

</div><!--#timed-email-offer-options-meta-box-->