<?php
/**
 * Integrations page template.
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
	<h1 class="wp-fcu-page-title"> <?php echo esc_html__( 'Integrations', 'wp-fcu' ); ?> </h1>

</div>

