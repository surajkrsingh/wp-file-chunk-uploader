<div class="wp-fcu-file-info-container">
	<h2><?php esc_html_e( 'Storage Usage Analysis', 'tuxedo-big-file-uploads' ); ?></h2>
	<div class="wp-fcu-file-info-main">
		<div class="wp-fcu-file-info">
			<p class="lead mb-0"><?php esc_html_e( "Total Bytes / Files", 'tuxedo-big-file-uploads' ); ?></p>
			<h2 class="h2 text-nowrap"><?php echo size_format( $total_storage, 2 ); ?><small class="text-muted"> / <?php echo number_format_i18n( $total_files ); ?></small></h2>

			<div class="container p-0 ml-md-3">
				<?php foreach ( get_filetypes( false ) as $type ) { ?>
					<div class="row mt-2">
						<div class="col-1"><span class="badge badge-pill" style="background-color: <?php echo $type->color; ?>">&nbsp;</span></div>
						<div class="col-4 lead text-nowrap"><?php echo $type->label; ?></div>
						<div class="col-5 text-nowrap"><strong><?php echo size_format( $type->size, 2 ); ?> / <?php echo number_format_i18n( $type->files ); ?></strong></div>
					</div>
				<?php } ?>
				<div class="row mt-2">
					<div class="col text-muted"><small><?php printf( esc_html__( 'Scanned %s ago', 'tuxedo-big-file-uploads' ), human_time_diff( $scan_results['scan_finished'] ) ); ?> &dash; <a href="#" class="badge badge-primary" data-toggle="modal" data-target="#scan-modal"><span
									data-toggle="tooltip"
									title="<?php esc_attr_e( 'Run a new scan to detect recently uploaded files.', 'tuxedo-big-file-uploads' ); ?>"><?php esc_html_e( 'Refresh', 'tuxedo-big-file-uploads' ); ?></span></a></small>
					</div>
				</div>
			</div>
		</div>
		<div class="wp-fcu-file-info-pie">
			<canvas id="bfu-local-pie"></canvas>
		</div>
	</div>
</div>
