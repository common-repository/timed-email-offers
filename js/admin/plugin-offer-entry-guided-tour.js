/* global jQuery */
jQuery( 'document' ).ready( function( $ ) {
    
    function create_close_button( t , $pointer_options ) {

        var $btnClose = $( '<button></button>' , {
            'class': 'button button-large',
            'type': 'button'
        } ).html( teo_offer_entry_guided_tour_params.texts.btn_close_tour );

        $btnClose.click( function() {

            var data = {
                action : teo_offer_entry_guided_tour_params.actions.close_tour,
                nonce  : teo_offer_entry_guided_tour_params.nonces.close_tour,
            };

            $.post( teo_offer_entry_guided_tour_params.urls.ajax , data , function( response ) {

                if ( response.success )
                    t.element.pointer( 'close' );
                
            } );

        }) ;

        return $btnClose;

    }

    function create_prev_button( t , $pointer_options ) {

        if ( !$pointer_options.prev )
            return;

        var $btnPrev = $( '<button></button>' , {
            'class': 'button button-large',
            'type': 'button'
        } ).html( teo_offer_entry_guided_tour_params.texts.btn_prev_tour );

        if ( $pointer_options.prev.indexOf( '@' ) >= 0 ) {

            // Meaning the next guide will be on the same screen

            $btnPrev.click( function() {
                
                var prev_guide_id        = $pointer_options.prev.replace( '@' , '' ),
                    prev_pointer_options = null;

                for ( var i = 0 ; i <= teo_offer_entry_guided_tour_params.screen.length ; i++ ) {

                    if (  teo_offer_entry_guided_tour_params.screen[ i ][ 'id' ] == prev_guide_id ) {

                        prev_pointer_options = teo_offer_entry_guided_tour_params.screen[ i ];
                        break;

                    }

                }

                if ( prev_pointer_options ) {

                    t.element.pointer( 'close' );
                    init_pointer( prev_pointer_options );

                }

            } );

        } else {

            // Assumed to be a url

            $btnPrev.click( function() {
                window.location.href = $pointer_options.prev; // TODO: Validate url
            } );

        }

        return $btnPrev;

    }

    function create_next_button( t , $pointer_options ) {

        if ( !$pointer_options.next )
            return;

        // Check if this is the first screen of the tour.
        var text = ( !$pointer_options.prev ) ? teo_offer_entry_guided_tour_params.texts.btn_start_tour : teo_offer_entry_guided_tour_params.texts.btn_next_tour;

        var $btnStart = $( '<button></button>' , {
            'class' : 'button button-large button-primary',
            'type'  : 'button'
        } ).html( text );


        if ( $pointer_options.next.indexOf( '@' ) >= 0 ) {

            // Meaning the next guide will be on the same screen

            $btnStart.click( function() {
                
                var next_guide_id        = $pointer_options.next.replace( '@' , '' ),
                    next_pointer_options = null;

                for ( var i = 0 ; i <= teo_offer_entry_guided_tour_params.screen.length ; i++ ) {

                    if (  teo_offer_entry_guided_tour_params.screen[ i ][ 'id' ] == next_guide_id ) {

                        next_pointer_options = teo_offer_entry_guided_tour_params.screen[ i ];
                        break;

                    }

                }

                if ( next_pointer_options ) {

                    t.element.pointer( 'close' );
                    init_pointer( next_pointer_options );

                }

            } );

        } else {

            // Assumed to be a url

            $btnStart.click( function() {
                window.location.href = $pointer_options.next; // TODO: Validate url
            } );

        }

        return $btnStart;

    }

    function create_buttons( t , $pointer_options ) {

        var $buttons = $( '<div></div>' , { 'class': 'teo-tour-buttons' } );

        $buttons.append( create_close_button( t , $pointer_options ) );
        $buttons.append( create_prev_button( t , $pointer_options ) );
        $buttons.append( create_next_button( t , $pointer_options ) );

        return $buttons;
        
    }

    function init_pointer( $pointer_options ) {

        if ( $( $pointer_options.elem ).length ) {

            $( 'html, body' ).animate( {
                scrollTop: $( $pointer_options.elem ).offset().top - 200
            } , 300 );

            $( $pointer_options.elem ).pointer( {
                content: $pointer_options.html,
                position: {
                    align : $pointer_options.align,
                    edge  : $pointer_options.edge,
                },
                buttons: function( event , t ) {
                    return create_buttons( t , $pointer_options );
                },
            } ).pointer( 'open' );

        }

    }

    function init_offer_entry_guide() {

        if ( teo_offer_entry_guided_tour_params.screen.length > 0 )
            init_pointer( teo_offer_entry_guided_tour_params.screen[0] );

    }

    init_offer_entry_guide();

} );
