<?php
/**
 * Get CPT URL
 */
if ( ! function_exists('get_wcfm_cpt_url') ) {
	function get_wcfm_cpt_url( $cpt_slug, $cpt_status = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$get_wcfm_cpt_url = wcfm_get_endpoint_url( $cpt_slug, '', $wcfm_page );
		if ( $cpt_status ) $get_wcfm_cpt_url = add_query_arg( $cpt_slug . '_status', $cpt_status, $get_wcfm_cpt_url );
		return apply_filters( 'wcfm_' . $cpt_slug . '_url', $get_wcfm_cpt_url );
	}
}

if(!function_exists('get_wcfm_cpt_manage_url')) {
	function get_wcfm_cpt_manage_url( $cpt_slug, $cpt_id = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$get_wcfm_cpt_manage_url = wcfm_get_endpoint_url( $cpt_slug . '-manage', $cpt_id, $wcfm_page );
		return apply_filters( 'wcfm_video_manage_url', $get_wcfm_cpt_manage_url );
	}
}

if(!function_exists('get_wcfm_cpt_manager_messages')) {
	function get_wcfm_cpt_manager_messages() {
		global $WCFM;
		
		$messages = apply_filters( 'wcfm_validation_messages_cpt_manager', array(
			'no_title'        => __('Please insert Title before submit.', 'wcfm-cpt'),
			'cpt_saved'       => __('Successfully Saved.', 'wcfm-cpt'),
			'cpt_pending'     => __( 'Successfully submitted for moderation.', 'wcfm-cpt' ),
			'cpt_published'   => __('Successfully Published.', 'wcfm-cpt'),
			'delete_confirm'  => __( "Are you sure and want to delete this?\nYou can't undo this action ...", 'wcfm-cpt'),
		) );
		
		return $messages;
	}
}