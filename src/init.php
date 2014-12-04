<?php
/**
 * Metaplate main class
 *
 * @package caldera\metaplate
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer
 */

namespace caldera\metaplate\core;


/**
 * Class metaplate
 *
 * @package caldera\metaplate
 */
class init {
	/**
	 * @var      string
	 */
	protected $plugin_slug = 'metaplate';
	/**
	 * @var      object
	 */
	protected static $instance = null;
	/**
	 * @var      array
	 */
	protected $plugin_screen_hook_suffix = array();

	/**
	 * Return an instance of this class.
	 *
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, FALSE, basename( MTPT_PATH ) . '/languages');

	}

	public function define_admin_template_path() {
		$template_path = dirname( __FILE__ ).'/templates/';

		if ( ! defined( 'MTPT_ADMIN_TEMPLATE_PATH' ) ) {
			define( 'MTPT_ADMIN_TEMPLATE_PATH', $template_path );
		}
	}


} 
