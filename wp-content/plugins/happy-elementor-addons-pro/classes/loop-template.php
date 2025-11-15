<?php
namespace Happy_Addons_Pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor template library local source.
 *
 * Elementor template library local source handler class is responsible for
 * handling local Elementor templates saved by the user locally on his site.
 *
 * @since 1.0.0
 */
class Loop_Template {

	private static $instance = null;

	public function init() {
		add_filter('happyaddons/theme-builder/template-types', [ $this, 'loop_template_add'], 10, 1);
		add_action( 'wp_head', [ $this, 'loop_template_editor_width' ] );
	}

	public function loop_template_add($template_types) {
		$template_types['loop-template'] = esc_html__('Loop Template', 'happy-addons-pro');
		return $template_types;
	}

	public function loop_template_editor_width() {
		if( 'loop-template' == get_post_meta(get_the_ID(), '_ha_library_type', true) ) {
			echo '<style>
				.ha_library-template.ha_library-template-elementor_canvas {
					display: flex;
					justify-content: center;
					align-items: center;
					min-height: 100vh;
				}
				.elementor.elementor-edit-area.elementor-edit-mode {
					width: 500px;
				}
				.elementor.elementor-edit-area.elementor-edit-mode .elementor-section-wrap:has(.elementor-element) + #elementor-add-new-section {
					display: none;
				}
				.elementor.elementor-edit-area.elementor-edit-mode .elementor-section-wrap:has(.elementor-element) {
					padding-bottom: 100px;
				}
			</style>';
		}
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			// self::$instance->init();
		}

		return self::$instance;
	}
}

Loop_Template::instance()->init();
