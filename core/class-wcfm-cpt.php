<?php

if (!defined('ABSPATH')) {
exit;
}

class Wolf_WCFM_CPT_Module {

	private $slug;
	private $label;
	private $plugin_path;
	private $plugin_url;
	private $icon;
	private $manage_url_callback;

	public function __construct( array $args ) {

		$this->slug                = $args['slug'];
		$this->label               = $args['label'];
		$this->icon                = $args['icon'] ?? 'video';
		$this->manage_url_callback = $args['manage_url_callback'] ?? function() {
			return wcfm_get_endpoint_url('wcfm-' . $this->slug . '-manage');
		};

		$this->plugin_path = WWCFI_DIR . '/';
		$this->plugin_url  = WWCFI_URI . '/';

		add_filter( 'wcfm_query_vars', [$this, 'add_query_vars'], 50 );
		add_filter( 'wcfm_endpoint_title', array( $this, 'endpoint_title' ), 20, 2 );

		add_action( 'init', array( $this, 'init' ), 20 );

		add_filter( 'wcfm_endpoints_slug', [$this, 'add_endpoints_slug'] );
		add_filter( 'wcfm_menu_dependancy_map', [$this, 'manage_dependency_map'] );
		add_filter( 'wcfm_blocked_product_popup_views', [$this, 'blocked_product_popup_views'] );
		add_filter( 'wcfm_menus', [$this, 'add_menu'] );

		add_action( 'wcfm_load_scripts', [$this, 'load_scripts'], 30 );
		add_action( 'wcfm_load_styles', [$this, 'load_styles'], 30 );
		add_action( 'wcfm_load_views', [$this, 'load_views'], 30 );
		add_action( 'after_wcfm_ajax_controller', [$this, 'ajax_controller'], 30 );
	}

	/**
   * WCFM Cpt1 End Point Title
   */
	public function endpoint_title( $title, $endpoint ) {
		global $wp;
		switch ( $endpoint ) {
			case 'wcfm-' . $this->slug:
					$title = sprintf( __( '%s Dashboard', 'wc-frontend-manager' ), $this->label );
				break;
			case 'wcfm-' . $this->slug . '-manage':
				$title = sprintf( __( '%s Manager', 'wc-frontend-manager' ), $this->label );
				break;
		}

		return $title;
	}

	/**
	 * WCFM CPT Endpoint Intialize
	 */
	function init() {
		global $WCFM_Query;

		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();

		if ( ! get_option( 'wcfm_updated_end_point_wcfm_' . $this->slug ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_wcfm_' . $this->slug, 1 );
		}
	}

	public function add_query_vars( $query_vars) {
		$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );

		$query_vars = array_merge($query_vars, [
			'wcfm-' . $this->slug                 => $wcfm_modified_endpoints['wcfm-' . $this->slug] ?? $this->slug,
			'wcfm-' . $this->slug . '-manage'     => $wcfm_modified_endpoints['wcfm-' . $this->slug . '-manage'] ?? $this->slug . '-manage',
		]);

		return $query_vars;
	}

	public function add_endpoints_slug( $endpoints) {
		return array_merge($endpoints, [
			'wcfm-' . $this->slug => $this->slug,
			'wcfm-' . $this->slug . '-manage' => $this->slug . '-manage',
		]);
	}

	public function manage_dependency_map( $mappings) {
		$mappings['wcfm-' . $this->slug . '-manage'] = 'wcfm-' . $this->slug;
		return $mappings;
	}

	public function blocked_product_popup_views( $views) {
		$views[] = 'wcfm-' . $this->slug . '-manage';
		return $views;
	}

	public function add_menu( $menus ) {
		$menus['wcfm-' . $this->slug] = [
			'label'       => $this->label,
			'url'         => wcfm_get_endpoint_url('wcfm-' . $this->slug),
			'icon'        => $this->icon,
			'has_new'     => 'yes',
			'new_class'   => 'wcfm_sub_menu_items_' . $this->slug . '_manage',
			'new_url'     => call_user_func($this->manage_url_callback),
			'capability'  => 'wcfm_' . $this->slug . '_menu',
			'submenu_capability' => 'wcfm_add_new_' . $this->slug . '_sub_menu',
			'priority'    => 4
		];
		return $menus;
	}

