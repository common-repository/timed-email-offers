/* global jQuery */
jQuery( document ).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Variables
     |--------------------------------------------------------------------------
     */

    var $offer_conditions_meta_box = $( "#timed-email-offer-conditions-meta-box" ),
        $meta                      = $offer_conditions_meta_box.find( ".meta" ),
        offer_id                   = $.trim( $meta.find( ".offer-id" ).text() ),
        $offer_conditions          = $offer_conditions_meta_box.find( "#offer-conditions" );




    /*
     |--------------------------------------------------------------------------
     | Add Condition Group
     |--------------------------------------------------------------------------
     */

    $offer_conditions.on( "processing_mode" , ".offer-condition-group-controls" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this
            .addClass( 'processing-mode' )
            .find( "select" ).attr( "disabled" , "disabled" ).end()
            .find( "input" ).attr( "disabled" , "disabled" );

        return $this;

    } );

    $offer_conditions.on( "normal_mode" , ".offer-condition-group-controls" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this
            .removeClass( 'processing-mode' )
            .find( "select" ).removeAttr( "disabled" ).end()
            .find( "input" ).removeAttr( "disabled" );

        return $this;

    } );

    $offer_conditions.on( "construct_offer_condition_group_data" , ".offer-condition-group-controls .add-condition-group" , function( event , args , $this , $control_container ) {

        event.stopPropagation();

        if ( $offer_conditions.find( ".offer-condition-group" ).length > 0 )
            args[ 'show-condition-group-logic' ] = true;

        return $this;

    } );

    $offer_conditions.on( "add_offer_condition_group" , ".offer-condition-group-controls .add-condition-group" , function( event , args , $this , $control_container ) {

        event.stopPropagation();

        $.ajax( {
                url : ajaxurl,
                type : "POST",
                data : { action : "teo_generate_offer_condition_group_markup" , offer_id : offer_id , args : args , 'ajax-nonce' : timed_email_offer_conditions_params.nonce_generate_offer_condition_group_markup },
                dataType : "json"
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    if ( $offer_conditions.find( "#no-offer-condition-container" ).length > 0 )
                        $offer_conditions.find( "#no-offer-condition-container" ).remove();

                    $control_container.before( data.mark_up );

                } else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                console.log( jqxhr );
                vex.dialog.alert( timed_email_offer_conditions_params.i18n_failed_generate_condition_markup );

            } )
            .always( function() {

                $control_container.trigger( "normal_mode" );

            } );

        return $this;

    } );

    $offer_conditions.on( "click" , ".offer-condition-group-controls .add-condition-group" , function() {

        var $this              = $( this ),
            $control_container = $this.closest( ".offer-condition-group-controls" ),
            args               = {};

        $control_container.trigger( "processing_mode" );

        $this.trigger( "construct_offer_condition_group_data" , [ args , $this , $control_container ] );

        if ( $.isEmptyObject( args ) )
            args = null; // Coz empty objects aren't passed through POST requests

        $this.trigger( "add_offer_condition_group" , [ args , $this , $control_container ] );

    } );




    /*
     |--------------------------------------------------------------------------
     | Remove Condition Group
     |--------------------------------------------------------------------------
     */

    $offer_conditions.on( "click" , ".offer-condition-group .offer-condition-group-actions .remove-condition-group" , function() {

        var condition_group = $( this ).closest( ".offer-condition-group" );

        vex.dialog.confirm( {
            message  : timed_email_offer_conditions_params.i18n_confirm_remove_condition_group ,
            callback : function( value ) {

                if ( value ) {

                    if ( condition_group.is( ":first-child" ) ) {

                        condition_group.next( ".offer-condition-group-logic" ).slideUp( "fast" , function() {

                            condition_group.next( ".offer-condition-group-logic" ).remove();

                        } );

                    } else {

                        condition_group.prev( ".offer-condition-group-logic" ).slideUp( "fast" , function() {

                            condition_group.prev( ".offer-condition-group-logic" ).remove();

                        } );

                    }

                    condition_group.slideUp( "fast" , function() {

                        if ( $offer_conditions.find( ".offer-condition-group").length == 1 ) {

                            condition_group.replaceWith(
                                '<div id="no-offer-condition-container">' +
                                    '<p id="no-condition-message">' + timed_email_offer_conditions_params.i18n_no_condition_set + '</p>' +
                                '</div>'
                            );

                        } else
                            condition_group.remove();

                    } );

                }

            }

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Add Condition
     |--------------------------------------------------------------------------
     */

    $offer_conditions.on( "processing_mode" , ".offer-condition-controls" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this
            .addClass( "processing-mode" )
            .find( "select" ).attr( "disabled" , "disabled" ).end()
            .find( "input" ).attr( "disabled" , "disabled" );

        return $this;

    } );

    $offer_conditions.on( "neutral_mode" , ".offer-condition-controls" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this
            .removeClass( "processing-mode" )
            .find( "select" ).removeAttr( "disabled" ).end()
            .find( "input" ).removeAttr( "disabled" );

        return $this;

    } );

    $offer_conditions.on( "adding_condition_mode" , ".offer-condition-controls" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this.addClass( "adding-condition-mode" );

        return $this;

    } );

    $offer_conditions.on( "normal_mode" , ".offer-condition-controls" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this
            .removeClass( "adding-condition-mode" );

        return $this;

    } );

    $offer_conditions.on( "construct_new_timed_email_offer_condition_markup" , ".offer-condition-controls .add-condition" , function( event , args , errors , $this , $controls_container ) {

        event.stopPropagation();

        var $this                  = $( this ),
            $offer_condition_group = $this.closest( ".offer-condition-group" );

        if ( $controls_container.find( ".condition-types" ).val() == "" )
            errors.push( timed_email_offer_conditions_params.i18n_please_specify_offer_condition_type );
        else
            args[ 'condition-type' ] = $controls_container.find( ".condition-types" ).val();

        if ( $offer_condition_group.find( ".offer-condition" ).length > 0 )
            args[ 'show-condition-logic' ] = true;

        return $this;

    } );

    $offer_conditions.on( "click" , ".offer-condition-controls .show-add-condition-controls" , function() {

        var $this               = $( this ),
            $controls_container = $this.closest( ".offer-condition-controls" );

        $controls_container.trigger( "adding_condition_mode" );

    } );

    $offer_conditions.on( "click" , ".offer-condition-controls .hide-add-condition-controls" , function() {

        var $this               = $( this ),
            $controls_container = $this.closest( ".offer-condition-controls" );

        $controls_container.trigger( "normal_mode" );

    } );

    $offer_conditions.on( "click" , ".offer-condition-controls .add-condition" , function() {

        var $this               = $( this ),
            $controls_container = $this.closest( ".offer-condition-controls" ),
            args                = {},
            errors              = [];

        $controls_container.trigger( "processing_mode" );

        $this.trigger( "construct_new_timed_email_offer_condition_markup" , [ args , errors , $this , $controls_container ] );

        if ( errors.length > 0 ) {

            var err_msg = '<strong>' + timed_email_offer_conditions_params.i18n_please_fill_form_properly + '</strong><br/>';

            for ( var i = 0 ; i < errors.length ; i++ )
                err_msg += errors[ i ] + "<br/>";

            vex.dialog.alert( err_msg );

            $controls_container.trigger( "neutral_mode" );

        } else {

            if ( $.isEmptyObject( args ) )
                args = null; // Coz jquery do not pass empty objects to ajax request parameters

            $.ajax( {
                url : ajaxurl,
                type : "POST",
                data : { action : "teo_generate_offer_condition_markup" , offer_id : offer_id , args : args , 'ajax-nonce' : timed_email_offer_conditions_params.nonce_generate_offer_condition_markup },
                dataType : "json"
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    if ( $controls_container.siblings( ".empty-condition-group-container" ).length >= 0 )
                        $controls_container.siblings( ".empty-condition-group-container" ).remove();

                    $controls_container.before( data.mark_up );

                } else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                console.log( jqxhr );
                vex.dialog.alert( timed_email_offer_conditions_params.i18n_failed_generate_offer_condition_markup );

            } )
            .always( function() {

                $controls_container.trigger( "neutral_mode" );
                $controls_container.trigger( "normal_mode" );

            } );

        }

    } );




    /*
     |--------------------------------------------------------------------------
     | Remove Condition
     |--------------------------------------------------------------------------
     */

    $offer_conditions.on( "click" , ".offer-condition .remove-condition" , function() {

        var $offer_condition = $( this ).closest( ".offer-condition"),
            $condition_group = $offer_condition.closest( ".offer-condition-group" );

        vex.dialog.confirm( {
            message  : timed_email_offer_conditions_params.i18n_confirm_remove_condition ,
            callback : function( value ) {

                if ( value ) {

                    // Different approach of finding out if this is the first condition within a condition group
                    if ( $offer_condition.prev( ".offer-condition-logic" ).length <= 0 ) {

                        $offer_condition.next( ".offer-condition-logic" ).slideUp( "fast" , function() {

                            $offer_condition.next( ".offer-condition-logic" ).remove();

                        } );

                    } else {

                        $offer_condition.prev( ".offer-condition-logic" ).slideUp( "fast" , function() {

                            $offer_condition.prev( ".offer-condition-logic" ).remove();

                        } );

                    }

                    $offer_condition.slideUp( "fast" , function() {

                        if ( $condition_group.find( ".offer-condition" ).length == 1 ) {

                            $offer_condition.replaceWith(
                                '<div class="empty-condition-group-container">' +
                                    '<p class="empty-condition-group-message">' + timed_email_offer_conditions_params.i18n_empty_condition_group + '</p>' +
                                '</div>'
                            );

                        } else
                            $offer_condition.remove();

                    } );

                }

            }

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Product Quantity In Cart Condition
     |--------------------------------------------------------------------------
     */

    // Controls Init

    $offer_conditions.on( "reset_fields" , ".offer-condition .fields" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this
            .find( ".product-in-order option:first" ).attr( "selected" , "selected" ).trigger( "change" ).trigger( "chosen:updated" ).end()
            .find( ".product-in-order-quantity-condition option:first" ).attr( "selected" , "selected" ).end()
            .find( ".product-in-order-quantity" ).val( 1 );

        return $this;

    } );

    $offer_conditions.on( "processing_mode" , ".offer-condition" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this
            .addClass( "processing-mode" )
            .find( "select" ).attr( "disabled" , "disabled" ).trigger( "chosen:updated" ).end()
            .find( "input" ).attr( "disabled" , "disabled" );

        return $this;

    } );

    $offer_conditions.on( "neutral_mode" , ".offer-condition" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this
            .removeClass( "processing-mode" )
            .find( "select" ).removeAttr( "disabled").trigger( "chosen:updated" ).end()
            .find( "input" ).removeAttr( "disabled" );

        return $this;

    } );

    $offer_conditions.on( 'DOMNodeInserted' , function( e ) {

        var $new_element = $( e.target );

        if ( $new_element.attr( "class" ) == "offer-condition" && $new_element.attr( "data-condition-type" ) == "product-quantity" ) {

            $new_element.find( ".product-in-order" ).chosen( { allow_single_deselect: true , search_contains: true } );

        }

        return $( this );

    } );

    $offer_conditions.on( "render_additional_product_data" , ".offer-condition" , function( event , data , variation_id ) {

        event.stopPropagation();

        var $this = $( this );

        if ( data.product_data.product_type == "variable" ) {

            var $product_in_cart_field_set = $this.find( ".product-in-order-field-set" ),
                any_selected               = ( variation_id && $.inArray( 'any' , variation_id ) != -1 ) ? 'selected="selected"' : '',
                variations_control_markup  = '<select class="product-in-order-variations" multiple style="min-width: 340px;" data-placeholder="' + timed_email_offer_conditions_params.i18n_please_select_variation + '">' +
                                                '<option value="any" ' + any_selected + '>' + timed_email_offer_conditions_params.i18n_any_variation + '</option>';

            for ( var key in data.product_data.product_variations  ) {

                if ( data.product_data.product_variations.hasOwnProperty( key ) ) {

                    var variation = data.product_data.product_variations[ key ],
                        disabled  = '',
                        selected  = '';

                    if ( variation.disabled || !variation.visible )
                        disabled = 'disabled="disabled"';

                    //if ( variation_id && variation_id == variation.value )
                    if ( variation_id && $.inArray( variation.value , variation_id ) != -1 )
                        selected = 'selected="selected"';

                    variations_control_markup += '<option value="' + variation.value + '" ' + disabled + ' ' + selected + '>' + variation.text + '</option>';

                }

            }

            variations_control_markup += '</select>';

            $product_in_cart_field_set.append(
                '<div class="additional-product-data">' +
                    '<label>' + timed_email_offer_conditions_params.i18n_product_variations + '</label> ' +
                    variations_control_markup +
                '</div>'
            );

            $product_in_cart_field_set.find( ".product-in-order-variations" ).chosen( { search_contains: true } );

        }

        return $this;

    } );

    $offer_conditions.on( "change" , ".offer-condition .product-in-order-field-set .product-in-order" , function( event , variation_id ) {

        event.stopPropagation();

        var $this                      = $( this ),
            $offer_condition           = $this.closest( ".offer-condition" ),
            $product_in_cart_field_set = $offer_condition.find( ".product-in-order-field-set" ),
            $meta_container            = $product_in_cart_field_set.find( ".meta" );

        $product_in_cart_field_set.find( ".additional-product-data" ).slideUp( 'fast' , function() {

            $product_in_cart_field_set.find( ".additional-product-data" ).remove();

        } );

        if ( $this.val() != "" ) {

            $offer_condition.trigger( "processing_mode" );

            // Get additional data for the current product

            $.ajax( {
                url      : ajaxurl,
                type     : "POST",
                data     : { action : "teo_get_product_additional_info" , product_id : $this.val() , 'ajax-nonce' : timed_email_offer_conditions_params.nonce_get_product_additional_info },
                dataType : "json"
            } )
            .done( function( data , text_response , jqxhr ) {

                if ( data.status == 'success' ) {

                    $meta_container.find( ".product-type" ).text( data.product_data.product_type );
                    $offer_condition.trigger( "render_additional_product_data" , [ data , variation_id ] );

                } else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_response , error_thrown ) {

                console.log( jqxhr );
                vex.dialog.alert( timed_email_offer_conditions_params.i18n_failed_retrieve_product_data );

            } )
            .always( function() {

                $offer_condition.trigger( "neutral_mode" );

            } );

        }

        return $this;

    } );


    // Add Product

    $offer_conditions.on( "construct_product_quantity_in_order_condition_entry_markup" , ".offer-condition .button-field-set .add-product-in-order-btn" , function( event , data , errors , $this , $offer_condition ) {

        event.stopPropagation();

        data[ 'product-type' ] = $offer_condition.find( ".product-in-order-field-set .meta .product-type" ).text();

        if ( $offer_condition.find( ".product-in-order" ).val() == "" )
            errors.push( timed_email_offer_conditions_params.i18n_please_select_product );
        else
            data[ 'product-id' ] = $offer_condition.find( ".product-in-order" ).val();

        if ( $offer_condition.find( ".product-in-order-variations" ).length > 0 ) {

            if ( !$offer_condition.find( ".product-in-order-variations" ).val() )
                errors.push( timed_email_offer_conditions_params.i18n_please_select_product_variation );
            else
                data[ 'product-variation-id' ] = $offer_condition.find( ".product-in-order-variations" ).val();

        }

        if ( $offer_condition.find( ".product-in-order-quantity-condition" ).val() == "" )
            errors.push( timed_email_offer_conditions_params.i18n_please_select_product_quantity_condition );
        else
            data[ 'product-quantity-condition' ] = $offer_condition.find( ".product-in-order-quantity-condition" ).val();

        if ( $offer_condition.find( ".product-in-order-quantity" ).val() == "" )
            errors.push( timed_email_offer_conditions_params.i18n_please_select_product_quantity );
        else
            data[ 'product-quantity' ] = $offer_condition.find( ".product-in-order-quantity" ).val()

        return $this;

    } );

    $offer_conditions.on( "add_product_quantity_in_order_condition_entry" , ".offer-condition .product-quantity-table" , function( event , data , $offer_condition , $product_quantity_table ) {

        event.stopPropagation();

        $.ajax( {
            url      : ajaxurl,
            type     : "POST",
            data     : { action : "teo_generate_product_quantity_in_order_entry_markup" , offer_id : offer_id , data : data , 'ajax-nonce' : timed_email_offer_conditions_params.nonce_generate_product_quantity_in_order_entry_markup },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( data.status == 'success' ) {

                if ( $product_quantity_table.find( "tbody .no-items" ).length > 0 )
                    $product_quantity_table.find( "tbody .no-items" ).remove();

                $product_quantity_table.find( "tbody" ).append( data.mark_up );

            } else {

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( timed_email_offer_conditions_params.i18n_failed_generate_product_quantity_in_cart_entry_markup );

        } )
        .always( function() {

            $offer_condition.trigger( "neutral_mode" );
            $offer_condition.find( ".fields" ).trigger( "reset_fields" );

        } );

        return $( this );

    } );

    $offer_conditions.on( "click" , ".offer-condition .button-field-set .add-product-in-order-btn" , function() {

        var $this                   = $( this ),
            $offer_condition        = $this.closest( ".offer-condition" ),
            $product_quantity_table = $offer_condition.find( ".product-quantity-table" ),
            data                    = {},
            errors                  = [];

        $offer_condition.trigger( "processing_mode" );

        $this.trigger( "construct_product_quantity_in_order_condition_entry_markup" , [ data , errors , $this , $offer_condition ] );

        if ( errors.length > 0 ) {

            var err_msg = '<strong>' + timed_email_offer_conditions_params.i18n_please_fill_form_properly + '</strong><br/>';

            for ( var i = 0 ; i < errors.length ; i++ )
                err_msg += errors[ i ] + '<br/>';

            vex.dialog.alert( err_msg );

            $offer_condition.trigger( "neutral_mode" );

        } else
            $product_quantity_table.trigger( "add_product_quantity_in_order_condition_entry" , [ data , $offer_condition , $product_quantity_table ] );

    } );


    // Edit Product

    $offer_conditions.on( "prepopulate_product_quantity_in_cart_fields" , ".offer-condition .fields" , function( event , data ) {

        event.stopPropagation();

        var $this        = $( this ),
            variation_id = ( data.hasOwnProperty( "product-variation-id" ) ) ? data[ 'product-variation-id' ] : null;

        $this.find( ".product-in-order-field-set .meta .product-type" ).text( data[ 'product-type' ] );
        $this.find( ".product-in-order-field-set .product-in-order" ).val( data[ 'product-id' ] ).trigger( "change" , [ variation_id ] ).trigger( "chosen:updated" );
        $this.find( ".product-in-order-quantity-condition-field-set .product-in-order-quantity-condition" ).val( data[ 'product-quantity-condition' ] );
        $this.find( ".product-in-order-quantity-field-set .product-in-order-quantity" ).val( data[ 'product-quantity' ] );

        return $this;

    } );

    $offer_conditions.on( "construct_product_data_to_edit" , ".offer-condition .product-quantity-table tbody tr .row-controls .edit-product" , function( event , data , $tr ) {

        event.stopPropagation();

        var $row_meta = $tr.find( ".row-meta" );

        data[ 'product-type' ] = $row_meta.find( ".product-type" ).text();
        data[ 'product-id' ]   = $row_meta.find( ".product-id" ).text();

        if ( $row_meta.find( ".product-variation-id" ).length > 0 ) {

            var product_variation_ids = [];

            $row_meta.find( ".product-variation-id .variation-id" ).each( function() {

                if ( $( this ).text() != 'any' )
                    product_variation_ids.push( parseInt( $( this ).text() , 10 ) );
                else
                    product_variation_ids.push( $( this ).text() );

            } );

            data[ 'product-variation-id' ] = product_variation_ids;

        }

        data[ 'product-quantity-condition' ] = $row_meta.find( ".product-quantity-condition" ).text();
        data[ 'product-quantity' ]           = $row_meta.find( ".product-quantity" ).text();

        return $( this );

    } );

    $offer_conditions.on( "edit_product_quantity_in_cart_condition_entry" , ".offer-condition .product-quantity-table" , function( event , data , $offer_condition , $product_quantity_table ) {

        event.stopPropagation();

        $.ajax( {
            url      : ajaxurl,
            type     : "POST",
            data     : { action : "teo_generate_product_quantity_in_order_entry_markup" , offer_id : offer_id , data : data , 'ajax-nonce' : timed_email_offer_conditions_params.nonce_generate_product_quantity_in_order_entry_markup },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( data.status == 'success' ) {

                if ( $product_quantity_table.find( "tbody .no-items" ).length > 0 )
                    $product_quantity_table.find( "tbody .no-items" ).remove();

                data.mark_up = data.mark_up.replace( '<tr>' , '' );
                data.mark_up = data.mark_up.replace( '</tr>' , '' );

                $product_quantity_table.find( "tbody tr.processing-mode" ).html( data.mark_up );

            } else {

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( timed_email_offer_conditions_params.i18n_failed_generate_product_quantity_in_cart_entry_markup );

        } )
        .always( function() {

            $offer_condition.trigger( "neutral_mode" );

            $offer_condition.find( ".fields" ).trigger( "reset_fields" );

            $offer_condition.find( ".fields .button-field-set" ).removeClass( "edit-mode" );

            $product_quantity_table
                .removeClass( "processing-mode" )
                .find( "tr" ).removeClass( "processing-mode" );

        } );

        return $( this );

    } );

    $offer_conditions.on( "click" , ".offer-condition .product-quantity-table tbody tr .row-controls .edit-product" , function() {

        var $this                   = $( this ),
            $offer_condition        = $this.closest( ".offer-condition" ),
            $product_quantity_table = $this.closest( ".product-quantity-table" ),
            $tr                     = $this.closest( "tr" ),
            data                    = {};

        $tr.addClass( "processing-mode" );
        $product_quantity_table.addClass( "processing-mode" );

        $this.trigger( "construct_product_data_to_edit" , [ data , $tr ] );

        $offer_condition.find( ".fields .button-field-set" ).addClass( "edit-mode" );
        $offer_condition.find( ".fields" ).trigger( "prepopulate_product_quantity_in_cart_fields" , [ data ] );

    } );

    $offer_conditions.on( "click" , ".offer-condition .button-field-set .cancel-edit-product-in-order-btn" , function() {

        var $this                   = $( this ),
            $offer_condition        = $this.closest( ".offer-condition" ),
            $product_quantity_table = $offer_condition.find( ".product-quantity-table" );

        $offer_condition.find( ".fields" ).trigger( "reset_fields" );

        $offer_condition.find( ".fields .button-field-set" ).removeClass( "edit-mode" );

        $product_quantity_table
            .removeClass( "processing-mode" )
            .find( "tr" ).removeClass( "processing-mode" );

    } );

    $offer_conditions.on( "click" , ".offer-condition .button-field-set .edit-product-in-order-btn" , function() {

        var $this                   = $( this ),
            $offer_condition        = $this.closest( ".offer-condition" ),
            $product_quantity_table = $offer_condition.find( ".product-quantity-table" ),
            data                    = {},
            errors                  = [];

        $offer_condition.trigger( "processing_mode" );

        $offer_condition.find( ".add-product-in-order-btn" ).trigger( "construct_product_quantity_in_order_condition_entry_markup" , [ data , errors , $this , $offer_condition ] );

        if ( errors.length > 0 ) {

            var err_msg = '<strong>' + timed_email_offer_conditions_params.i18n_please_fill_form_properly + '</strong><br/>';

            for ( var i = 0 ; i < errors.length ; i++ )
                err_msg += errors[ i ] + '<br/>';

            vex.dialog.alert( err_msg );

            $offer_condition.trigger( "neutral_mode" );

        } else
            $product_quantity_table.trigger( "edit_product_quantity_in_cart_condition_entry" , [ data , $offer_condition , $product_quantity_table ] );

    } );


    // Remove Product

    $offer_conditions.on( "click" , ".offer-condition .product-quantity-table tbody tr .row-controls .delete-product" , function() {

        var $this                   = $( this ),
            $product_quantity_table = $this.closest( ".product-quantity-table" ),
            $tr                     = $this.closest( "tr" );

        $tr.addClass( "processing-mode" );
        $product_quantity_table.addClass( "processing-mode" );

        vex.dialog.confirm( {
            message  : timed_email_offer_conditions_params.i18n_confirm_remove_product,
            callback : function( value ) {

                if ( value ) {

                    $tr.slideUp( "fast" , function() {

                        $tr.remove();

                        if ( $product_quantity_table.find( "tbody tr" ).length <= 0 ) {

                            $product_quantity_table.find( "tbody" ).append(
                                '<tr class="no-items">' +
                                    '<td class="colspanchange" colspan="' + timed_email_offer_conditions_params.product_quantity_table_total_columns + '">' + timed_email_offer_conditions_params.i18n_no_products_added + '</td>' +
                                '</tr>'
                            );

                        }

                    } );

                }

                $tr.removeClass( "processing-mode" );
                $product_quantity_table.removeClass( "processing-mode" );

            }

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Save Conditions
     |--------------------------------------------------------------------------
     */

    $( "#save-offer-conditions" ).on( "construct_timed_email_offer_conditions_data" , function( event , data ) {

        event.stopPropagation();

        var $save_offer_btn = $( this );

        $offer_conditions.find( ".offer-condition-group" ).each( function() {

            var $current_condition_group = $( this ),
                condition_group_data     = {},
                conditions_data          = { conditions : [] };

            if ( $current_condition_group.find( ".offer-condition" ).length > 0 ) {

                $current_condition_group.find( ".offer-condition" ).each( function() {

                    var $current_condition = $( this ),
                        condition_data     = {};

                    if ( $current_condition.attr( "data-condition-type" ) == 'product-quantity' ) {

                        var product_conditions = [];

                        $current_condition.find( ".product-quantity-table tbody tr" ).each( function() {

                            var $tr_meta               = $( this ).find( ".row-meta" ),
                                product_condition_data = {};

                            if ( $tr_meta.length > 0 ) {

                                product_condition_data[ 'product-type' ] = $tr_meta.find( '.product-type' ).text();
                                product_condition_data[ 'product-id' ]   = $tr_meta.find( '.product-id' ).text();

                                if ( $tr_meta.find( '.product-variation-id .variation-id' ).length > 0 ) {

                                    var product_variation_id = [];

                                    $tr_meta.find( '.product-variation-id .variation-id' ).each( function() {

                                        if ( $( this ).text() == 'any' )
                                            product_variation_id.push( $( this ).text() );
                                        else
                                            product_variation_id.push( parseInt( $( this ).text() , 10 ) );

                                    } );

                                    product_condition_data[ 'product-variation-id' ] = product_variation_id;

                                }

                                product_condition_data[ 'product-quantity-condition' ] = $tr_meta.find( '.product-quantity-condition' ).text();
                                product_condition_data[ 'product-quantity' ]           = $tr_meta.find( '.product-quantity' ).text();

                                product_conditions.push( product_condition_data );

                            }

                        } );

                        if ( product_conditions.length > 0 ) {

                            if ( $current_condition.prev( '.offer-condition-logic' ).length > 0 && conditions_data.conditions.length > 0 )
                                condition_data[ 'condition-logic' ] = $current_condition.prev( '.offer-condition-logic' ).find( '.condition-logic' ).val();

                            condition_data[ 'condition-type' ]     = 'product-quantity';
                            condition_data[ 'product-conditions' ] = product_conditions;

                        }

                    } else
                        $save_offer_btn.trigger( "construct_condition_data" , [ $current_condition , condition_data , conditions_data ] );

                    if ( !$.isEmptyObject( condition_data ) )
                        conditions_data.conditions.push( condition_data );

                } );

            }

            if ( conditions_data.conditions.length > 0 ) {

                if ( $current_condition_group.prev( '.offer-condition-group-logic' ).length > 0 && data.length > 0 )
                    condition_group_data[ 'condition-group-logic' ] = $current_condition_group.prev( '.offer-condition-group-logic' ).find( '.condition-group-logic' ).val();

                condition_group_data[ 'conditions' ] = conditions_data.conditions;

            }

            if ( !$.isEmptyObject( condition_group_data ) )
                data.push( condition_group_data );

        } );

        return $save_offer_btn;

    } );

    $( "#save-offer-conditions").on( "click" , function( event , options_processing_stat ) {

        var $this = $( this ),
            data  = [];

        $this.closest( "#offer-condition-general-controls" ).addClass( "processing-mode" );
        $this.attr( "disabled" , "disabled" );

        $this.trigger( 'construct_timed_email_offer_conditions_data' , [ data ] );

        if ( $.isEmptyObject( data ) )
            data = null;

        $.ajax( {
            url : ajaxurl,
            type : "POST",
            data : { action : "teo_save_timed_email_offer_conditions" , offer_id : offer_id , data : data , 'ajax-nonce' : timed_email_offer_conditions_params.nonce_save_timed_email_offer_conditions },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( !options_processing_stat ) {

                if ( data.status == 'success' )
                    vex.dialog.alert( timed_email_offer_conditions_params.i18n_success_save_conditions );
                else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            if ( !options_processing_stat ) {

                console.log( jqxhr );
                vex.dialog.alert( timed_email_offer_conditions_params.i18n_failed_save_conditions );

            }

        } )
        .always( function() {

            $this.closest( "#offer-condition-general-controls" ).removeClass( "processing-mode" );
            $this.removeAttr( "disabled" );

            if ( options_processing_stat )
                options_processing_stat.offer_condition_processing_status = true;

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Initialization
     |--------------------------------------------------------------------------
     */

    $offer_conditions.find( ".offer-condition-group .offer-condition .product-in-order-field-set .product-in-order" ).chosen( { allow_single_deselect: true , search_contains: true } );

    // Initialize Vex Library
    vex.defaultOptions.className = 'vex-theme-plain';

} );