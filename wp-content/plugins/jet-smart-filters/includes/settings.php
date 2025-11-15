<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Settings' ) ) {
	/**
	 * Define Jet_Smart_Filters_Settings class
	 */
	class Jet_Smart_Filters_Settings {

		public $key = 'jet-smart-filters-settings';

		// global settings
		public $url_structure_type;
		public $url_taxonomy_term_name;
		public $is_seo_enabled;
		public $wc_hide_out_of_stock_variations;

		public function __construct() {

			// init global settings
			$this->url_structure_type              = $this->get( 'url_structure_type', 'plain' );
			$this->url_taxonomy_term_name          = $this->get( 'url_taxonomy_term_name', 'term_id' );
			$this->is_seo_enabled                  = filter_var( $this->get( 'use_seo_sitemap', false ), FILTER_VALIDATE_BOOLEAN );
			$this->wc_hide_out_of_stock_variations = filter_var( $this->get( 'wc_hide_out_of_stock_variations', false ), FILTER_VALIDATE_BOOLEAN );
		}

		public function get( $setting, $default = false ) {

			$current = get_option( $this->key, array() );

			return isset( $current[ $setting ] ) ? $current[ $setting ] : $default;
		}

		public function update( $setting, $value ) {

			$current = get_option( $this->key, array() );
			$current[$setting] = is_array( $value ) ? $value : esc_attr( $value );

			return update_option( $this->key, $current );
		}
	}
}
