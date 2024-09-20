<?php
/**
 * Admin Header template.
 *
 * @package WP_FCU
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wp_fcu_icon           = apply_filters( 'wp_fcu_admin_icon', true );
$wp_fcu_visit_site_url = apply_filters( 'wp_fcu_visit_site_url', '' );

?>
<div id="wp-fcu-admin-header" class="wp-fcu-admin-header">
	<div class="wp-fcu-logo">
		<a href="<?php echo esc_url( $wp_fcu_visit_site_url ); ?>" target="_blank" rel="noopener" >
			<?php if ( $wp_fcu_icon ) { ?>
				<img src="<?php echo esc_url( WP_FCU_PLUGIN_URL . '/dist/images/logo.png' ); ?>" class="wp-fcusettings-page-header-icon" alt="<?php echo esc_attr( 'Card View' ); ?> " >
			<?php } ?>
		</a>
	</div>
	<?php
	$current_slug = isset( $_GET['subpage'] ) ? sanitize_text_field( wp_unslash( $_GET['subpage'] ) ) : 'welcone'; // phpcs:ignore

	$nav_items = apply_filters(
		'wp_fcu_admin_menu_items',
		array(
			'welcome'      => __( 'Welcome', 'ea-styles' ),
			'settings'     => __( 'Settings', 'ea-styles' ),
			'integrations' => __( 'Integrations', 'ea-styles' ),
			'tools'        => __( 'Tools', 'ea-styles' ),
		)
	);

	?>
	<ul class="wp-fcu-admin-menu">
		<?php
		foreach ( $nav_items as $key => $nav ) {
			printf(
				'<li>
					<a href="%s" class="wp-fcu-admin-menu-item %s">%s</a>
				</li>',
				esc_url( admin_url( 'admin.php?page=wp-file-chunk-uploader&subpage=' . $key ) ),
				$current_slug === $key ? esc_html( 'active' ) : '',
				esc_html( $nav ),
			);
		}
		?>
	</ul>
	<div class="wp-fcu-admin-header-right">
		<?php
			echo sprintf(
				'<p class="wp-fcu-admin-header-version m-0 p-0"> File Chunk Uploader | v%s </p>',
				WP_FCU_PLUGIN_VERSION
			);
			?>
	</div>
</div>
