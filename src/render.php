<?php
/**
 * Renders the metaplate
 *
 * @package caldera\metaplate
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer
 */

namespace calderawp\metaplate\core;

use Handlebars\Handlebars;
use calderawp\filter;

class render {

	/**
	 * Return the content with metaplate applied.
	 *
	 * @uses "the_content" filter
	 *
	 * @param string $content Post content
	 * @param array|null $meta_stalk Optional. The metaplate to use to render the data. If is null, the default, one will be load, if possible, based on current global $post object.
	 * @param array|null $template_data Optional. Prepared field data to render metaplate with. If is null, the default, meta stalk will be retrieved, if possible, based on current global $post object.
	 * @param string|null $placement Optional. Where to put the template output, before, after or in place of $content. If is null, option from metaplate is used. Default is--funcitonally speaking--replace. Options: prepend|append|replace|null.
	 *
	 * @return    string    Rendered HTML with templates applied--if templates and data were provided.
	 */
	public function render_metaplate( $content, $meta_stack = null, $template_data = null, $placement = null ) {

		if ( is_null( $meta_stack ) ) {
			$meta_stack = data::get_active_metaplates();
		}
		
		// clear out <!--metaplate-->
		$content = str_replace( '<p><!--metaplate--></p>', '', $content );
		// in case the wpautop didnt detect the tag.
		$content = str_replace( '<!--metaplate-->', '', $content );

		if( empty( $meta_stack ) ){
			return $content;

		}

		if ( is_null( $template_data ) ) {
			global $post;
			$template_data = data::get_custom_field_data( $post->ID );
		}

		if( ! $template_data || empty( $template_data ) ){
			return $content;

		}

		// unserilize if needed.
		foreach( $template_data as &$meta_item ){
			$meta_item = maybe_unserialize( $meta_item );
		}
	
		// add filter.
		$magic = new filter\magictag();
		$content = $magic->do_magic_tag( trim( $content ) );

		$style_data = null;
		$script_data = null;

		$engine = new Handlebars;

		$engine = $this->helpers( $engine );

		foreach( $meta_stack as $metaplate ){

			// apply filter to data for this metaplate
			$template_data = apply_filters( 'metaplate_data', $template_data, $metaplate );

			// check CSS
			if ( isset( $metaplate[ 'css' ][ 'code' ] )  ) {
				$style_data .= $engine->render( $metaplate['css']['code'], $template_data );
			} else {
				$style_data = '';
			}

			if ( isset( $metaplate['js']['code'] )  ) {
				// check JS
				$script_data .= $engine->render( $metaplate['js']['code'], $template_data );
			} else {
				$script_data = '';
			}

			if ( ! is_null( $placement ) ) {
				$metaplate[ 'placement' ] = $placement;
			}

			if ( ! isset( $metaplate['placement'] ) || ! in_array( $metaplate['placement'], array( 'prepend', 'append', 'replace' ) ) ) {
				$metaplate['placement'] = false;
			}

			$template = $metaplate[ 'html' ][ 'code' ];

			switch ( $metaplate['placement'] ){
				case 'prepend':
					$content = $engine->render( $template, $template_data ) . $content;
					break;
				case 'append':
					$content .= $engine->render( $template, $template_data );
					break;
				case 'replace':
					$content = $engine->render( str_replace( '{{content}}', $content, $template ), $template_data );
					break;
				default :
					$content = $engine->render( str_replace( '{{content}}', $content, $template ), $template_data );
			}


		}


		// insert CSS
		if( ! empty( $style_data ) ){
			$content = '<style>' . $style_data . '</style>' . $content;
		}

		// insert JS
		if( ! empty( $script_data ) ){
			$content .= '<script type="text/javascript">' . $script_data . '</script>';
		}

		// add magic filter.
		$magic = new filter\magictag();
		$content = $magic->do_magic_tag( $content );

		return $content;

	}

	/**
	 * Register helpers.
	 *
	 * Adds the default helpers, plus any set on "caldera_metaplate_handlebars_helpers" filter.
	 *
	 * @param obj|\Handlebars\Handlebars $handlebars Current instance of Handlebars.
	 *
	 * @return \Handlebars\Handlebars Current instance of Handlebars with the additional helpers added on.
	 */
	private function helpers( $handlebars ) {
		$helpers = $this->default_helpers();

		/**
		 *
		 * @param array $helpers {
		 *     Name, class & callback for the helper.
		 *
		 *     @type string $name Name of helper to use in Handlebars.
		 *     @type string $class Class containing callback function.
		 *     @type string $callback. Optional. The name of the callback function. If not set, "helper" will be used.
		 * }
		 * @param obj|\Handlebars\Handlebars $handlebars Handlebars.php class instance
		 *
		 */
		$helpers = apply_filters( 'caldera_metaplate_handlebars_helpers', $helpers, $handlebars );
		$handlebars = new helper_loader( $handlebars, $helpers );

		if ( isset( $handlebars->handlebars ) ) {
			$handlebars = $handlebars->handlebars;
		}

		return $handlebars;

	}

	/**
	 * The default helpers.
	 *
	 * @return array
	 */
	private function default_helpers() {
		return  array(
			array(
				'name' => 'is',
				'class' => 'calderawp\helpers\is'
			),
			array(
				'name' => '_image',
				'class' => 'calderawp\helpers\image'
			),
			array(
				'name' => 'vardump',
				'class' => 'calderawp\helpers\vardump'
			),
			array(
				'name' => 'sanitize',
				'class' => 'calderawp\helpers\sanitize'
			),
			array(
				'name' => 'format_date',
				'class' => 'calderawp\helpers\date'
			)
		);

	}

} 
