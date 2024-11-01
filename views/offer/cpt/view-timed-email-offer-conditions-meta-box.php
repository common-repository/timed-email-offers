<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post;
$disabled_condition_types = array(); ?>

<div id="timed-email-offer-conditions-meta-box" class="teo-cpt-meta-box">

    <div class="meta" style="display: none !important;">
        <span class="offer-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'Add customer as a recipient for this offer if conditions below are met' , 'timed-email-offers' ); ?></h3>

    <div id="offer-conditions">

        <?php if ( !empty( $timed_email_offer_conditions ) ) {

            foreach ( $timed_email_offer_conditions as $condition_group ) {

                if ( isset( $condition_group[ 'condition-group-logic' ] ) ) { ?>

                    <div class="offer-condition-group-logic">

                        <div class="controls">

                            <select class="condition-group-logic" autocomplete="off">
                                <option value="and" <?php echo ( $condition_group[ 'condition-group-logic' ] == 'and' ) ? 'selected="selected"' : ''; ?>><?php _e( 'AND' , 'timed-email-offers' ); ?></option>
                                <option value="or" <?php echo ( $condition_group[ 'condition-group-logic' ] == 'or' ) ? 'selected="selected"' : ''; ?>><?php _e( 'OR' , 'timed-email-offers' ); ?></option>
                            </select>

                        </div>

                    </div>

                <?php } ?>

                <div class="offer-condition-group">

                    <div class="offer-condition-group-actions">
                        <?php do_action( 'teo_offer_condition_group_additional_actions' ); ?>
                        <a class="remove-condition-group"><?php _e( 'Remove Condition Group' , 'timed-email-offers' ); ?></a>
                    </div>
                    
                    <?php foreach ( $condition_group[ 'conditions' ] as $condition )
                        do_action( 'teo_render_' . $condition[ 'condition-type' ] . '_offer_condition_markup' , $condition ); ?>
                    
                    <div class="offer-condition-controls">

                        <div class="controls">

                            <?php do_action( 'teo_offer_condition_group_additional_controls' ); ?>

                            <select class="condition-types">
                                <?php foreach ( $offer_condition_types as $key => $text ) {

                                    $disabled = '';
                                    if ( $offer_condition_types_simple_mode && $key != 'product-quantity' ) {

                                        $disabled = 'disabled="disabled"';
                                        $text .= ' (PREMIUM)';

                                    } ?>

                                    <option value="<?php echo $key; ?>" <?php echo $disabled; ?>><?php echo $text; ?></option>

                                <?php } ?>
                            </select>

                            <input type="button" class="show-add-condition-controls button button-secondary" value="<?php _e( 'Add Condition' , 'timed-email-offers' ); ?>">
                            <input type="button" class="add-condition button button-primary" value="<?php _e( 'Add' , 'timed-email-offers' ); ?>">
                            <input type="button" class="hide-add-condition-controls button button-secondary" value="<?php _e( 'Cancel' , 'timed-email-offers' ); ?>">

                            <span class="spinner"></span>

                        </div>

                    </div>

                </div>

            <?php }

        } else { ?>

            <div id="no-offer-condition-container">
                <p id="no-condition-message"><?php _e( 'No conditions are set for this offer yet. This means that all customers will receive this Offer on completion of their Order.<br/>Click <b>"Add Condition Group"</b> and <b>"Add Condition"</b> buttons to add some conditions.' , 'timed-email-offers' ); ?></p>
            </div>

        <?php } ?>

        <div class="offer-condition-group-controls">

            <div class="controls">

                <?php do_action( 'teo_offer_condition_group_additional_controls' ); ?>
                <input type="button" class="add-condition-group button button-secondary" value="<?php _e( 'Add Condition Group' , 'timed-email-offers' ); ?>">
                <span class="spinner"></span>

            </div>

        </div>

    </div><!--#offer-conditions-->

    <div id="offer-condition-general-controls">

        <?php do_action( 'teo_offer_condition_additional_general_controls' ); ?>
        <input type="button" id="save-offer-conditions" class="button button-primary" value="<?php _e( 'Save Conditions' , 'timed-email-offers' ); ?>">
        <span class="spinner"></span>

    </div><!--#offer-condition-general-controls-->

</div>
