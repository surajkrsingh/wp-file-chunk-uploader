<?php
/**
 * Admin Page
 *
 * @package WP_FCU
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_slug = isset( $_GET['subpage'] ) ? sanitize_text_field( wp_unslash( $_GET['subpage'] ) ) : 'welcome';
?>
<main class="wrap <?php echo esc_attr( $current_slug ); ?>">
	<?php do_action( 'wpfcu_admin_content_' . $current_slug ); ?>
</main>
