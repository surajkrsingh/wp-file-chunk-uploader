<?php
/**
 * Card view settings Page template.
 *
 * @package wp-fcu-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_slug      = isset( $_GET['subpage'] ) ? sanitize_text_field( wp_unslash( $_GET['subpage'] ) ) : 'welcome'; // phpcs:ignore
$current_slug      = str_replace( '-', '_', $current_slug );
$wp_fcu_wrapper_class = apply_filters( 'bbp_welcome_wrapper_class', array( $current_slug ) );
$current_screen_id = get_current_screen()->id;

?>
<div class="wp-fcu-welcome-page-container">
	setting page
</div>
