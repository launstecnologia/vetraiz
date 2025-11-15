<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_WPML_Package\Meta_Boxes;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * A reference to an instance of compatibility package.
	 *
	 * @access private
	 * @var    object
	 */
	private $package = null;

	private function __construct( $package = null ) {
		$this->package = $package;

		// Post meta conditions
		add_filter( 'jet-engine/meta-boxes/conditions/post-has-terms/check-terms', array( $this, 'set_translated_check_terms' ), 10, 2 );
	}

	public function set_translated_check_terms( $terms, $tax ) {
		return array_map( function ( $term ) use ( $tax ) {
			return apply_filters( 'wpml_object_id', $term, $tax, true );
		}, $terms );
	}

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return object
	 */
	public static function instance( $package = null ) {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self( $package );
		}

		return self::$instance;

	}
	
}
