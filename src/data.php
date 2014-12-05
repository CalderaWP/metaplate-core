<?php
/**
 * Sets up data for loading metaplates and the fields.
 *
 * @package caldera\metaplate\core
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer
 */

namespace calderawp\metaplate\core;

/**
 * Class data
 *
 * @package caldera\metaplate\core
 */
class data {
	/**
	 * Get the metaplates for the post
	 *
	 * @return    array    active metaplates for this post type
	 */
	public static function get_active_metaplates( ) {

		global $post;

		// GET METAPLATEs
		$metaplates = get_option( '_metaplates_registry' );
		$meta_stack = array();
		foreach( $metaplates as $metaplate_try ){
			$is_plate = get_option( $metaplate_try['id'] );
			if( !empty( $is_plate['post_type'][$post->post_type] ) ){
				switch ($is_plate['page_type']) {
					case 'single':
						if( is_single() || is_page() ){
							$meta_stack[] = $is_plate;
						}
						break;
					case 'archive':
						if( ( is_archive() || is_front_page() ) && ( !is_page( ) || !is_single( ) ) ){
							$meta_stack[] = $is_plate;
						}
						break;
					default:
						$meta_stack[] = $is_plate;
						break;
				}
			}
		}

		return $meta_stack;

	}

	/**
	 * Merge in Custom field data, meta and post data
	 *
	 * @return    array    array with merged data
	 */
	public static function get_custom_field_data( $post_id ) {

		global $post;

		$raw_data = get_post_meta( $post_id  );

		// break to standard arrays
		$template_data = array();
		foreach( $raw_data as $meta_key=>$meta_data ){
			if ( 0 === strpos( $meta_key, '_' ) ) {
				continue;
			}

			if( count( $meta_data ) === 1 ){
				if( strlen( trim( $meta_data[0] ) ) > 0 ){ // check value is something else leave it out.
					$template_data[$meta_key] = trim( $meta_data[0] );
				}
			}else{
				$template_data[$meta_key] = $meta_data;
			}
		}
		// ACF support
		if( class_exists( 'acf' ) ){
			$fields = get_fields( $post->ID );
			if ( is_array( $fields ) && ! empty( $fields ) ) {
				$template_data = array_merge( $template_data, $fields );
			}

		}
		// CFS support
		if( class_exists( 'Custom_Field_Suite' ) ){
			$fields = CFS()->get();
			if ( is_array( $fields ) && ! empty( $fields ) ) {
				$template_data = array_merge( $template_data, $fields );
			}

		}

		//Pods support
		if ( class_exists( 'Pods' ) && false != ( $pods = pods( $post->post_type, $post->ID, true ) ) ) {
			$fields = $pods->export();
			if ( is_array( $fields ) && ! empty( $fields ) ) {
				$template_data = array_merge( $template_data, $fields );
			}

		}


		// include post values if in a post
		if( !empty( $post ) ){
			foreach( $post as $post_key=>$post_value ){
				$template_data[$post_key] = $post_value;
			}
		}

		return $template_data;

	}

} 
