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
		add_action( 'wp_fcu_admin_settings', array( $this, 'add_general_settings' ) );
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

		$upload_max_filesize = wp_fcu_size_format( wp_max_upload_size() );

		$section_nav = array(
			'general'       => array(
				'label'    => esc_html__( 'General', 'wp-fcu' ),
				'slug'     => 'general',
				'icon'     => 'dashicons dashicons-admin-generic',
				'url'      => admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $current_sub_page . '&section=general' ),
				'settings' => array(
					'chunk_size'    => array(
						'label'       => esc_html__( 'Upload Chunk Size', 'wp-fcu' ),
						'description' => esc_html__( 'Upload Chunk Size refers to the size (in MB) of the data segments used to divide large files for efficient uploading.', 'wp-fcu' ),
						'note'        => sprintf( esc_html__( 'Note: The default chunk size is 10MB. You can adjust the chunk size in the settings, but it should not be more than %s', 'wp-fcu' ), $upload_max_filesize ),
						'default'     => '10MB',
						'type'        => 'number',
						'max'         => intval( preg_replace( '/[^0-9]/', '', $upload_max_filesize ) ),
						'min'         => 1,
						'name'        => 'chunk_size',
					),
					'max_file_size' => array(
						'label'       => esc_html__( 'Max File Size', 'wp-fcu' ),
						'description' => esc_html__( 'Max File Size is the maximum size in MB of the uploaded file.', 'wp-fcu' ),
						'default'     => '10MB',
						'type'        => 'text',
						'max'         => intval( preg_replace( '/[^0-9]/', '', $upload_max_filesize ) ),
						'min'         => 0,
						'name'        => 'max_file_size',
					),
					'upload_dir'    => array(
						'label'       => esc_html__( 'Upload Directory', 'wp-fcu' ),
						'description' => esc_html__( 'Upload Directory refers to the directory where the uploaded files will be stored.', 'wp-fcu' ),
						'note'        => esc_html__( 'Note: The default path is wp-content/uploads/', 'wp-fcu' ),
						'type'        => 'text',
						'name'        => 'upload_dir',
					),
				),
			),
			'media_library' => array(
				'label'    => esc_html__( 'Media Library', 'wp-fcu' ),
				'slug'     => 'media_library',
				'icon'     => 'dashicons dashicons-admin-media',
				'url'      => admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $current_sub_page . '&section=media_library' ),
				'settings' => array(
					'upload_in_media_library' => array(
						'label'       => esc_html__( 'Upload in Media Library', 'wp-fcu' ),
						'description' => esc_html__( 'Upload in Media Library refers to whether the uploaded files will be uploaded to the media library.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'upload_in_media_library',
						'default'     => true,
					),
				),
			),
			'advanced'      => array(
				'label'    => esc_html__( 'Advanced', 'wp-fcu' ),
				'slug'     => 'advanced',
				'icon'     => 'dashicons dashicons-admin-tools',
				'url'      => admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $current_sub_page . '&section=advanced' ),
				'settings' => array(),
			),
			'codes'  => array(
				'label'    => esc_html__( 'Custom Codes', 'wp-fcu' ),
				'slug'     => 'codes',
				'icon'     => 'dashicons dashicons-editor-code',
				'url'      => admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $current_sub_page . '&section=codes' ),
				'settings' => array(
					'custom_css' => array(
						'label'       => esc_html__( 'Custom CSS', 'wp-fcu' ),
						'description' => esc_html__( 'Custom CSS refers to any additional CSS that you want to add to the chunk uploader.', 'wp-fcu' ),
						'type'        => 'textarea',
						'name'        => 'custom_css',
						'default'     => '',
					),
					'custom_js'  => array(
						'label'       => esc_html__( 'Custom JS', 'wp-fcu' ),
						'description' => esc_html__( 'Custom JS refers to any additional JS that you want to add to the chunk uploader.', 'wp-fcu' ),
						'type'        => 'textarea',
						'name'        => 'custom_js',
						'default'     => '',
					),
				),
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

		$this->render_input( $setting );
	}

	public function render_input( $settings ) {

		if ( empty( $settings ) || empty( $settings['type'] ) ) {
			return;
		}

		switch ( $settings['type'] ) {
			case 'number':
			case 'range':
			case 'email':
			case 'text':
				$max         = isset( $settings['max'] ) ? sprintf( 'max="%s"', $settings['max'] ) : '';
				$min         = isset( $settings['min'] ) ? sprintf( 'min="%s"', $settings['min'] ) : '';
				$placeholder = isset( $settings['placeholder'] ) ? sprintf( 'placeholder="%s"', $settings['placeholder'] ) : '';
				?>
				<input type="<?php echo esc_attr( $settings['type'] ); ?>" <?php echo esc_attr( $max ); ?> <?php echo esc_attr( $min ); ?> <?php echo esc_attr( $placeholder ); ?> name="<?php echo esc_attr( $settings['name'] ); ?>" value="<?php echo ''; ?>"/>
				<?php
				break;
			case 'textarea':
				?>
				<textarea name="<?php echo esc_attr( $settings['name'] ); ?>" rows="5" cols="50"></textarea>
				<?php
				break;
			case 'checkbox':
				?>
					<div class="can-toggle can-toggle--size-small">
						<input id="<?php echo esc_attr( $settings['name'] ); ?>" type="checkbox" name="<?php echo esc_attr( $settings['name'] ); ?>" value="1" >
						<label for="<?php echo esc_attr( $settings['name'] ); ?>">
							<div class="can-toggle__switch" data-checked="On" data-unchecked="Off"></div>
						</label>
					</div>
				<?php
				break;
		}
	}
}
