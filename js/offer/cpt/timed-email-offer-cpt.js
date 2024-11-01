/* global jQuery */
jQuery( document ).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Save Offer Components
     | =========================================================================
     | Whenever the cpt entry "publish", "update" or "save draft" button is clicked
     | Save also the cpt entry ( offer ) components data
     |--------------------------------------------------------------------------
     */

    $( "body" ).on( "save_offer_components" , function( event , offer_components_processing_status ) {

        $( "#save-offer-conditions" ).trigger( "click" , [ offer_components_processing_status ] );
        $( "#save-accept-offer-actions-btn" ).trigger( "click" , [ offer_components_processing_status ] );
        $( "#save-decline-offer-actions-btn" ).trigger( "click" , [ offer_components_processing_status ] );

    } );

    var offer_components_processing_status = null,
        submit_form = false;

    $( "#publishing-action #publish, #save-action #save-post" ).click( function ( e ) {

        var $this = $( this );

        if ( !submit_form ) {

            if ( offer_components_processing_status ) {

                // Check if all offer components have been processed
                var all_offer_components_processed = true;
                for ( var offer_component_stat in offer_components_processing_status ) {

                    if ( offer_components_processing_status.hasOwnProperty( offer_component_stat ) )
                        all_offer_components_processed = all_offer_components_processed && offer_components_processing_status[ offer_component_stat ];

                }

                if ( all_offer_components_processed ) {

                    $this.removeAttr( "disabled" );

                    setTimeout( function() {

                        submit_form = true;
                        $this.trigger( "click" );

                    } , 250 );

                } else
                    setTimeout( function() { $this.trigger( "click" ); } , 250 );

            }

            if ( !offer_components_processing_status ) {

                // Initialize offer components processing status
                offer_components_processing_status = {
                    offer_condition_processing_status       : false,
                    accept_offer_actions_processing_status  : false,
                    decline_offer_actions_processing_status : false
                };

                // Allow external plugins extending this plugin to also initialize the processing status of the
                // offer components ( meta boxes ) that those plugins may have added.
                $( "body" ).trigger( "initialize_offer_components_processing_status" , [ offer_components_processing_status ] );

                // Trigger save offer components
                $( "body" ).trigger( "save_offer_components" , [ offer_components_processing_status ] );

                $this.attr( "disabled" , "disabled" );
                $this.siblings( ".spinner" ).css( "visibility" , "visible" );

                setTimeout( function() { $this.trigger( "click" ); } , 250 );

            }

            return false;

        }

        // If code reaches here, its submits the form

    } );

} );