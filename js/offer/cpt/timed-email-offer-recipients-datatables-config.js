// Available via window object globally
var offer_recipients_datatable_handle = null,
    offer_recipients_datatable_config = {
    "dom" : 'rtlip',
    "processing" : true,
    "serverSide" : true,
    "order" : [ [ 3 , 'desc' ] ],
    "searching" : false,
    "columnDefs" : [
        {
            "targets"   : 0,
            "orderable" : true,
            "className" : "recipient-name"
        },
        {
            "targets"   : 1,
            "orderable" : true,
            "className" : "recipient-email"
        },
        {
            "targets"   : 2,
            "orderable" : true,
            "className" : "recipient-order-no"
        },
        {
            "targets"   : 3,
            "orderable" : true,
            "className" : "recipient-order-completed-date"
        },
        {
            "targets"   : 4,
            "orderable" : true,
            "className" : "recipient-offer-response-status"
        },
        {
            "targets"   : 5,
            "orderable" : false,
            "className" : "column-controls"
        }
    ],
    "language" : {
        "zeroRecords": offer_recipients_datatables_config_params.i18n_no_recipients
    },
    "ajax" : {
        "url"      : ajaxurl, // Provided by WordPress when script is loaded via admin_enqueue_script
        "type"     : "POST",
        // offer id of 0 just a placeholder, will be overridden on initialization
        // response_status_filter of 'pending send' is fallback, every initialization we load only pending send recipients.
        "data"     : { action : "teo_get_offer_recipients" , offer_id : 0 , response_status_filter : 'na' , 'ajax-nonce' : offer_recipients_datatables_config_params.nonce_get_offer_recipients },
        "dataType" : "json"
    },
    "preDrawCallback" : function( settings ) {

        // Before draw
        jQuery( "#offer-recipients-table" ).trigger( "retrieving_data_mode" );

    },
    "drawCallback" : function() {

        var $recipients_table = jQuery( "#offer-recipients-table" ),
            $tr               = $recipients_table.find( "th[aria-sort]" );

        // Clone table header to footer
        var header_row = $recipients_table.find( 'thead tr' ).clone( true );
        $recipients_table.find( 'tfoot tr' ).replaceWith( header_row );

        // After draw
        $recipients_table.trigger( "normal_mode" );

    }
};