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
use calderawp\file_locator\file_locator;

/**
 * Class file_load
 *
 * @package calderawp\metaplate\core
 */
class file_load {

	protected static $context = 'caldera-metaplate';

	/**
	 * Attempts to load a file and if so, puts its contents in an array as expected by Metaplate
	 *
	 * @param string $file File path. Can be relative to current theme or absolute. Must be .html or .htm
	 *
	 * @return array|void Returns an array 'html' => file contents if possible.
	 */
	public static function load( $file ) {

		add_filter( 'calderawp_file_locator_allow_extensions', function( $allowed, $context ) {
			if ( self::$context === $context ) {
				$allowed = array( 'html', 'htm' );
			}

			return $allowed;

		}, 10, 2 );

		$file = file_locator::locate( $file, self::$context );


		$file = file_locator::locate( $file, self::$context, true );

		if ( is_string( $file ) ) {
			return array( 'html' => array( 'code' => file_get_contents( $file ) ) );

		}

	}
	

}

