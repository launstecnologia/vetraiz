<?php
namespace Jet_Engine\Bricks_Views\Dynamic_Data;

use Bricks\Helpers;
use Jet_Engine\Modules\Custom_Content_Types\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Provider_Jet_Engine_Macros extends \Bricks\Integrations\Dynamic_Data\Providers\Base {
	public function register_tags() {
		$name = 'je_macros';

		$tag = [
			'name'     => '{' . $name . '}',
			'label'    => 'Macros - add macros after :',
			'group'    => 'Jet Engine Dynamic Data',
			'field'    => 'text',
			'provider' => $this->name
		];

		$this->tags[ $name ] = $tag;
	}

	public function get_tag_value( $tag, $post, $args, $context ) {
		if ( empty( $args ) ) {
			if ( bricks_is_frontend() ) {
				return '';
			}

			return Helpers::get_element_placeholder( [
				'title'       => esc_html__( 'Generate the necessary macros using the macros generator and pass it as an argument for the dynamic token.', 'jet-engine' ),
				'description' => esc_html__( 'Go to: JetEngine > Macros Generator.', 'jet-engine' ),
				'icon-class'  => 'ti-alert',
			], 'info' );
		}

		$post    = jet_engine()->listings->data->get_current_object();
		$post_id = $post->ID ?? $post->_ID ?? '';

		// Use a regular expression to find the relevant part of the string
		$patternPercentage = '/%%(.*?)%%/';

		// Replace the part of the string with the JSON format
		$args = preg_replace_callback( $patternPercentage, [ $this, 'replace_symbols' ], $args );

		// Use a regular expression to find the relevant part of the string
		$patternDoubleAngleBrackets = '/<<(.+?)>>/';

		// Replace the part of the string with the JSON format
		$args = preg_replace_callback( $patternDoubleAngleBrackets, [ $this, 'convert_to_json' ], $args );

		// STEP: Check for filter args
		$filters = $this->get_filters_from_args( $args );

		$value = jet_engine()->listings->macros->do_macros( $args );

		// STEP: Apply context (text, link, image, media)
		$value = $this->format_value_for_context( $value, $tag, $post_id, $filters, $context );

		return $value;
	}

	// This method replaces specific symbol patterns in the matched part of the string
	public function replace_symbols( $matches ) {
		$replacements = [
			'%%' => '%',
			'==' => ':',
			'++' => '"',
			'<~' => '[',
			'~>' => ']',
			'<<' => '{',
			'>>' => '}',
		];

		return strtr($matches[0], $replacements);
	}

	// Function to process the matched part and convert it to JSON
	public function convert_to_json( $matches ) {
		// Split the parameters by ';;'
		$params = explode( ';;', $matches[1] );

		// Create an associative array
		$result = [];
		foreach ( $params as $param ) {
			list( $key, $value ) = explode( '==', $param );
			$result[ $key ] = $value;
		}

		// Return the array in JSON format
		return json_encode( $result );
	}
}
