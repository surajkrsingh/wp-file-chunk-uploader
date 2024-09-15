<?php
/**
 * Media_Library_Uploader class.
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
 * Class Media_Library_Uploader
 */
class Media_Library_Uploader {

	use Singleton;

	/**
	 * Max upload size.
	 *
	 * @var int
	 */
	public $max_upload_size = GB_IN_BYTES * 5;

	/**
	 * Max chunk size.
	 *
	 * @var int
	 */
	public $max_chunk_size = MB_IN_BYTES * 10;

	/**
	 * Default chunk size.
	 *
	 * @var integer
	 */
	public $default_chunk_size = MB_IN_BYTES * 10;

	/**
	 * Upload capability.
	 *
	 * @var string
	 */
	public $capability = 'upload_files';

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
	private function setup_variables() {
		$this->max_upload_size = GB_IN_BYTES * 5;
		$this->max_chunk_size  = MB_IN_BYTES * 10;
	}

	/**
	 * To setup action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		add_filter( 'upload_post_params', array( $this, 'update_plupload_params' ) );
		add_filter( 'plupload_default_params', array( $this, 'update_plupload_params' ) );
		add_filter( 'plupload_init', array( $this, 'update_plupload_settings' ) );
		add_filter( 'plupload_default_settings', array( $this, 'update_plupload_settings' ) );
		add_filter( 'upload_size_limit', array( $this, 'update_upload_size_limit' ) );
		add_filter( 'block_editor_settings_all', array( $this, 'gutenberg_size_filter' ) );
		add_action( 'post-upload-ui', array( $this, 'update_plupload_form' ) );
		add_action( 'wp_ajax_wp_ml_chunk_uploader', array( $this, 'chunk_upload_handler' ) );
	}

	/**
	 * Add action in plupload params.
	 *
	 * @param array $plupload_params PlUpload params.
	 *
	 * @return array $plupload_params
	 */
	public function update_plupload_params( $plupload_params ) {

		$plupload_params['action'] = 'wp_ml_chunk_uploader';

		return $plupload_params;
	}

	/**
	 * Filter plupload settings.
	 *
	 * @param array $plupload_settings Plupload settings.
	 *
	 * @return array
	 */
	public function update_plupload_settings( $plupload_settings ) {

		$max_chunk  = ! empty( $this->max_chunk_size ) ? $this->max_chunk_size : $this->default_chunk_size;
		$chunk_size = $max_chunk / KB_IN_BYTES;

		$plupload_settings['url']                      = admin_url( 'admin-ajax.php' );
		$plupload_settings['filters']['max_file_size'] = $this->max_upload_size . 'b';
		$plupload_settings['chunk_size']               = $chunk_size . 'kb';
		$plupload_settings['max_retries']              = 1;

		return $plupload_settings;
	}

	/**
	 * Filter the max upload size.
	 *
	 * @param array $editor_settings Editor settings.
	 *
	 * @return array
	 */
	public function gutenberg_size_filter( $editor_settings ) {
		$editor_settings['maxUploadFileSize'] = $this->max_upload_size;

		return $editor_settings;
	}

	/**
	 * Filter the max upload size.
	 *
	 * @return int $bytes
	 */
	public function update_upload_size_limit() {
		return $this->max_upload_size;
	}

