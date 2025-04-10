<?php
/**
 * Plugin Name: Wolf WCFM Integration
 * Description: Integrate existing custom post type into WCFM dashboard.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wolf_WCFM_Integration {

	/**
	 * @var string
	 */
	public $version = '1.0.0';

	public function __construct() {

		$this->define_constants();

		add_action( 'wcfm_init', array( $this, 'init' ), 20 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'load_default_styles' ) );
	}

	public function init() {

		require_once 'helpers/core-functions.php';
		require_once 'core/class-wcfm-cpt.php';
		require_once 'core/class-wcfm-filters.php';

		$cpt_config = require 'config/cpt-config.php';

		foreach ( $cpt_config as $cpt ) {
			new Wolf_WCFM_CPT_Module( $cpt );
		}

		new Wolf_WCFM_Filters();
	}

	/**
		 * Define constant if not already set
		 *
		 * @param  string      $name The constant to define.
		 * @param  string|bool $value The constant value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Define WR Constants
		 */
		private function define_constants() {

			$constants = array(
				'WWCFI_DIR'            => $this->plugin_path(),
				'WWCFI_URI'            => $this->plugin_url(),
				'WWCFI_CSS'            => $this->plugin_url() . '/assets/css',
				'WWCFI_JS'             => $this->plugin_url() . '/assets/js',
				'WWCFI_SLUG'           => plugin_basename( dirname( __FILE__ ) ),
				'WWCFI_PATH'           => plugin_basename( __FILE__ ),
				'WWCFI_VERSION'        => $this->version,
			);

			foreach ( $constants as $name => $value ) {
				$this->define( $name, $value );
			}
		}

	/**
	 * Load styles
	 *
	 * @param [type] $end_point
	 * @return void
	 */
	public function load_default_styles() {

		global $WCFM;
		wp_enqueue_style( 'wcfm_base_css', $this->plugin_url() . 'css/wcfm-styles.css', array(), $WCFM->version );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

new Wolf_WCFM_Integration();
