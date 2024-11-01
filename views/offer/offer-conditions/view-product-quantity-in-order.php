<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php if ( $show_condition_logic ) { ?>

    <div class="offer-condition-logic">

        <div class="controls">

            <select class="condition-logic">
                <option value="and" <?php echo $condition_logic_val == 'and' ? 'selected="selected"' : ''; ?>><?php _e( 'AND' , 'timed-email-offers' ); ?></option>
                <option value="or" <?php echo $condition_logic_val == 'or' ? 'selected="selected"' : ''; ?>><?php _e( 'OR' , 'timed-email-offers' ); ?></option>
            </select>

        </div>

    </div>

<?php } ?>

<div class="offer-condition" data-condition-type="product-quantity">

    <div class="offer-condition-actions">
        <?php do_action( 'teo_offer_condition_additional_actions' ); ?>
        <a class="remove-condition"><?php _e( 'Remove Condition' , 'timed-email-offers' ); ?></a>
    </div>

    <div class="fields">

        <div class="field-set product-in-order-field-set">
            <span class="meta" style="display: none !important;">
                <span class="product-type"></span>
            </span>

            <label><?php _e( 'Product In Order' , 'timed-email-offers' ); ?></label>
            <select class="product-in-order" style="min-width: 340px;" data-placeholder="<?php _e( 'Please select a product...' , 'timed-email-offers' ); ?>">
                <?php echo $all_products_select_options; ?>
            </select>
        </div>

        <div class="field-set product-in-order-quantity-condition-field-set">
            <label><?php _e( 'Condition' , 'timed-email-offers' ); ?></label>
            <select class="product-in-order-quantity-condition">
                <?php foreach ( $logic_conditions as $condition_val => $condition_text ) { ?>
                    <option value="<?php echo $condition_val; ?>"><?php echo $condition_text; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="field-set product-in-order-quantity-field-set">
            <label><?php _e( 'Quantity' , 'timed-email-offers' ); ?></label>
            <input type="number" class="product-in-order-quantity" value="1" min="0">
        </div>

        <?php do_action( 'teo_product-quantity_condition_additional_controls' ); ?>

        <div class="field-set button-field-set">
            <input type="button" class="add-product-in-order-btn button button-primary" value="<?php _e( 'Add' , 'timed-email-offers' ); ?>">
            <input type="button" class="edit-product-in-order-btn button button-primary" value="<?php _e( 'Edit' , 'timed-email-offers' ); ?>">
            <input type="button" class="cancel-edit-product-in-order-btn button button-secondary" value="<?php _e( 'Cancel' , 'timed-email-offers' ); ?>">
            <span class="spinner"></span>
        </div>

        <div style="clear: both; float: none; display: block;"></div>

    </div>

    <table class="product-quantity-table wp-list-table widefat fixed striped" cellspacing="0" width="100%">

        <thead>
            <tr>
                <th class="product-heading"><?php _e( 'Product' , 'timed-email-offers' ); ?></th>
                <th class="product-quantity-condition-heading"><?php _e( 'Condition' , 'timed-email-offers' ); ?></th>
                <th class="product-quantity-heading"><?php _e( 'Quantity' , 'timed-email-offers' ); ?></th>
                <?php do_action( 'teo_product-quantity_condition_additional_column_heading_markup' ); ?>
                <th class="controls-heading"></th>
            </tr>
        </thead>

        <tbody class="the-list">

            <?php if ( !empty( $product_conditions ) ) {

                foreach ( $product_conditions as $data )
                    include ( $views_path . 'offer/offer-conditions/view-product-to-check-in-order-entry.php' );

            } else { ?>

                <tr class="no-items">
                    <td class="colspanchange" colspan="<?php echo $product_in_order_table_total_columns; ?>"><?php _e( 'No products added' , 'timed-email-offers' ); ?></td>
                </tr>

            <?php } ?>

        </tbody>

    </table>

</div>