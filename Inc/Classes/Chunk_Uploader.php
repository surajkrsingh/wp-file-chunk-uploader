<?php
/**
 * Chunk Uploader class.
 *
 * This class is used to handle chunked file uploads via AJAX.
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
class Chunk_Uploader {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {
		$this->setup_variables();
		$this->setup_hooks();
	}

	/**
	 * Setup variables.
	 *
	 * @return void
	 */
	protected function setup_variables() {
		// do something
	}

	/**
	 * To setup action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		add_shortcode( 'wpfcu_chunk_uploader', array( $this, 'add_chunk_uploader' ) );
		add_action( 'wp_ajax_chunk_upload', array( $this, 'chunk_upload' ) );
	}

	/**
	 * Add admin menu page content.
	 *
	 * @return void
	 */
	public function add_chunk_uploader() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once WP_FCU_PLUGIN_PATH . '/templates/uploader-page.php';
	}

	/**
	 * Handle chunked file uploads via AJAX.
	 *
	 * This function processes the chunked uploads, assembles the final file, and stores it in the WordPress uploads directory.
	 *
	 * @return void
	 */
	public function chunk_upload() {
		// Verify nonce for security
		// if ( ! isset( $_POST['upload_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['upload_nonce'] ) ), 'chunk_upload_nonce' ) ) {
		// wp_send_json_error( __( 'Invalid nonce verification.', 'textdomain' ) );
		// }

		// Check if the required fields are present
		if ( ! isset( $_FILES['file'], $_POST['fileName'], $_POST['chunkIndex'], $_POST['totalChunks'] ) ) {
			wp_send_json_error( __( 'Missing required fields.', 'textdomain' ) );
		}

		// Sanitize and validate input fields
		$file         = $_FILES['file'];
		$file_name    = sanitize_file_name( wp_unslash( $_POST['fileName'] ) );
		$chunk_index  = absint( $_POST['chunkIndex'] );
		$total_chunks = absint( $_POST['totalChunks'] );

		// Validate file type (only allow ZIP files)
		$allowed_file_type = 'application/zip';
		$file_type         = wp_check_filetype( $file_name );
		if ( $file_type['type'] !== $allowed_file_type ) {
			wp_send_json_error( __( 'Invalid file type. Only ZIP files are allowed.', 'textdomain' ) );
		}

		// Create a temporary directory for storing file chunks
		$upload_dir = wp_upload_dir();
		$target_dir = trailingslashit( $upload_dir['basedir'] ) . 'chunked_uploads/' . pathinfo( $file_name, PATHINFO_FILENAME );

		// Ensure the target directory exists
		if ( ! file_exists( $target_dir ) ) {
			if ( ! wp_mkdir_p( $target_dir ) ) {
				wp_send_json_error( __( 'Failed to create upload directory.', 'textdomain' ) );
			}
		}

		// Save the chunk to the temporary directory
		$chunk_file = trailingslashit( $target_dir ) . 'chunk_' . $chunk_index;
		if ( ! move_uploaded_file( $file['tmp_name'], $chunk_file ) ) {
			wp_send_json_error( __( 'Failed to save chunk file.', 'textdomain' ) );
		}

		// If this is the last chunk, assemble the final file
		if ( $chunk_index + 1 === $total_chunks ) {
			$final_file_path = trailingslashit( $upload_dir['basedir'] ) . $file_name;

			// Open the final file for writing
			$output = fopen( $final_file_path, 'wb' );
			if ( ! $output ) {
				wp_send_json_error( __( 'Failed to open final file for writing.', 'textdomain' ) );
			}

			// Append all chunks to the final file
			for ( $i = 0; $i < $total_chunks; $i++ ) {
				$chunk_file_path = trailingslashit( $target_dir ) . 'chunk_' . $i;
				$chunk_content   = file_get_contents( $chunk_file_path );

				if ( false === $chunk_content ) {
					fclose( $output );
					wp_send_json_error( __( 'Failed to read chunk file.', 'textdomain' ) );
				}

				// Write the chunk content to the final file
				if ( false === fwrite( $output, $chunk_content ) ) {
					fclose( $output );
					wp_send_json_error( __( 'Failed to write chunk to final file.', 'textdomain' ) );
				}

				// Delete the chunk after appending it
				unlink( $chunk_file_path );
			}

			// Close the final file
			fclose( $output );

			// Optionally remove the temporary directory after final assembly
			rmdir( $target_dir );

			// Return success response after complete upload
			wp_send_json_success(
				array(
					'message'  => __( 'File uploaded successfully.', 'textdomain' ),
					'fileUrl'  => esc_url( trailingslashit( $upload_dir['baseurl'] ) . $file_name ),
					'fileName' => esc_html( $file_name ),
				)
			);
		} else {
			// Return success response for each chunk
			wp_send_json_success( __( 'Chunk uploaded successfully.', 'textdomain' ) );
		}
	}

}
