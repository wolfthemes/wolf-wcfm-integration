<?php
/**
 * Plugin Name: WCFM Video CPT Integration
 * Description: Integrate existing 'video' custom post type into WCFM dashboard.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

class WCFM_Video_Module {

    public function __construct() {
        add_filter('wcfm_query_vars', [$this, 'add_query_vars'], 50);
        add_filter('wcfm_endpoints_slug', [$this, 'add_endpoints_slug']);
        add_filter('wcfm_menus', [$this, 'add_menu']);
        add_action('wcfm_load_views', [$this, 'load_view']);
        add_action('after_wcfm_ajax_controller', [$this, 'ajax_controller']);
    }

    public function add_query_vars($vars) {
        $vars['video'] = 'video';
        return $vars;
    }

    public function add_endpoints_slug($endpoints) {
        $endpoints['video'] = 'video';
        return $endpoints;
    }

    public function add_menu($menus) {
        $menus['video'] = [
            'label'    => __('Videos', 'wcfm'),
            'url'      => wcfm_get_endpoint_url('video'),
            'icon'     => 'video',
            'priority' => 70
        ];
        return $menus;
    }

    public function load_view($endpoint) {
        if ($endpoint === 'video') {
            include plugin_dir_path(__FILE__) . 'views/wcfm-view-videos.php';
        }
    }

    public function ajax_controller() {
        if (isset($_POST['controller']) && $_POST['controller'] === 'wcfm-video') {
            include plugin_dir_path(__FILE__) . 'controllers/wcfm-controller-videos.php';
            new WCFM_Video_Controller();
        }
    }
}

new WCFM_Video_Module();

add_action('admin_enqueue_scripts', function() {
    wp_enqueue_media();
});