<?php
/**
 * Welcome Page
 *
 * @package WP_FCU
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$display_name = $current_user->display_name;
?>

<div class="wp-fcu-welcome-page-container">
	<div class="wp-fcu-welcome-section">
		<div class="wp-fcu-welcome-section-content">
			<p class="wp-fcu-welcome-section-greeting"><?php echo sprintf( 'Hello %s,', esc_html( $display_name ) ); ?></p>
			<h1 class="wp-fcu-welcome-section-title"> Welcome to File Chunk Uploader.</h1>
			<p class="wp-fcu-welcome-section-description"> Welcome to File Chunk Uploade, your trusted solution for handling large file uploads effortlessly. With advanced chunking technology, it ensures seamless uploads by bypassing server limits, while offering pause and resume capabilities for uninterrupted progress. Enjoy faster, reliable file management—no more timeouts or failed uploads!</p>
			<div class="wp-fcu-welcome-section-actions">
				<a class="button button-primary" href="https://honorswp.com/plugins/wp-file-chunk-uploader/" target="_blank" rel="noopener">View Documentation</a>
				<a class="button button-primary" href="https://wordpress.org/plugins/wp-file-chunk-uploader/" target="_blank" rel="noopener">Download</a>
			</div>
		</div>
		<div class="wp-fcu-welcome-section-media">
			<iframe width="560" height="315" src="https://www.youtube.com/embed/1yWd_mZ7eF4?si=T_5SoF5h2nGm5QNR" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
		</div>
	</div>

	<div class="wp-fcu-content">
		<div class="wp-fcu-content-main">
			<h2>Quick Settings</h2>
			<div class="wp-fcu-quick-settings">
				<?php
				$settings = array(
					'chunk_size'  => array(
						'label'       => 'Chunk Size',
						'description' => 'Set the chunk size in MB',
						'default'     => '10',
						'icon'        => 'dashicons dashicons-clipboard',
						'link'        => 'https://wp-file-chunk-uploader.com/documentation/quick-settings/#chunk-size',
					),
					'chunk_size1' => array(
						'label'       => 'Chunk Size',
						'description' => 'Set the chunk size in MB',
						'default'     => '10',
						'icon'        => 'dashicons dashicons-clipboard',
						'link'        => 'https://wp-file-chunk-uploader.com/documentation/quick-settings/#chunk-size',
					),
					'chunk_size2' => array(
						'label'       => 'Chunk Size',
						'description' => 'Set the chunk size in MB',
						'default'     => '10',
						'icon'        => 'dashicons dashicons-clipboard',
						'link'        => 'https://wp-file-chunk-uploader.com/documentation/quick-settings/#chunk-size',
					),
					'chunk_size3' => array(
						'label'       => 'Chunk Size',
						'description' => 'Set the chunk size in MB',
						'default'     => '10',
						'icon'        => 'dashicons dashicons-clipboard',
						'link'        => 'https://wp-file-chunk-uploader.com/documentation/quick-settings/#chunk-size',
					),
					'chunk_size4' => array(
						'label'       => 'Chunk Size',
						'description' => 'Set the chunk size in MB',
						'default'     => '10',
						'icon'        => 'dashicons dashicons-clipboard',
						'link'        => 'https://wp-file-chunk-uploader.com/documentation/quick-settings/#chunk-size',
					),
					'chunk_size5' => array(
						'label'       => 'Chunk Size',
						'description' => 'Set the chunk size in MB',
						'default'     => '10',
						'icon'        => 'dashicons dashicons-clipboard',
						'link'        => 'https://wp-file-chunk-uploader.com/documentation/quick-settings/#chunk-size',
					),
					'chunk_size6' => array(
						'label'       => 'Chunk Size',
						'description' => 'Set the chunk size in MB',
						'default'     => '10',
						'icon'        => 'dashicons dashicons-clipboard',
						'link'        => 'https://wp-file-chunk-uploader.com/documentation/quick-settings/#chunk-size',
					),
				);

				foreach ( $settings as $key => $setting ) {

					?>
					<div class="wp-fcu-quick-settings-item">
						<div class="wp-fcu-quick-settings-icon">
							<i class="dashicons <?php echo esc_attr( $setting['icon'] ); ?>" aria-hidden="true"></i>
						</div>
						<div class="wp-fcu-quick-settings-description">
							<h3><?php echo esc_html( $setting['label'] ); ?></h3>
						</div>
						<div class="wp-fcu-quick-settings-action">
							<strong> On </strong>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>

		<aside class="wp-fcu-content-aside">
			<section class="wp-fcu-box">
				<h2 class="wp-fcu-box-title">Need Help?</h2>
				<p class="wp-fcu-box-description">If you need help, please visit our <a href="https://honorswp.com/support/wp-file-chunk-uploader/" target="_blank" rel="noopener">support page</a></p>
			</section>

			<section class="wp-fcu-box">
				<h2 class="wp-fcu-box-title">Join the Community</h2>
				<p class="wp-fcu-box-description">Got a question about the plugin, want to share your awesome project or just say hi? Join our wonderful community!</p>
				<a class="wp-fcu-box-button" href="https://wordpress.org/support/plugin/wp-file-chunk-uploader" target="_blank" rel="noopener">Join the Community</a>
			</section>

			<section class="wp-fcu-box">
				<h2 class="wp-fcu-box-title">Rate Us</h2>
				<p class="wp-fcu-box-description">If you like the plugin, please rate it to help us make it even better!</p>
				<a class="wp-fcu-box-button" href="https://wordpress.org/support/plugin/wp-file-chunk-uploader/reviews/#new-post" target="_blank" rel="noopener">Rate Us</a>
			</section>
		</aside>
	</div>
</div>
