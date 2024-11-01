( function() {
    
    window.TEO = window.TEO || {
        Admin: {}
    };

} () );

( function( $ ) {

    function Tour() {

        if ( !teo_initial_guided_tour_params.screen.elem )
            return;

        this.initPointer();

    }

    Tour.prototype.initPointer = function() {

        var self = this;

        self.$elem = $( teo_initial_guided_tour_params.screen.elem ).pointer( {
            content: teo_initial_guided_tour_params.screen.html,
            position: {
                align: teo_initial_guided_tour_params.screen.align,
                edge: teo_initial_guided_tour_params.screen.edge,
            },
            buttons: function( event , t ) {
                return self.createButtons(t);
            },
        } ).pointer( 'open' );

    };

    Tour.prototype.createButtons = function( t ) {

        this.$buttons = $( '<div></div>', {
            'class': 'teo-tour-buttons'
        } );

        this.createCloseButton( t );
        this.createPrevButton( t );
        this.createNextButton( t );

        return this.$buttons;

    };

    Tour.prototype.createCloseButton = function(t) {

        var $btnClose = $( '<button></button>' , {
            'class': 'button button-large',
            'type': 'button'
        } ).html( teo_initial_guided_tour_params.texts.btn_close_tour );

        $btnClose.click(function() {
            
            var data = {
                action : teo_initial_guided_tour_params.actions.close_tour,
                nonce  : teo_initial_guided_tour_params.nonces.close_tour,
            };

            $.post( teo_initial_guided_tour_params.urls.ajax , data , function( response ) {

                if ( response.success )
                    t.element.pointer( 'close' );
                
            } );

        });

        this.$buttons.append($btnClose);

    };

    Tour.prototype.createPrevButton = function( t ) {

        if ( !teo_initial_guided_tour_params.screen.prev )
            return;

        var $btnPrev = $( '<button></button>' , {
            'class': 'button button-large',
            'type': 'button'
        } ).html( teo_initial_guided_tour_params.texts.btn_prev_tour );

        $btnPrev.click( function() {
            window.location.href = teo_initial_guided_tour_params.screen.prev;
        } );

        this.$buttons.append($btnPrev);

    };

    Tour.prototype.createNextButton = function( t ) {

        if (!teo_initial_guided_tour_params.screen.next)
            return;

        // Check if this is the first screen of the tour.
        var text = ( !teo_initial_guided_tour_params.screen.prev ) ? teo_initial_guided_tour_params.texts.btn_start_tour : teo_initial_guided_tour_params.texts.btn_next_tour;

        var $btnStart = $( '<button></button>' , {
            'class' : 'button button-large button-primary',
            'type'  : 'button'
        } ).html( text );

        $btnStart.click( function() {
            window.location.href = teo_initial_guided_tour_params.screen.next;
        } );

        this.$buttons.append( $btnStart );
    };

    TEO.Admin.Tour = Tour;

}( jQuery ) );

( function( $ ) {
    
    // DOM ready
    $( function() {

        new TEO.Admin.Tour();

        $( "#teo-add-first-offer" ).on( 'click' , function( e ) {

            e.preventDefault();
            var $this = $( this ),
                href = this.href,
                data = {
                            action : teo_initial_guided_tour_params.actions.close_tour,
                            nonce  : teo_initial_guided_tour_params.nonces.close_tour,
                        };
            
            $this.attr( 'disabled' , 'disabled' );

            $.post( teo_initial_guided_tour_params.urls.ajax , data , function( response ) {

                if ( response.success ) {

                    window.location = href;
                    t.element.pointer( 'close' );

                } else {

                    console.log( response );
                    $this.removeAttr( 'disabled' );

                }
                
            } );

            return false;

        } );

    } );

}( jQuery ) );