	/**
	 * Chunk Upload handler.
	 *
	 * Reference: https://www.plupload.com/docs/Chunking
	 *
	 * @return mixed
	 */
	public function chunk_upload_handler() {

		check_admin_referer( 'media-form' );

		// Check that we have an upload and there are no errors.
		if ( empty( $_FILES ) || ! empty( $_FILES['async-upload']['error'] ) ) {
			wp_die( esc_html__( 'Sorry, there was an error uploading your file. Please try again.' ) );
		}

		// Check that we have a user that can upload.
		if ( ! is_user_logged_in() || ! current_user_can( $this->capability ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to upload files.' ) );
		}

		// Get the file name, chunk number and chunks total.
		$chunk        = isset( $_POST['chunk'] ) ? intval( $_POST['chunk'] ) : 0;
		$chunks       = isset( $_POST['chunks'] ) ? intval( $_POST['chunks'] ) : 0;
		$file_name    = isset( $_POST['name'] ) ? $_POST['name'] : $_FILES['async-upload']['name'];
		$current_part = $chunk + 1;

		$wp_fcu_temp_dir = apply_filters( 'wp_fcu_temp_dir', WP_CONTENT_DIR . '/wp-chunked-uploads' );

		// Check if this is first chunk.
		if ( 0 === $chunk ) {
			if ( ! is_dir( $wp_fcu_temp_dir ) ) {
				wp_mkdir_p( $wp_fcu_temp_dir );
			}

			// Protect temp directory from browsing.
			$index_path_name = trailingslashit( $wp_fcu_temp_dir ) . 'index.php';
			if ( ! file_exists( $index_path_name ) ) {
				$file = fopen( $index_path_name, 'w' );
				if ( false !== $file ) {
					fwrite( $file, "<?php\n// Silence is golden.\n" );
					fclose( $file );
				}
			}

			// Scan temp dir for files older than 24 hours and delete them.
			$files = glob( $wp_fcu_temp_dir . '/*.part' );
			if ( is_array( $files ) ) {
				foreach ( $files as $file ) {
					if ( @filemtime( $file ) < time() - DAY_IN_SECONDS ) {
						@unlink( $file );
					}
				}
			}
		}

		$file_path = sprintf( '%s/%d-%s.part', $wp_fcu_temp_dir, get_current_blog_id(), sha1( $file_name ) );

		// Add logs if debug mode is on.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$size = file_exists( $file_path ) ? size_format( filesize( $file_path ), 3 ) : '0 B';
			error_log( sprintf( 'Chunk file uploader: Processing "%s" part %d of %d as %s. %s processed so far."', $file_name, $current_part, $chunks, $file_path, $size ) );
		}

		if ( file_exists( $file_path ) && filesize( $file_path ) + filesize( $_FILES['async-upload']['tmp_name'] ) > $this->max_upload_size ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Chunk file uploader: File size limit exceeded.' );
			}

			if ( ! $chunks || $chunk === $chunks - 1 ) {
				@unlink( $file_path );

				if ( ! isset( $_POST['short'] ) || ! isset( $_POST['type'] ) ) {
					echo wp_json_encode(
						array(
							'success' => false,
							'data'    => array(
								'message'  => __( 'The file size has exceeded the maximum file size setting.', 'wp-file-chunk-uploader' ),
								'filename' => $file_name,
							),
						)
					);
					wp_die();
				} else {
					status_header( 202 );
					printf(
						'<div class="error-div error">%s <strong>%s</strong><br />%s</div>',
						sprintf(
							'<button type="button" class="dismiss button-link" onclick="jQuery(this).parents(\'div.media-item\').slideUp(200, function(){jQuery(this).remove();});">%s</button>',
							__( 'Dismiss' )
						),
						sprintf(
						/* translators: %s: Name of the file that failed to upload. */
							__( '&#8220;%s&#8221; has failed to upload.' ),
							esc_html( $file_name )
						),
						__( 'The file size has exceeded the maximum file size setting.', 'tuxedo-big-file-uploads' )
					);
					exit;
				}
			}

			die();
		}

		/** Open temp file. */
		if ( $chunk == 0 ) {
			$out = @fopen( $file_path, 'wb' );
		} elseif ( is_writable( $file_path ) ) {
			$out = @fopen( $file_path, 'ab' );
		} else {
			$out = false;
		}

