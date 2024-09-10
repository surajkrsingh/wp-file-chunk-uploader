<?php
/**
 * Plugin Name:       WP File Chunk Uploader
 * Plugin URI:        https://github.com/surajkrsingh/wp-file-chunk-uploader
 * Description:       A plugin that enables seamless uploading of large files by splitting them into manageable chunks.
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.4.0
 * Author:            Suraj Kumar Singh
 * Author URI:        https://github.com/surajkrsingh
 * Text Domain:       wp-file-chunk-uploader
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Tags:              File Upload, Chunked Upload, Large File Upload, WP File Uploader, File Management, Upload Enhancements, WP Plugin, File Splitter, Upload Optimization, WordPress File Handling
 *
 * @package WP_FCU
 */

namespace WP_FCU;

// If this file called directly then abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_FCU\Classes\Main;

/**
 * Constant as plugin version.
 */
if ( ! defined( 'WP_FCU_PLUGIN_VERSION' ) ) {
	define( 'WP_FCU_PLUGIN_VERSION', '1.0.0' );
}

/**
 * Constant as plugin file.
 */
if ( ! defined( 'WP_FCU_PLUGIN_FILE' ) ) {
	define( 'WP_FCU_PLUGIN_FILE', plugin_dir_path( __FILE__ ) . 'wp-file-chunk-uploader.php' );
}

/**
 *
 * Constant as dir of plugin.
 */
if ( ! defined( 'WP_FCU_PLUGIN_DIR_NAME' ) ) {
	define( 'WP_FCU_PLUGIN_DIR_NAME', untrailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );
}

/**
 * Constant as plugin path.
 */
if ( ! defined( 'WP_FCU_PLUGIN_PATH' ) ) {
	define( 'WP_FCU_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

/**
 * Constant as plugin URL.
 */
if ( ! defined( 'WP_FCU_PLUGIN_URL' ) ) {
	define( 'WP_FCU_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}

// Load the autoloader.
if ( ! class_exists( Main::class ) && is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
	require_once WP_FCU_PLUGIN_PATH . '/inc/helpers/functions.php';
	require_once WP_FCU_PLUGIN_PATH . '/inc/Helpers/actions.php';
}

/**
 * Init the plugin main class if class exists and user is admin.
 */
if ( class_exists( Main::class ) ) {
	Main::get_instance();
}
