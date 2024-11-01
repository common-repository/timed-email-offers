/* global JQuery */
jQuery( document ).ready( function ( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Variables
     |--------------------------------------------------------------------------
     */

    var $add_blacklist_manually_controls = $( "#add-blacklist-manually-controls" ),
        $blacklist_email                 = $add_blacklist_manually_controls.find( "#blacklist-email" ),
        $manually_blacklist_email        = $add_blacklist_manually_controls.find( "#manually-blacklist-email" ),
        $blacklist_type_filter           = $( "#blacklist-type-filter" ),
        $blacklist_table                 = $( "#blacklist-table" );




    /*
     |--------------------------------------------------------------------------
     | Functions
     |--------------------------------------------------------------------------
     */

    function validate_email( email ) {

        var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        var valid = emailReg.test( email );

        return ( ! valid ) ? false : true;

    }




    /*
     |--------------------------------------------------------------------------
     | Events
     |--------------------------------------------------------------------------
     */

    $manually_blacklist_email.click( function() {

        var $this = $( this ),
            email = $.trim( $blacklist_email.val() );

        $this
            .attr( 'disabled' , 'disabled' )
            .siblings( ".spinner" )
                .css( 'visibility' , 'visible' );

        $blacklist_type_filter.attr( 'disabled' , 'disabled' );

        if ( !validate_email( email ) ) {

            vex.dialog.alert( blacklist_params.i18n_input_valid_email );

            $this.removeAttr( 'disabled' )
                .siblings( ".spinner" )
                .css( 'visibility' , 'hidden' );

            $blacklist_type_filter.removeAttr( 'disabled' );

            return false;

        }

        vex.dialog.confirm( {
            message  : blacklist_params.i18n_confirm_blacklist_email,
            callback : function( value ) {

                if ( value ) {

                    $.ajax( {
                        url : ajaxurl,
                        type : 'POST',
                        data : { action : 'teo_manually_opt_out_email' , email : email , 'ajax-nonce' : blacklist_params.nonce_manually_opt_out_email },
                        dataType : 'json'
                    } )
                    .done( function( data , text_status , jqxhr ) {

                        if ( data.status == 'success' ) {

                            blacklist_datatable_handle.ajax.reload( function() {

                                $blacklist_email.val( "" );
                                $blacklist_type_filter.removeAttr( 'disabled' );

                            } );

                        } else {

                            console.log( data );
                            vex.dialog.alert( data.error_message );
                            $blacklist_type_filter.removeAttr( 'disabled' );

                        }

                    } )
                    .fail( function( jqxhr , text_status , error_thrown ) {

                        console.log( jqxhr );
                        vex.dialog.alert( blacklist_params.i18n_failed_opt_out_email );
                        $blacklist_type_filter.removeAttr( 'disabled' );

                    } )
                    .always( function() {

                        $this.removeAttr( 'disabled' )
                            .siblings( ".spinner" )
                            .css( 'visibility' , 'hidden' );

                    } );

                } else {

                    $this.removeAttr( 'disabled' )
                        .siblings( ".spinner" )
                        .css( 'visibility' , 'hidden' );

                    $blacklist_type_filter.removeAttr( 'disabled' );

                }

            }
        } );

    } );

    $blacklist_type_filter.change( function() {

        var $this = $( this );

        blacklist_datatable_config[ "ajax" ][ "data" ][ "blacklist_type" ] = $this.val();

        blacklist_datatable_handle.destroy();
        blacklist_datatable_handle = jQuery( "#blacklist-table" ).DataTable( blacklist_datatable_config );

    } );

    $blacklist_table.on( "click" ,  ".remove-blacklist-item" , function() {

        var $this          = $( this ),
            $tr            = $this.closest( 'tr' ),
            email          = $this.attr( 'data-blacklist-email' ),
            blacklist_type = $this.attr( 'data-blacklist-type' ),
            msg            = '';

        $blacklist_type_filter.attr( 'disabled' , 'disabled' );
        $tr.addClass( 'processing-row' );
        $blacklist_table.find( 'tbody tr td .dashicons' ).css( 'visibility' , 'hidden' );

        if ( blacklist_type == 'unsubscribed' ) {

            msg = blacklist_params.i18n_confirm_opt_out_email + '<br/>';
            msg += blacklist_params.i18n_warning_opt_out_email;

        } else
            msg = blacklist_params.i18n_confirm_opt_out_email;

        vex.dialog.confirm( {
            message  : msg,
            callback : function( value ) {

                if ( value ) {

                    $.ajax( {
                        url : ajaxurl,
                        type : 'POST',
                        data : { action : 'teo_un_opt_out_email' , email : email , 'ajax-nonce' : blacklist_params.nonce_un_opt_out_email },
                        dataType : 'json'
                    } )
                    .done( function( data , text_status , jqxhr ) {
                        
                        if ( data.status == 'success' ) {

                            blacklist_datatable_handle.ajax.reload( function() {

                                $blacklist_type_filter.removeAttr( 'disabled' );

                            } );

                        } else {

                            console.log( data );
                            vex.dialog.alert( data.error_message );
                            $blacklist_type_filter.removeAttr( 'disabled' );

                        }

                    } )
                    .fail( function( jqxhr , text_status , error_thrown ) {

                        console.log( jqxhr );
                        vex.dialog.alert( blacklist_params.i18n_failed_opt_out_email );
                        $blacklist_type_filter.removeAttr( 'disabled' );

                    } )
                    .always( function() {

                        $tr.removeClass( 'processing-row' );
                        $blacklist_table.find( 'tbody tr td .dashicons' ).css( 'visibility' , 'visible' );

                    } );

                } else {

                    $blacklist_type_filter.removeAttr( 'disabled' );
                    $tr.removeClass( 'processing-row' );
                    $blacklist_table.find( 'tbody tr td .dashicons' ).css( 'visibility' , 'visible' );

                }

            }
        } );

    } );




    /*
     |--------------------------------------------------------------------------
     | Initialization
     |--------------------------------------------------------------------------
     */

    // Initialize Vex Library
    vex.defaultOptions.className = 'vex-theme-plain';

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
     |   'before_initialize_blacklist_datatable' event, so TEOP needs to attached a
     |   callback first on document ready then TEO executes the event on window load
     */

    var $blacklist_table = jQuery( "#blacklist-table" );

    // Trigger custom event to allow external plugins to modify the data tables config
    $blacklist_table.trigger( "before_initialize_blacklist_datatable" );

    // Initialize blacklist data table.
    blacklist_datatable_handle = $blacklist_table.DataTable( blacklist_datatable_config );

} );