<?php
/**
 * Off Canvas
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Off_Canvas extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'custom_content';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
				'title',
				'description',
			];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'title':
				return __( 'Off Canvas: Title', 'happy-addons-pro' );
			case 'description':
				return __( 'Off Canvas: Description', 'happy-addons-pro' );
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'title':
				return 'LINE';
			case 'description':
				return 'VISUAL';
			default:
				return '';
		}
	}
}
