// Available via window object globally
var blacklist_datatable_handle = null,
    blacklist_datatable_config = {
    "dom": 'rtlip',
    "processing": true,
    "serverSide": true,
    "order": [ [ 1 , 'desc' ] ],
    "searching": false,
    "columnDefs": [
        {
            "targets": 0,
            "orderable" : true,
            "className": "email"
        },
        {
            "targets": 1,
            "orderable" : true,
            "className": "opt-out-date"
        },
        {
            "targets": 2,
            "orderable" : true,
            "className": "type"
        },
        {
            "targets": 3,
            "orderable" : false,
            "className" : "column-controls"
        }
    ],
    "preDrawCallback": function( settings ) {

        // Before draw
        jQuery( "#blacklist-table" ).trigger( "retrieving_data_mode" );

    },
    "language": {
        "zeroRecords": blacklist_datatable_config_params.i18n_no_blacklisted_emails
    },
    "ajax": {
        "url"      : ajaxurl, // Provided by WordPress when script is loaded via admin_enqueue_script
        "type"     : "POST",
        // blacklist_type of 'all' is fallback, every initialization we load all.
        "data"     : { action : "teo_get_blacklist" , blacklist_type : 'all' , 'ajax-nonce' : blacklist_datatable_config_params.nonce_get_blacklist },
        "dataType" : "json"
    },
    "drawCallback" : function() {

        var $blacklist_table = jQuery( "#blacklist-table" ),
            $tr              = $blacklist_table.find( "th[aria-sort]" );

        jQuery( "#blacklist-table" ).trigger( "normal_mode" );

        // Add Sort Icons
        if ( $tr.attr( 'aria-sort' ) == 'descending' ) {

            $blacklist_table.find( 'th .dashicons' ).remove();
            $tr.append( '<span class="dashicons dashicons-arrow-down"></span>' );

        } else {

            $blacklist_table.find( 'th .dashicons' ).remove();
            $tr.append( '<span class="dashicons dashicons-arrow-up"></span>' );

        }

        // Clone table header to footer
        var header_row = $blacklist_table.find( 'thead tr' ).clone( true );
        $blacklist_table.find( 'tfoot tr' ).replaceWith( header_row );

    }
};
