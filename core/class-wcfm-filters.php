<?php
/**
 * Filters overable
 */

if (!defined('ABSPATH')) exit;

class Wolf_WCFM_Filters {

    private $theme_slug;

    public function __construct() {

        $this->theme_slug = 'overable';

        add_filter( $this->theme_slug . '_work_index_params', array( $this, 'add_store_id_filter' ) );
        add_filter( $this->theme_slug . '_video_index_params', array( $this, 'add_store_id_filter' ) );
        add_filter( $this->theme_slug . '_product_index_params', array( $this, 'add_store_id_filter' ) );
        add_filter( $this->theme_slug . '_event_index_params', array( $this, 'add_store_id_filter' ) );
        add_filter( $this->theme_slug . '_post_index_params', array( $this, 'add_store_id_filter' ) );
        add_filter( $this->theme_slug . '_post_module_main_query_args', array( $this, 'filter_post_query' ), 10, 2 );

    }

    public function get_vendors() {
        global $wpdb;

        $vendor_array[] = esc_html__( 'Not set', 'wolf-wcfp-integration' );

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
    public function add_store_id_filter( $params ) {

        $vendors = $this->get_vendors();
        
        $params['params'][] = array(
            'label'      => esc_html__( 'Vendor', 'wolf-wcfp-integration' ),
            'param_name' => 'vendor_id',
            'type'       => 'select',
            'options'    => $vendors,
            'group'       => esc_html__( 'Query', 'wolf-wcfp-integration' ),
        );

        return $params;
    }

    public function filter_post_query( $args, $atts ) {
        // 'meta_key'    => '_vendor_id',
        $vendor_id = ( ! empty( $atts['vendor_id'] ) ) ? esc_attr($atts['vendor_id']) : null;

        if ( $vendor_id ) {
            $args['author'] = $vendor_id;
        }

        return $args;
    }
}