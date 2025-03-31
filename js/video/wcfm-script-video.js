$video_cat = '';
$video_vendor = '';

jQuery(document).ready(function($) {

    //console.log( $('#wcfm-video') );
	
	$wcfm_video_table = $('#wcfm-video').DataTable( {
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
				d.controller = 'wcfm-video',
				d.video_cat      = $video_cat,
				d.video_vendor   = $video_vendor,
				d.video_status   = GetURLParameter( 'video_status' )
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-video table refresh complete
				$( document.body ).trigger( 'updated_wcfm-video' );
			}
		}
	} );
	
	if( $('.dropdown_video_cat').length > 0 ) {
		$('.dropdown_video_cat').on('change', function() {
			$video_cat = $('.dropdown_video_cat').val();
			$wcfm_video_table.ajax.reload();
		});
	}
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$video_vendor = $('#dropdown_vendor').val();
			$wcfm_video_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Delete video
	$( document.body ).on( 'updated_wcfm-video', function() {
		$('.wcfm_video_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_video_manage_messages.delete_confirm);
				if(rconfirm) deleteWCFMvideo($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMvideo(item) {
		jQuery('#wcfm-video_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action    : 'delete_wcfm_video',
			videoid : item.data('videoid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_video_table) $wcfm_video_table.ajax.reload();
				jQuery('#wcfm-video_wrapper').unblock();
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-video', function() {
		$.each(wcfm_video_screen_manage, function( column, column_val ) {
		  $wcfm_video_table.column(column).visible( false );
		} );
	});
	
} );