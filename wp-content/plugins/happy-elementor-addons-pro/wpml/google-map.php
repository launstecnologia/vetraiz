<?php
/**
 * Advanced Google Map
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Google_Map_Ha_Gmap_Polylines extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'ha_gmap_polylines';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
				'ha_gmap_polyline_title',
			];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'ha_gmap_polyline_title':
				return __( 'Advanced Google Map: Title', 'happy-addons-pro' );
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
			case 'ha_gmap_polyline_title':
				return 'LINE';
			default:
				return '';
		}
	}
}

class WPML_Google_Map_List_Legend extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'list_legend';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
				'legend_item_title',
			];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'legend_item_title':
				return __( 'Advanced Google Map: Title', 'happy-addons-pro' );
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
			case 'legend_item_title':
				return 'LINE';
			default:
				return '';
		}
	}
}

class WPML_Google_Map_List_Marker extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'list_marker';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
				'pin_item_title',
				'pin_item_description'
			];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'pin_item_title':
				return __( 'Advanced Google Map: Title', 'happy-addons-pro' );
			case 'pin_item_description':
				return __( 'Advanced Google Map: Description', 'happy-addons-pro' );
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
			case 'pin_item_title':
				return 'LINE';
			case 'pin_item_description':
				return 'AREA';
			default:
				return '';
		}
	}
}
