<?php
defined( 'ABSPATH' ) || exit;

/**
 * List all store IDs
 */
function wolf_wcfmi_get_vendors() {
    global $wpdb;

    $vendor_array[] = esc_html__( 'Not set', '%TEXTDOMAIN%' );

    // Fetch vendors from the database
    $vendors = $wpdb->get_results("
        SELECT u.ID, u.display_name 
        FROM {$wpdb->users} u
        INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
        WHERE um.meta_key = '{$wpdb->prefix}capabilities' 
        AND um.meta_value LIKE '%wcfm_vendor%'
    ");

    // Convert results to an array with user ID as key and name as value
    if (!empty($vendors)) {
        foreach ($vendors as $vendor) {
            $vendor_array[$vendor->ID] = $vendor->display_name;
        }
    }

    // Output the array
    // print_r($vendor_array);
   
    return $vendor_array;
}

/** 
 * Add Elementor settings
 */
function wolf_wcfmi_add_store_id_filter( $params ) {

    $vendors = wolf_wcfmi_get_vendors();
    
    $params['params'][] = array(
        'label'      => esc_html__( 'Vendor', '%TEXTDOMAIN%' ),
		'param_name' => 'vendor_id',
		'type'       => 'select',
		'options'    => $vendors,
        'group'       => esc_html__( 'Query', '%TEXTDOMAIN%' ),
    );

    return $params;
}
add_filter( 'overable_work_index_params', 'wolf_wcfmi_add_store_id_filter' );
add_filter( 'overable_video_index_params', 'wolf_wcfmi_add_store_id_filter' );
add_filter( 'overable_product_index_params', 'wolf_wcfmi_add_store_id_filter' );
add_filter( 'overable_event_index_params', 'wolf_wcfmi_add_store_id_filter' );
add_filter( 'overable_post_index_params', 'wolf_wcfmi_add_store_id_filter' );

/**
 * Filter post query
 */
function wolf_wcfmi_filter_post_query( $args, $atts ) {
    // 'meta_key'    => '_vendor_id',
    $vendor_id = ( ! empty( $atts['vendor_id'] ) ) ? esc_attr($atts['vendor_id']) : null;

    if ( $vendor_id ) {
        $args['author'] = $vendor_id;
    }

    return $args;
}
add_filter( 'overable_post_module_main_query_args', 'wolf_wcfmi_filter_post_query', 10, 2 );