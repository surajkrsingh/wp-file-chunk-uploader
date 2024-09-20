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
			'general'         => array(
				'label'    => esc_html__( 'General', 'wp-fcu' ),
				'slug'     => 'general',
				'icon'     => 'dashicons dashicons-admin-generic',
				'url'      => admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $current_sub_page . '&section=general' ),
				'settings' => array(
					'file_upload_method' => array(
						'label'       => esc_html__( 'File Upload Method', 'wp-fcu' ),
						'description' => esc_html__( 'File Upload Method refers to the method used to upload files.', 'wp-fcu' ),
						'default'     => 'auto',
						'type'        => 'select',
						'name'        => 'file_upload_method',
						'options'     => array(
							'auto'   => esc_html__( 'Normal', 'wp-fcu' ),
							'native' => esc_html__( 'Chunked Upload', 'wp-fcu' ),
						),
						'quick_edit'  => true,
						'icon'        => 'dashicons dashicons-upload',
					),
					'allow_file_types'   => array(
						'label'       => esc_html__( 'Allowed File Types', 'wp-fcu' ),
						'description' => esc_html__( 'Allowed File Types refers to the file types that are allowed to be uploaded.', 'wp-fcu' ),
						'default'     => 'zip,jpg,jpeg,png,doc,docx,pdf',
						'type'        => 'text',
						'name'        => 'allow_file_types',
						'placeholder' => 'zip,jpg,jpeg,png,doc,docx,pdf',
						'note'        => esc_html__( 'Enter the file types separated by commas.', 'wp-fcu' ),
						'icon'        => 'dashicons dashicons-format-gallery',
					),
					'chunk_size'         => array(
						'label'       => esc_html__( 'Upload Chunk Size', 'wp-fcu' ),
						'description' => esc_html__( 'Upload Chunk Size refers to the size (in MB) of the data segments used to divide large files for efficient uploading.', 'wp-fcu' ),
						'note'        => sprintf( esc_html__( 'Note: The default chunk size is 10MB. You can adjust the chunk size in the settings, but it should not be more than %s', 'wp-fcu' ), $upload_max_filesize ),
						'default'     => '10MB',
						'type'        => 'number',
						'max'         => intval( preg_replace( '/[^0-9]/', '', $upload_max_filesize ) ),
						'min'         => 1,
						'placeholder' => '10',
						'name'        => 'chunk_size',
						'quick_edit'  => true,
						'icon'        => 'dashicons dashicons-hammer',
					),
					'max_file_size'      => array(
						'label'       => esc_html__( 'Maximum File Size', 'wp-fcu' ),
						'description' => esc_html__( 'Max File Size is the maximum size in MB of the uploaded file.', 'wp-fcu' ),
						'default'     => '10MB',
						'type'        => 'text',
						'max'         => intval( preg_replace( '/[^0-9]/', '', $upload_max_filesize ) ),
						'min'         => 0,
						'placeholder' => intval( preg_replace( '/[^0-9]/', '', $upload_max_filesize ) ),
						'name'        => 'max_file_size',
						'quick_edit'  => true,
						'icon'        => 'dashicons dashicons-hammer',
					),
					'max_upload_size'    => array(
						'label'       => esc_html__( 'Maximum Upload Size', 'wp-fcu' ),
						'description' => esc_html__( 'Max Upload Size is the maximum size in MB of the uploaded file.', 'wp-fcu' ),
						'default'     => 0,
						'type'        => 'number',
						'max'         => intval( preg_replace( '/[^0-9]/', '', $upload_max_filesize ) ),
						'min'         => 0,
						'placeholder' => intval( preg_replace( '/[^0-9]/', '', $upload_max_filesize ) ),
						'name'        => 'max_upload_size',
						'quick_edit'  => true,
						'icon'        => 'dashicons dashicons-hammer',
					),
					'upload_dir'         => array(
						'label'       => esc_html__( 'Upload Directory', 'wp-fcu' ),
						'description' => esc_html__( 'Upload Directory refers to the directory where the uploaded files will be stored.', 'wp-fcu' ),
						'note'        => esc_html__( 'Note: The default path is wp-content/uploads/', 'wp-fcu' ),
						'type'        => 'text',
						'placeholder' => 'wp-content/uploads/',
						'name'        => 'upload_dir',
						'icon'        => 'dashicons dashicons-upload',
					),
				),
			),
			'media_library'   => array(
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
						'quick_edit'  => true,
						'icon'        => 'dashicons dashicons-admin-media',
					),
				),
			),
			'advanced'        => array(
				'label'    => esc_html__( 'Advanced', 'wp-fcu' ),
				'slug'     => 'advanced',
				'icon'     => 'dashicons dashicons-admin-tools',
				'url'      => admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $current_sub_page . '&section=advanced' ),
				'settings' => array(
					'is_create_attachments'  => array(
						'label'       => esc_html__( 'Create Media Attachments', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want the uploaded files to be added as attachments in the media library.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'is_create_attachments',
						'default'     => true,
						'note'        => esc_html__( 'Note: When uploading images via external pages or shortcodes, files will automatically be created as WordPress media attachments.', 'wp-fcu' ),
						'quick_edit'  => true,
						'icon'        => 'dashicons dashicons-admin-links',
					),
					'enable_logging'         => array(
						'label'       => esc_html__( 'Enable Logging', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to enable logging.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'enable_logging',
						'default'     => false,
						'note'        => esc_html__( 'Note: Logging is disabled by default.', 'wp-fcu' ),
					),
					'user_role_based_access' => array(
						'label'       => esc_html__( 'User Role Based Access', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to enable user role based access.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'user_role_based_access',
						'default'     => false,
						'note'        => esc_html__( 'Note: User role based access is disabled by default.', 'wp-fcu' ),
						'help'        => esc_html__( 'Note: User role based access is disabled by default.', 'wp-fcu' ),
					),
					'google_captcha'         => array(
						'label'       => esc_html__( 'Google ReCaptcha', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to enable Google ReCaptcha.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'google_captcha',
						'default'     => false,
						'note'        => esc_html__( 'Note: Google ReCaptcha is disabled by default.', 'wp-fcu' ),
					),
					'max_upload_per_user'    => array(
						'label'       => esc_html__( 'Max Upload Per User', 'wp-fcu' ),
						'description' => esc_html__( 'Max Upload Per User refers to the maximum number of files that can be uploaded per user.', 'wp-fcu' ),
						'type'        => 'number',
						'name'        => 'max_upload_per_user',
						'default'     => 1,
						'note'        => esc_html__( 'Note: Max Upload Per User is set to 1 by default.', 'wp-fcu' ),
					),
					'file_scanning'          => array(
						'label'       => esc_html__( 'File Scanning', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to enable file scanning.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'file_scanning',
						'default'     => false,
						'note'        => esc_html__( 'Note: File scanning is disabled by default.', 'wp-fcu' ),
					),
					'disable_progress_bar'   => array(
						'label'       => esc_html__( 'Disable Progress Bar', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to disable the progress bar.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'disable_progress_bar',
						'default'     => false,
						'note'        => esc_html__( 'Note: Progress bar is enabled by default.', 'wp-fcu' ),
					),
				),
			),
			'user_experience' => array(
				'label'    => esc_html__( 'User Experience', 'wp-fcu' ),
				'slug'     => 'user_experience',
				'icon'     => 'dashicons dashicons-admin-appearance',
				'url'      => admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $current_sub_page . '&section=user_experience' ),
				'settings' => array(
					'display_upload_status' => array(
						'label'       => esc_html__( 'Display Upload Status', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to display the upload status.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'display_upload_status',
						'default'     => false,
						'note'        => esc_html__( 'Note: Upload status is enabled by default.', 'wp-fcu' ),
					),
					'enable_drag_drop'      => array(
						'label'       => esc_html__( 'Enable Drag & Drop', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to enable drag & drop.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'enable_drag_drop',
						'default'     => false,
						'note'        => esc_html__( 'Note: Drag & Drop is enabled by default.', 'wp-fcu' ),
					),
					'auto_upload_start'     => array(
						'label'       => esc_html__( 'Auto Upload Start', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to enable auto upload start.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'auto_upload_start',
						'default'     => false,
						'note'        => esc_html__( 'Note: Auto upload start is enabled by default.', 'wp-fcu' ),
					),
					'allow_pause_resume'    => array(
						'label'       => esc_html__( 'Allow Pause / Resume', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to allow pause / resume.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'allow_pause_resume',
						'default'     => false,
						'note'        => esc_html__( 'Note: Pause / Resume is enabled by default.', 'wp-fcu' ),
					),
					'allow_cancel_upload'   => array(
						'label'       => esc_html__( 'Allow Cancel Upload', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to allow cancel upload.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'allow_cancel_upload',
						'default'     => false,
						'note'        => esc_html__( 'Note: Cancel upload is enabled by default.', 'wp-fcu' ),
					),
					'backup_on_failure'     => array(
						'label'       => esc_html__( 'Backup on Failure', 'wp-fcu' ),
						'description' => esc_html__( 'Enable this option if you want to enable backup on failure.', 'wp-fcu' ),
						'type'        => 'checkbox',
						'name'        => 'backup_on_failure',
						'default'     => false,
						'note'        => esc_html__( 'Note: Backup on failure is enabled by default.', 'wp-fcu' ),
					),
				),
			),
			'codes'           => array(
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

	/**
	 * Render the setting.
	 *
	 * @param array $setting Setting array.
	 *
	 * @return mixed
	 */
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
				$placeholder = isset( $settings['placeholder'] ) ? sprintf( 'placeholder=%s', $settings['placeholder'] ) : '';
				?>
				<div class="wp-fcu-input-wrapper">
					<label for="<?php echo esc_attr( $settings['name'] ); ?>">
						<input type="<?php echo esc_attr( $settings['type'] ); ?>" <?php echo esc_attr( $max ); ?> <?php echo esc_attr( $min ); ?> <?php echo esc_attr( $placeholder ); ?> name="<?php echo esc_attr( $settings['name'] ); ?>" value="<?php echo ''; ?>"/>
					</label>
				</div>
				<?php
				break;
			case 'textarea':
				?>
				<div class="wp-fcu-input-wrapper">
					<textarea name="<?php echo esc_attr( $settings['name'] ); ?>" rows="5" cols="50"></textarea>
				</div>
				<?php
				break;
			case 'checkbox':
				?>
					<div class="can-toggle wp-fcu-input-wrapper">
						<input id="<?php echo esc_attr( $settings['name'] ); ?>" type="checkbox" name="<?php echo esc_attr( $settings['name'] ); ?>" value="1" >
						<label for="<?php echo esc_attr( $settings['name'] ); ?>">
							<div class="can-toggle__switch" data-checked="On" data-unchecked="Off"></div>
						</label>
					</div>
				<?php
				break;
			case 'select':
				?>
				<div class="wp-fcu-input-wrapper">
					<select name="<?php echo esc_attr( $settings['name'] ); ?>">
						<?php
						foreach ( $settings['options'] as $key => $option ) {
							?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $option ); ?></option>
							<?php
						}
						?>
					</select>
				</div>
				<?php
				break;
		}
	}
}
