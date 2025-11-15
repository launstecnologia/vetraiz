<?php
/**
 * Metro Grid
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Metro_Grid extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'media_list';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'title',
			'subtitle',
			'url',
			'youtube_url',
			'vimeo_url',
			'dailymotion_url',
			'external_url' => ['url']
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
				return __( 'Metro Grid: Title', 'happy-addons-pro' );
			case 'subtitle':
				return __( 'Metro Grid: Subtitle', 'happy-addons-pro' );
			case 'url':
				return __( 'Metro Grid: Link URL', 'happy-addons-pro' );
			case 'youtube_url':
				return __( 'Metro Grid: Link', 'happy-addons-pro' );
			case 'vimeo_url':
				return __( 'Metro Grid: Link', 'happy-addons-pro' );
			case 'dailymotion_url':
				return __( 'Metro Grid: Link', 'happy-addons-pro' );
			case 'external_url':
				return __( 'Metro Grid: URL', 'happy-addons-pro' );
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
			case 'url':
			case 'youtube_url':
			case 'vimeo_url':
			case 'dailymotion_url':
				return 'LINE';
			case 'subtitle':
				return 'AREA';
			case 'external_url':
				return 'LINK';
			default:
				return '';
		}
	}
}
