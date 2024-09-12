<?php
/**
 * Custom functions.
 *
 * @package WP_FCU
 */

// Disable the direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get size in human readable format.
 *
 * @param int $bytes Bytes.
 * @return string
 */
function wp_fcu_size_format( $bytes ) {
	if ( $bytes >= 1073741824 ) {
		return number_format( $bytes / 1073741824, 2 ) . ' GB';
	} elseif ( $bytes >= 1048576 ) {
		return number_format( $bytes / 1048576, 2 ) . ' MB';
	} elseif ( $bytes >= 1024 ) {
		return number_format( $bytes / 1024, 2 ) . ' KB';
	} else {
		return $bytes . ' bytes';
	}
}
