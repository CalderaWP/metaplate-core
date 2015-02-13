<?php
/**
 * Load a Metplate HTML form a file.
 *
 * @package calderawp\metaplate\core
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 Josh Pollock
 */

namespace calderawp\metaplate\core;

/**
 * Class file_load
 *
 * @package calderawp\metaplate\core
 */
class file_load {

	/**
	 * Attempts to load a file and if so, puts its contents in an array as expected by Metaplate
	 *
	 * @param string $file File path. Can be relative to current theme or absolute. Must be .html or .htm
	 *
	 * @return array|void Returns an array 'html' => file contents if possible.
	 */
	public static function load( $file ) {
		$file = self::locate_template( $file );
		if ( self::verify_files( $file ) ) {
			return array( 'html' => file_get_contents( $file ) );

		}

	}

	/**
	 * Verify that the file exists and is of an acceptable type
	 *
	 * @param string $file File path. Can be relative to current theme or absolute. Must be .html or .htm
	 *
	 * @return bool True if file is valid.
	 */
	private static function verify_files( $file_path ) {
		if ( file_exists( $file_path ) && is_file( $file_path ) ) {
			$extension = pathinfo( $file_path );
			if ( in_array( $extension, array( 'html', 'htm' ) ) ) {
				return true;

			}

		}

	}


	/**
	 * Locate the template's file path.
	 *
	 * Much copypasta from https://github.com/pods-framework/pods/blob/master/classes/PodsView (c) Pods Foundation. Much GPL, very thanks.
	 *
	 * @param string $file File path. Can be relative to current theme or absolute. Must be .html or .htm
	 *
	 * @return bool|mixed|string|void
	 */
	private static function locate_template( $file ) {

		// Keep it safe
		$file = trim( str_replace( array( '../', '\\' ), array( '', '/' ), (string) $file ) );
		$file = preg_replace( '/\/+/', '/', $file );

		if ( empty( $file ) ) {
			return false;
		}

		$_real_view = realpath( $file );

		if ( empty( $_real_view ) ) {
			$_real_view = $file;
		}

		$located = false;

		if ( false === strpos( $_real_view, realpath( WP_PLUGIN_DIR ) ) && false === strpos( $_real_view, realpath( WPMU_PLUGIN_DIR ) ) ) {
			$_real_view = trim( $_real_view, '/' );

			if ( empty( $_real_view ) ) {
				return false;
			}

			if ( file_exists( realpath( get_stylesheet_directory() . '/' . $_real_view ) ) ) {
				$located = realpath( get_stylesheet_directory() . '/' . $_real_view );
			}
			elseif ( file_exists( realpath( get_template_directory() . '/' . $_real_view ) ) ) {
				$located = realpath( get_template_directory() . '/' . $_real_view );
			}

		} elseif ( file_exists( $file ) ) {
			$located = $file;
		}
		else {
			$located = apply_filters( 'caldera_metaplate_core_locate_template', $located, $file );
		}

		return $located;

	}

}
