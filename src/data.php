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
	 * @var string The key for the metaplate registry
	 */
	public static $registry_option_name = '_metaplates_registry';

	/**
	 * Get the metaplates for the post
	 *
	 * @return    array    active metaplates for this post type
	 */
	public static function get_active_metaplates( ) {

		global $post;

		// GET METAPLATEs
		$metaplates = self::get_registry();
		$meta_stack = array();
		foreach( $metaplates as $metaplate_try ){
			$is_plate = self::get_metaplate( $metaplate_try['id'] );
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
		if ( ! is_object( $post ) ) {
			$post = get_post( $post_id );
		}

		$raw_data = get_post_meta( $post_id  );

		if ( ! is_array( $raw_data ) ) {
			return;

		}

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

	/**
	 * Get a metaplate by ID or slug
	 *
	 * @param string $id
	 *
	 * @return array|bool
	 */
	public static function get_metaplate( $id ) {
		$metaplates = self::get_registry();
		if ( array_key_exists( $id, $metaplates ) ) {
			$metaplate = get_option( $id );
		}
		else {
			$metaplate = self::get_metaplate_id_by_slug( $id );
			if ( is_string( $metaplate ) ) {
				$metaplate = self::get_metaplate( $metaplate );

			}

		}

		if ( is_string( $metaplate ) ) {
			$metaplate =  json_decode( $metaplate, ARRAY_A );
		}

		if ( is_object( $metaplate ) ) {
			$metaplate = (array) $metaplate;
		}

		$metaplate = self::code( $metaplate );

		return (array) $metaplate;

	}

	/**
	 * Make sure the code fields are arrays not objects and are set
	 *
	 * @param array $metaplate The metaplate
	 *
	 * @return array The metaplate with the code fields as arrays
	 */
	private static function code( $metaplate ) {
		foreach ( array( 'html', 'js', 'css' ) as $field ) {
			if ( isset( $metaplate[ $field ] ) ) {
				if ( is_object( $metaplate[ $field ] ) ) {
					$value = $metaplate[ $field ];
					$value = $value->code;
					unset( $metaplate[ $field ] );
					$metaplate[ $field ]['code' ] = $value;
				}

			}
			else {
				$metaplate[ $field ][ 'code' ] = 1;
			}
		}

		return $metaplate;

	}

	/**
	 * Get the metaplate registry
	 *
	 * @return array|bool
	 */
	public static function get_registry() {
		return get_option( self::$registry_option_name );
	}

	/**
	 * Get a metaplate's ID using its slug
	 *
	 * @param string $slug The metaplate's slug.
	 * @param null|array $metaplates Optional. The metaplate registry to look in.
	 *
	 * @return bool|array
	 */
	public static function get_metaplate_id_by_slug( $slug, $metaplates = null ) {
		if ( is_null( $metaplates ) ) {
			$metaplates = self::get_registry();
		}

		if ( is_array( $metaplates ) ) {
			$search = wp_list_pluck( $metaplates, 'slug' );
			return array_search( $slug, $search );

		}

		return false;
	}

	/**
	 * Update registry of metaplates
	 *
	 * Note: Does not save the metaplate itself.
	 *
	 * @param array $new_value The new item to add.
	 * @param string $id Id of new item to add.
	 *
	 * @return bool
	 */
	public static function update_registry( $new_value, $id ) {
		$registry = self::get_registry();
		$registry[ $id ] = $new_value;

		return update_option( self::$registry_option_name, $registry );

	}

} 
