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

namespace caldera\metaplate\core;

use Handlebars\Handlebars;

class render {

	/**
	 * Return the content with metaplate applied.
	 *
	 * @uses "the_content" filter
	 *
	 * @param string $content Post content
	 *
	 * @return    string    rendered HTML with templates applied
	 */
	public function render_metaplate( $content ) {

		global $post;

		$meta_stack = data::get_active_metaplates();
		if( empty( $meta_stack ) ){
			return $content;
		}

		$style_data = null;
		$script_data = null;

		$template_data = data::get_custom_field_data( $post->ID );

		$engine = new Handlebars;

		$engine = $this->helpers( $engine );


		foreach( $meta_stack as $metaplate ){
			// apply filter to data for this metaplate
			$template_data = apply_filters( 'metaplate_data', $template_data, $metaplate );
			// check CSS
			$style_data .= $engine->render( $metaplate['css']['code'], $template_data );
			// check JS
			$script_data .= $engine->render( $metaplate['js']['code'], $template_data );

			switch ( $metaplate['placement'] ){
				case 'prepend':
					$content = $engine->render( $metaplate['html']['code'], $template_data ) . $content;
					break;
				case 'append':
					$content .= $engine->render( $metaplate['html']['code'], $template_data );
					break;
				case 'replace':
					$content = $engine->render( str_replace( '{{content}}', $content, $metaplate['html']['code']), $template_data );
					break;
			}
		}

		// insert CSS
		if( !empty( $style_data ) ){
			$content = '<style>' . $style_data . '</style>' . $content;
		}
		// insert JS
		if( !empty( $script_data ) ){
			$content .= '<script type="text/javascript">' . $script_data . '</script>';
		}

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
				'class' => 'caldera\helpers\is' ),
			array(
				'name' => '_image',
				'class' => 'caldera\helpers\image' ),
		);

	}

} 
