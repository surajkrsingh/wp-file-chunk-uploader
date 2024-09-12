<?php
/**
 * Main class file for plugin.
 *
 * @package WP_FCU
 */

namespace WP_FCU\Classes;

// Disable the direct access to this class.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_FCU\Traits\Singleton;
use WP_FCU\Classes\Admin;
use WP_FCU\Classes\Settings;
use WP_FCU\Classes\Chunk_Uploader;

/**
 * Class Background_Process
 */
class Main {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {
		// Load plugin classes.
		Admin::get_instance();
		Settings::get_instance();
		Chunk_Uploader::get_instance();
		$this->setup_hooks();
	}

	/**
	 * To setup action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		register_activation_hook( WP_FCU_PLUGIN_FILE, array( $this, 'plugin_activation' ) );
		register_deactivation_hook( WP_FCU_PLUGIN_FILE, array( $this, 'plugin_deactivation' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_text_domain' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
	}

	/**
	 * Activation of plugin.
	 *
	 * @return void
	 */
	public function plugin_activation() {
		do_action( 'wp_fcu_activated' );
		flush_rewrite_rules();
	}

	/**
	 * Deactivation of plugin.
	 *
	 * @return void
	 */
	public function plugin_deactivation() {
		do_action( 'wp_fcu_deactivated' );
		flush_rewrite_rules();
	}

	/**
	 * Load plugin text domain.
	 *
	 * @return void
	 */
	public function load_plugin_text_domain() {
		load_plugin_textdomain( 'wp-file-chunk-uploader', false, WP_FCU_PLUGIN_DIR_NAME . '/languages' );
	}

	/**
	 * Add admin menu page content.
	 *
	 * @return void
	 */
	public function load_scripts() {
		// $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'wp_fcu_main_scripts', WP_FCU_PLUGIN_URL . '/dist/js/public.js', array( 'jquery' ), WP_FCU_PLUGIN_VERSION, true );
		wp_enqueue_script( 'wp_fcu_main_scripts' );

		wp_localize_script(
			'wp_fcu_main_scripts',
			'FCU_Objects',
			array(
				'ajaxURL' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wp_fcu_nonce' ),
			)
		);
	}
}
