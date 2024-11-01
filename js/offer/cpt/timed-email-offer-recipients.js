/* global JQuery */
jQuery( document).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Variables
     |--------------------------------------------------------------------------
     */
    var $offer_recipients_table = $( "#offer-recipients-table" );




    /*
     |--------------------------------------------------------------------------
     | Get offer recipients data
     |--------------------------------------------------------------------------
     */

    $offer_recipients_table.on( "retrieving_data_mode" , function( event ) {

        event.stopPropagation();

        $( this ).addClass( "retrieving-data-mode" );

        return $( this );

    } );

    $offer_recipients_table.on( "processing_mode" , function( event , $tr ) {

        event.stopPropagation();

        $( this ).addClass( "processing-mode" );
        $tr.addClass( "processing-row" ).find( ".column-controls" ).prepend( '<span class="spinner"></span>' );

        return $( this );

    } );

    $offer_recipients_table.on( "normal_mode" , function( event ) {

        event.stopPropagation();

        $( this )
            .removeClass( "retrieving-data-mode" )
            .removeClass( "processing-mode" )
                .find( "tr" )
            .removeClass( "processing-row" )
                .find( ".column-controls .spinner" )
                .remove();

        return $( this );

    } );

    $offer_recipients_table.on( "click" , ".view-recipient-details" , function( event ) {

        var $this    = $( this ),
            $tr      = $this.closest( 'tr' ),
            offer_id = $this.attr( 'data-offer-id' ),
            order_id = $this.attr( 'data-order-id' );

        $tr.addClass( 'processing-row' );
        $offer_recipients_table.find( '.dashicons' ).css( 'visibility' , 'hidden' );

        $.ajax( {
            'url'      : ajaxurl,
            'type'     : 'POST',
            'data'     : { action : 'teo_generate_offer_recipient_data_popup_markup' , offer_id : offer_id , order_id : order_id , 'ajax-nonce' : timed_email_offer_recipients_params.nonce_generate_offer_recipient_data_popup_markup },
            'dataType' : 'json'
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( data.status == 'success' ) {

                $.magnificPopup.open( {
                    items               : { src: data.markup , type : 'inline' },
                    closeOnContentClick : false,
                    closeOnBgClick      : true,
                    enableEscapeKey     : true,
                    showCloseBtn        : true
                } );

            } else {

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( timed_email_offer_recipients_params.i18n_failed_retrieving_recipient_data  );

        } )
        .always( function() {

            $tr.removeClass( 'processing-row' );
            $offer_recipients_table.find( '.dashicons' ).css( 'visibility' , 'visible' );

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Remove recipient scheduled email
     |--------------------------------------------------------------------------
     */

    $( "body" ).on( "click" , ".delete-recipient-schedule" , function( event ) {

        var $this                 = $( this ),
            $tr                   = $this.closest( 'tr' ),
            $table                = $tr.closest( 'table' ),
            offer_id              = $.trim( $this.attr( 'data-offer-id' ) ),
            order_id              = $.trim( $this.attr( 'data-order-id' ) ),
            unique_email_token    = $.trim( $this.attr( 'data-unique-email-token' ) );

        $tr.addClass( 'processing-row' );
        $table.find( '.dashicons' ).css('display' , 'none' );

        vex.dialog.confirm( {
            message : timed_email_offer_recipients_params.i18n_confirm_remove_scheduled_email,
            callback : function( value ) {

                if ( value ) {

                    $.ajax( {
                        url      : ajaxurl,
                        type     : 'POST',
                        data     : { action : 'teo_remove_recipient_scheduled_email' , offer_id : offer_id , order_id : order_id , unique_email_token : unique_email_token , 'ajax-nonce' : timed_email_offer_recipients_params.nonce_remove_recipient_scheduled_email },
                        dataType : 'json'
                    } )
                    .done( function( data , $text_status , $jqxhr ) {

                        if ( data.status == 'success' ) {

                            $tr.slideUp( "fast" , function() {

                                $tr.remove();

                                if ( $table.find( "tbody tr" ).length <= 0 ) {

                                    $table.find( "tbody" ).append(
                                        '<tr class="no-scheduled-emails">' +
                                            '<td colspan="' + timed_email_offer_recipients_params.recipient_schedules_table_total_columns + '">' + timed_email_offer_recipients_params.i18n_no_scheduled_emails + '</td>' +
                                        '</tr>'
                                    );

                                }

                            } );

                        } else {

                            console.log( data );
                            vex.dialog.alert( data.error_message );

                        }

                    } )
                    .fail( function( $jqxhr , $text_status , $error_thrown ) {

                        console.log( $jqxhr );
                        vex.dialog.alert( timed_email_offer_recipients_params.i18n_failed_remove_scheduled_email );

                    } )
                    .always( function() {

                        $tr.removeClass( 'processing-row' );
                        $table.find( '.dashicons' ).css('display', 'inline-block' );

                    } );

                } else {

                    $tr.removeClass( 'processing-row' );
                    $table.find( '.dashicons' ).css('display', 'inline-block' );

                }

            }
        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Remove recipient from offer
     |--------------------------------------------------------------------------
     */

    $offer_recipients_table.on( "click" , ".delete-recipient" , function() {

        var $this    = $( this ),
            $tr      = $this.closest( 'tr' ),
            offer_id = $.trim( $this.attr( 'data-offer-id' ) ),
            order_id = $.trim( $this.attr( 'data-order-id' ) );

        $tr.addClass( 'processing-row' );
        $offer_recipients_table.find( '.dashicons' ).css( 'display' , 'none' );

        vex.dialog.confirm( {
            message: timed_email_offer_recipients_params.i18n_confirm_remove_recipient,
            callback : function( value ) {

                if ( value ) {

                    $.ajax( {
                        url      : ajaxurl,
                        type     : 'POST',
                        data     : { action : 'teo_remove_recipient_from_offer' , offer_id : offer_id , order_id : order_id , 'ajax-nonce' : timed_email_offer_recipients_params.nonce_remove_recipient_from_offer },
                        dataType : 'json'
                    } )
                    .done( function( data , text_status , jqxhr ) {

                        if ( data.status == 'success' ) {

                            offer_recipients_datatable_handle.ajax.reload( null , false );

                        } else {

                            console.log( data );
                            vex.dialog.alert( data.error_message );

                        }

                    } )
                    .fail( function( jqxhr , text_status , error_thrown ) {

                        console.log( jqxhr );
                        vex.dialog.alert( timed_email_offer_recipients_params.i18n_failed_remove_offer_recipient );

                    } )
                    .always( function() {

                        $tr.removeClass( 'processing-row' );
                        $offer_recipients_table.find( '.dashicons' ).css( 'display' , 'inline-block' );

                    } );

                } else {

                    $tr.removeClass( 'processing-row' );
                    $offer_recipients_table.find( '.dashicons' ).css( 'display' , 'inline-block' );

                }

            }
        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Recipient Offer Response DataTable Custom Filter
     |--------------------------------------------------------------------------
     */

    $( '#recipient-response-status-filter' ).change( function( event ) {

        var $this = $( this );

        offer_recipients_datatable_config[ "ajax" ][ "data" ][ "response_status_filter" ] = $this.val();

        offer_recipients_datatable_handle.destroy();
        offer_recipients_datatable_handle = $offer_recipients_table.DataTable( offer_recipients_datatable_config );

    } );

} );

jQuery( window ).load( function() {

    /*
     |--------------------------------------------------------------------------
     | Initialize Survey Questions Datatable
     |--------------------------------------------------------------------------
     |
     | - We need to run this on window.load, window.load runs after document.ready
     |   window.load runs after all assets are loaded ( css , js, images , etc...)
     |   document.ready run after dom is fully constructed ( regardless if assets are done loading )
     |
     | - The reason mainly is for extensibility with TEOP, we need TEOP to hook into
     |   'before_initialize_offer_templates_datatable' event, so TEOP needs to attached a
     |   callback first on document ready then TEO executes the event on window load
     */

    var $offer_recipients_meta_box = jQuery( "#timed-email-offer-recipients-meta-box" ),
        $offer_recipients_table    = $offer_recipients_meta_box.find( "#offer-recipients-table" ),
        $offer_recipients_meta     = $offer_recipients_meta_box.find( ".meta" ),
        offer_id                   = jQuery.trim( $offer_recipients_meta.find( ".offer-id" ).text() );

    $offer_recipients_meta_box.on( "before_initialize_offer_recipients_datatable" , function() {

        // Set offer id
        offer_recipients_datatable_config[ "ajax" ][ "data" ][ "offer_id" ] = offer_id;

    } );

    // Trigger custom event to allow external plugins to modify the data tables config
    $offer_recipients_meta_box.trigger( "before_initialize_offer_recipients_datatable" );

    // Initialize offer recipients data table.
    offer_recipients_datatable_handle = $offer_recipients_table.DataTable( offer_recipients_datatable_config );

} );
