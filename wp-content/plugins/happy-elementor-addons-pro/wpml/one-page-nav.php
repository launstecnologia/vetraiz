<?php
/**
 * One Page Nav
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_One_Page_Nav extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'navigation_lists';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
				'nav_title',
				'tooltip_title',
			];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'nav_title':
				return __( 'One Page Nav: Navigation Title', 'happy-addons-pro' );
			case 'tooltip_title':
				return __( 'One Page Nav: Tooltip Title', 'happy-addons-pro' );
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
			case 'nav_title':
				return 'LINE';
			case 'tooltip_title':
				return 'LINE';
			default:
				return '';
		}
	}
}
