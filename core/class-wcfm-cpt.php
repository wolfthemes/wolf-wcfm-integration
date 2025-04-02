<?php

if (!defined('ABSPATH')) exit;

class Wolf_WCFM_CPT {

    private $plugin_path;
    private $plugin_url;

    public function __construct( $file ) {
        $this->plugin_path = plugin_dir_path( $file );
        $this->plugin_url  = plugin_dir_url( $file );

    
        add_filter( 'wcfm_query_vars', [$this, 'add_query_vars'], 50 );
        add_filter( 'wcfm_endpoints_slug', [$this, 'add_endpoints_slug']);
        add_filter( 'wcfm_menu_dependancy_map', array( $this, 'manage_dependency_map' ) );
        add_filter( 'wcfm_blocked_product_popup_views', array( $this, 'blocked_product_popup_views' ) );
        add_filter( 'wcfm_menus', [$this, 'add_menu']);

        add_action( 'wcfm_load_scripts', array( $this, 'load_scripts' ), 30 );

        add_action( 'wcfm_load_styles', array( &$this, 'load_styles' ), 30 );

        add_action( 'wcfm_load_views', array( $this, 'load_views' ), 30 );

        add_action( 'after_wcfm_ajax_controller', array( $this, 'ajax_controller' ), 30 );

        define( 'WCFM_VIDEO_CPT_LABEL', 'Videos' );
        define( 'WCFM_VIDEO_CPT_SLUG', 'video' );
    }

    public function add_query_vars($query_vars) {
       $wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
  	
		$query_cpt_vars = array(
			'wcfm-' . WCFM_VIDEO_CPT_SLUG                 => ! empty( $wcfm_modified_endpoints['wcfm-' . WCFM_VIDEO_CPT_SLUG] ) ? $wcfm_modified_endpoints['wcfm-' . WCFM_VIDEO_CPT_SLUG] : WCFM_VIDEO_CPT_SLUG,
			'wcfm-' .  WCFM_VIDEO_CPT_SLUG . '-manage'    => ! empty( $wcfm_modified_endpoints['wcfm-' . WCFM_VIDEO_CPT_SLUG . '-manage'] ) ? $wcfm_modified_endpoints['wcfm-' . WCFM_VIDEO_CPT_SLUG . '-manage'] : WCFM_VIDEO_CPT_SLUG.'-manage',
		);
		
		$query_vars = array_merge( $query_vars, $query_cpt_vars );
		
		return $query_vars;
    }

    public function add_endpoints_slug($endpoints) {
       $cpt_endpoints = array(
            'wcfm-' . WCFM_VIDEO_CPT_SLUG => WCFM_VIDEO_CPT_SLUG,
            'wcfm-' . WCFM_VIDEO_CPT_SLUG . '-manage'  	=> WCFM_VIDEO_CPT_SLUG.'-manage',
        );
		$endpoints = array_merge( $endpoints, $cpt_endpoints );
		
        return $endpoints;
    }

    /**
   * CPT 1 manage menu dependency mapping
   */
    public function manage_dependency_map( $mappings ) {
        $mappings['wcfm-' . WCFM_VIDEO_CPT_SLUG . '-manage'] = 'wcfm-' . WCFM_VIDEO_CPT_SLUG; 
        return $mappings;
    }

    /**
	 * BLock Product Popup Views
	 */
	function blocked_product_popup_views( $views ) {
		$views[] = 'wcfm-' . WCFM_VIDEO_CPT_SLUG . '-manage';
		return $views;
	}

    public function add_menu($menus) {
        global $WCFM;
  	
		$cpt1_menus = array( 'wcfm-' . WCFM_VIDEO_CPT_SLUG => array(   'label'  => WCFM_VIDEO_CPT_LABEL,
            'url'      => wcfm_get_endpoint_url( 'wcfm-' . WCFM_VIDEO_CPT_SLUG ),
            'icon'     => 'video',
            'has_new'    => 'yes',
            'new_class'  => 'wcfm_sub_menu_items_' . WCFM_VIDEO_CPT_SLUG . '_manage',
            'new_url'    => get_wcfm_video_manage_url(),
            'capability' => 'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_menu',
            'submenu_capability' => 'wcfm_add_new_' . WCFM_VIDEO_CPT_SLUG . '_sub_menu',
            'priority'  => 4
        ) );
		
		$menus = array_merge( $menus, $cpt1_menus );
  	    return $menus;
    }

    /**
     * Load styles
     *
     * @param [type] $end_point
     * @return void
     */
    public function load_styles( $end_point ) {
        global $WCFM;

        switch( $end_point ) {
            case 'wcfm-' . WCFM_VIDEO_CPT_SLUG:
            wp_enqueue_style( 'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_css',  $this->plugin_url . 'css/' . WCFM_VIDEO_CPT_SLUG . '/wcfm-style-' . WCFM_VIDEO_CPT_SLUG . '.css', array(), $WCFM->version );
            break;

            case 'wcfm-' . WCFM_VIDEO_CPT_SLUG . '-manage':
            wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
            wp_enqueue_style( 'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_manage_css',  $this->plugin_url . 'css/' . WCFM_VIDEO_CPT_SLUG . '/wcfm-style-' . WCFM_VIDEO_CPT_SLUG . '-manage.css', array(), $WCFM->version );
        break;
        }
	}

