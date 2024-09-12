<?php
/**
 * Card view settings Page template.
 *
 * @package wp-fcu-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_sub_page = isset( $_GET['subpage'] ) ? sanitize_text_field( wp_unslash( $_GET['subpage'] ) ) : 'welcome'; // phpcs:ignore
$current_sub_page = str_replace( '-', '_', $current_sub_page );

$current_section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : 'general'; // phpcs:ignore
$current_section = str_replace( '-', '_', $current_section );

$setting_options = $this->get_settings( $current_section );

if ( empty( $setting_options ) ) {
	return;
}

?>
<?php foreach ( $setting_options as $key => $option ) : ?>
	<div class="wp-fcu-setting">
		<div class="wp-fcu-setting-label">
			<h2 for="wp-fcu-setting-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $option['label'] ); ?></h2>
			<p class="wp-fcu-setting-description"><?php echo esc_html( $option['description'] ); ?></p>
			<?php if ( ! empty( $option['note'] ) ) : ?>
				<p class="wp-fcu-setting-note"><?php echo esc_html( $option['note'] ); ?></p>
			<?php endif; ?>
		</div>
		<div class="wp-fcu-setting-input">
			<?php $this->render_setting( $option ); ?>
		</div>
	</div>
<?php endforeach; ?>
