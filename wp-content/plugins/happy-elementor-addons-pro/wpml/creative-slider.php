<?php
/**
 * Creative Slider
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Creative_Slider extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'ha_creative_slider_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
				'cc_pre_title',
				'cc_title',
				'cc_sub_title',
				'cc_description',
				'cc_btn_text',
				'cc_btn_url' => ['url']
			];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'cc_pre_title':
				return __( 'Creative Slider: Pre Title', 'happy-addons-pro' );
			case 'cc_title':
				return __( 'Creative Slider: Title', 'happy-addons-pro' );
			case 'cc_sub_title':
				return __( 'Creative Slider: Sub Title', 'happy-addons-pro' );
			case 'cc_description':
				return __( 'Creative Slider: Description', 'happy-addons-pro' );
			case 'cc_btn_text':
				return __( 'Creative Slider: Button Text', 'happy-addons-pro' );
			case 'url':
				return __( 'Creative Slider: Button Link', 'happy-addons-pro' );
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
			case 'cc_pre_title':
				return 'LINE';
			case 'cc_title':
				return 'LINE';
			case 'cc_sub_title':
				return 'LINE';
			case 'cc_description':
				return 'AREA';
			case 'cc_btn_text':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
