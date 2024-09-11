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
		add_action( 'wp_fcu_admin_settings_general', array( $this, 'add_general_settings' ) );
	}

	/**
	 * Add settings page.
	 *
	 * @return void
	 */
	public function add_settings_page_content() {
		require_once WP_FCU_PLUGIN_PATH . '/Templates/settings-page.php';
	}

	/**
	 * Add general settings.
	 *
	 * @return void
	 */
	public function add_general_settings( $current_section ) {

		if ( empty( $current_section ) ) {
			return;
		}

		require_once WP_FCU_PLUGIN_PATH . '/Templates/settings-options.php';
	}

	public function get_settings( $section = '' ) {

		$current_sub_page = isset( $_GET['subpage'] ) ? sanitize_text_field( wp_unslash( $_GET['subpage'] ) ) : 'welcome'; // phpcs:ignore
		$current_sub_page = str_replace( '-', '_', $current_sub_page );

		$current_section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : 'general'; // phpcs:ignore
		$current_section = str_replace( '-', '_', $current_section );

		$section_nav = array(
			'general'  => array(
				'label'    => esc_html__( 'General', 'wp-fcu' ),
				'slug'     => 'general',
				'icon'     => 'dashicons dashicons-admin-generic',
				'url'      => admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $current_sub_page . '&section=general' ),
				'settings' => array(
					'chunk_size' => array(
						'label'       => esc_html__( 'Upload Chunk Size', 'wp-fcu' ),
						'description' => esc_html__( 'Upload Chunk Size is the size in MB of data segments used to split large files for efficient uploading.', 'wp-fcu' ),
						'default'     => '10MB',
						'type'        => 'text',
						'name'        => 'chunk_size',
					),
					'max_file_size' => array(
						'label'       => esc_html__( 'Max File Size', 'wp-fcu' ),
						'description' => esc_html__( 'Max File Size is the maximum size in MB of the uploaded file.', 'wp-fcu' ),
						'default'     => '10MB',
						'type'        => 'text',
						'name'        => 'max_file_size',
					),
				),
			),
			'advanced' => array(
				'label' => esc_html__( 'Advanced', 'wp-fcu' ),
				'slug'  => 'advanced',
				'icon'  => 'dashicons dashicons-admin-tools',
				'url'   => admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $current_sub_page . '&section=advanced' ),
			),
		);

		if ( isset( $section_nav[ $section ] ) ) {
			return $section_nav[ $section ]['settings'];
		}

		return $section_nav;
	}

	public function render_setting( $setting ) {

		if ( empty( $setting ) ) {
			return;
		}

		$this->render_input( $setting['type'], $setting['name'], $value = '' );
	}

	public function render_input( $type, $name, $value = '' ) {
		switch ( $type ) {
			case 'text':
				?>
				<input  placeholder="<?php echo esc_attr( $value ); ?>" type="text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
				<?php
				break;
		}
	}
}
