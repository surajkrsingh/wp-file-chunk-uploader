<?php
/**
 * Tools page template.
 *
 * @package wp-fcu-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_sub_page = isset( $_GET['subpage'] ) ? sanitize_text_field( wp_unslash( $_GET['subpage'] ) ) : 'welcome'; // phpcs:ignore
$current_sub_page = str_replace( '-', '_', $current_sub_page );

$current_section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : 'general'; // phpcs:ignore
$current_section      = str_replace( '-', '_', $current_section );
$wp_fcu_wrapper_class = array();
?>
<div class="container">
	<?php
	if ( ! class_exists( 'Infinite_Uploads' ) ) {
		$scan_results = get_site_option( 'tuxbfu_file_scan' );
		if ( isset( $scan_results['scan_finished'] ) && $scan_results['scan_finished'] ) {
			if ( isset( $scan_results['types'] ) ) {
				$total_files   = array_sum( wp_list_pluck( $scan_results['types'], 'files' ) );
				$total_storage = array_sum( wp_list_pluck( $scan_results['types'], 'size' ) );
			} else {
				$total_files   = 0;
				$total_storage = 0;
			}
			require_once WP_FCU_PLUGIN_PATH . '/templates/scan-results.php';
		} else {
			require_once WP_FCU_PLUGIN_PATH . '/templates/scan-start.php';
		}
	}
	?>
</div>

