<?php
/**
 * Base class for recursive array access.
 *
 * @package   calderawp\metaplate\core\pods
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace calderawp\metaplate\core\pods;

/**
 * Class array_access_recursive
 *
 * @package calderawp\metaplate\core\pods
 */
abstract class array_access_recursive implements \ArrayAccess {

	/**
	 * Pod data
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Clones!
	 *
	 * @since 1.1.0
	 */
	public function __clone() {
		foreach ( $this->data as $key => $value )  {
			if ($value instanceof self ) {
				$this[ $key ] = clone $value;
			}

		}

	}

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 *
	 * @param array $data IntialPod data
	 */
	public function __construct(array $data = array()) {
		foreach ($data as $key => $value) {
			$this[ $key ] = $value;
		}
	}

	/**
	 * Set an offest
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $offset Key to set in
	 * @param mixed $data Data to set
	 */
	public function offsetSet($offset, $data) {
		//if (is_array($data)) $data = new self($data);
		if ($offset === null) {
			$this->data[] = $data;
		} else {
			$this->data[$offset] = $data;
		}
	}

	/**
	 * Convert to array
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function toArray() {
		$data = $this->data;
		foreach ($data as $key => $value) {
			if ( $value instanceof self ) {
				$data[ $key ] = $value->toArray();
			}

		}

		return $data;

	}

	/**
	 * Get an offset
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $offset
	 *
	 * @return mixed
	 */
	public function offsetGet($offset) {

		return $this->data[ $offset ];
	}

	/**
	 * Check if offset exists
	 *
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->data[ $offset ] );
	}

	/**
	 * Unset an offset
	 *
	 * @param mixed $offset
	 */
	public function offsetUnset( $offset ) {
		unset($this->data);
	}

}
