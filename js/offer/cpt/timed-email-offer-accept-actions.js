/* global jQuery */
jQuery( document ).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Variables
     |--------------------------------------------------------------------------
     */

    var $accept_offer_actions_meta_box = $( "#accept-timed-email-offer-actions-meta-box" ),
        $meta                          = $accept_offer_actions_meta_box.find( ".meta" ),
        offer_id                       = $.trim( $meta.find( ".offer-id" ).text() ),
        $accept_offer_actions          = $accept_offer_actions_meta_box.find( "#accept-offer-actions" ),
        $add_offer_action_controls     = $accept_offer_actions_meta_box.find( "#add-offer-action-controls" ),
        $add_accept_offer_action_btn   = $add_offer_action_controls.find( "#add-accept-offer-action-btn" ),
        $save_accept_offer_actions_btn = $add_offer_action_controls.find( "#save-accept-offer-actions-btn" ),
        $accept_offer_action_types     = $add_offer_action_controls.find( "#accept-offer-action-types" ),
        $add_action_btn                = $add_offer_action_controls.find( "#add-action-btn" ),
        $cancel_add_action_btn         = $add_offer_action_controls.find( "#cancel-add-action-btn" );




    /*
     |--------------------------------------------------------------------------
     | Add Action
     |--------------------------------------------------------------------------
     */

    $accept_offer_actions.on( 'DOMNodeInserted' , function( e ) {

        var $condition_container = $( e.target );

        if ( $condition_container.attr( 'id' ) == "add-products-to-cart-action-container" )
            $condition_container.find( "#add-products-to-cart-filter" ).chosen( { allow_single_deselect: true , search_contains: true } );
        else if ( $( e.target ).attr( 'id' ) == "apply-coupons-to-cart-action-container" )
            $condition_container.find( "#apply-coupons-to-cart-filter" ).chosen( { allow_single_deselect: true , search_contains: true } );

        return $( this );

    } );

    $add_offer_action_controls.on( "processing_mode" , function( event ) {

        event.stopPropagation();

        $add_offer_action_controls
            .find( ".button" ).attr( "disabled" , "disabled" ).end()
            .find( "select" ).attr( "disabled" , "disabled" );

        $add_offer_action_controls.find( ".spinner" ).css( "visibility" , "visible" );

        if ( !$add_offer_action_controls.hasClass( "adding-action-mode" ) )
            $add_offer_action_controls.find( ".spinner" ).css( "margin-top" , "3px" );

        return $( this );

    } );

    $add_offer_action_controls.on( "normal_mode" , function( event ) {

        event.stopPropagation();

        $add_offer_action_controls
            .find( ".button" ).removeAttr( "disabled" ).end()
            .find( "select" ).removeAttr( "disabled" );

        $add_offer_action_controls.find( ".spinner").removeAttr( "style" );

        return $( this );

    } );

    $accept_offer_actions.on( 'add_new_accept_offer_action' , function( event , action_type ) {

        event.stopPropagation();

        if ( action_type == 'add-products-to-cart' || action_type == 'apply-coupons-to-cart' ) {

            var args = {
                action_type : action_type
            };

            $.ajax( {
                url      : ajaxurl,
                type     : 'POST',
                data     : { action : 'teo_get_new_accept_offer_action_markup' , offer_id : offer_id , args : args , 'ajax-nonce' : accept_timed_email_offer_actions_params.nonce_get_new_accept_offer_action_markup },
                dataType : 'json'
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    if ( $accept_offer_actions.find( ".no-actions" ).length > 0 )
                        $accept_offer_actions.find( ".no-actions").remove();

                    $accept_offer_actions.append( data.mark_up );
                    $accept_offer_action_types.find( "option[value='" + action_type + "']" ).attr( "disabled" , "disabled" );


                } else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                console.log( jqxhr );
                vex.dialog.alert( accept_timed_email_offer_actions_params.i18n_failed_add_accept_offer_action );

            } )
            .always( function() {

                $add_offer_action_controls.trigger( "normal_mode" );
                $add_offer_action_controls.removeClass( "adding-action-mode" );

            } );

        }

        return $( this );

    } );

    $add_accept_offer_action_btn.click( function() {

        $accept_offer_action_types.val( "" );
        $add_offer_action_controls.addClass( "adding-action-mode" );

    } );

    $cancel_add_action_btn.click( function() {

        $accept_offer_action_types.val( "" );
        $add_offer_action_controls.removeClass( "adding-action-mode" );

    } );

    $add_action_btn.click( function() {

        if ( $accept_offer_action_types.val() ) {

            $add_offer_action_controls.trigger( "processing_mode" );

            var action_type = $.trim( $accept_offer_action_types.val() );

            $accept_offer_actions.trigger( "add_new_accept_offer_action" , [ action_type ] );

        } else
            vex.dialog.alert( accept_timed_email_offer_actions_params.i18n_select_action_to_execute );

    } );




    /*
     |--------------------------------------------------------------------------
     | Add Products To Cart Action
     |--------------------------------------------------------------------------
     */

    /*---- Add product -----*/

    $accept_offer_actions.on( "processing_mode" , "#add-products-to-cart-action-container" , function( event ) {

        event.stopPropagation();

        var $this = $( this ),
            $product_filter_field_set   = $this.find( ".product-filter-field-set" ),
            $product_quantity_field_set = $this.find( ".product-quantity-field-set" ),
            $button_field_set           = $this.find( ".button-field-set" );

        $product_filter_field_set
            .find( "input" ).attr( "disabled" , "disabled" ).end()
            .find( "select" ).attr( "disabled" , "disabled" ).trigger( "chosen:updated" );

        $product_quantity_field_set.find( "input" ).attr( "disabled" , "disabled" );

        $button_field_set
            .find( "input" ).attr( "disabled" , "disabled" ).end()
            .find( ".spinner" ).css( "visibility" , "visible" );

        return $this;

    } );

    $accept_offer_actions.on( "normal_mode" , "#add-products-to-cart-action-container" , function( event ) {

        event.stopPropagation();

        var $this = $( this ),
            $product_filter_field_set   = $this.find( ".product-filter-field-set" ),
            $product_quantity_field_set = $this.find( ".product-quantity-field-set" ),
            $button_field_set           = $this.find( ".button-field-set" );

        $product_filter_field_set
            .find( "input" ).removeAttr( "disabled" ).end()
            .find( "select" ).removeAttr( "disabled" ).trigger( "chosen:updated" );

        $product_quantity_field_set.find( "input" ).removeAttr( "disabled" );

        $button_field_set
            .find( "input" ).removeAttr( "disabled" ).end()
            .find( ".spinner" ).css( "visibility" , "hidden" );

        return $this;

    } );

    $accept_offer_actions.on( "reset_fields" , "#add-products-to-cart-action-container" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this.find( "#add-products-to-cart-filter" ).val( "" ).trigger( "change" ).trigger( "chosen:updated" );
        $this.find( "#add-products-to-cart-quantity" ).val( 1 );

        return $this;

    } );

    $accept_offer_actions.on( "render_additional_product_data" , "#add-products-to-cart-action-container #add-products-to-cart-filter" , function( event , $parent_action_container , data , variation_id ) {

        event.stopPropagation();

        if ( data.product_data.product_type == "variable" ) {

            var $product_filter_field_set = $parent_action_container.find( ".product-filter-field-set" ),
                variations_control_markup = '<select id="add-products-to-cart-filter-variations" style="min-width: 360px;" data-placeholder="' + accept_timed_email_offer_actions_params.i18n_select_variation + '">' +
                                            '<option value=""></option>';

            for ( var key in data.product_data.product_variations  ) {

                if ( data.product_data.product_variations.hasOwnProperty( key ) ) {

                    var variation = data.product_data.product_variations[ key ],
                        disabled  = '',
                        selected  = '';

                    if ( variation.disabled || !variation.visible )
                        disabled = 'disabled="disabled"';

                    if ( variation_id && variation_id == variation.value )
                        selected = 'selected="selected"';

                    variations_control_markup += '<option value="' + variation.value + '" ' + disabled + ' ' + selected + '>' + variation.text + '</option>';

                }

            }

            variations_control_markup += '</select>';

            $product_filter_field_set.append(
                '<div class="additional-product-data">' +
                    '<label for="add-products-to-cart-filter-variations">' + accept_timed_email_offer_actions_params.i18n_product_variations + '</label> ' +
                    variations_control_markup +
                '</div>'
            );

            $product_filter_field_set.find( "#add-products-to-cart-filter-variations" ).chosen( { allow_single_deselect: true , search_contains: true } );

        }

        return $( this );

    } );

    $accept_offer_actions.on( "change" , "#add-products-to-cart-action-container #add-products-to-cart-filter" , function( event , variation_id ) {

        event.stopPropagation();

        var $this = $( this ),
            $parent_action_container = $this.closest( "#add-products-to-cart-action-container" ),
            $product_filter_field_set = $parent_action_container.find( ".product-filter-field-set" ),
            $meta_container = $product_filter_field_set.find( ".meta" );

        $product_filter_field_set.find( ".additional-product-data" ).slideUp( 'fast' , function() {

            $product_filter_field_set.find( ".additional-product-data" ).remove();

        } );

        if ( $this.val() != "" ) {

            $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "processing_mode" );

            // Get additional data for the current product

            $.ajax( {
                url      : ajaxurl,
                type     : "POST",
                data     : { action : "teo_get_product_additional_info" , product_id : $this.val() , 'ajax-nonce' : accept_timed_email_offer_actions_params.nonce_get_product_additional_info },
                dataType : "json"
            } )
            .done( function( data , text_response , jqxhr ) {

                if ( data.status == 'success' ) {

                    $meta_container.find( ".product-type" ).text( data.product_data.product_type );
                    $accept_offer_actions.find( "#add-products-to-cart-action-container #add-products-to-cart-filter" ).trigger( "render_additional_product_data" , [ $parent_action_container , data , variation_id ] );

                } else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_response , error_thrown ) {

                console.log( jqxhr );
                vex.dialog.alert( accept_timed_email_offer_actions_params.i18n_failed_retrieve_product_data );

            } )
            .always( function() {

                $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "normal_mode" );

            } );

        }

        return $this;

    } );

    $accept_offer_actions.on( "keydown" , "#add-products-to-cart-action-container #add-products-to-cart-quantity" , function( e ) {

        // Allow: backspace, delete, tab, escape, enter and .
        if ( $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190] ) !== -1 ||
                // Allow: Ctrl+A, Command+A
            ( e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                // Allow: home, end, left, right, down, up
            ( e.keyCode >= 35 && e.keyCode <= 40 ) ) {
            // let it happen, don't do anything
            return;
        }

        // Ensure that it is a number and stop the keypress
        if ( ( e.shiftKey || (e.keyCode < 48 || e.keyCode > 57) ) && ( e.keyCode < 96 || e.keyCode > 105 ) ) {
            e.preventDefault();
        }

    } );

    $accept_offer_actions.on( "construct_data" , "#add-products-to-cart-action-container #add-product-to-cart-btn" , function( event , $action_container , data , errors ) {

        event.stopPropagation();

        var $product_filter_field_set      = $action_container.find( ".product-filter-field-set" ),
            $meta                          = $product_filter_field_set.find( ".meta" ),
            $add_products_to_cart_filter   = $product_filter_field_set.find( "#add-products-to-cart-filter" ),
            $product_quantity_field_set    = $action_container.find( ".product-quantity-field-set" ),
            $add_products_to_cart_quantity = $product_quantity_field_set.find( "#add-products-to-cart-quantity" );

        data[ 'product-type' ] = $meta.find( ".product-type" ).text();

        if ( $add_products_to_cart_filter.val() == "" )
            errors.push( accept_timed_email_offer_actions_params.i18n_select_product_to_add + "<br/>" );
        else
            data[ 'product-id' ] = $add_products_to_cart_filter.val();

        if ( $add_products_to_cart_quantity.val() == "" || $add_products_to_cart_quantity.val() <= 0 )
            errors.push( accept_timed_email_offer_actions_params.i18n_specify_product_quantity + "<br/>" );
        else
            data[ 'product-quantity' ] = $add_products_to_cart_quantity.val();

        if ( $product_filter_field_set.find( "#add-products-to-cart-filter-variations" ).length > 0 ) {

            if ( $product_filter_field_set.find( "#add-products-to-cart-filter-variations" ).val() == '' )
                errors.push( accept_timed_email_offer_actions_params.i18n_select_product_variation_to_add + "<br/>" );
            else
                data[ 'product-variation-id' ] = $product_filter_field_set.find( "#add-products-to-cart-filter-variations" ).val();

        }

        $accept_offer_actions.find( "#add-products-to-cart-action-container #add-product-to-cart-btn" ).trigger( "construct_additional_data" , [ $action_container , data , errors ] ); // For extensibility

        return $( this );

    } );

    $accept_offer_actions.on( "new_product_to_add" , "#add-products-to-cart-action-container #add-products-to-cart-table" , function( event , data ) {

        event.stopPropagation();

        $.ajax( {
            url      : ajaxurl,
            type     : "POST",
            data     : { action : "teo_generate_product_to_add_entry_markup" , offer_id : offer_id , data : data , 'ajax-nonce' : accept_timed_email_offer_actions_params.nonce_generate_product_to_add_entry_markup },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) {

            console.log( data );

            if ( data.status == 'success' ) {

                if ( $accept_offer_actions.find( "#add-products-to-cart-table tbody" ).find( ".no-items" ) )
                    $accept_offer_actions.find( "#add-products-to-cart-table tbody" ).find( ".no-items" ).remove();

                $accept_offer_actions.find( "#add-products-to-cart-table tbody" ).append( data.mark_up );

            } else {

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( accept_timed_email_offer_actions_params.i18n_failed_generate_product_entry_markup );

        } )
        .always( function() {

            $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "reset_fields" );
            $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "normal_mode" );

        } );

        return $( this );

    } );

    $accept_offer_actions.on( "click" , "#add-products-to-cart-action-container #add-product-to-cart-btn" , function() {

        $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "processing_mode" );

        var $action_container = $accept_offer_actions.find( "#add-products-to-cart-action-container" ),
            data              = {},
            errors            = [];

        $accept_offer_actions.find( "#add-products-to-cart-action-container #add-product-to-cart-btn" ).trigger( "construct_data" , [ $action_container , data , errors ] );

        if ( errors.length > 0 ) {

            var err_msg = '<strong>' + accept_timed_email_offer_actions_params.i18n_fill_form_properly + '</strong><br>';

            for ( var i = 0; i < errors.length; i++ )
                err_msg += errors[ i ];

            vex.dialog.alert( err_msg );

            $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "normal_mode" );

        } else
            $accept_offer_actions.find( "#add-products-to-cart-action-container #add-products-to-cart-table" ).trigger( "new_product_to_add" , [ data ] );

    } );


    /*---- Edit product -----*/

    $accept_offer_actions.on( "processing_mode" , "#add-products-to-cart-table" , function( event ) {

        event.stopPropagation();

        $accept_offer_actions.find( "#add-products-to-cart-table" ).addClass( "processing_mode" );
        return $( this );

    } );

    $accept_offer_actions.on( "normal_mode" , "#add-products-to-cart-table" , function( event ) {

        event.stopPropagation();

        $accept_offer_actions
            .find( "#add-products-to-cart-table" ).removeClass( "processing_mode" )
            .find( "tr").removeClass( "processing" );

        return $( this );

    } );

    $accept_offer_actions.on( "construct_data_to_edit" , "#add-products-to-cart-table" , function( event , $tr , data ) {

        event.stopPropagation();

        data[ 'product-type' ]     = $tr.find( ".row-meta .product-type" ).text();
        data[ 'product-id' ]       = $tr.find( ".row-meta .product-id" ).text();
        data[ 'product-quantity' ] = $tr.find( ".row-meta .product-quantity" ).text();

        if ( $tr.find( ".row-meta .product-variation-id" ).length > 0 )
            data[ 'product-variation-id' ] = $tr.find( ".row-meta .product-variation-id" ).text();

        $accept_offer_actions.find( "#add-products-to-cart-table" ).trigger( "construct_additional_data_to_edit" , [ $tr , data ] );
        return $( this );

    } );

    $accept_offer_actions.on( "prepopulate_fields" , "#add-products-to-cart-action-container .fields" , function( event , data ) {

        event.stopPropagation();

        var $this        = $( this ),
            variation_id = data.hasOwnProperty( "product-variation-id" ) ? data[ "product-variation-id" ] : "";

        $this.find( ".meta .product-type" ).text( data[ 'product-type' ] );
        $this.find( "#add-products-to-cart-filter" ).val( data[ 'product-id' ] ).trigger( "change" , [ variation_id ] ).trigger( "chosen:updated" );
        $this.find( "#add-products-to-cart-quantity" ).val( data[ 'product-quantity' ] );

        return $this;

    } );

    $accept_offer_actions.on( "edit_mode" , "#add-products-to-cart-action-container .fields .button-field-set" , function( event ) {

        event.stopPropagation();

        $( this ).addClass( "edit-mode" );
        return $( this );

    } );

    $accept_offer_actions.on( "add_mode" , "#add-products-to-cart-action-container .fields .button-field-set" , function( event ) {

        event.stopPropagation();

        $( this ).removeClass( "edit-mode" );
        return $( this );

    } );

    $accept_offer_actions.on( "click" , "#add-products-to-cart-table .edit-product" , function() {

        var $tr  = $( this ).closest( "tr"),
            data = {};

        $tr.addClass( "processing" );
        $accept_offer_actions.find( "#add-products-to-cart-table" ).trigger( "processing_mode" );

        $accept_offer_actions.find( "#add-products-to-cart-table" ).trigger( "construct_data_to_edit" , [ $tr , data ] );

        $accept_offer_actions.find( "#add-products-to-cart-action-container .fields" ).trigger( "prepopulate_fields" , [ data ] );

        $accept_offer_actions.find( "#add-products-to-cart-action-container .fields .button-field-set" ).trigger( "edit_mode" );

    } );

    $accept_offer_actions.on( "click" , "#add-products-to-cart-action-container #cancel-add-product-cart-edit-button" , function() {

        $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "reset_fields" );

        $accept_offer_actions.find( "#add-products-to-cart-action-container .fields .button-field-set" ).trigger( "add_mode" );

        $accept_offer_actions.find( "#add-products-to-cart-table" ).trigger( "normal_mode" );

    } );

    $accept_offer_actions.on( "edit_product_to_add" , "#add-products-to-cart-action-container #add-product-to-cart-edit-button" , function( event , data ) {

        event.stopPropagation();

        $.ajax( {
            url      : ajaxurl,
            type     : "POST",
            data     : { action : "teo_generate_product_to_add_entry_markup" , offer_id : offer_id , data : data , 'ajax-nonce' : accept_timed_email_offer_actions_params.nonce_generate_product_to_add_entry_markup },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( data.status == 'success' ) {

                if ( $accept_offer_actions.find( "#add-products-to-cart-table tbody" ).find( ".no-items" ) )
                    $accept_offer_actions.find( "#add-products-to-cart-table tbody" ).find( ".no-items" ).remove();

                var $tr = $accept_offer_actions.find( "#add-products-to-cart-table tbody tr.processing" );

                data.mark_up = data.mark_up.replace( '<tr>' , '' );
                data.mark_up = data.mark_up.replace( '</tr>' , '' );

                $tr.html( data.mark_up );

            } else {

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( accept_timed_email_offer_actions_params.i18n_failed_generate_product_entry_markup );

        } )
        .always( function() {

            $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "reset_fields" );
            $accept_offer_actions.find( "#add-products-to-cart-action-container .fields .button-field-set" ).trigger( "add_mode" );
            $accept_offer_actions.find( "#add-products-to-cart-table" ).trigger( "normal_mode" );
            $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "normal_mode" );

        } );

        return $( this );

    } );

    $accept_offer_actions.on( "click" , "#add-products-to-cart-action-container #add-product-to-cart-edit-button" , function() {

        $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "processing_mode" );

        var $action_container = $accept_offer_actions.find( "#add-products-to-cart-action-container" ),
            data              = {},
            errors            = [];

        $accept_offer_actions.find( "#add-products-to-cart-action-container #add-product-to-cart-btn" ).trigger( "construct_data" , [ $action_container , data , errors ] );

        if ( errors.length > 0 ) {

            var err_msg = '<strong>' + accept_timed_email_offer_actions_params.i18n_fill_form_properly + '</strong><br>';

            for ( var i = 0; i < errors.length; i++ )
                err_msg += errors[ i ];

            vex.dialog.alert( err_msg );

            $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "normal_mode" );

        } else
            $accept_offer_actions.find( "#add-products-to-cart-action-container #add-product-to-cart-edit-button" ).trigger( "edit_product_to_add" , [ data ] );

    } );


    /*---- Delete product -----*/

    $accept_offer_actions.on( "click" , "#add-products-to-cart-table .delete-product" , function() {

        var $tr  = $( this ).closest( "tr");

        $tr.addClass( "processing" );
        $accept_offer_actions.find( "#add-products-to-cart-table" ).trigger( "processing_mode" );

        vex.dialog.confirm( {
            message  : accept_timed_email_offer_actions_params.i18n_confirm_delete_product ,
            callback : function( value ) {

                if ( value ) {

                    $tr.slideUp( "fast" , function() {

                        $tr.remove();

                        if ( $accept_offer_actions.find( "#add-products-to-cart-table tbody tr" ).length <= 0 ) {

                            $accept_offer_actions.find( "#add-products-to-cart-table tbody" ).append(
                                '<tr class="no-items">' +
                                    '<td class="colspanchange" colspan="' + accept_timed_email_offer_actions_params.add_product_table_total_columns + '">' + accept_timed_email_offer_actions_params.i18n_no_products_found + '</td>' +
                                '</tr>'
                            );

                        }

                    } );

                }

                $accept_offer_actions.find( "#add-products-to-cart-table" ).trigger( "normal_mode" );

            }

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Apply Coupons To Cart Action
     |--------------------------------------------------------------------------
     */

    /*---- Add coupon -----*/

    $accept_offer_actions.on( "processing_mode" , "#apply-coupons-to-cart-action-container" , function( event ) {

        event.stopPropagation();

        var $this = $( this ),
            $coupons_filter_field_set = $this.find( ".coupons-filter-field-set" ),
            $button_field_set         = $this.find( ".button-field-set" );

        $coupons_filter_field_set
            .find( "input" ).attr( "disabled" , "disabled" ).end()
            .find( "select" ).attr( "disabled" , "disabled" ).trigger( "chosen:updated" );

        $button_field_set
            .find( "input" ).attr( "disabled" , "disabled" ).end()
            .find( ".spinner" ).css( "visibility" , "visible" );

        return $this;

    } );

    $accept_offer_actions.on( "normal_mode" , "#apply-coupons-to-cart-action-container" , function( event ) {

        event.stopPropagation();

        var $this = $( this ),
            $coupons_filter_field_set = $this.find( ".coupons-filter-field-set" ),
            $button_field_set         = $this.find( ".button-field-set" );

        $coupons_filter_field_set
            .find( "input" ).removeAttr( "disabled" ).end()
            .find( "select" ).removeAttr( "disabled" ).trigger( "chosen:updated" );

        $button_field_set
            .find( "input" ).removeAttr( "disabled" ).end()
            .find( ".spinner" ).css( "visibility" , "hidden" );

        return $this;

    } );

    $accept_offer_actions.on( "reset_fields" , "#apply-coupons-to-cart-action-container" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this.find( "#apply-coupons-to-cart-filter" ).val( "" ).trigger( "change" ).trigger( "chosen:updated" );

        return $this;

    } );

    $accept_offer_actions.on( "construct_data" , "#apply-coupons-to-cart-action-container #add-coupon-to-be-applied-to-cart-btn" , function( event , $action_container , data , errors ) {

        event.stopPropagation();

        var $coupons_filter_field_set    = $action_container.find( ".coupons-filter-field-set" ),
            $apply_coupon_to_cart_filter = $coupons_filter_field_set.find( "#apply-coupons-to-cart-filter" );

        if ( $apply_coupon_to_cart_filter.val() == "" )
            errors.push( accept_timed_email_offer_actions_params.i18n_select_coupon_to_apply + "<br/>" );
        else {

            data[ 'coupon-id' ]        = $apply_coupon_to_cart_filter.val();
            data[ 'coupon-url' ]       = $apply_coupon_to_cart_filter.find( "option:selected" ).attr( "data-coupon-url" );
            data[ 'coupon-type' ]      = $apply_coupon_to_cart_filter.find( "option:selected" ).attr( "data-coupon-type" );
            data[ 'coupon-type-text' ] = $apply_coupon_to_cart_filter.find( "option:selected" ).attr( "data-coupon-type-text" );
            data[ 'coupon-amount' ]    = $apply_coupon_to_cart_filter.find( "option:selected" ).attr( "data-coupon-amount" );

        }

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container #add-coupon-to-be-applied-to-cart-btn" ).trigger( "construct_additional_data" , [ $action_container , data , errors ] ); // For extensibility

        return $( this );

    } );

    $accept_offer_actions.on( "new_coupon_to_apply" , "#apply-coupons-to-cart-action-container #apply-coupons-to-cart-table" , function( event , data ) {

        event.stopPropagation();

        $.ajax( {
            url      : ajaxurl,
            type     : "POST",
            data     : { action : "teo_generate_coupon_to_apply_entry_markup" , offer_id : offer_id , data : data , 'ajax-nonce' : accept_timed_email_offer_actions_params.nonce_generate_coupon_to_apply_entry_markup },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( data.status == 'success' ) {

                if ( $accept_offer_actions.find( "#apply-coupons-to-cart-table tbody" ).find( ".no-items" ) )
                    $accept_offer_actions.find( "#apply-coupons-to-cart-table tbody" ).find( ".no-items" ).remove();

                $accept_offer_actions.find( "#apply-coupons-to-cart-table tbody" ).append( data.mark_up );

            } else {

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( accept_timed_email_offer_actions_params.i18n_failed_generate_coupon_entry_markup );

        } )
        .always( function() {

            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "reset_fields" );
            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "normal_mode" );

        } );

        return $( this );

    } );

    $accept_offer_actions.on( "click" , "#apply-coupons-to-cart-action-container #add-coupon-to-be-applied-to-cart-btn" , function() {

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "processing_mode" );

        var $action_container = $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ),
            data              = {},
            errors            = [];

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container #add-coupon-to-be-applied-to-cart-btn" ).trigger( "construct_data" , [ $action_container , data , errors ] );

        if ( errors.length > 0 ) {

            var err_msg = '<strong>' + accept_timed_email_offer_actions_params.i18n_fill_form_properly + '</strong><br>';

            for ( var i = 0; i < errors.length; i++ )
                err_msg += errors[ i ];

            vex.dialog.alert( err_msg );

            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "normal_mode" );

        } else
            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container #apply-coupons-to-cart-table" ).trigger( "new_coupon_to_apply" , [ data ] );

    } );


    /*---- Edit coupon -----*/

    $accept_offer_actions.on( "processing_mode" , "#apply-coupons-to-cart-table" , function( event ) {

        event.stopPropagation();

        $accept_offer_actions.find( "#apply-coupons-to-cart-table" ).addClass( "processing_mode" );
        return $( this );

    } );

    $accept_offer_actions.on( "normal_mode" , "#apply-coupons-to-cart-table" , function( event ) {

        event.stopPropagation();

        $accept_offer_actions
            .find( "#apply-coupons-to-cart-table" ).removeClass( "processing_mode" )
            .find( "tr").removeClass( "processing" );

        return $( this );

    } );

    $accept_offer_actions.on( "construct_data_to_edit" , "#apply-coupons-to-cart-table" , function( event , $tr , data ) {

        event.stopPropagation();

        data[ 'coupon-id' ] = $tr.find( ".row-meta .coupon-id" ).text();

        $accept_offer_actions.find( "#apply-coupons-to-cart-table" ).trigger( "construct_additional_data_to_edit" , [ $tr , data ] );
        return $( this );

    } );

    $accept_offer_actions.on( "prepopulate_fields" , "#apply-coupons-to-cart-action-container .fields" , function( event , data ) {

        event.stopPropagation();

        var $this = $( this );

        $this.find( "#apply-coupons-to-cart-filter" ).val( data[ 'coupon-id' ] ).trigger( "chosen:updated" );

        return $this;

    } );

    $accept_offer_actions.on( "edit_mode" , "#apply-coupons-to-cart-action-container .fields .button-field-set" , function( event ) {

        event.stopPropagation();

        $( this ).addClass( "edit-mode" );
        return $( this );

    } );

    $accept_offer_actions.on( "add_mode" , "#apply-coupons-to-cart-action-container .fields .button-field-set" , function( event ) {

        event.stopPropagation();

        $( this ).removeClass( "edit-mode" );
        return $( this );

    } );

    $accept_offer_actions.on( "click" , "#apply-coupons-to-cart-table .edit-coupon" , function() {

        var $tr  = $( this ).closest( "tr"),
            data = {};

        $tr.addClass( "processing" );
        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container #apply-coupons-to-cart-table" ).trigger( "processing_mode" );

        $accept_offer_actions.find( "#apply-coupons-to-cart-table" ).trigger( "construct_data_to_edit" , [ $tr , data ] );

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container .fields" ).trigger( "prepopulate_fields" , [ data ] );

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container .fields .button-field-set" ).trigger( "edit_mode" );

    } );

    $accept_offer_actions.on( "click" , "#apply-coupons-to-cart-action-container #cancel-edit-coupon-to-be-applied-to-cart-btn" , function() {

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "reset_fields" );

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container .fields .button-field-set" ).trigger( "add_mode" );

        $accept_offer_actions.find( "#apply-coupons-to-cart-table" ).trigger( "normal_mode" );

    } );

    $accept_offer_actions.on( "edit_coupon_to_apply" , "#apply-coupons-to-cart-action-container #edit-coupon-to-be-applied-to-cart-btn" , function( event , data ) {

        event.stopPropagation();

        $.ajax( {
            url      : ajaxurl,
            type     : "POST",
            data     : { action : "teo_generate_coupon_to_apply_entry_markup" , offer_id : offer_id , data : data , 'ajax-nonce' : accept_timed_email_offer_actions_params.nonce_generate_coupon_to_apply_entry_markup },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( data.status == 'success' ) {

                if ( $accept_offer_actions.find( "#apply-coupons-to-cart-table tbody" ).find( ".no-items" ) )
                    $accept_offer_actions.find( "#apply-coupons-to-cart-table tbody" ).find( ".no-items" ).remove();

                var $tr = $accept_offer_actions.find( "#apply-coupons-to-cart-table tbody tr.processing" );

                data.mark_up = data.mark_up.replace( '<tr>' , '' );
                data.mark_up = data.mark_up.replace( '</tr>' , '' );

                $tr.html( data.mark_up );

            } else {

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( accept_timed_email_offer_actions_params.i18n_failed_generate_coupon_entry_markup );

        } )
        .always( function() {

            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "reset_fields" );
            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container .fields .button-field-set" ).trigger( "add_mode" );
            $accept_offer_actions.find( "#apply-coupons-to-cart-table" ).trigger( "normal_mode" );
            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "normal_mode" );

        } );

        return $( this );

    } );

    $accept_offer_actions.on( "click" , "#apply-coupons-to-cart-action-container #edit-coupon-to-be-applied-to-cart-btn" , function() {

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "processing_mode" );

        var $action_container = $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ),
            data              = {},
            errors            = [];

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container #add-coupon-to-be-applied-to-cart-btn" ).trigger( "construct_data" , [ $action_container , data , errors ] );

        if ( errors.length > 0 ) {

            var err_msg = '<strong>' + accept_timed_email_offer_actions_params.i18n_fill_form_properly + '</strong><br>';

            for ( var i = 0; i < errors.length; i++ )
                err_msg += errors[ i ];

            vex.dialog.alert( err_msg );

            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "normal_mode" );

        } else
            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container #edit-coupon-to-be-applied-to-cart-btn" ).trigger( "edit_coupon_to_apply" , [ data ] );

    } );


    /*---- Delete coupon -----*/

    $accept_offer_actions.on( "click" , "#apply-coupons-to-cart-table .delete-coupon" , function() {

        var $tr  = $( this ).closest( "tr");

        $tr.addClass( "processing" );
        $accept_offer_actions.find( "#apply-coupons-to-cart-table" ).trigger( "processing_mode" );

        vex.dialog.confirm( {
            message  : accept_timed_email_offer_actions_params.i18n_confirm_delete_coupon ,
            callback : function( value ) {

                if ( value ) {

                    $tr.slideUp( "fast" , function() {

                        $tr.remove();

                        if ( $accept_offer_actions.find( "#apply-coupons-to-cart-table tbody tr" ).length <= 0 ) {

                            $accept_offer_actions.find( "#apply-coupons-to-cart-table tbody" ).append(
                                '<tr class="no-items">' +
                                    '<td class="colspanchange" colspan="' + accept_timed_email_offer_actions_params.apply_coupon_table_total_columns + '">' + accept_timed_email_offer_actions_params.i18n_no_coupons_added + '</td>' +
                                '</tr>'
                            );

                        }

                    } );

                }

                $accept_offer_actions.find( "#apply-coupons-to-cart-table" ).trigger( "normal_mode" );

            }

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Remove Action
     |--------------------------------------------------------------------------
     */

    $accept_offer_actions.on( "click" , ".remove-action" , function() {
        
        var $this = $( this );

        vex.dialog.confirm( {
            message  : accept_timed_email_offer_actions_params.i18n_confirm_delete_action ,
            callback : function( value ) {

                if ( value ) {

                    var $action_container = $this.closest( ".accept-offer-action" ),
                        action_type = $action_container.attr( "data-action-type" );

                    $action_container.slideUp( "fast" , function() {

                        $action_container.remove();
                        $accept_offer_action_types.find( 'option[value="' + action_type + '"]' ).removeAttr( "disabled" );

                        if ( $accept_offer_actions.find( ".accept-offer-action" ).length <= 0 )
                            $accept_offer_actions.append( accept_timed_email_offer_actions_params.i18n_no_action_to_take );

                    } );

                }

            }

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Save Actions
     |--------------------------------------------------------------------------
     */

    $save_accept_offer_actions_btn.on( "construct_action_data" , function( event , data , $this ) {

        event.stopPropagation();

        if ( $this.attr( "data-action-type" ) == "add-products-to-cart" ) {

            var products_data = [];

            $this.find( "#add-products-to-cart-table tbody tr" ).each( function() {

                var $row_meta = $( this ).find( ".row-meta " );

                if ( $row_meta.find( '.product-type' ).length > 0 ) {

                    var $d = {};

                    $d[ 'product-type' ]     = $row_meta.find( '.product-type' ).text();
                    $d[ 'product-id' ]       = $row_meta.find( '.product-id' ).text();
                    $d[ 'product-quantity' ] = $row_meta.find( '.product-quantity' ).text();

                    if ( $row_meta.find( '.product-variation-id' ).length > 0 )
                        $d[ 'product-variation-id' ] = $row_meta.find( '.product-variation-id' ).text();

                    products_data.push( $d );

                }

            } );

            // We have to do this as for some reason, jquery ajax don't pass empty objects or objects with all attributes being empty
            data[ 'add-products-to-cart' ] = products_data.length > 0 ? products_data : null;

        } else if ( $this.attr( "data-action-type" ) == "apply-coupons-to-cart" ) {

            var coupons_data = [];

            $this.find( "#apply-coupons-to-cart-table tbody tr" ).each( function() {

                var $row_meta = $( this ).find( ".row-meta " );

                if ( $row_meta.find( '.coupon-id' ).length > 0 ) {

                    var $d = {};

                    $d[ 'coupon-id' ] = $row_meta.find( '.coupon-id' ).text();

                    coupons_data.push( $d );

                }

            } );

            // We have to do this as for some reason, jquery ajax don't pass empty objects or objects with all attributes being empty
            data[ 'apply-coupons-to-cart' ] = coupons_data.length > 0 ? coupons_data : null;

        }

        return $( this );

    } );

    $save_accept_offer_actions_btn.on( "click" , function( event , options_processing_stat ) {

        $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "processing_mode" );
        $accept_offer_actions.find( "#add-products-to-cart-table" ).trigger( "processing_mode" );

        $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "processing_mode" );
        $accept_offer_actions.find( "#apply-coupons-to-cart-table" ).trigger( "processing_mode" );

        $add_offer_action_controls.trigger( "processing_mode" );

        var data = {};

        $accept_offer_actions.find( ".accept-offer-action" ).each( function() {

            var $this = $( this );
            $save_accept_offer_actions_btn.trigger( "construct_action_data" , [ data , $this ] );

        } );

        if ( $.isEmptyObject( data ) ) // coz if its empty object, jquery won't pass it on POST parameter
            data = null;

        $.ajax( {
            url      : ajaxurl,
            type     : "POST",
            data     : { action : "teo_save_accept_offer_actions" , offer_id : offer_id , data : data , 'ajax-nonce' : accept_timed_email_offer_actions_params.nonce_save_accept_offer_actions },
            dataType : "json"

        } )
        .done( function( data , text_status , jqxhr ) {

            if ( !options_processing_stat ) {

                if ( data.status == 'success' )
                    vex.dialog.alert( accept_timed_email_offer_actions_params.i18n_successfully_saved_action );
                else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            if ( !options_processing_stat ) {

                console.log( jqxhr );
                vex.dialog.alert( accept_timed_email_offer_actions_params.i18n_failed_save_action );

            }

        } )
        .always( function() {

            $accept_offer_actions.find( "#add-products-to-cart-action-container" ).trigger( "normal_mode" );
            $accept_offer_actions.find( "#add-products-to-cart-table" ).trigger( "normal_mode" );

            $accept_offer_actions.find( "#apply-coupons-to-cart-action-container" ).trigger( "normal_mode" );
            $accept_offer_actions.find( "#apply-coupons-to-cart-table" ).trigger( "normal_mode" );

            $add_offer_action_controls.trigger( "normal_mode" );

            if ( options_processing_stat )
                options_processing_stat.accept_offer_actions_processing_status = true;

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Initialization
     |--------------------------------------------------------------------------
     */

    $add_offer_action_controls.trigger( "normal_mode" );

    // Initialize add products to cart action product select field
    $accept_offer_actions.find( "#add-products-to-cart-action-container .product-filter-control" ).val( "" ).chosen( { allow_single_deselect: true , search_contains: true } );
    $accept_offer_actions.find( "#apply-coupons-to-cart-action-container .coupon-filter-control" ).val( "" ).chosen( { allow_single_deselect: true , search_contains: true } );

} );
