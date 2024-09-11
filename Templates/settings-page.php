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
$section_nav     = $this->get_settings();
?>
<div class="wp-fcu-settings-page-container">
	<h1 class="wp-fcu-page-title"> <?php echo esc_html__( 'Settings', 'wp-fcu' ); ?> </h1>
	<main class="wp-fcu-setting-main <?php echo esc_attr( implode( ' ', $wp_fcu_wrapper_class ) ); ?>">
		<aside class="wp-fcu-sidebar">
			<ul class="wp-fcu-sidebar-nav">
				<?php foreach ( $section_nav as $key => $section ) : ?>
					<li>
						<a href="<?php echo esc_url( $section['url'] ); ?>" class="wp-fcu-sidebar-link <?php echo esc_attr( $current_section === $key ? 'active' : '' ); ?>">
							<i class="<?php echo esc_attr( $section['icon'] ); ?>"></></i>
							<span class="wp-fcu-sidebar-label"><?php echo esc_html( $section['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</aside>
		<form class="wp-fcu-settings" method="post">
			<input type="hidden" name="wp-fcu-settings-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp-fcu-settings-nonce' ) ); ?>" />
			<input type="hidden" name="wp-fcu-settings-current-section" value="<?php echo esc_attr( $current_section ); ?>" />
			<input type="hidden" name="wp-fcu-settings-current-subpage" value="<?php echo esc_attr( $current_sub_page ); ?>" />
			<input type="hidden" name="wp-fcu-settings-action" value="wp_fcu_save_settings" />
			<div class="wp-fcu-settings-header">
				<button button="submit" class="button button-primary wp-fcu-button"> <?php echo esc_html__( 'Save Changes', 'wp-fcu' ); ?> </button>
			</div>
			<?php do_action( 'wp_fcu_admin_settings_' . $current_section, $current_section ); ?>
			<div class="wp-fcu-settings-footer">
				<button button="submit" class="button button-primary wp-fcu-button"> <?php echo esc_html__( 'Save Changes', 'wp-fcu' ); ?> </button>
			</div>
		</form>
	</main>
</div>

