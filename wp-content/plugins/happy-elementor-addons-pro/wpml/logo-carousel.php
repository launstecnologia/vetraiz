<?php
/**
 * Logo Carousel
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Logo_Carousel extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'logo_items';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
				'name',
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
			case 'name':
				return __( 'Logo Carousel: Brand Name', 'happy-addons-pro' );
			case 'url':
				return __( 'Logo Carousel: Website Url', 'happy-addons-pro' );
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
			case 'name':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