	public function load_styles( $end_point) {
		global $WCFM;
		switch ( $end_point ) {
			case 'wcfm-' . $this->slug:
				wp_enqueue_style('wcfm_' . $this->slug . '_css', $this->plugin_url . 'css/' . $this->slug . '/wcfm-style-' . $this->slug . '.css', [], $WCFM->version);
				break;
			case 'wcfm-' . $this->slug . '-manage':
				wp_enqueue_style('collapsible_css', $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', [], $WCFM->version);
				wp_enqueue_style('wcfm_' . $this->slug . '_manage_css', $this->plugin_url . 'css/' . $this->slug . '/wcfm-style-' . $this->slug . '-manage.css', [], $WCFM->version);
				break;
		}
	}

	public function load_scripts( $end_point) {
		global $WCFM;

		switch ( $end_point ) {
			case 'wcfm-' . $this->slug:
				$WCFM->library->load_datatable_lib();
				$WCFM->library->load_select2_lib();
				wp_enqueue_script('wcfm_' . $this->slug . '_js', $this->plugin_url . 'js/' . $this->slug . '/wcfm-script-' . $this->slug . '.js', ['jquery', 'dataTables_js'], $WCFM->version, true);

				$screen_manager = get_option('wcfm_screen_manager', []);
				$data           = $screen_manager[$this->slug] ?? [];
				$data           = wcfm_is_vendor() ? ( $data['vendor'] ?? $data ) : ( $data['admin'] ?? $data );
				$data[3]        = 'yes';

				wp_localize_script('wcfm_' . $this->slug . '_js', 'wcfm_' . $this->slug . '_screen_manage', $data);
				wp_localize_script('wcfm_' . $this->slug . '_js', 'wcfm_' . $this->slug . '_manage_messages', get_wcfm_cpt_manager_messages());
				wp_localize_script('wcfm_' . $this->slug . '_js', 'wcfm_params', [
					'ajax_url' => admin_url('admin-ajax.php'),
					'wcfm_ajax_nonce' => wp_create_nonce('wcfm_ajax_nonce'),
				]);
				break;

			case 'wcfm-' . $this->slug . '-manage':
				$WCFM->library->load_upload_lib();
				$WCFM->library->load_select2_lib();
				$WCFM->library->load_collapsible_lib();
				wp_enqueue_script('wcfm_' . $this->slug . '_manage_js', $this->plugin_url . 'js/' . $this->slug . '/wcfm-script-' . $this->slug . '-manage.js', ['jquery'], $WCFM->version, true);
				wp_localize_script('wcfm_' . $this->slug . '_manage_js', 'wcfm_' . $this->slug . '_manage_messages', get_wcfm_cpt_manager_messages());
				break;
		}
	}

	public function load_views( $end_point) {
		switch ( $end_point ) {
			case 'wcfm-' . $this->slug:
				include_once $this->plugin_path . 'views/' . $this->slug . '/wcfm-view-' . $this->slug . '.php';
				break;
			case 'wcfm-' . $this->slug . '-manage':
				include_once $this->plugin_path . 'views/' . $this->slug . '/wcfm-view-' . $this->slug . '-manage.php';
				break;
		}
	}

	public function ajax_controller() {
		$controllers_path = $this->plugin_path . 'controllers/' . $this->slug . '/';

		if (isset($_POST['controller'])) {
			$controller = $_POST['controller'];
			switch ($controller) {
				case 'wcfm-' . $this->slug:
					include_once $controllers_path . 'wcfm-controller-' . $this->slug . '.php';
					$controller_class = 'WCFM_' . ucfirst($this->slug) . '_Controller';
					new $controller_class();
					break;
				case 'wcfm-' . $this->slug . '-manage':
					include_once $controllers_path . 'wcfm-controller-' . $this->slug . '-manage.php';
					$controller_class = 'WCFM_' . ucfirst($this->slug) . '_Manage_Controller';
					new $controller_class();
					break;
			}
		}
	}
}
