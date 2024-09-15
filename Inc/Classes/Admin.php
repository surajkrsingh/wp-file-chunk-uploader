<?php
/**
 * Admin class.
 *
 * This is core class which is responsible for admin functionality.
 *
 * @package WP_FCU
 */

namespace WP_FCU\Classes;

use WP_FCU\Traits\Singleton;

// Disable the direct access to this class.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin
 */
class Admin {

	use Singleton;

	/**
	 * Current screen
	 *
	 * @var string
	 */
	private $current_screen = 'settings_page_wp-file-chunk-uploader';

	/**
	 * Construct method.
	 */
	protected function __construct() {
		$this->setup_hooks();
	}

	/**
	 * To setup action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_filter( 'admin_body_class', array( $this, 'add_admin_body_class' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'in_admin_header', array( $this, 'add_settings_header' ), 99 );
		add_action( 'wpfcu_admin_content_welcome', array( $this, 'add_welcome_page_content' ) );
	}

	/**
	 * Add plugin menu.
	 */
	public function add_menu() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_submenu_page(
			'options-general.php',
			esc_html__( 'File Chunk Uploader', 'background-process' ),
			esc_html__( 'File Chunk Uploader', 'background-process' ),
			'manage_options',
			'wp-file-chunk-uploader',
			array( $this, 'add_page_content' )
		);
	}

	/**
	 * Add admin page content.
	 *
	 * @return void
	 */
	public function add_page_content() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once WP_FCU_PLUGIN_PATH . '/templates/admin.php';
	}

	/**
	 * Add body class for admin settings page.
	 *
	 * @param string $classes Body classes name.
	 *
	 * @return string
	 */
	public function add_admin_body_class( $classes ) {

		if ( get_current_screen()->id !== $this->current_screen ) {
			return $classes;
		}

		$classes .= ' wp-fcu-admin-page';

		return $classes;
	}

	/**
	 * Add admin menu page content.
	 *
	 * @return void
	 */
	public function add_welcome_page_content() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once WP_FCU_PLUGIN_PATH . '/templates/welcome-page.php';
	}

	/**
	 * Include settings header content.
	 *
	 * @return void
	 */
	public function add_settings_header() {
		if ( get_current_screen()->id !== $this->current_screen ) {
			return;
		}

		require_once WP_FCU_PLUGIN_PATH . '/Templates/admin-header.php';
	}

	/**
	 * To enqueue scripts and styles in admin.
	 *
	 * @param string $hook_suffix Admin page name.
	 *
	 * @return void
	 */
	public function load_scripts( $hook_suffix ) {

		if ( get_current_screen()->id !== $this->current_screen ) {
			return;
		}

		wp_enqueue_style(
			'wp_fcu_admin_styles',
			WP_FCU_PLUGIN_URL . '/dist/css/admin.css',
			array(),
			WP_FCU_PLUGIN_VERSION
		);

		wp_register_script(
			'wp_fcu_admin_scripts',
			WP_FCU_PLUGIN_URL . '/dist/js/admin.js',
			array( 'jquery' ),
			WP_FCU_PLUGIN_VERSION,
			true
		);

		wp_localize_script(
			'wp_fcu_admin_scripts',
			'FCU_Objects',
			array(
				'ajaxURL'             => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'wp_fcu_nonce' ),
				'default_upload_size' => Media_Library_Uploader::get_instance()->max_upload_size,
				'local_types'         => get_filetypes( true ),
			)
		);

		wp_enqueue_script( 'wp_fcu_admin_scripts' );
	}
}
