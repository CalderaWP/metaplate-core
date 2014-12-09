<?php
/**
 * Helper functions for metaplate output
 *
 * @package caldera\metaplate\core
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Josh Pollock
 */ 

if ( ! function_exists( 'caldera_metaplate_do_metaplate' ) ) :
	function caldera_metaplate_do_metaplate( $post_content, $metplate, $data ) {
		if ( is_string(  $metplate ) ) {
			$metplate = calderawp\metaplate\core\data::get_metaplate( $metplate );
		}

		if ( ! is_array( $metplate ) ) {
			return;

		}

		$render = new calderawp\metaplate\core\render();
		$output = $render->render_metaplate( $post_content, $metplate, $data );
		if ( is_string( $output ) ) {
			return $output;

		}
	}
endif;

if ( ! function_exists( 'caldera_metaplate_get_metastack' ) ) {
	function caldera_metaplate_get_metastack( $id ) {
		return calderawp\metaplate\core\data::get_metaplate( $id );

	}
}

