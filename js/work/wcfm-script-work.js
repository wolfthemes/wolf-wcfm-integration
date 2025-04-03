$work_cat = '';
$work_vendor = '';

jQuery(document).ready(function($) {

	$wcfm_work_table = $('#wcfm-work').DataTable( {
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
				d.controller = 'wcfm-work',
                d.wcfm_ajax_nonce = wcfm_params.wcfm_ajax_nonce;
				d.work_cat      = $work_cat,
				d.work_vendor   = $work_vendor,
				d.work_status   = GetURLParameter( 'work_status' )
			},
			"complete" : function ( response ) {

                //console.log( response )
                //console.log( response.responseText )

                initiateTip();

				// Fire wcfm-work table refresh complete
				$( document.body ).trigger( 'updated_wcfm-work' );
			}
		}
	} );

	if( $('.dropdown_work_cat').length > 0 ) {
		$('.dropdown_work_cat').on('change', function() {
			$work_cat = $('.dropdown_work_cat').val();
			$wcfm_work_table.ajax.reload();
		});
	}

	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$work_vendor = $('#dropdown_vendor').val();
			$wcfm_work_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}

	// Delete work
	$( document.body ).on( 'updated_wcfm-work', function() {
		$('.wcfm_work_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_work_manage_messages.delete_confirm);
				if(rconfirm) deleteWCFMwork($(this));
				return false;
			});
		});
	});

	function deleteWCFMwork(item) {
		jQuery('#wcfm-work_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action    : 'delete_wcfm_work',
			workid : item.data('workid')
		}
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_work_table) $wcfm_work_table.ajax.reload();
				jQuery('#wcfm-work_wrapper').unblock();
			}
		});
	}

	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}

	// Screen Manager
	$( document.body ).on( 'updated_wcfm-work', function() {
		$.each(wcfm_work_screen_manage, function( column, column_val ) {
		  $wcfm_work_table.column(column).visible( false );
		} );
	});

} );