		if ( $out ) {

			/** Read binary input stream and append it to temp file. */
			$in = @fopen( $_FILES['async-upload']['tmp_name'], 'rb' );

			if ( $in ) {
				while ( $buff = fread( $in, 4096 ) ) {
					fwrite( $out, $buff );
				}
			} else {
				/** Failed to open input stream. */
				/** Attempt to clean up unfinished output. */
				@fclose( $out );
				@unlink( $file_path );
				error_log( "BFU: Error reading uploaded part $current_part of $chunks." );

				if ( ! isset( $_POST['short'] ) || ! isset( $_POST['type'] ) ) {
					echo wp_json_encode(
						array(
							'success' => false,
							'data'    => array(
								'message'  => sprintf( __( 'There was an error reading uploaded part %1$d of %2$d.', 'tuxedo-big-file-uploads' ), $current_part, $chunks ),
								'filename' => esc_html( $file_name ),
							),
						)
					);
					wp_die();
				} else {
					status_header( 202 );
					printf(
						'<div class="error-div error">%s <strong>%s</strong><br />%s</div>',
						sprintf(
							'<button type="button" class="dismiss button-link" onclick="jQuery(this).parents(\'div.media-item\').slideUp(200, function(){jQuery(this).remove();});">%s</button>',
							__( 'Dismiss' )
						),
						sprintf(
						/* translators: %s: Name of the file that failed to upload. */
							__( '&#8220;%s&#8221; has failed to upload.' ),
							esc_html( $file_name )
						),
						sprintf( __( 'There was an error reading uploaded part %1$d of %2$d.', 'tuxedo-big-file-uploads' ), $current_part, $chunks )
					);
					exit;
				}
			}

			@fclose( $in );
			@fclose( $out );
			@unlink( $_FILES['async-upload']['tmp_name'] );
		} else {
			/** Failed to open output stream. */
			error_log( "BFU: Failed to open output stream $file_path to write part $current_part of $chunks." );

			if ( ! isset( $_POST['short'] ) || ! isset( $_POST['type'] ) ) {
				echo wp_json_encode(
					array(
						'success' => false,
						'data'    => array(
							'message'  => __( 'There was an error opening the temp file for writing. Available temp directory space may be exceeded or the temp file was cleaned up before the upload completed.', 'tuxedo-big-file-uploads' ),
							'filename' => esc_html( $file_name ),
						),
					)
				);
				wp_die();
			} else {
				status_header( 202 );
				printf(
					'<div class="error-div error">%s <strong>%s</strong><br />%s</div>',
					sprintf(
						'<button type="button" class="dismiss button-link" onclick="jQuery(this).parents(\'div.media-item\').slideUp(200, function(){jQuery(this).remove();});">%s</button>',
						__( 'Dismiss' )
					),
					sprintf(
					/* translators: %s: Name of the file that failed to upload. */
						__( '&#8220;%s&#8221; has failed to upload.' ),
						esc_html( $file_name )
					),
					__( 'There was an error opening the temp file for writing. Available temp directory space may be exceeded or the temp file was cleaned up before the upload completed.', 'tuxedo-big-file-uploads' )
				);
				exit;
			}
		}

		/** Check if file has finished uploading all parts. */
		if ( ! $chunks || $chunk === $chunks - 1 ) {

			// debugging
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$size = file_exists( $file_path ) ? size_format( filesize( $file_path ), 3 ) : '0 B';
				error_log( "BFU: Completing \"$file_name\" upload with a $size final size." );
			}

			/** Recreate upload in $_FILES global and pass off to WordPress. */
			$_FILES['async-upload']['tmp_name'] = $file_path;
			$_FILES['async-upload']['name']     = $file_name;
			$_FILES['async-upload']['size']     = filesize( $_FILES['async-upload']['tmp_name'] );
			$wp_filetype                        = wp_check_filetype_and_ext( $_FILES['async-upload']['tmp_name'], $_FILES['async-upload']['name'] );
			$_FILES['async-upload']['type']     = $wp_filetype['type'];

