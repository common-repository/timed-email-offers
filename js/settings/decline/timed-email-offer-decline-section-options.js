jQuery( document ).ready( function( $ ) {

    // Only allow letters, numbers and underscores in timeout period field
    $( "#teo_offer_timeout_period" ).keyup( function() {

        var raw_text =  jQuery(this).val();
        var return_text = raw_text.replace(/[^0-9]/g,'');
        jQuery(this).val(return_text);

    } );

} );