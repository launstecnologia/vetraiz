<?php
/**
 * Title Tips
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Title_Tips extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'text_item_data';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'title',
			'description',
			'link' => ['url']
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
				return __( 'Title Tips: Title', 'happy-addons-pro' );
			case 'description':
				return __( 'Title Tips: Description', 'happy-addons-pro' );
			case 'link':
				return __( 'Title Tips: Link', 'happy-addons-pro' );
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
				return 'AREA';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