			header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );

			if ( ! isset( $_POST['short'] ) || ! isset( $_POST['type'] ) ) { // ajax like media uploader in modal

				// Compatibility with Easy Digital Downloads plugin.
				if ( function_exists( 'edd_change_downloads_upload_dir' ) ) {
					global $pagenow;
					$pagenow = 'async-upload.php';
					edd_change_downloads_upload_dir();
				}

				send_nosniff_header();
				nocache_headers();

				$this->wp_ajax_upload_attachment();
				die( '0' );

			} else { // non-ajax like add new media page
				$post_id = 0;
				if ( isset( $_POST['post_id'] ) ) {
					$post_id = absint( $_POST['post_id'] );
					if ( ! get_post( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
						$post_id = 0;
					}
				}

				$id = media_handle_upload(
					'async-upload',
					$post_id,
					array(),
					array(
						'action'    => 'wp_handle_sideload',
						'test_form' => false,
					)
				);
				if ( is_wp_error( $id ) ) {
					printf(
						'<div class="error-div error">%s <strong>%s</strong><br />%s</div>',
						sprintf(
							'<button type="button" class="dismiss button-link" onclick="jQuery(this).parents(\'div.media-item\').slideUp(200, function(){jQuery(this).remove();});">%s</button>',
							__( 'Dismiss' )
						),
						sprintf(
						/* translators: %s: Name of the file that failed to upload. */
							__( '&#8220;%s&#8221; has failed to upload.' ),
							esc_html( $_FILES['async-upload']['name'] )
						),
						esc_html( $id->get_error_message() )
					);
					exit;
				}

				if ( $_POST['short'] ) {
					// Short form response - attachment ID only.
					echo $id;
				} else {
					// Long form response - big chunk of HTML.
					$type = $_POST['type'];

					echo apply_filters( "async_upload_{$type}", $id );
				}
			}
		}

		die();
	}

	/**
	 * Enqueue html markup on the plupload form.
	 *
	 * @return void
	 */
	public function update_plupload_form() {
		global $pagenow;

		if ( ! current_user_can( 'manage_options' ) || is_null( $pagenow ) || ! in_array( $pagenow, array( 'post-new.php', 'post.php', 'upload.php', 'media-new.php' ), true ) ) {
			return;
		}

		?>
		<script type="text/javascript">
			jQuery(".max-upload-size").append(' <small><a style="text-decoration:none;" href="<?php echo esc_url( '#' ); ?>"><?php esc_html_e( 'Update', 'tuxedo-big-file-uploads' ); ?></a></small>');

			jQuery(function() {
				/**
				 * @var plupload.Uploader
				 * 
				 * @reference - https://www.plupload.com/docs/Chunking
				 */
				if ( typeof uploader !== 'undefined' ) {
					uploader.bind('ChunkUploaded', function (up, file, info) {
						if (info.status === 202) {
							up.removeFile(file);
							uploadSuccess(file, info.response);
						}
					});
				}
			});

		</script>
		<?php
	}

	/**
	 * Copied from wp-admin/includes/ajax-actions.php because we have to override the args for
	 * the media_handle_upload function. As of WP 6.0.1
	 */
	public function wp_ajax_upload_attachment() {
		check_ajax_referer( 'media-form' );

		/*
		 * This function does not use wp_send_json_success() / wp_send_json_error()
		 * as the html4 Plupload handler requires a text/html content-type for older IE.
		 * See https://core.trac.wordpress.org/ticket/31037
		 */

		if ( ! current_user_can( 'upload_files' ) ) {
			echo wp_json_encode(
				array(
					'success' => false,
					'data'    => array(
						'message'  => __( 'Sorry, you are not allowed to upload files.' ),
						'filename' => esc_html( $_FILES['async-upload']['name'] ),
					),
				)
			);

			wp_die();
		}

		if ( isset( $_REQUEST['post_id'] ) ) {
			$post_id = $_REQUEST['post_id'];

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				echo wp_json_encode(
					array(
						'success' => false,
						'data'    => array(
							'message'  => __( 'Sorry, you are not allowed to attach files to this post.' ),
							'filename' => esc_html( $_FILES['async-upload']['name'] ),
						),
					)
				);

				wp_die();
			}
		} else {
			$post_id = null;
		}

		$post_data = ! empty( $_REQUEST['post_data'] ) ? _wp_get_allowed_postdata( _wp_translate_postdata( false, (array) $_REQUEST['post_data'] ) ) : array();

		if ( is_wp_error( $post_data ) ) {
			wp_die( $post_data->get_error_message() );
		}

		// If the context is custom header or background, make sure the uploaded file is an image.
		if ( isset( $post_data['context'] ) && in_array( $post_data['context'], array( 'custom-header', 'custom-background' ), true ) ) {
			$wp_filetype = wp_check_filetype_and_ext( $_FILES['async-upload']['tmp_name'], $_FILES['async-upload']['name'] );

			if ( ! wp_match_mime_types( 'image', $wp_filetype['type'] ) ) {
				echo wp_json_encode(
					array(
						'success' => false,
						'data'    => array(
							'message'  => __( 'The uploaded file is not a valid image. Please try again.' ),
							'filename' => esc_html( $_FILES['async-upload']['name'] ),
						),
					)
				);

				wp_die();
			}
		}

		// this is the modded function from wp-admin/includes/ajax-actions.php
		$attachment_id = media_handle_upload(
			'async-upload',
			$post_id,
			$post_data,
			array(
				'action'    => 'wp_handle_sideload',
				'test_form' => false,
			)
		);

		if ( is_wp_error( $attachment_id ) ) {
			echo wp_json_encode(
				array(
					'success' => false,
					'data'    => array(
						'message'  => $attachment_id->get_error_message(),
						'filename' => esc_html( $_FILES['async-upload']['name'] ),
					),
				)
			);

			wp_die();
		}

		if ( isset( $post_data['context'] ) && isset( $post_data['theme'] ) ) {
			if ( 'custom-background' === $post_data['context'] ) {
				update_post_meta( $attachment_id, '_wp_attachment_is_custom_background', $post_data['theme'] );
			}

			if ( 'custom-header' === $post_data['context'] ) {
				update_post_meta( $attachment_id, '_wp_attachment_is_custom_header', $post_data['theme'] );
			}
		}

		$attachment = wp_prepare_attachment_for_js( $attachment_id );
		if ( ! $attachment ) {
			wp_die();
		}

		echo wp_json_encode(
			array(
				'success' => true,
				'data'    => $attachment,
			)
		);

		wp_die();
	}
}
