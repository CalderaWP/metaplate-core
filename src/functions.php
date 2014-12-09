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
	/**
	 * Render and return a metaplate
	 *
	 * @param string $content Content to add metaplate to.
	 * @param string|array $metaplate The content
	 * @param array $data The field data to use with the template.
	 *
	 * @param string $placement Optional. Where to put the template output, before, after or in place of $content. Default is replace. Options: prepend|append|replace
	 *
	 * @return string|null The rendered content if it was able to be rendered.
	 */
	function caldera_metaplate_do_metaplate( $content, $metaplate, $data, $placement = 'replace' ) {
		if ( is_string( $metaplate ) ) {
			$metaplate = calderawp\metaplate\core\data::get_metaplate( $metaplate );
		}

		if ( ! is_array( $metaplate ) ) {
			return;

		}

		$render = new calderawp\metaplate\core\render();
		$output = $render->render_metaplate( $content, array( $metaplate ), $data, $placement );
		if ( is_string( $output ) ) {
			return $output;

		}

	}
endif;

if ( ! function_exists( 'caldera_metaplate_get_metastack' ) ) {
	/**
	 * Get metaplate data by ID or slug
	 *
	 * @param string $id_or_slug
	 *
	 * @return array|bool Returns the array for the metaplate or false if not found.
	 */
	function caldera_metaplate_get_metastack( $id_or_slug ) {
		return calderawp\metaplate\core\data::get_metaplate( $id_or_slug );

	}
}


