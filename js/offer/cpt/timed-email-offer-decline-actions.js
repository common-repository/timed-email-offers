/* global jQuery */
jQuery( document ).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Variables
     |--------------------------------------------------------------------------
     */

    var $decline_offer_actions_meta_box = $( "#decline-timed-email-offer-actions-meta-box" ),
        $meta                           = $decline_offer_actions_meta_box.find( ".meta" ),
        offer_id                        = $.trim( $meta.find( ".offer-id" ).text() ),
        $decline_offer_action_types     = $decline_offer_actions_meta_box.find( "#decline-offer-action-types" ),
        $additional_action_type_options = $decline_offer_actions_meta_box.find( "#additional-decline-offer-action-type-options" ),
        $save_decline_offer_actions_btn = $decline_offer_actions_meta_box.find( "#save-decline-offer-actions-btn" );
    
    
    
    
    /*
     |--------------------------------------------------------------------------
     | Decline Offer Actin Type Change
     |--------------------------------------------------------------------------
     */

    $decline_offer_action_types.on( 'change' , function( event ) {

        event.stopPropagation();

        var $this = $( this );

        if ( $this.val() == 'do-nothing' )
            $additional_action_type_options.html( '' );
        
        return $this;

    } );
    



    /*
     |--------------------------------------------------------------------------
     | Save Actions
     |--------------------------------------------------------------------------
     */

    $save_decline_offer_actions_btn.on( 'construct_action_data' , function( event , data , errors ) {

        event.stopPropagation();

        if ( $decline_offer_action_types.val() == 'do-nothing' ) {

            data[ $decline_offer_action_types.val() ] = null;

        }

        return $( this );

    } );

    $save_decline_offer_actions_btn.on( "click" , function( event , options_processing_stat ) {

        var $this            = $( this ),
            $action_controls = $this.closest( "#decline-offer-action-controls" ),
            data             = {},
            errors           = { err_msgs : [] };

        $decline_offer_action_types.attr( 'disabled' , 'disabled' );

        $action_controls
            .addClass( 'processing-mode' )
            .find( 'input' )
                .attr( 'disabled' , 'disabled' );

        $this.trigger( "construct_action_data" , [ data , errors ] );

        if ( errors.err_msgs.length > 0 ) {

            var err_msg = '<strong>' + decline_timed_email_offer_actions_params.i18n_please_fill_form_properly + '</strong><br/>';

            for ( var i = 0 ; i < errors.err_msgs.length ; i++ )
                err_msg += errors.err_msgs[ i ];

            vex.dialog.alert( err_msg );

            $decline_offer_action_types.removeAttr( 'disabled' );

            $action_controls
                .removeClass( 'processing-mode' )
                .find( 'input' )
                    .removeAttr( 'disabled' );

        } else {

            $.ajax( {
                url : ajaxurl,
                type : 'POST',
                data : { action : "teo_save_decline_offer_actions" , offer_id : offer_id , data : data , 'ajax-nonce' : decline_timed_email_offer_actions_params.nonce_save_decline_offer_actions },
                dataType : 'json'
            } )
            .done( function( data , text_status , jqxhr ) {

                if ( !options_processing_stat ) {

                    if ( data.status == 'success' )
                        vex.dialog.alert( decline_timed_email_offer_actions_params.i18n_success_save_decline_offer_actions )
                    else {

                        console.log( data );
                        vex.dialog.alert( data.error_message );

                    }

                }

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                if ( !options_processing_stat ) {

                    console.log( jqxhr );
                    vex.dialog.alert( decline_timed_email_offer_actions_params.i18n_failed_save_decline_offer_actions );

                }

            } )
            .always( function() {

                $decline_offer_action_types.removeAttr( 'disabled' );

                $action_controls
                    .removeClass( 'processing-mode' )
                    .find( 'input' )
                        .removeAttr( 'disabled' );

                if ( options_processing_stat )
                    options_processing_stat.decline_offer_actions_processing_status = true;

            } );

        }

    } );




    /*
     |--------------------------------------------------------------------------
     | Initialization
     |--------------------------------------------------------------------------
     */

    $decline_offer_action_types.chosen( { search_contains: true } );

} );
