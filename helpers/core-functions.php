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