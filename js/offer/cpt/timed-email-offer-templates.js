/* global jQuery */
jQuery( document ).ready( function( $ ) {
    
    /*
     |--------------------------------------------------------------------------
     | Variables
     |--------------------------------------------------------------------------
     */

    var $templates_meta_box            = $( "#timed-email-offer-templates-meta-box" ),
        $meta                          = $templates_meta_box.find( ".meta" ),
        offer_id                       = $.trim( $meta.find( ".offer-id" ).text() ),
        $offer_templates_controls      = $templates_meta_box.find( "#offer-templates-controls" ),
        $wc_wrap_chkbox                = $offer_templates_controls.find( "#wrap-with-wc-header-footer" ),
        $show_offer_templates_controls = $templates_meta_box.find( "#show-offer-templates-controls" ),
        $offer_templates               = $templates_meta_box.find( "#offer-templates" ),
        $send_test_email_popup         = $( "#send-test-email-popup" ),
        $template_legend               = $( "#template-legend" );
    
    
    
    
    /*
     |--------------------------------------------------------------------------
     | Initialization
     |--------------------------------------------------------------------------
     */

    // WP_Editor bug
    // WP_Editor breaks if you add it to the meta box and the container meta box is moved
    $( '.meta-box-sortables' ).sortable( {
        disabled: true
    } );

    $( '.postbox .hndle' ).css( 'cursor' , 'pointer' );



    
    /*
     |--------------------------------------------------------------------------
     | Initialize Template Legend Events
     |--------------------------------------------------------------------------
     */

    $( "#show-template-legend" ).click( function() {

        var $this = $( this );

        $this.css( 'display' , 'none' );
        $template_legend.slideDown( 'fast' );

    } );

    $( "#hide-template-legend" ).click( function() {

        var $this = $( this );

        $template_legend.slideUp( 'fast' , function() {
            $( "#show-template-legend" ).css( 'display' , 'inline-block' );
        } );

    } );
    
    


    /*
     |--------------------------------------------------------------------------
     | Initialize Tooltips
     |--------------------------------------------------------------------------
     */

    $( '.tooltip' ).tipTip( {
        'attribute' : 'data-tip',
        'fadeIn'    : 50,
        'fadeOut'   : 50,
        'delay'     : 200
    } );



    
    /*
     |--------------------------------------------------------------------------
     | Show/Hide Templates Controls
     |--------------------------------------------------------------------------
     */

    $offer_templates_controls.on( "reset_templates_controls" , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this.find( "#schedule " ).val( "" );
        $this.find( "#heading-text" ).val( "" );
        $wc_wrap_chkbox.attr( "checked" , false ).trigger( "change" );
        $this.find( "#subject" ).val( "" );
        $this.find( "#message_body-tmce" ).trigger( "click" );
        $this.find( "#message_body_ifr" ).contents().find( "#tinymce" ).html( timed_email_offer_templates_params.i18n_default_email_template_content );
        $this.find( ".index" ).text( "" );

        return $this;

    } );

    $show_offer_templates_controls.click( function() {

        var $this = $( this );

        $offer_templates_controls.find( "#message_body-tmce" ).trigger( "click" );
        $offer_templates_controls.slideDown( 'fast' , function() {

            $this.css( 'display' , 'none' );

        } );

    } );

    $offer_templates_controls.find( "#cancel-add-template" ).click( function() {

        $offer_templates_controls.slideUp( 'fast' , function() {

            $offer_templates_controls.trigger( 'reset_templates_controls' );
            $offer_templates_controls.trigger( 'add_mode' );
            $show_offer_templates_controls.css( 'display' , 'inline-block' );

            $offer_templates.removeClass( 'processing-mode' );
            $offer_templates.find( "tr" ).removeClass( 'processing' );

        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | WC Wrap Header Text Toggle
     |--------------------------------------------------------------------------
     */

    $wc_wrap_chkbox.change( function() {

        var $this                   = $( this ),
            $heading_text_field_set = $offer_templates_controls.find( ".heading-text-field-set" );

        if ( $this.is( ":checked" ) ) {

            $heading_text_field_set.slideDown( 'fast' );

        } else {

            $heading_text_field_set.slideUp( 'fast' );

        }

    } );

    $wc_wrap_chkbox.trigger( "change" );




    // Only allow letters, numbers and underscores in schedule
    $( "#schedule" ).keyup( function() {

        var raw_text =  jQuery(this).val();
        var return_text = raw_text.replace(/[^0-9]/g,'');
        jQuery(this).val(return_text);

    } );




    /*
     |--------------------------------------------------------------------------
     | Add Templates
     |--------------------------------------------------------------------------
     */

    $offer_templates_controls.on( "construct_template_data" , function( event , template_data , errors ) {

        event.stopPropagation();

        var $this = $( this );

        template_data[ 'schedule' ]      = parseInt( $.trim( $this.find( "#schedule" ).val() ) , 10 );
        template_data[ 'schedule-text' ] = $.trim( $this.find( "#schedule" ).val() + timed_email_offer_templates_params.i18n_days_after_order_completed );
        template_data[ 'subject' ]       = $.trim( $this.find( "#subject" ).val() );
        template_data[ 'message' ]       = '';

        if ( $wc_wrap_chkbox.is( ":checked" ) ) {

            template_data[ 'wrap-wc-header-footer' ] = 'yes';

            if ( $this.find( '.heading-text-field-set' ).is( ':visible' ) && $this.find( '#heading-text' ).val() != "" )
                template_data[ 'heading-text' ] = $this.find( '#heading-text' ).val();
            else
                errors[ 'error-messages' ].push( timed_email_offer_templates_params.i18n_please_specify_heading_text );

        } else {

            template_data[ 'wrap-wc-header-footer' ] = 'no';
            template_data[ 'heading-text' ] = '';

        }

        if ( $this.find( "#message_body_ifr" ).contents().find( "#tinymce" ).text() != "" )
            template_data[ 'message' ] = $.trim( $this.find( "#message_body_ifr" ).contents().find( "#tinymce" ).html() );

        if ( template_data[ 'schedule' ] == '' || isNaN( template_data[ 'schedule' ] ) )
            errors[ 'error-messages' ].push( timed_email_offer_templates_params.i18n_please_specify_schedule );
        else if ( template_data[ 'schedule' ] <= 0 )
            errors[ 'error-messages' ].push( timed_email_offer_templates_params.i18n_schedule_must_be_greater_zero );

        if ( template_data[ 'subject' ] == '' )
            errors[ 'error-messages' ].push( timed_email_offer_templates_params.i18n_please_specify_subject );

        if ( template_data[ 'message' ] == '' )
            errors[ 'error-messages' ].push( timed_email_offer_templates_params.i18n_please_specify_message );

        return $this;

    } );

    $offer_templates_controls.find( "#add-template" ).click( function() {

        var $this         = $( this ),
            template_data = {},
            errors        = { 'error-messages' : [] };

        $offer_templates_controls
            .trigger( "processing_state" )
            .find( ".button" )
                .attr( "disabled" , "disabled" );

        $offer_templates_controls.trigger( "construct_template_data" , [ template_data , errors ] );

        if ( errors[ 'error-messages' ].length ) {

            var err_msg = '<b>' + timed_email_offer_templates_params.i18n_please_fill_form_properly + '</b><br/>';
            for ( var i = 0; i < errors[ 'error-messages' ].length ; i++ )
                err_msg += errors[ 'error-messages' ][ i ] + '<br/>';

            vex.dialog.alert( {
                message  : err_msg,
                callback : function() {

                    $offer_templates_controls
                        .trigger( "normal_state" )
                        .find( ".button" )
                            .removeAttr( "disabled" );

                }
            } );

        } else {

            $.ajax( {
                url : ajaxurl,
                type :'POST',
                data : { action : 'teo_add_offer_template' , offer_id : offer_id , template_data : template_data , 'ajax-nonce' : timed_email_offer_templates_params.nonce_add_offer_template },
                dataType: 'json'
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    offer_templates_datatable_handle.ajax.reload();

                    $offer_templates_controls
                        .slideUp( 'fast' , function() {

                            $offer_templates_controls.trigger( "reset_templates_controls" )
                            $show_offer_templates_controls.css( 'display' , 'inline-block' );

                        } );

                } else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                console.log( jqxhr );
                vex.dialog.alert( timed_email_offer_templates_params.i18n_fail_add_template );

            } )
            .always( function() {

                $offer_templates_controls
                    .trigger( "normal_state" )
                    .find( ".button" )
                        .removeAttr( "disabled" );

            } );

        }

    } );




    /*
     |--------------------------------------------------------------------------
     | Edit Template
     |--------------------------------------------------------------------------
     */

    $offer_templates_controls.on( 'edit_mode' , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this.addClass( 'edit-mode' );

        return $this;

    } );

    $offer_templates_controls.on( 'add_mode' , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this.removeClass( 'edit-mode' );

        return $this;

    } );

    $offer_templates_controls.on( 'processing_state' , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this.addClass( 'processing' );

        return $this;

    } );

    $offer_templates_controls.on( 'normal_state' , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        $this.removeClass( 'processing' );

        return $this;

    } );

    $offer_templates_controls.on( 'prepopulate_controls' , function( event , template_data , index ) {

        event.stopPropagation();

        var $this = $( this );

        $this.find( "#schedule" ).val( template_data[ 'schedule' ] );

        if ( template_data[ 'wrap-wc-header-footer' ] == 'yes' ) {

            $this.find( "#heading-text" ).val( template_data[ 'heading-text' ] );
            $wc_wrap_chkbox.attr( "checked" , true ).trigger( 'change' );

        } else {

            $this.find( "#heading-text" ).val( '' );
            $wc_wrap_chkbox.attr( "checked" , false ).trigger( 'change' );

        }

        $this.find( "#subject" ).val( template_data[ 'subject' ] );
        $this.find( "#message_body-tmce" ).trigger( "click" );
        $this.find( "#message_body_ifr" ).contents().find( "#tinymce" ).html( template_data[ 'message' ] );

        if ( index )
            $this.find( ".index" ).text( index );

        return $this;

    } );

    $offer_templates.on( 'click' , '.edit-offer-template' , function( event ) {

        event.stopPropagation();

        var $this = $( this),
            $tr   = $this.closest( 'tr' ),
            index = $this.attr( 'data-template-index' );

        $offer_templates.addClass( 'processing-mode' );
        $tr.addClass( 'processing' );

        $.ajax( {
            url      : ajaxurl,
            type     : 'POST',
            data     : { action : 'teo_get_offer_template_info' , offer_id : offer_id , index : index , 'ajax-nonce' : timed_email_offer_templates_params.nonce_get_offer_template_info },
            dataType : 'json'
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( data.status == 'success' ) {

                $offer_templates_controls.trigger( 'prepopulate_controls' , [ data.template_data , index ] );
                $offer_templates_controls.trigger( 'edit_mode' );

                $offer_templates_controls.slideDown( 'fast' , function() {

                    $show_offer_templates_controls.css( 'display' , 'none' );

                } );

            } else {

                $offer_templates.removeClass( 'processing-mode' );
                $tr.removeClass( 'processing' );

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( timed_email_offer_templates_params.i18n_fail_retrieve_template_info );

        } )
        .always( function() {} );

        return $this;

    } );

    $offer_templates_controls.find( "#edit-template" ).click( function() {

        var $this         = $( this ),
            template_data = {},
            errors        = { 'error-messages' : [] },
            index         = $.trim( $offer_templates_controls.find( ".index" ).text() );

        $offer_templates_controls
            .trigger( "processing_state" )
            .find( ".button" )
                .attr( "disabled" , "disabled" );

        $offer_templates_controls.trigger( "construct_template_data" , [ template_data , errors ] );

        if ( errors[ 'error-messages' ].length ) {

            var err_msg = '<b>' + timed_email_offer_templates_params.i18n_please_fill_form_properly + '</b><br/>';
            for ( var i = 0; i < errors[ 'error-messages' ].length ; i++ )
                err_msg += errors[ 'error-messages' ][ i ] + '<br/>';

            vex.dialog.alert( {
                message  : err_msg,
                callback : function() {

                    $offer_templates_controls
                        .trigger( "normal_state" )
                        .find( ".button" )
                            .removeAttr( "disabled" );

                }
            } );

        } else if ( index == "" ) {

            vex.dialog.alert( timed_email_offer_templates_params.i18n_missing_template_index );

        } else {

            $.ajax( {
                url : ajaxurl,
                type :'POST',
                data : { action : 'teo_edit_offer_template' , offer_id : offer_id , index : index , template_data : template_data , 'ajax-nonce' : timed_email_offer_templates_params.nonce_edit_offer_template },
                dataType: 'json'
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    offer_templates_datatable_handle.ajax.reload();

                    $offer_templates_controls
                        .slideUp( 'fast' , function() {

                            $offer_templates_controls.trigger( "reset_templates_controls" );
                            $offer_templates_controls.trigger( 'add_mode' );
                            $show_offer_templates_controls.css( 'display' , 'inline-block' );

                        } );

                    $offer_templates.removeClass( 'processing-mode' );
                    $offer_templates.find( "tr" ).removeClass( 'processing' );

                } else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                console.log( jqxhr );
                vex.dialog.alert( timed_email_offer_templates_params.i18n_fail_edit_template );

            } )
            .always( function() {

                $offer_templates_controls
                    .trigger( "normal_state" )
                    .find( ".button" )
                        .removeAttr( "disabled" );

            } );

        }

    } );




    /*
     |--------------------------------------------------------------------------
     | Delete Template
     |--------------------------------------------------------------------------
     */

    $offer_templates.on( 'click' , '.delete-offer-template' , function( event ) {

        event.stopPropagation();

        var $this = $( this ),
            $tr   = $this.closest( 'tr' ),
            index = $this.attr( 'data-template-index' );

        $offer_templates.addClass( 'processing-mode' );
        $tr.addClass( 'processing' );

        vex.dialog.confirm( {
            message  : timed_email_offer_templates_params.i18n_confirm_delete_template,
            callback : function( value ) {

                if ( value ) {

                    $offer_templates.addClass( 'processing-mode' );
                    $tr.addClass( 'processing' );

                    $.ajax( {
                        url      : ajaxurl,
                        type     : 'POST',
                        data     : { action : 'teo_delete_offer_template' , offer_id : offer_id , index : index , 'ajax-nonce' : timed_email_offer_templates_params.nonce_delete_offer_template },
                        dataType : 'json'
                    } )
                    .done( function( data , text_status , jqxhr ) {

                        if ( data.status == 'success' ) {

                            offer_templates_datatable_handle.ajax.reload();

                        } else {

                            console.log( data );
                            vex.dialog.alert( data.error_message );

                        }

                    } )
                    .fail( function( jqxhr , text_status , error_thrown ) {

                        console.log( jqxhr );
                        vex.dialog.alert( timed_email_offer_templates_params.i18n_fail_delete_template );

                    } )
                    .always( function() {

                        $offer_templates.removeClass( 'processing-mode' );
                        $tr.removeClass( 'processing' );

                    } );

                } else {

                    $offer_templates.removeClass( 'processing-mode' );
                    $tr.removeClass( 'processing' );

                }

            }

        } );

        return $this;

    } );




    /*
     |--------------------------------------------------------------------------
     | Send Test Email
     |--------------------------------------------------------------------------
     */

    // Initialize send test email pop up triggers
    $offer_templates.on( 'DOMNodeInserted' , function( e ) {

        var $new_element = $( e.target );

        if ( $new_element.hasClass( "offer-template-row" ) ) {

            $new_element.find( ".send-test-email" ).magnificPopup( {
                type                : 'inline',
                midClick            : true,
                closeOnContentClick : false,
                closeOnBgClick      : true,
                enableEscapeKey     : true,
                showCloseBtn        : true,
                callbacks: {
                    elementParse : function() {

                        $send_test_email_popup.find( ".template-index" ).text( $( this.st.el ).attr( 'data-template-index' ) );

                        $send_test_email_popup.trigger( 'element_parse_send_test_email_popup' , [ $( this.st.el ) ] );

                    },
                    close : function() {

                        $send_test_email_popup
                            .find( ".template-index").text( "" ).end()
                            .find( "#test-email-recipient" ).val( "" ).end()
                            .find( "#send-test-email" ).removeAttr( "disabled" );

                        $send_test_email_popup.trigger( 'close_send_test_email_popup' );

                    }
                }
            } );

        }

        return $( this );

    } );


    // Send test email

    $send_test_email_popup.on( 'construct_test_email_data' , function( event , test_email_data , errors ) {

        event.stopPropagation();

        test_email_data[ 'offer_id' ]        = $.trim( $templates_meta_box.find( '> .meta .offer-id' ).text() );
        test_email_data[ 'template_index' ]  = $.trim( $send_test_email_popup.find( '.meta .template-index').text() );
        test_email_data[ 'recipient_email' ] = $.trim( $send_test_email_popup.find( "#test-email-recipient" ).val() );

        if ( test_email_data[ 'recipient_email' ] == '' )
            errors[ 'error_messages' ].push( timed_email_offer_templates_params.i18n_please_provide_email_recipient );

        return $( this );

    } );

    $send_test_email_popup.find( '#send-test-email' ).click( function() {

        var $this           = $( this ),
            test_email_data = {},
            errors          = { error_messages : [] };

        $this.attr( 'disabled' , 'disabled' );

        $send_test_email_popup.trigger( 'construct_test_email_data' , [ test_email_data , errors ] );

        if ( errors[ 'error_messages' ].length > 0 ) {

            var err_msg = '<b>' + timed_email_offer_templates_params.i18n_fill_test_email_form_properly + '</b><br/>';
            for ( var i = 0 ; i < errors[ 'error_messages' ].length ; i++ )
                err_msg += errors[ 'error_messages' ][ i ] + '<br/>';

            vex.dialog.alert( {
                message : err_msg,
                callback : function() {
                    $this.removeAttr( 'disabled' );
                }
            } );

        } else {

            $this
                .css( 'display' , 'none' )
                .siblings( '#sending-email-message' )
                    .css( 'display' , 'block' );

            $.ajax( {
                url : ajaxurl,
                type : 'POST',
                data : { action : 'teo_send_offer_test_email' , offer_id : test_email_data[ 'offer_id' ] , template_index : test_email_data[ 'template_index' ] , recipient_email : test_email_data[ 'recipient_email' ] , 'ajax-nonce' : timed_email_offer_templates_params.nonce_send_offer_test_email },
                dataType : 'json'
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    vex.dialog.alert( {
                        message  : timed_email_offer_templates_params.i18n_test_email_sent,
                        callback : function() {
                            $.magnificPopup.close();
                        }
                    } );

                } else {

                    console.log( data );
                    vex.dialog.alert( data.error_message );

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                console.log( jqxhr );
                vex.dialog.alert( timed_email_offer_templates_params.i18n_fail_send_test_email );

            } )
            .always( function() {

                $this
                    .removeAttr( 'disabled' )
                    .css( 'display' , 'block' )
                    .siblings( '#sending-email-message' )
                        .css( 'display' , 'none' );

            } );

        }

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

    var $offer_templates_meta_box = jQuery( "#timed-email-offer-templates-meta-box" ),
        $offer_templates_table    = $offer_templates_meta_box.find( "#offer-templates" ),
        $offer_templates_meta     = $offer_templates_meta_box.find( ".meta" ),
        offer_id                   = jQuery.trim( $offer_templates_meta.find( ".offer-id" ).text() );

    $offer_templates_meta_box.on( "before_initialize_offer_templates_datatable" , function() {

        // Offer Id
        offer_templates_datatable_config[ "ajax" ][ "data" ][ "offer_id" ] = offer_id;

    } );

    // Trigger custom event to allow external plugins to modify the data tables config
    $offer_templates_meta_box.trigger( "before_initialize_offer_templates_datatable" );

    // Initialize offer templates data table.
    offer_templates_datatable_handle = $offer_templates_table.DataTable( offer_templates_datatable_config );

} );