    /**
     * Load Scripts
     */
    public function load_scripts( $end_point ) {
	  global $WCFM;
    
	  switch( $end_point ) {
	  	case 'wcfm-' . WCFM_VIDEO_CPT_SLUG:
      	    $WCFM->library->load_datatable_lib();
      	    $WCFM->library->load_select2_lib();
	    	wp_enqueue_script( 'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_js', $this->plugin_url . 'js/' . WCFM_VIDEO_CPT_SLUG . '/wcfm-script-' . WCFM_VIDEO_CPT_SLUG . '.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
	    	
	    	// Screen manager
	    	$wcfm_screen_manager = get_option( 'wcfm_screen_manager', array() );
	    	$wcfm_screen_manager_data = array();
	    	if( isset( $wcfm_screen_manager[ WCFM_VIDEO_CPT_SLUG ] ) ) $wcfm_screen_manager_data = $wcfm_screen_manager[ WCFM_VIDEO_CPT_SLUG ];
	    	if( !isset( $wcfm_screen_manager_data['admin'] ) ) {
					$wcfm_screen_manager_data['admin'] = $wcfm_screen_manager_data;
					$wcfm_screen_manager_data['vendor'] = $wcfm_screen_manager_data;
				}
				if( wcfm_is_vendor() ) {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['vendor'];
					$wcfm_screen_manager_data[5] = 'yes';
				} else {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['admin'];
				}
				$wcfm_screen_manager_data[3] = 'yes';
	    	
                // Screen manager
                wp_localize_script(
                    'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_js',
                    'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_screen_manage',
                    $wcfm_screen_manager_data
                );
	    	
	    	// Localized Script
            $wcfm_messages = get_wcfm_cpt_manager_messages();
			// Localized Script Messages
            wp_localize_script(
                'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_js',
                'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_manage_messages',
                $wcfm_messages
            );

            wp_localize_script(
            'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_js',
            'wcfm_params',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'wcfm_ajax_nonce' => wp_create_nonce('wcfm_ajax_nonce')
                )
            );
      break;
      
      case 'wcfm-' . WCFM_VIDEO_CPT_SLUG . '-manage':
      	$WCFM->library->load_upload_lib();
      	$WCFM->library->load_select2_lib();
      	$WCFM->library->load_collapsible_lib();
	  		wp_enqueue_script( 'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_manage_js', $this->plugin_url . 'js/' . WCFM_VIDEO_CPT_SLUG . '/wcfm-script-' . WCFM_VIDEO_CPT_SLUG . '-manage.js', array('jquery'), $WCFM->version, true );
	  		
	  		// Localized Script
        $wcfm_messages = get_wcfm_cpt_manager_messages();
            wp_localize_script( 'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_manage_js', 'wcfm_' . WCFM_VIDEO_CPT_SLUG . '_manage_messages', $wcfm_messages );
	  	break;
	  }
	}

    public function load_views($end_point) {
        global $WCFM;
	  
        switch( $end_point ) {
            case 'wcfm-' . WCFM_VIDEO_CPT_SLUG:
            include_once( $this->plugin_path . 'views/' . WCFM_VIDEO_CPT_SLUG . '/wcfm-view-' . WCFM_VIDEO_CPT_SLUG . '.php' );
        break;
        
        case 'wcfm-' . WCFM_VIDEO_CPT_SLUG . '-manage':
            include_once( $this->plugin_path . 'views/' . WCFM_VIDEO_CPT_SLUG . '/wcfm-view-' . WCFM_VIDEO_CPT_SLUG . '-manage.php' );
        break;
        }
    }

    public function ajax_controller() {
        global $WCFM;
  	
        $controllers_path = $this->plugin_path . 'controllers/' . WCFM_VIDEO_CPT_SLUG . '/';
        
        $controller = '';
        if( isset( $_POST['controller'] ) ) {
            $controller = $_POST['controller'];
            
            switch( $controller ) {
                case 'wcfm-' . WCFM_VIDEO_CPT_SLUG:
                        include_once( $controllers_path . 'wcfm-controller-' . WCFM_VIDEO_CPT_SLUG . '.php' );
                        new WCFM_Video_Controller();
                    break;
                    
                    case 'wcfm-' . WCFM_VIDEO_CPT_SLUG . '-manage':
                        include_once( $controllers_path . 'wcfm-controller-' . WCFM_VIDEO_CPT_SLUG . '-manage.php' );
                        new WCFM_Video_Manage_Controller();
                    break;
            }
        }
    }
}