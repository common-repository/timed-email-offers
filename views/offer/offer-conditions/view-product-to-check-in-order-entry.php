<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<tr>
    <td class="row-meta hidden">

        <span class="product-type"><?php echo $data[ 'product-type' ]; ?></span>
        <span class="product-id"><?php echo $data[ 'product-id' ]; ?></span>

        <?php if ( isset( $data[ 'product-variation-id' ] ) ) { ?>
            <span class="product-variation-id">

                <?php foreach ( $data['product-variation-id' ] as $variation_id ) { ?>
                    <span class="variation-id"><?php echo $variation_id; ?></span>
                <?php } ?>

            </span>
        <?php } ?>

        <span class="product-quantity-condition"><?php echo $data[ 'product-quantity-condition' ]; ?></span>
        <span class="product-quantity"><?php echo $data[ 'product-quantity' ]; ?></span>
        <?php do_action( 'teo_product-quantity_condition_additional_meta_markup' , $data ); ?>

    </td>

    <td class="product-text">

        <?php $product_text = "";

        if ( $data[ "product-type" ] == 'variable' ) {
            // Variable Product

            $product_text  = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';

            foreach ( $data[ "product-variation-id" ] as $variation_id ) {

                if ( $variation_id == 'any' )
                    $product_text .= '<div class="product-variation">' . __( 'Any Variation' , 'timed-email-offers' ) . '</div>';
                else {

                    $variation_info_arr = TEO_Helper::get_product_variations( array(
                        'variable_id'  => $data[ 'product-id' ],
                        'variation_id' => $variation_id
                    ) );

                    $product_text .= '<div class="product-variation">' . $variation_info_arr[ 0 ][ 'text' ] . '</div>';

                }

            }


        } else if ( $data[ "product-type" ] == 'simple' )
            $product_text =  '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';
        else
            $product_text = apply_filters( 'teo_' . $data[ "product-type" ] . '_product_quantity_in_order_entry_text' , $product_text , $data );

        echo  $product_text; ?>

    </td>

    <td class="product-quantity-condition"><?php echo $logic_conditions[ $data[ 'product-quantity-condition' ] ]; ?></td>
    <td class="product-quantity"><?php echo $data[ 'product-quantity' ]; ?></td>

    <?php do_action( 'teo_product-quantity_condition_additional_column_data_markup' , $data ); ?>

    <td class="row-controls">
        <span class="dashicons dashicons-edit edit-product"></span>
        <span class="dashicons dashicons-no delete-product"></span>
    </td>
</tr>