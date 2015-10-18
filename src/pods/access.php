<?php
/**
 * Treat a Pod as an array
 *
 * @package calderawp\metaplate\core\pods
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock, Pods Foundation.
 */

namespace calderawp\metaplate\core\pods;

/**
 * Class access
 *
 * @package calderawp\metaplate\core\pods
 */
class access extends array_access_recursive {

	/**
	 * Current Pod object
	 *
	 * @since 0.1.0
	 *
	 * @acces private
	 *
	 * @var object||Pods
	 */
	private $pod;

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 *
	 * @param array $data Should be an array with Pods object in index 0.
	 */
	function __construct( $data ) {
		$this->set_pod( $data );
		if ( isset( $this->pod )  ) {
			parent::__construct( $this->prepare_data() );
		}
	}


	/**
	 * Prepare initial data
	 *
	 * @return mixed
	 */
	protected function prepare_data() {
		$data = $this->pod->api->export_pod_item( array( 'depth' => 1 ), $this->pod );
		return $data;
	}

	/**
	 * Get an offset if already set or traverse Pod and then set plus reutrn.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $offset Offset to get
	 *
	 * @return mixed
	 */
	public function offsetGet($offset) {
		if ( $this->offsetExists( $offset ) ) {
			return parent::offsetGet( $offset );
		} else {
			if( 'id' == $offset || 'ID' == $offset ) {
				$_value = $this->pod->id();
			}else{
				$_value = $this->pod->field( $offset );
			}

			if( $_value ) {
				parent::offsetSet( $offset, $_value );
				return $_value;
			}
		}
	}

	/**
	 * Set the pod property
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 *
	 * @param array $data
	 */
	private function set_pod( $data ) {
		if( isset( $data[0] ) && is_object( $data[0] ) )
			$this->pod = $data[0];
	}


}
