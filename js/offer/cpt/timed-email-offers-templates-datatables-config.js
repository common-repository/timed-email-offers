// Available via window object globally
var offer_templates_datatable_handle = null,
    offer_templates_datatable_config = {
        "dom" : 'rt',
        "processing" : true,
        "serverSide" : true,
        "order" : [ [ 2 , 'asc' ] ],
        "searching" : false,
        "columnDefs" : [
            {
                "targets"   : 0,
                "orderable" : true,
                "className" : "tid"
            },
            {
                "targets"   : 1,
                "orderable" : true,
                "className" : "subject"
            },
            {
                "targets"   : 2,
                "orderable" : true,
                "className" : "schedule"
            },
            {
                "targets"   : 3,
                "orderable" : true,
                "className" : "wrap-wc"
            },
            {
                "targets"   : 4,
                "orderable" : true,
                "className" : "heading-text"
            },
            {
                "targets"   : 5,
                "orderable" : false,
                "className" : "column-controls"
            }
        ],
        "createdRow": function( row, data, dataIndex ) {

            jQuery( row ).addClass( 'offer-template-row' ); // Add class to the row

        },
        "language" : {
            "zeroRecords": templates_datatables_config_param.i18n_no_email_templates
        },
        "ajax" : {
            "url"      : ajaxurl, // Provided by WordPress when script is loaded via admin_enqueue_script
            "type"     : "POST",
            "data"     : { action : "teo_get_offer_email_templates" , offer_id : 0 , 'ajax-nonce' : templates_datatables_config_param.nonce_get_offer_email_templates },
            "dataType" : "json"
        },
        "preDrawCallback" : function( settings ) {

            // Before draw
            jQuery( "#offer-templates" ).trigger( "retrieving_data_mode" );

        },
        "drawCallback" : function() {

            var $templates_table = jQuery( "#offer-templates" ),
                $tr               = $templates_table.find( "th[aria-sort]" );

            // Clone table header to footer
            var header_row = $templates_table.find( 'thead tr' ).clone( true );
            $templates_table.find( 'tfoot tr' ).replaceWith( header_row );

            // After draw
            $templates_table.trigger( "normal_mode" );

        }
    };