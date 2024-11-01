<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post;
$disabled_accept_offer_action_types = array(); ?>

<div id="accept-timed-email-offer-actions-meta-box" class="teo-meta-box">

    <div class="meta" style="display: none !important;">
        <span class="offer-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'If offer is accepted do the following: ' , 'timed-email-offers' ); ?></h3>

    <div id="accept-offer-actions">

        <?php if ( !empty( $accept_offer_actions ) ) {

            foreach ( $accept_offer_actions as $action_type => $action_data ) {

                if ( $action_type == "add-products-to-cart" ) {

                    $disabled_accept_offer_action_types[] = "add-products-to-cart"; ?>

                    <div id="add-products-to-cart-action-container" class="accept-offer-action" data-action-type="add-products-to-cart">

                        <div class="action-controls">
                            <a class="remove-action"><?php _e( 'Remove Action' , 'timed-email-offers' ); ?></a>
                        </div>

                        <div class="fields">

                            <div class="field-set product-filter-field-set">

                                <span class="meta" style="display: none !important;">
                                    <span class="product-type"></span>
                                </span>

                                <label for="add-products-to-cart-filter"><?php _e( 'Add product to cart' , 'timed-email-offers' ); ?></label>
                                <select id="add-products-to-cart-filter" class="product-filter-control" style="min-width: 360px;" data-placeholder="<?php _e( 'Please select a product...' , 'timed-email-offers' ); ?>">
                                    <?php echo $all_products_select_options; ?>
                                </select>

                            </div>

                            <div class="field-set product-quantity-field-set">
                                <label for="add-products-to-cart-quantity"><?php _e( 'Quantity' , 'timed-email-offers' ); ?></label>
                                <input type="number" id="add-products-to-cart-quantity" value="1" min="1">
                            </div>

                            <?php do_action( 'teo_add-products-to-cart_additional_controls' ); ?>

                            <div class="field-set button-field-set">
                                <input type="button" id="add-product-to-cart-btn" class="button button-primary" value="<?php _e( 'Add' , 'timed-email-offers' ); ?>">
                                <input type="button" id="add-product-to-cart-edit-button" class="button button-primary" value="<?php _e( 'Edit' , 'timed-email-offers' ); ?>">
                                <input type="button" id="cancel-add-product-cart-edit-button" class="button button-secondary" value="<?php _e( 'Cancel' , 'timed-email-offers' ); ?>">
                                <span class="spinner"></span>
                            </div>

                            <div style="clear: both; float: none; display: block;"></div>

                        </div>

                        <table id="add-products-to-cart-table" class="wp-list-table widefat fixed striped" cellspacing="0" width="100%">

                            <thead>
                                <tr>
                                    <th class="product-heading"><?php _e( 'Product' , 'timed-email-offers' ); ?></th>
                                    <th class="quantity-heading"><?php _e( 'Quantity' , 'timed-email-offers' ); ?></th>
                                    <?php do_action( 'teo_add-products-to-cart_additional_column_heading_markup' ); ?>
                                    <th class="controls-heading"></th>
                                </tr>
                            </thead>

                            <tbody class="the-list">

                            <?php if ( !empty( $action_data ) ) {

                                foreach ( $action_data as $action ) { ?>
                                    <tr>

                                        <td class="row-meta hidden">
                                            <span class="product-type"><?php echo $action[ "product-type" ]; ?></span>
                                            <span class="product-id"><?php echo $action[ "product-id" ]; ?></span>

                                            <?php if ( isset( $action[ "product-variation-id" ] ) ) { ?>

                                                <span class="product-variation-id"><?php echo $action[ "product-variation-id" ]; ?></span>

                                            <?php } ?>

                                            <span class="product-quantity"><?php echo $action[ "product-quantity" ]; ?></span>
                                            <?php do_action( 'teo_add-products-to-cart_additional_meta_markup' ); ?>
                                        </td>

                                        <td class="product-text">

                                            <?php $product_text = "";

                                            if ( $action[ "product-type" ] == 'variable' ) {
                                                // Variable Product

                                                $variation_info_arr = teo_Helper::get_product_variations( array(
                                                    'variable_id'  => $action[ 'product-id' ],
                                                    'variation_id' => $action[ "product-variation-id" ]
                                                ) );

                                                $product_text  = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $action[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $action[ 'product-id' ] . '] ' . get_the_title( $action[ 'product-id' ] ) . '</a></div>';
                                                $product_text .= '<div class="product-variation">' . $variation_info_arr[ 0 ][ 'text' ] . '</div>';

                                            } else if ( $action[ "product-type" ] == 'simple' )
                                                $product_text =  '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $action[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $action[ 'product-id' ] . '] ' . get_the_title( $action[ 'product-id' ] ) . '</a></div>';
                                            else
                                                $product_text = apply_filters( 'teo_' . $action[ "product-type" ] . '_product_to_add_entry_text' , $product_text , $action );

                                            echo  $product_text; ?>

                                        </td>

                                        <td class="product-quantity">
                                            <?php echo $action[ "product-quantity" ]; ?>
                                        </td>

                                        <?php do_action( 'teo_add-products-to-cart_additional_column_data_markup' , $action ); ?>

                                        <td class="row-controls">
                                            <span class="dashicons dashicons-edit edit-product"></span>
                                            <span class="dashicons dashicons-no delete-product"></span>
                                        </td>

                                    </tr>

                                <?php }

                            } else { ?>

                                <tr class="no-items">
                                    <td class="colspanchange" colspan="<?php echo $add_products_to_cart_table_total_columns; ?>"><?php _e( 'No products added' , 'timed-email-offers' ); ?></td>
                                </tr>

                            <?php } ?>

                            </tbody>

                        </table>

                    </div>

                <?php } elseif ( $action_type == "apply-coupons-to-cart" ) {

                    $disabled_accept_offer_action_types[] = "apply-coupons-to-cart"; ?>

                    <div id="apply-coupons-to-cart-action-container" class="accept-offer-action" data-action-type="apply-coupons-to-cart">

                        <div class="action-controls">
                            <a class="remove-action"><?php _e( 'Remove Action' , 'timed-email-offers' ); ?></a>
                        </div>

                        <div class="fields">

                            <div class="field-set coupons-filter-field-set">

                                <label for="apply-coupons-to-cart-filter"><?php _e( 'Apply coupon to cart' , 'timed-email-offers' ); ?></label>
                                <select id="apply-coupons-to-cart-filter" class="coupon-filter-control" style="min-width: 360px;" data-placeholder="<?php _e( 'Please select a coupon...' , 'timed-email-offers' ); ?>">
                                    <?php echo $all_coupons_select_options; ?>
                                </select>

                            </div>

                            <?php do_action( 'teo_apply-coupons-to-cart_additional_controls' ); ?>

                            <div class="field-set button-field-set">
                                <input type="button" id="add-coupon-to-be-applied-to-cart-btn" class="button button-primary" value="<?php _e( 'Add' , 'timed-email-offers' ); ?>">
                                <input type="button" id="edit-coupon-to-be-applied-to-cart-btn" class="button button-primary" value="<?php _e( 'Edit' , 'timed-email-offers' ); ?>">
                                <input type="button" id="cancel-edit-coupon-to-be-applied-to-cart-btn" class="button button-secondary" value="<?php _e( 'Cancel' , 'timed-email-offers' ); ?>">
                                <span class="spinner"></span>
                            </div>

                            <div style="clear: both; float: none; display: block;"></div>

                        </div>

                        <table id="apply-coupons-to-cart-table" class="wp-list-table widefat fixed striped" cellspacing="0" width="100%">

                            <thead>
                                <tr>
                                    <th class="coupon-heading"><?php _e( 'Coupon' , 'timed-email-offers' ); ?></th>
                                    <th class="coupon-type-heading"><?php _e( 'Coupon Type' , 'timed-email-offers' ); ?></th>
                                    <th class="coupon-amount-heading"><?php _e( 'Coupon Amount' , 'timed-email-offers' ); ?></th>
                                    <?php do_action( 'teo_apply-coupons-to-cart_additional_column_heading_markup' ); ?>
                                    <th class="controls-heading"></th>
                                </tr>
                            </thead>

                            <tbody class="the-list">

                            <?php if ( !empty( $action_data ) ) {

                                foreach ( $action_data as $action ) {
                                    $coupon_details = teo_Helper::get_coupon_info( $action[ 'coupon-id' ] );
                                    $coupon_amount  = TEO_Helper::get_coupon_data( $coupon_details[ 'coupon_obj' ] , 'coupon_amount' ); ?>

                                    <tr>

                                        <td class="row-meta hidden">
                                            <span class="coupon-id"><?php echo $action[ 'coupon-id' ]; ?></span>
                                            <?php do_action( 'teo_apply-coupons-to-cart_additional_meta_markup' ); ?>
                                        </td>
                                        <td class="coupon">
                                            <?php $coupon_text = '<div class="coupon"><a href="' . $coupon_details[ 'coupon_url' ] . '" target="_blank">[ID : ' .  $action[ 'coupon-id' ] . '] ' . get_the_title( $action[ 'coupon-id' ] ) . '</a></div>';
                                            $coupon_text = apply_filters( 'teo_coupon_to_apply_entry_text' , $coupon_text , $action , $coupon_details );
                                            echo $coupon_text; ?>
                                        </td>
                                        <td class="coupon-type-text"><?php echo $coupon_details[ 'coupon_type_text' ]; ?></td>
                                        <td class="coupon-amount"><?php echo $coupon_amount; ?></td>

                                        <?php do_action( 'teo_apply-coupons-to-cart_additional_column_data_markup' , $action , $coupon_details ); ?>

                                        <td class="row-controls">
                                            <span class="dashicons dashicons-edit edit-coupon"></span>
                                            <span class="dashicons dashicons-no delete-coupon"></span>
                                        </td>

                                    </tr>

                                <?php }

                            } else { ?>

                                <tr class="no-items">
                                    <td class="colspanchange" colspan="<?php echo $apply_coupons_to_cart_table_total_columns; ?>"><?php _e( 'No coupons added' , 'timed-email-offers' ); ?></td>
                                </tr>

                            <?php } ?>

                            </tbody>

                        </table>

                    </div>

                <?php } else {

                    $disabled_accept_offer_action_types[] = $action_type;
                    do_action( 'teo_render_' . $action_type . '_accept_offer_action_markup' , $action_data );

                }

            }

        } else { ?>

            <p class="no-actions"><?php _e( 'Take the customer to the site and then ...<br/>There\'s currently no acceptance actions defined. Click <b>"Add Action"</b> to define what should happen after clicking Accept in an email.' , 'timed-email-offers' ); ?></p>

        <?php } ?>

    </div><!--#accept-offer-actions-->

    <div id="add-offer-action-controls">

        <?php do_action( 'teo_add_accept_offer_action_additional_controls' ); ?>

        <input type="button" id="add-accept-offer-action-btn" class="button button-primary" value="<?php _e( 'Add Action' , 'timed-email-offers' ); ?>">
        <input type="button" id="save-accept-offer-actions-btn" class="button button-primary" value="<?php _e( 'Save Actions' , 'timed-email-offers' ); ?>">

        <select id="accept-offer-action-types">
            <option value=""><?php _e( '--Select Action--' , 'timed-email-offers' ); ?></option>
            <?php foreach ( $accept_offer_action_types as $key => $val ) {

                $disabled = in_array( $key , $disabled_accept_offer_action_types ) ? 'disabled="disabled"' : ''; ?>

                <option value="<?php echo $key; ?>" <?php echo $disabled; ?>><?php echo $val; ?></option>

            <?php } ?>

            <?php if ( !array_key_exists( 'remove-products-from-cart' , $accept_offer_action_types ) && !array_key_exists( 'remove-coupons-from-cart' , $accept_offer_action_types ) ) { ?>

                <option disabled="disabled" value="remove-products-from-cart"><?php _e( 'Remove Products From Cart (PREMIUM)' , 'timed-email-offers' ); ?></option>
                <option disabled="disabled" value="remove-coupons-from-cart"><?php _e( 'Remove Coupons From Cart (PREMIUM)' , 'timed-email-offers' ); ?></option>

            <?php } ?>
        </select>

        <input type="button" id="add-action-btn" class="button button-primary" value="<?php _e( 'Add' , 'timed-email-offers' ); ?>">
        <input type="button" id="cancel-add-action-btn" class="button button-secondary" value="<?php _e( 'Cancel' , 'timed-email-offers' ); ?>">
        <span class="spinner"></span>

    </div><!--#add-offer-action-controls-->

</div><!--#accept-timed-email-offer-actions-meta-box-->
