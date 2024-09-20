<?php
/**
 * Welcome Page
 *
 * @package WP_FCU
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user         = wp_get_current_user();
$display_name = $user->display_name;

$settings = WP_FCU\Classes\Settings::get_instance()->get_settings();

$quick_settings = array();

foreach ( $settings as $section ) {
	if ( isset( $section['settings'] ) ) {
		$quick_edit_settings = array_filter(
			$section['settings'],
			function( $setting ) {
				return ! empty( $setting['quick_edit'] );
			}
		);

		$quick_settings = array_merge( $quick_settings, $quick_edit_settings );
	}
}

?>

<div class="wp-fcu-welcome-page-container container">
	<div class="wp-fcu-welcome-section row">
		<div class="wp-fcu-welcome-section-content col-lg-6 col-xs-12 my-4">
			<p class="wp-fcu-welcome-section-greeting"><?php echo sprintf( 'Hello %s,', esc_html( $display_name ) ); ?></p>
			<h4 class="wp-fcu-welcome-section-title mb-8"> Welcome to File Chunk Uploader.</h4>
			<p class="wp-fcu-welcome-section-description"> Welcome to File Chunk Uploade, your trusted solution for handling large file uploads effortlessly. With advanced chunking technology, it ensures seamless uploads by bypassing server limits, while offering pause and resume capabilities for uninterrupted progress. Enjoy faster, reliable file management—no more timeouts or failed uploads!</p>
			<div class="wp-fcu-welcome-section-actions">
				<a class="btn btn-primary" href="https://honorswp.com/plugins/wp-file-chunk-uploader/" target="_blank" rel="noopener">View Documentation</a>
				<a class="btn btn-primary" href="https://wordpress.org/plugins/wp-file-chunk-uploader/" target="_blank" rel="noopener">Download</a>
			</div>
		</div>
		<div class="wp-fcu-welcome-section-media col-lg-6 col-xs-12 m-10">
			<div class="ratio ratio-16x9 overflow-hidden">
			<!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/1yWd_mZ7eF4?si=T_5SoF5h2nGm5QNR" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe> -->
				<div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
					<div class="carousel-indicators">
						<button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
						<button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
					</div>
					<div class="carousel-inner">
						<div class="carousel-item active" data-bs-interval="10000">
							<img src="https://dummyimage.com/500x300/ffff00/000" class="d-block w-100 ratio ratio-16x9" alt="...">
							<div class="carousel-caption d-none d-md-block">
								<h5>First slide label</h5>
								<p>Some representative placeholder content for the first slide.</p>
							</div>
						</div>
						<div class="carousel-item" data-bs-interval="2000">
							<img src="https://dummyimage.com/700x500/ff6600/fff" class="d-block w-100 ratio ratio-16x9" alt="...">
							<div class="carousel-caption d-none d-md-block">
								<h5>Second slide label</h5>
								<p>Some representative placeholder content for the second slide.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="wp-fcu-content row">
		<div class="wp-fcu-content-main col col-lg-8 col-xs-12">
			<div class="row-header">
				<div class="d-flex align-items-center">
					<h4 class="mb-16"><?php esc_html_e( 'Quick Settings', 'tuxedo-big-file-uploads' ); ?></h4>
				</div>
			</div>
			<div class="wp-fcu-quick-settings d-flex flex-wrap justify-content-between">
				<?php

				foreach ( $quick_settings as $key => $setting ) {

					?>
					<div class="wp-fcu-quick-settings-item d-flex align-items-center justify-content-between p-0 mb-3">
						<div class="wp-fcu-quick-settings-icon">
							<i class="dashicons <?php echo esc_attr( $setting['icon'] ); ?>" aria-hidden="true"></i>
						</div>
						<div class="wp-fcu-quick-settings-description py-2">
							<h6 class="mb-0"><?php echo esc_html( $setting['label'] ); ?></h6>
						</div>
						<div class="wp-fcu-quick-settings-action pe-4">
							<?php WP_FCU\Classes\Settings::get_instance()->render_input( $setting ); ?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>

		<aside class="wp-fcu-content-aside col col-xs-12 col-lg-4 pe-0">
			<section class="wp-fcu-box">
				<div class="row-header">
					<div class="d-flex align-items-center">
						<h4 class="wp-fcu-box-title mb-16"><?php esc_html_e( 'Need Help?', 'tuxedo-big-file-uploads' ); ?></h4>
					</div>
				</div>
				<p class="wp-fcu-box-description">If you need help, please visit our <a href="https://honorswp.com/support/wp-file-chunk-uploader/" target="_blank" rel="noopener">support page</a></p>
			</section>

			<section class="wp-fcu-box">
				<div class="row-header">
					<div class="d-flex align-items-center">
						<h4 class="wp-fcu-box-title mb-16"><?php esc_html_e( 'Join the Community', 'tuxedo-big-file-uploads' ); ?></h4>
					</div>
				</div>
				<p class="wp-fcu-box-description">Got a question about the plugin, want to share your awesome project or just say hi? Join our wonderful community!</p>
				<a class="wp-fcu-box-button" href="https://wordpress.org/support/plugin/wp-file-chunk-uploader" target="_blank" rel="noopener">Join the Community</a>
			</section>

			<section class="wp-fcu-box">
				<div class="row-header wp-fcu-box-title">
					<div class="d-flex align-items-center">
						<h4 class="wp-fcu-box-title mb-16"><?php esc_html_e( 'Rate Us', 'tuxedo-big-file-uploads' ); ?></h4>
					</div>
				</div>
				<p class="wp-fcu-box-description">If you like the plugin, please rate it to help us make it even better!</p>
				<a class="wp-fcu-box-button" href="https://wordpress.org/support/plugin/wp-file-chunk-uploader/reviews/#new-post" target="_blank" rel="noopener">Rate Us</a>
			</section>
		</aside>
	</div>
</div>
