<?php
/**
 * Settings class.
 *
 * This file is used to load the settings page and its content.
 *
 * @package  WP_FCU
 */

namespace WP_FCU\Classes;

use WP_FCU\Traits\Singleton;

// Disable the direct access to this class.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Settings
 *
 * This class is responsible to load the settings page and its settings.
 */
class Settings {

	use Singleton;

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
		add_action( 'wpfcu_admin_content_settings', array( $this, 'add_settings_page_content' ) );
	}

	/**
	 * Add settings page.
	 *
	 * @return void
	 */
	public function add_settings_page_content() {
		require_once WP_FCU_PLUGIN_PATH . '/Templates/settings-page.php';
	}
}
