<?php
/**
 * Plugin Name: Wolf WCFM Integration
 * Description: Integrate existing custom post type into WCFM dashboard.
 * Version: 1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wolf_WCFM_Integration {

	/**
	 * @var string
	 */
	public $version = '1.0.2';

	public function __construct() {

		$this->define_constants();

		add_action( 'wcfm_init', array( $this, 'init' ), 20 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'load_default_styles' ), 99 );

		add_action( 'wp_ajax_delete_wcfm_video', array( $this, 'delete_wcfm_video_handler' ) );
		add_action( 'wp_ajax_delete_wcfm_event', array( $this, 'delete_wcfm_event_handler' ) );
		add_action( 'wp_ajax_delete_wcfm_work', array( $this, 'delete_wcfm_work_handler' ) );

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
			'WWCFI_DIR'     => $this->plugin_path(),
			'WWCFI_URI'     => $this->plugin_url(),
			'WWCFI_CSS'     => $this->plugin_url() . '/assets/css',
			'WWCFI_JS'      => $this->plugin_url() . '/assets/js',
			'WWCFI_SLUG'    => plugin_basename( __DIR__ ),
			'WWCFI_PATH'    => plugin_basename( __FILE__ ),
			'WWCFI_VERSION' => $this->version,
		);

		foreach ( $constants as $name => $value ) {
			$this->define( $name, $value );
		}
	}

	/**
	 * Delete video handler
	 */
	public function delete_wcfm_video_handler() {
		$video_id = isset( $_POST['videoid'] ) ? absint( $_POST['videoid'] ) : 0;

		if ( ! $video_id ) {
			wp_send_json_error( array( 'message' => 'Invalid video ID' ) );
		}

		$post = get_post( $video_id );
		if ( ! $post || $post->post_type !== 'video' ) {
			wp_send_json_error( array( 'message' => 'Invalid video' ) );
		}

		// For vendors, check if they own the post
		if ( function_exists( 'wcfm_is_vendor' ) && wcfm_is_vendor() ) {
			$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			if ( $post->post_author != $current_user_id ) {
				wp_send_json_error( array( 'message' => 'Permission denied' ) );
			}
		}

		// Delete the post
		$deleted = wp_trash_post( $video_id );

		if ( $deleted ) {
			wp_send_json_success( array( 'message' => 'Video deleted successfully' ) );
		} else {
			wp_send_json_error( array( 'message' => 'Failed to delete video' ) );
		}
	}

	/**
	 * Delete event handler
	 */
	public function delete_wcfm_event_handler() {
		$event_id = isset( $_POST['eventid'] ) ? absint( $_POST['eventid'] ) : 0;

		if ( ! $event_id ) {
			wp_send_json_error( array( 'message' => 'Invalid event ID' ) );
		}

		$post = get_post( $event_id );
		if ( ! $post || $post->post_type !== 'event' ) {
			wp_send_json_error( array( 'message' => 'Invalid event' ) );
		}

		if ( function_exists( 'wcfm_is_vendor' ) && wcfm_is_vendor() ) {
			$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			if ( $post->post_author != $current_user_id ) {
				wp_send_json_error( array( 'message' => 'Permission denied' ) );
			}
		}

		$deleted = wp_trash_post( $event_id );

		if ( $deleted ) {
			wp_send_json_success( array( 'message' => 'Event deleted successfully' ) );
		} else {
			wp_send_json_error( array( 'message' => 'Failed to delete event' ) );
		}
	}

	/**
	 * Delete work handler
	 */
	public function delete_wcfm_work_handler() {
		$work_id = isset( $_POST['workid'] ) ? absint( $_POST['workid'] ) : 0;

		if ( ! $work_id ) {
			wp_send_json_error( array( 'message' => 'Invalid work ID' ) );
		}

		$post = get_post( $work_id );
		if ( ! $post || $post->post_type !== 'work' ) {
			wp_send_json_error( array( 'message' => 'Invalid work' ) );
		}

		if ( function_exists( 'wcfm_is_vendor' ) && wcfm_is_vendor() ) {
			$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			if ( $post->post_author != $current_user_id ) {
				wp_send_json_error( array( 'message' => 'Permission denied' ) );
			}
		}

		$deleted = wp_trash_post( $work_id );

		if ( $deleted ) {
			wp_send_json_success( array( 'message' => 'Work deleted successfully' ) );
		} else {
			wp_send_json_error( array( 'message' => 'Failed to delete work' ) );
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
		wp_enqueue_style( 'wcfm_base_css', $this->plugin_url() . '/css/wcfm-styles.css', array(), $WCFM->version );
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