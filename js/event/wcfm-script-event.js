$event_cat = '';
$event_vendor = '';

jQuery(document).ready(function($) {
	
	$wcfm_event_table = $('#wcfm-event').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"pageLength": dataTables_config.pageLength,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
            { responsivePriority: 2 },
            { responsivePriority: 1 },
            { responsivePriority: 3 },
            { responsivePriority: 4 },
            { responsivePriority: 3 },
            { responsivePriority: 2 },
            { responsivePriority: 5 },
            { responsivePriority: 3 },
        ],
        "columnDefs": [ { "targets": 0, "orderable" : false }, 
            { "targets": 1, "orderable" : false }, 
            { "targets": 2, "orderable" : false }, 
            { "targets": 3, "orderable" : false }, 
            { "targets": 4, "orderable" : false }, 
            { "targets": 5, "orderable" : false },
            { "targets": 6, "orderable" : false },
            { "targets": 7, "orderable" : false },
		],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action     = 'wcfm_ajax_controller',
				d.controller = 'wcfm-event',
                d.wcfm_ajax_nonce = wcfm_params.wcfm_ajax_nonce;
				d.event_cat      = $event_cat,
				d.event_vendor   = $event_vendor,
				d.event_status   = GetURLParameter( 'event_status' )
			},
			"complete" : function ( response ) {
                
                //console.log( response )
                //console.log( response.responseText )
                
                initiateTip();
				
				// Fire wcfm-event table refresh complete
				$( document.body ).trigger( 'updated_wcfm-event' );
			}
		}
	} );
	
	if( $('.dropdown_event_cat').length > 0 ) {
		$('.dropdown_event_cat').on('change', function() {
			$event_cat = $('.dropdown_event_cat').val();
			$wcfm_event_table.ajax.reload();
		});
	}
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$event_vendor = $('#dropdown_vendor').val();
			$wcfm_event_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Delete event
	$( document.body ).on( 'updated_wcfm-event', function() {
		$('.wcfm_event_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_event_manage_messages.delete_confirm);
				if(rconfirm) deleteWCFMevent($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMevent(item) {
		jQuery('#wcfm-event_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action    : 'delete_wcfm_event',
			eventid : item.data('eventid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_event_table) $wcfm_event_table.ajax.reload();
				jQuery('#wcfm-event_wrapper').unblock();
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-event', function() {
		$.each(wcfm_event_screen_manage, function( column, column_val ) {
		  $wcfm_event_table.column(column).visible( false );
		} );
	});
	
} );