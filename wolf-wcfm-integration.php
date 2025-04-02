<?php
/**
 * Plugin Name: Wolf WCFM Integration
 * Description: Integrate existing custom post type into WCFM dashboard.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

class Wolf_WCFM_Integration {

    private $plugin_path;
    private $plugin_url;

    public function __construct() {

        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url  = plugin_dir_url(__FILE__);

        add_action( 'wcfm_init', array( $this, 'init' ), 20 );
        add_action( 'wp_enqueue_scripts', array( &$this, 'load_default_styles' ) );
    }

    public function init() {

        require_once 'helpers/core-functions.php';

        require_once( 'core/class-wcfm-cpt.php' );
        global $WCFM, $WWCFMcpt, $WCFM_Query;

        new Wolf_WCFM_CPT_Module( __FILE__, [
            'slug'  => 'video',
            'label' => 'Videos',
            'icon'  => 'video',
        ]);
        $GLOBALS['WWCFMcpt'] = $WWCFMcpt;

        require_once 'core/class-wcfm-filters.php';
        new Wolf_WCFM_Filters();
    }

    /**
     * Load styles
     *
     * @param [type] $end_point
     * @return void
     */
    public function load_default_styles() {

        global $WCFM;
        wp_enqueue_style( 'wcfm_base_css',  $this->plugin_url . 'css/wcfm-styles.css', array(), $WCFM->version );
    }  
    
}

new Wolf_WCFM_Integration();