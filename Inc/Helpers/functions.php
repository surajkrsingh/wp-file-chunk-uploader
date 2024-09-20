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


/**
 * Get data array of filescan results.
 *
 * @since 2.0
 *
 * @param false $is_chart If data should be formatted for chart.
 *
 * @return array
 */
function get_filetypes( $is_chart = false ) {

	$results = get_site_option( 'tuxbfu_file_scan' );
	if ( isset( $results['types'] ) ) {
		$types = $results['types'];
	} else {
		$types = array();
	}

	$data = array();
	foreach ( $types as $type => $meta ) {
		$data[ $type ] = (object) array(
			'color' => get_file_type_format( $type, 'color' ),
			'label' => get_file_type_format( $type, 'label' ),
			'size'  => $meta->size,
			'files' => $meta->files,
		);
	}

	$chart = array();
	if ( $is_chart ) {
		foreach ( $data as $item ) {
			$chart['datasets'][0]['data'][]            = $item->size;
			$chart['datasets'][0]['backgroundColor'][] = $item->color;
			$chart['labels'][]                         = $item->label . ': ' . sprintf( _n( '%1$s file totalling %2$s', '%1$s files totalling %2$s', $item->files, 'tuxedo-big-file-uploads' ), number_format_i18n( $item->files ), size_format( $item->size, 1 ) );
		}

		$total_size     = array_sum( wp_list_pluck( $data, 'size' ) );
		$total_files    = array_sum( wp_list_pluck( $data, 'files' ) );
		$chart['total'] = sprintf( _n( '%1$s / %2$s File', '%1$s / %2$s Files', $total_files, 'tuxedo-big-file-uploads' ), size_format( $total_size, 2 ), number_format_i18n( $total_files ) );

		return $chart;
	}

	return $data;
}

function get_filetypes_list() {
	$extensions = array_keys( wp_get_mime_types() );
	$list       = array();
	foreach ( array_keys( wp_get_ext_types() ) as $key ) {
		$list[ $key ] = array();
	}

	foreach ( $extensions as $extension ) {
		$type = wp_ext2type( explode( '|', $extension )[0] );
		if ( $type ) {
			$list[ $type ][ $extension ] = array(
				'label'  => str_replace( '|', '/', $extension ),
				'custom' => false,
			);
		} else {
			$list['other'][ $extension ] = array(
				'label'  => str_replace( '|', '/', $extension ),
				'custom' => false,
			);
		}
	}

	$list['image']['heif']     = array(
		'label'  => 'heif',
		'custom' => true,
	);
	$list['image']['webp']     = array(
		'label'  => 'webp',
		'custom' => true,
	);
	$list['image']['svg|svgz'] = array(
		'label'  => 'svg/svgz',
		'custom' => true,
	);
	$list['image']['apng']     = array(
		'label'  => 'apng',
		'custom' => true,
	);
	$list['image']['avif']     = array(
		'label'  => 'avif',
		'custom' => true,
	);

	$list['interactive']['keynote'] = array(
		'label'  => 'keynote',
		'custom' => true,
	);

	return $list;
}

/**
 * Add to the list of common file extensions and their types.
 *
 * @return array[] Multi-dimensional array of file extensions types keyed by the type of file.
 */
function filter_ext_types( $types ) {
	return array_merge_recursive(
		$types,
		array(
			'image'       => array( 'webp', 'svg', 'svgz', 'apng', 'avif' ),
			'audio'       => array( 'ra', 'ram', 'mid', 'midi', 'wax' ),
			'video'       => array( 'webm', 'wmx', 'wm' ),
			'document'    => array( 'wri', 'mpp', 'dotx', 'onetoc', 'onetoc2', 'onetmp', 'onepkg', 'odg', 'odc', 'odf' ),
			'spreadsheet' => array( 'odb', 'xla', 'xls', 'xlt', 'xlw', 'mdb', 'xltx', 'xltm', 'xlam', 'odb' ),
			'interactive' => array( 'pot', 'potx', 'potm', 'ppam' ),
			'text'        => array( 'ics', 'rtx', 'vtt', 'dfxp', 'log', 'conf', 'text', 'def', 'list', 'ini' ),
			'application' => array( 'class', 'exe' ),
		)
	);
}

/**
 * Get HTML format details for a filetype.
 *
 * @since 2.0
 *
 * @param string $type
 * @param string $key
 *
 * @return mixed
 */
function get_file_type_format( $type, $key ) {
	$labels = array(
		'image'    => array(
			'color' => '#26A9E0',
			'label' => esc_html__( 'Images', 'tuxedo-big-file-uploads' ),
		),
		'audio'    => array(
			'color' => '#00A167',
			'label' => esc_html__( 'Audio', 'tuxedo-big-file-uploads' ),
		),
		'video'    => array(
			'color' => '#C035E2',
			'label' => esc_html__( 'Video', 'tuxedo-big-file-uploads' ),
		),
		'document' => array(
			'color' => '#EE7C1E',
			'label' => esc_html__( 'Documents', 'tuxedo-big-file-uploads' ),
		),
		'archive'  => array(
			'color' => '#EC008C',
			'label' => esc_html__( 'Archives', 'tuxedo-big-file-uploads' ),
		),
		'code'     => array(
			'color' => '#EFED27',
			'label' => esc_html__( 'Code', 'tuxedo-big-file-uploads' ),
		),
		'other'    => array(
			'color' => '#198754',
			'label' => esc_html__( 'Other', 'tuxedo-big-file-uploads' ),
		),
	);

	if ( isset( $labels[ $type ] ) ) {
		return $labels[ $type ][ $key ];
	} else {
		return $labels['other'][ $key ];
	}
}
