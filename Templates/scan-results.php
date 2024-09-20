<?php
/**
 * Scan Results template.
 *
 * @package Tuxedo Big File Uploads
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wp-fcu-file-info-container row">
	<div class="row-header">
		<div class="d-flex align-items-center">
			<h4 class="mb-16"><?php esc_html_e( 'Space Allocation Summary', 'tuxedo-big-file-uploads' ); ?></h4>
		</div>
	</div>
	<div class="col-lg col-xs-12">
		<p class="lead mb-0"><?php esc_html_e( 'Uploads > Total Bytes / Files', 'tuxedo-big-file-uploads' ); ?></p>
		<p class="h5 text-nowrap mb-4"><?php echo size_format( $total_storage, 2 ); ?><small class="text-muted"> / <?php echo number_format_i18n( $total_files ); ?></small></p>

		<div class="container p-0 ml-md-3">
			<?php foreach ( get_filetypes( false ) as $type ) { ?>
				<div class="row mt-2">
					<div class="col-1"><span class="badge badge-pill" style="background-color: <?php echo $type->color; ?>">&nbsp;</span></div>
					<div class="col-4 lead text-nowrap"><?php echo $type->label; ?></div>
					<div class="col-5 text-nowrap"><strong><?php echo size_format( $type->size, 2 ); ?> / <?php echo number_format_i18n( $type->files ); ?></strong></div>
				</div>
			<?php } ?>
		</div>
	</div>

	<div class="col-lg col-xs-12 mt-5 mt-lg-0 text-center bfu-pie-wrapper">
		<canvas id="bfu-local-pie"></canvas>
	</div>

	<div class="row mt-2 text-center">
		<div class="col text-muted fs-5">
			<small><?php printf( esc_html__( 'Scanned %s ago', 'tuxedo-big-file-uploads' ), human_time_diff( $scan_results['scan_finished'] ) ); ?> &dash; <a href="#" class="badge bg-primary" data-toggle="modal" data-target="#scan-modal"><span
			data-toggle="tooltip"
			title="<?php esc_attr_e( 'Run a new scan to detect recently uploaded files.', 'tuxedo-big-file-uploads' ); ?>"><?php esc_html_e( 'Refresh', 'tuxedo-big-file-uploads' ); ?></span></a></small>
		</div>
	</div>
</div>

