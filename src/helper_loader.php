<?php
/**
 * Validate and add additional Handlebars helpers
 *
 * @package caldera\metaplate
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Josh Pollock
 */

namespace caldera\metaplate\core;


/**
 * Class helper_loader
 *
 * @package caldera\metaplate
 */
class helper_loader {
	/**
	 * Handlebars.php class instance
	 *
	 * @var obj|\Handlebars\Handlebars Handlebars.php class instance
	 */
	public $handlebars;

	/**
	 * Constructor for class.
	 *
	 * @param obj|\Handlebars\Handlebars $handlebars Handlebars.php class instance
	 * @param array $helpers {
	 *     Name, class & callback for the helper.
	 *
	 *     @type string $name Name of helper to use in Handlebars.
	 *     @type string $class Class containing callback function.
	 *     @type string $callback. Optional. The name of the callback function. If not set, "helper" will be used.
	 * }
	 *
	 * @return obj|\Handlebars\Handlebars $handlebars Handlebars.php class instance
	 */
	function __construct( $handlebars, $helpers ) {
		if ( is_a( $handlebars, 'Handlebars\Handlebars' ) ) {
			$this->handlebars = $handlebars;
			$this->add_helpers( $helpers );


			return $this->handlebars;
		}

	}

	/**
	 * Add the helpers to the Handlebars instance, if validation passes.
	 *
	 * @param array $helpers Array of helpers to add
	 */
	private function add_helpers( $helpers ) {
		foreach( $helpers as $helper ) {
			$helper = $this->validate_helper_input( $helper );
			if ( is_array( $helper ) ) {
				$this->add_helper( $helper );
			}

		}

	}

	/**
	 * Add a single helper to the Handlebars instance.
	 *
	 * @param array $helper Array of information for this helper.
	 */
	private function add_helper( $helper ) {
		$this->handlebars->addHelper(
			$helper[ 'name' ],
			array(
				$helper[ 'class' ],
				$helper[ 'callback' ]
			)
		);
	}

	/**
	 * Make sure each helper passed in is valid.
	 *
	 * @todo ensure helper class/callback are callable. Preferably, without creating object of the class.
	 *
	 * @param array $helper Array to add helper with.
	 *
	 * @return bool|array Returns the array if valid, false if not.
	 */
	private function validate_helper_input( $helper ) {
		if ( ! is_array( $helper ) || empty( $helper ) ) {
			return false;
		}

		if ( !  isset( $helper[ 'name' ] ) || ! isset( $helper[ 'class' ]  ) ) {
			return false;
		}

		//if not set, set callback name to "helper"
		if ( ! isset( $helper[ 'callback' ] ) ) {
			$helper[ 'callback' ] = 'helper';
		}

		return $helper;

	}


} 
