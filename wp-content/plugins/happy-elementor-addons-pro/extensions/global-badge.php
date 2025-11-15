<?php
namespace Happy_Addons_Pro\Extension;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || die();

class Global_Badge {

	/**
	 * @var mixed
	 */
	private static $instance = null;

	/**
	 * @var mixed
	 */
	private $load_script = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init() {

		// Enqueue the required JS file.
		// add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/frontend/before_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'enqueue_preview_scripts' ] );

		// Creates Global Badge tab at the end of layout/content tab.
		add_action( 'elementor/element/section/section_layout/after_section_end', [ $this, 'register_controls' ], 10 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'register_controls' ], 10 );
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_controls' ], 10 );

		// Frontend Hooks.
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render' ] );
		add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render' ] );
		add_action( 'elementor/widget/before_render_content', [ $this, 'before_render' ], 10, 1 );

		// Editor Hooks.
		add_action( 'elementor/section/print_template', [ $this, 'print_template' ], 10, 2 );
		add_action( 'elementor/column/print_template', [ $this, 'print_template' ], 10, 2 );
		add_action( 'elementor/widget/print_template', [ $this, 'print_template' ], 10, 2 );

		if ( defined( 'ELEMENTOR_VERSION' ) && \Elementor\Plugin::instance()->experiments->is_feature_active( 'container' ) ) {
			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10 );
			add_action( 'elementor/container/print_template', array( $this, 'print_template' ), 10, 2 );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ) );
		}
	}

	public function register_scripts() {
		$suffix = ha_is_script_debug_enabled() ? '.' : '.min.';

		wp_register_script(
			'lottie-js',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/lottie/lottie.min.js',
			null,
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		wp_register_script(
			'happy-global-badge',
			HAPPY_ADDONS_PRO_ASSETS . 'js/global-badge' . $suffix . 'js',
			['jquery', 'happy-elementor-addons'],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

	}

	public function register_styles() {
		$suffix = ha_is_script_debug_enabled() ? '.' : '.min.';
		wp_register_style(
			'happy-global-badge',
			HAPPY_ADDONS_PRO_ASSETS . 'css/widgets/global-badge' . $suffix . 'css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);
	}

	public function enqueue_preview_scripts() {
		wp_enqueue_script( 'lottie-js' );
		wp_enqueue_script( 'anime' );
		wp_enqueue_script( 'happy-global-badge' );
	}

	/**
	 * Register Global badge controls.
	 */
	public function register_controls( $element ) {

		$tab = Controls_Manager::TAB_LAYOUT;
		if ( 'common' == $element->get_name() ) {
			$tab = Controls_Manager::TAB_CONTENT;
		}

		$element->start_controls_section(
			'_ha_global_badge_section',
			[
				'label' => esc_html__( 'Global Badge', 'happy-addons-pro' ) . ha_get_section_icon(),
				'tab'   => $tab,
			]
		);

		$this->add_content_controls( $element );

		$this->add_tabs_controls( $element );

		$element->add_control(
			'ha_global_badge_reset',
			[
				// 'label' => esc_html__( 'Reset', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::BUTTON,
				'separator' => 'before',
				'text' => esc_html__( 'Reset', 'happy-addons-pro' ),
				// 'button_type' => 'success',
				'event' => 'haGlobalBadgeReset',
				'condition'   => [
					'ha_gb_switcher' => 'yes',
				],
			]

		);

		$element->end_controls_section();
	}

	/**
	 * Add content controls.
	 */
	public function add_content_controls( $element ) {

		$element->add_control(
			'ha_gb_switcher',
			[
				'label'        => __( 'Enable Global Badge', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'ha-gb-',
				'render_type'  => 'template',
				'style_transfer' => false,
				'assets' => [
					'scripts' => [
						[
							'name' => 'lottie-js',
							'conditions' => [
								'terms' => [
									[
										'name' => 'ha_gb_switcher',
										'operator' => '===',
										'value' => 'yes',
									],
								],
							],
						],
						[
							'name' => 'anime',
							'conditions' => [
								'terms' => [
									[
										'name' => 'ha_gb_switcher',
										'operator' => '===',
										'value' => 'yes',
									],
								],
							],
						],
						[
							'name' => 'happy-global-badge',
							'conditions' => [
								'terms' => [
									[
										'name' => 'ha_gb_switcher',
										'operator' => '===',
										'value' => 'yes',
									],
								],
							],
						]
					],
					'styles' => [
						[
							'name' => 'happy-global-badge',
							'conditions' => [
								'terms' => [
									[
										'name' => 'ha_gb_switcher',
										'operator' => '===',
										'value' => 'yes',
									]
								]
							]
						]
					]
				],
				// 'frontend_available' => true,
			]
		);

		$element->add_control(
			'ha_gb_text',
			[
				'label'       => __( 'Text', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'New',
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
				'condition'   => [
					'ha_gb_switcher' => 'yes',
				],
			]
		);

		$element->add_control(
			'ha_gb_type',
			[
				'label'          => __( 'Style', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SELECT,
				'prefix_class'   => 'ha-gb-',
				'options'        => [
					'stripe'   => __( 'Stripe', 'happy-addons-pro' ),
					'flag'     => __( 'Flag', 'happy-addons-pro' ),
					'tri'      => __( 'Triangle', 'happy-addons-pro' ),
					'circle'   => __( 'Circle', 'happy-addons-pro' ),
					'bookmark' => __( 'Bookmark', 'happy-addons-pro' ),
					'custom'   => __( 'Custom Layout', 'happy-addons-pro' ),
				],
				'default'        => 'stripe',
				'condition'      => [
					'ha_gb_switcher' => 'yes',
				],
				'style_transfer' => true,
				'render_type'  => 'template',
			]
		);

		$element->add_control(
			'ha_gb_icon_type',
			[
				'label'       => __( 'Icon Type', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'render_type' => 'template',
				'frontend_available' => true,
				'options'     => [
					'none'   => __( 'None', 'happy-addons-pro' ),
					'icon'   => __( 'Icon', 'happy-addons-pro' ),
					'image'  => __( 'Image', 'happy-addons-pro' ),
					'lottie' => __( 'Lottie', 'happy-addons-pro' ),
				],
				'default'     => 'none',
				'condition'   => [
					'ha_gb_switcher' => 'yes',
				],
			]
		);

		$element->add_control(
			'ha_gb_icon',
			[
				'label'     => __( 'Choose Icon', 'happy-addons-pro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-ribbon',
					'library' => 'fa-solid',
				],
				'condition' => [
					'ha_gb_switcher'  => 'yes',
					'ha_gb_icon_type' => 'icon',
				],
			]
		);

		$element->add_control(
			'ha_gb_img',
			[
				'label'     => __( 'Choose Image', 'happy-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [
					'ha_gb_switcher'  => 'yes',
					'ha_gb_icon_type' => 'image',

				],
			]
		);

		$element->add_control(
			'ha_gb_lottie_url',
			[
				'label'       => __( 'Animation JSON URL', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_url( 'https://assets4.lottiefiles.com/private_files/lf30_ujs3c7ok.json' ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => [
					'ha_gb_switcher'  => 'yes',
					'ha_gb_icon_type' => 'lottie',

				],
			]
		);

		$element->add_control(
			'ha_gb_loop',
			[
				'label'        => __( 'Loop', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => [
					'ha_gb_switcher'  => 'yes',
					'ha_gb_icon_type' => 'lottie',
				],
			]
		);

		$element->add_control(
			'ha_gb_clip_enabled',
			[
				'label'       => __( 'Enable Clip Path', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'condition'   => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type'     => 'custom',
				],
			]
		);

		$element->add_control(
			'ha_gb_path',
			[
				'label'          => __( 'Path Value', 'happy-addons-pro' ),
				'type'           => Controls_Manager::TEXTAREA,
				'description'    => 'Get Clip Path code from <a href="https://bennettfeely.com/clippy/" target="_blank">Clippy</a>.',
				'placeholder'    => __( 'Paste your Path code here. EX: polygon(50% 0%, 0% 10%, 100% 100%)', 'happy-addons-pro' ),
				'label_block'    => true,
				'condition'      => [
					'ha_gb_switcher'     => 'yes',
					'ha_gb_type'         => 'custom',
					'ha_gb_clip_enabled' => 'yes',
				],
				'selectors'      => [
					'{{WRAPPER}}.ha-gb-custom > .ha-gb-wrap-{{ID}}' => 'filter:blur(.25px); clip-path: {{VALUE}}; -webkit-clip-path: {{VALUE}}; -ms-clip-path: {{VALUE}};',
				],
				'style_transfer' => true,
			]
		);

		$this->add_effects_controls( $element );
	}

	/**
	 * Add tabs controls.
	 */
	public function add_tabs_controls( $element ) {

		$element->add_control(
			'ha_gb_heading',
			[
				'label'     => esc_html__( 'Style & Layout', 'happy-addons-pro' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'ha_gb_switcher' => 'yes',
				],
			]
		);

		$element->start_controls_tabs( 'ha_gb_style_tabs' );

		// display section.
		$element->start_controls_tab(
			'ha_gb_display_controls',
			[
				'label'     => __( 'Layout', 'happy-addons-pro' ),
				'condition' => [
					'ha_gb_switcher' => 'yes',
				],
			]
		);

		$this->add_display_controls( $element );

		$element->end_controls_tab();

		// style section.
		$element->start_controls_tab(
			'ha_gb_style_controls',
			[
				'label'     => __( 'Style', 'happy-addons-pro' ),
				'condition' => [
					'ha_gb_switcher' => 'yes',
				],
			]
		);

		$this->add_style_controls( $element );

		$element->end_controls_tab();

		// icon style section.
		$element->start_controls_tab(
			'ha_gb_icon_styles',
			[
				'label'     => __( 'Icon', 'happy-addons-pro' ),
				'condition' => [
					'ha_gb_switcher'   => 'yes',
					'ha_gb_icon_type!' => 'none',
				],
			]
		);

		$this->add_icon_style( $element );

		$element->end_controls_tab();

		$element->end_controls_tabs();
	}

	/**
	 * Add display controls.
	 */
	public function add_display_controls( $element ) {

		/** Display & Position */
		$element->add_control(
			'ha_gb_display',
			[
				'label'          => __( 'Display', 'happy-addons-pro' ),
				'type'           => Controls_Manager::CHOOSE,
				'prefix_class'   => 'ha-gb-',
				'toggle'         => false,
				'options'        => [
					'row'    => [
						'title' => __( 'Inline', 'happy-addons-pro' ),
						'icon'  => 'eicon-ellipsis-h',
					],
					'column' => [
						'title' => __( 'Block', 'happy-addons-pro' ),
						'icon'  => 'eicon-ellipsis-v',
					],
				],
				'default'        => 'row',
				'condition'      => [
					'ha_gb_switcher'   => 'yes',
					'ha_gb_icon_type!' => 'none',
				],
				'selectors'      => [
					'{{WRAPPER}}.ha-gb-yes .ha-gb-wrap-{{ID}} .ha-gb-inner' => 'flex-direction: {{VALUE}};',
				],
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_hor',
			[
				'label'          => __( 'Horizontal Position', 'happy-addons-pro' ),
				'type'           => Controls_Manager::CHOOSE,
				'prefix_class'   => 'ha-gb-',
				'toggle'         => false,
				'options'        => [
					'left'  => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'        => 'right',
				'condition'      => [
					'ha_gb_switcher' => 'yes',
				],
				'selectors'      => [
					'{{WRAPPER}}:not(.ha-gb-flag):not(.ha-gb-bookmark):not(.ha-gb-circle):not(.ha-gb-custom) .ha-gb-wrap-{{ID}}, {{WRAPPER}}.ha-gb-custom > .ha-gb-svg-{{ID}}' => '{{VALUE}}: 0;',
					'{{WRAPPER}}.ha-gb-circle .ha-gb-wrap-{{ID}}, {{WRAPPER}}.ha-gb-custom .ha-gb-wrap-{{ID}}'                                                                 => '{{VALUE}}: 8px;',
					'{{WRAPPER}}.ha-gb-bookmark .ha-gb-wrap-{{ID}}'                                                                                                            => '{{VALUE}}: 20px;',
				],
				'style_transfer' => true,
				'render_type'  => 'template',
			]
		);

		$element->add_control(
			'ha_gb_ver',
			[
				'label'          => __( 'Vertical Position', 'happy-addons-pro' ),
				'type'           => Controls_Manager::CHOOSE,
				'toggle'         => false,
				'prefix_class'   => 'ha-gb-',
				'options'        => [
					'top'    => [
						'title' => __( 'Top', 'happy-addons-pro' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'happy-addons-pro' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'        => 'top',
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type'     => [ 'custom', 'circle' ],
				],
				'selectors'      => [
					'{{WRAPPER}}:not(.ha-gb-flag):not(.ha-gb-circle):not(.ha-gb-custom) .ha-gb-wrap-{{ID}}, {{WRAPPER}}.ha-gb-custom > .ha-gb-svg-{{ID}}' => '{{VALUE}}: 0;',
					'{{WRAPPER}}.ha-gb-circle .ha-gb-wrap-{{ID}}, {{WRAPPER}}.ha-gb-custom .ha-gb-wrap-{{ID}}'                                            => '{{VALUE}}: 8px;',
				],
				'style_transfer' => true,
			]
		);

		$element->add_responsive_control(
			'ha_gb_rotate',
			[
				'label'          => __( 'Rotate', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'deg' ],
				'default'        => [
					'unit' => 'deg',
					'size' => 0,
				],
				'range'          => [
					'deg' => [
						'min' => -180,
						'max' => 180,
					],
				],
				'selectors'      => [
					'{{WRAPPER}} .ha-gb-wrap-{{ID}}' => 'transform: rotate({{SIZE}}deg)',
				],
				'condition'      => [
					'ha_gb_switcher'               => 'yes',
					'ha_gb_type'                   => 'custom',
					'ha_gb_efct_translate_toggle!' => 'yes',
					'ha_gb_efct_rotate_toggle!'    => 'yes',
					'ha_gb_efct_scale_toggle!'     => 'yes',
				],
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_zindex',
			[
				'label'          => __( 'Z-Index', 'happy-addons-pro' ),
				'type'           => Controls_Manager::NUMBER,
				'step'           => 1,
				'description'    => __( 'Default is 5', 'happy-addons-pro' ),
				'selectors'      => [
					'{{WRAPPER}} .ha-gb-wrap-{{ID}}' => 'z-index: {{VALUE}}',
				],
				'condition'      => [
					'ha_gb_switcher' => 'yes',
				],
				'style_transfer' => true,
			]
		);
	}

	/**
	 * Add style controls.
	 */
	public function add_style_controls( $element ) {

		$element->add_responsive_control(
			'ha_gb_hor_offset',
			[
				'label'          => __( 'Horizontal Offset', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px', '%' ],
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type!'    => [ 'flag'],
				],
				'selectors'      => [
					'{{WRAPPER}}.ha-gb-flag .ha-gb-wrap-{{ID}}'             => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-circle .ha-gb-wrap-{{ID}}'           => '{{ha_gb_hor.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-custom .ha-gb-wrap-{{ID}}'           => '{{ha_gb_hor.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-bookmark .ha-gb-wrap-{{ID}}'         => '{{ha_gb_hor.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-tri .ha-gb-wrap-{{ID}} .ha-gb-inner' => 'left: {{SIZE}}px;',
					'{{WRAPPER}}.ha-gb-yes.ha-gb-stripe > .ha-gb-wrap-{{ID}} .ha-gb-inner' => '--ha-gb-strip-x: {{SIZE}}px;',
				],
				'style_transfer' => true,
			]
		);

		$element->add_responsive_control(
			'ha_gb_ver_offset',
			[
				'label'          => __( 'Vertical Offset', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px', '%' ],
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type!'    => [ 'bookmark' ],
				],
				'selectors'      => [
					'{{WRAPPER}}.ha-gb-flag .ha-gb-wrap-{{ID}}'                         => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-circle .ha-gb-wrap-{{ID}}'                       => '{{ha_gb_ver.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-custom .ha-gb-wrap-{{ID}}'                       => '{{ha_gb_ver.VALUE}}: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-tri.ha-gb-left .ha-gb-wrap-{{ID}} .ha-gb-inner'  => 'bottom: {{SIZE}}px;',
					'{{WRAPPER}}.ha-gb-tri.ha-gb-right .ha-gb-wrap-{{ID}} .ha-gb-inner' => 'top: {{SIZE}}px;',
					'{{WRAPPER}}.ha-gb-yes.ha-gb-stripe > .ha-gb-wrap-{{ID}} .ha-gb-inner' => '--ha-gb-strip-y: {{SIZE}}px;',
				],
				'style_transfer' => true,
			]
		);

		$indent_class = is_rtl() ? '.ha-gb-text' : '.ha-gb-icon';

		$element->add_responsive_control(
			'ha_gb_spacing',
			[
				'label'          => __( 'Spacing', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px', 'em' ],
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition'      => [
					'ha_gb_switcher'   => 'yes',
					'ha_gb_icon_type!' => 'none',
				],
				'selectors'      => [
					'{{WRAPPER}}.ha-gb-row .ha-gb-wrap-{{ID}} ' . $indent_class => 'text-indent: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-column .ha-gb-wrap-{{ID}} .ha-gb-icon'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'style_transfer' => true,
			]
		);

		/** Style */
		$element->add_responsive_control(
			'ha_gb_size',
			[
				'label'          => __( 'Box Size', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px', 'em' ],
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type!'    => [ 'bookmark', 'stripe', 'flag', 'circle' ],
				],
				'selectors'      => [
					'{{WRAPPER}}.ha-gb-tri.ha-gb-left > .ha-gb-wrap-{{ID}}'  => 'border-top-width: {{SIZE}}{{UNIT}}; border-bottom-width: {{SIZE}}{{UNIT}}; border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-tri.ha-gb-right > .ha-gb-wrap-{{ID}}' => 'border-left-width: {{SIZE}}{{UNIT}}; border-bottom-width: {{SIZE}}{{UNIT}}; border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-custom > .ha-gb-wrap-{{ID}} .ha-gb-inner'          => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_color',
			[
				'label'          => __( 'Text Color', 'happy-addons-pro' ),
				'type'           => Controls_Manager::COLOR,
				'condition'      => [
					'ha_gb_switcher' => 'yes',
				],
				'selectors'      => [
					'{{WRAPPER}} .ha-gb-wrap-{{ID}} .ha-gb-text' => 'color: {{VALUE}};',
				],
				'style_transfer' => true,
			]
		);

		$element->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'ha_gb_bg',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'selector'       => '{{WRAPPER}} .ha-gb-wrap-{{ID}} .ha-gb-inner',
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type!'    => [ 'bookmark', 'tri', 'flag' ],
				],
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_bgcolor',
			[
				'label'          => __( 'Background Color', 'happy-addons-pro' ),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}}.ha-gb-flag .ha-gb-wrap-{{ID}} .ha-gb-inner, {{WRAPPER}}.ha-gb-bookmark .ha-gb-wrap-{{ID}}'            => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.ha-gb-tri.ha-gb-left .ha-gb-wrap-{{ID}}'                                                              => 'border-top-color:{{VALUE}};',
					'{{WRAPPER}}.ha-gb-flag.ha-gb-right .ha-gb-wrap-{{ID}}:after'                                                      => 'border-left-color: {{VALUE}};',
					'{{WRAPPER}}.ha-gb-flag.ha-gb-left .ha-gb-wrap-{{ID}}:after, {{WRAPPER}}.ha-gb-tri.ha-gb-right .ha-gb-wrap-{{ID}}' => 'border-right-color:{{VALUE}};',
					'{{WRAPPER}}.ha-gb-bookmark .ha-gb-wrap-{{ID}} .ha-gb-inner'            => 'background-color: transparent;',
					'{{WRAPPER}}.ha-gb-bookmark > .ha-gb-wrap-{{ID}}:after'                                                            => 'border-right-color:{{VALUE}}; border-left-color:{{VALUE}};',
				],
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type'     => [ 'bookmark', 'tri', 'flag' ],
				],
				'style_transfer' => true,
			]
		);

		$element->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'           => 'ha_gb_text_shadow',
				'selector'       => '{{WRAPPER}} .ha-gb-wrap-{{ID}}',
				'condition'      => [
					'ha_gb_switcher' => 'yes',
				],
				'style_transfer' => true,
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'           => 'ha_gb_shadow',
				'selector'       => '{{WRAPPER}}:not(.ha-gb-bookmark) .ha-gb-wrap-{{ID}} .ha-gb-inner, {{WRAPPER}}.ha-gb-bookmark .ha-gb-wrap-{{ID}}',
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type!'    => 'tri',
				],
				'style_transfer' => true,
			]
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'ha_gb_typo',
				'selector'       => '{{WRAPPER}}:not(.ha-gb-stripe) .ha-gb-wrap-{{ID}}, {{WRAPPER}}.ha-gb-stripe > .ha-gb-wrap-{{ID}} .ha-gb-inner, {{WRAPPER}}.ha-gb-custom > .ha-gb-wrap-{{ID}} .ha-gb-inner ',
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type!'    => 'bookmark',
				],
				'style_transfer' => true,
			]
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'ha_gb_bookmark_typo',
				'selector'       => '{{WRAPPER}}.ha-gb-bookmark > .ha-gb-wrap-{{ID}}',
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type'     => 'bookmark',
				],
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_notice',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Use <b>Line Height</b> to control height.', 'happy-addons-pro' ),
				'content_classes' => 'papro-upgrade-notice',
				'condition'       => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type'     => 'bookmark',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'ha_gb_border',
				'selector'       => '{{WRAPPER}} .ha-gb-wrap-{{ID}} .ha-gb-inner',
				'condition'      => [
					'ha_gb_switcher'      => 'yes',
					'ha_gb_type!'         => [ 'bookmark', 'tri', 'stripe', 'flag' ],
					'ha_gb_clip_enabled!' => 'yes',
				],
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_border_rad',
			[
				'label'          => __( 'Border Radius', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px', '%', 'em' ],
				'condition'      => [
					'ha_gb_switcher'      => 'yes',
					'ha_gb_adv_radius!'   => 'yes',
					'ha_gb_clip_enabled!' => 'yes',
					'ha_gb_type!'         => [ 'bookmark', 'tri', 'stripe' ],
				],
				'selectors'      => [
					'{{WRAPPER}}:not(.ha-gb-flag) .ha-gb-wrap-{{ID}} .ha-gb-inner'       => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-gb-flag.ha-gb-left .ha-gb-wrap-{{ID}} .ha-gb-inner'  => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
					'{{WRAPPER}}.ha-gb-flag.ha-gb-right .ha-gb-wrap-{{ID}} .ha-gb-inner' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 {{SIZE}}{{UNIT}} ;',
				],
				'style_transfer' => true,
			]
		);

		$element->add_responsive_control(
			'ha_gb_border_rad2',
			[
				'label'          => __( 'Border Radius', 'happy-addons-pro' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => [ 'px', 'em' ],
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_clip_enabled!' => 'yes',
					'ha_gb_type'         => [ 'custom' ],
				],
				'selectors'      => [
					'{{WRAPPER}} .ha-gb-wrap-{{ID}} .ha-gb-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'style_transfer' => true,
			]
		);

		$element->add_responsive_control(
			'ha_gb_padding',
			[
				'label'          => __( 'Padding', 'happy-addons-pro' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => [ 'px', 'em' ],
				'condition'      => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type!'    => [ 'bookmark', 'tri' ],
				],
				'selectors'      => [
					'{{WRAPPER}} .ha-gb-wrap-{{ID}} .ha-gb-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'style_transfer' => true,
			]
		);

		$element->add_responsive_control(
			'ha_gb_padding_bookmark',
			[
				'label'              => __( 'Padding', 'happy-addons-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'size_units'         => [ 'px', 'em' ],
				'condition'          => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_type'     => 'bookmark',
				],
				'selectors'          => [
					'{{WRAPPER}}.ha-gb-bookmark .ha-gb-wrap-{{ID}}' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
				],
				'style_transfer'     => true,
			]
		);
	}

	/**
	 * Add icon style controls.
	 */
	public function add_icon_style( $element ) {

		$element->add_control(
			'ha_gb_icon_color',
			[
				'label'          => __( 'Icon Color', 'happy-addons-pro' ),
				'type'           => Controls_Manager::COLOR,
				'condition'      => [
					'ha_gb_switcher'  => 'yes',
					'ha_gb_icon_type' => 'icon',
				],
				'selectors'      => [
					'{{WRAPPER}} .ha-gb-wrap-{{ID}} .ha-gb-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
				'style_transfer' => true,
			]
		);

		$element->add_responsive_control(
			'ha_gb_icon_size',
			[
				'label'          => __( 'Icon Size', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px', 'em' ],
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition'      => [
					'ha_gb_switcher'   => 'yes',
					'ha_gb_icon_type!' => 'none',
				],
				'selectors'      => [
					'{{WRAPPER}}:not(.ha-gb-bookmark) > .ha-gb-wrap-{{ID}} .ha-gb-icon'                                                                                                         => 'font-size: {{SIZE}}{{UNIT}}; line-height:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.ha-gb-bookmark > .ha-gb-wrap-{{ID}} .ha-gb-icon'                                                                                                               => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} > .ha-gb-wrap-{{ID}} .ha-gb-icon, {{WRAPPER}} > .ha-gb-wrap-{{ID}} .ha-gb-lottie-animation, {{WRAPPER}}:not(.ha-gb-bookmark) > .ha-gb-wrap-{{ID}} .ha-gb-img ' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
				],
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_icon_rad',
			[
				'label'          => __( 'Border Radius', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px', '%', 'em' ],
				'condition'      => [
					'ha_gb_switcher'  => 'yes',
					'ha_gb_icon_type' => 'image',
				],
				'selectors'      => [
					'{{WRAPPER}} .ha-gb-wrap-{{ID}} .ha-gb-img, {{WRAPPER}} .ha-gb-wrap-{{ID}} .ha-gb-lottie-animation svg' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'style_transfer' => true,
			]
		);
	}

	/**
	 * Add floating effects controls.
	 */
	public function add_effects_controls( $element ) {

		$element->add_control(
			'ha_gb_effects',
			[
				'label'     => __( 'Effects', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'ha_gb_switcher' => 'yes',
				],
			]
		);

		$this->translate_effects_controls( $element );
		$this->rotate_effects_controls( $element );
		$this->scale_effects_controls( $element );
		$this->opacity_effects_controls( $element );
		$this->blur_effects_controls( $element );
		$this->grayscale_effects_controls( $element );

		$element->add_control(
			'ha_gb_disable_on_safari',
			[
				'label'        => __( 'Disable Effects On Safari', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'ha-gb-disable-on-safari-',
				'separator'    => 'before',
				'condition'    => [
					'ha_gb_switcher' => 'yes',
					'ha_gb_effects'  => 'yes',
				],
			]
		);
	}

	/**
	 * Add translate effects controls.
	 */
	public function translate_effects_controls( $element ) {

		$float_conditions = [
			'ha_gb_effects'  => 'yes',
			'ha_gb_switcher' => 'yes',
		];

		$element->add_control(
			'ha_gb_efct_translate_toggle',
			[
				'label'              => __( 'Translate', 'happy-addons-pro' ),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array_merge(
					$float_conditions,
					[
						'ha_gb_type!' => 'stripe',
					]
				),
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_gb_efct_translatex',
			[
				'label'          => __( 'Translate X', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'sizes' => [
						'start' => -5,
						'end'   => 5,
					],
					'unit'  => 'px',
				],
				'range'          => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'labels'         => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'         => 1,
				'handles'        => 'range',
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_translate_toggle' => 'yes',
						'ha_gb_type!'                 => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_efct_translatey',
			[
				'label'          => __( 'Translate Y', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'sizes' => [
						'start' => -5,
						'end'   => 5,
					],
					'unit'  => 'px',
				],
				'range'          => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'labels'         => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'         => 1,
				'handles'        => 'range',
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_translate_toggle' => 'yes',
						'ha_gb_type!'                 => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_efct_translate_speed',
			[
				'label'          => __( 'Speed', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'default'        => [
					'size' => 1,
				],
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_translate_toggle' => 'yes',
						'ha_gb_type!'                 => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->end_popover();
	}

	/**
	 * Add rotate effects controls.
	 */
	public function rotate_effects_controls( $element ) {
		$float_conditions = [
			'ha_gb_effects'  => 'yes',
			'ha_gb_switcher' => 'yes',
		];

		$element->add_control(
			'ha_gb_efct_rotate_toggle',
			[
				'label'              => __( 'Rotate', 'happy-addons-pro' ),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array_merge(
					$float_conditions,
					[
						'ha_gb_type!' => 'stripe',
					]
				),
				'style_transfer'     => true,
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_gb_efct_rotatex',
			[
				'label'          => __( 'Rotate X', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'sizes' => [
						'start' => 0,
						'end'   => 45,
					],
					'unit'  => 'deg',
				],
				'range'          => [
					'deg' => [
						'min' => -180,
						'max' => 180,
					],
				],
				'labels'         => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'         => 1,
				'handles'        => 'range',
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_rotate_toggle' => 'yes',
						'ha_gb_type!'              => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_efct_rotatey',
			[
				'label'          => __( 'Rotate Y', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'sizes' => [
						'start' => 0,
						'end'   => 45,
					],
					'unit'  => 'deg',
				],
				'range'          => [
					'deg' => [
						'min' => -180,
						'max' => 180,
					],
				],
				'labels'         => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'         => 1,
				'handles'        => 'range',
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_rotate_toggle' => 'yes',
						'ha_gb_type!'              => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_efct_rotatez',
			[
				'label'          => __( 'Rotate Z', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'sizes' => [
						'start' => 0,
						'end'   => 45,
					],
					'unit'  => 'deg',
				],
				'range'          => [
					'deg' => [
						'min' => -180,
						'max' => 180,
					],
				],
				'labels'         => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'         => 1,
				'handles'        => 'range',
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_rotate_toggle' => 'yes',
						'ha_gb_type!'              => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_efct_rotate_speed',
			[
				'label'          => __( 'Speed', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'default'        => [
					'size' => 1,
				],
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_rotate_toggle' => 'yes',
						'ha_gb_type!'              => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->end_popover();
	}

	/**
	 * Add scale effects controls.
	 */
	public function scale_effects_controls( $element ) {

		$float_conditions = [
			'ha_gb_effects'  => 'yes',
			'ha_gb_switcher' => 'yes',
		];

		$element->add_control(
			'ha_gb_efct_scale_toggle',
			[
				'label'              => __( 'Scale', 'happy-addons-pro' ),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array_merge(
					$float_conditions,
					[
						'ha_gb_type!' => 'stripe',
					]
				),
				'style_transfer'     => true,
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_gb_efct_scalex',
			[
				'label'          => __( 'Scale X', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'sizes' => [
						'from' => 1,
						'to'   => 1.2,
					],
					'unit'  => 'px',
				],
				'range'          => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => .1,
					],
				],
				'labels'         => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'         => 1,
				'handles'        => 'range',
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_scale_toggle' => 'yes',
						'ha_gb_type!'             => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_efct_scaley',
			[
				'label'          => __( 'Scale Y', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'sizes' => [
						'from' => 1,
						'to'   => 1.2,
					],
					'unit'  => 'px',
				],
				'range'          => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => .1,
					],
				],
				'labels'         => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'         => 1,
				'handles'        => 'range',
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_scale_toggle' => 'yes',
						'ha_gb_type!'             => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_efct_scale_speed',
			[
				'label'          => __( 'Speed', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'default'        => [
					'size' => 1,
				],
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_scale_toggle' => 'yes',
						'ha_gb_type!'             => 'stripe',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->end_popover();
	}

	/**
	 * Add opacity effects controls.
	 */
	public function opacity_effects_controls( $element ) {
		$float_conditions = [
			'ha_gb_effects'  => 'yes',
			'ha_gb_switcher' => 'yes',
		];

		$element->add_control(
			'ha_gb_efct_opacity_toggle',
			[
				'label'              => __( 'Opacity', 'happy-addons-pro' ),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => $float_conditions,
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_gb_efct_opacity_value',
			[
				'label'          => __( 'Value', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'sizes' => [
						'start' => 0,
						'end'   => 50,
					],
					'unit'  => '%',
				],
				'labels'         => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'         => 1,
				'handles'        => 'range',
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_opacity_toggle' => 'yes',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->add_control(
			'ha_gb_efct_opacity_speed',
			[
				'label'          => __( 'Speed', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'default'        => [
					'size' => 1,
				],
				'condition'      => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_opacity_toggle' => 'yes',
					]
				),
				'style_transfer' => true,
			]
		);

		$element->end_popover();
	}

	/**
	 * Add blur effects controls.
	 */
	public function blur_effects_controls( $element ) {
		$float_conditions = [
			'ha_gb_effects'  => 'yes',
			'ha_gb_switcher' => 'yes',
		];

		$element->add_control(
			'ha_gb_efct_blur_toggle',
			[
				'label'              => __( 'Blur', 'happy-addons-pro' ),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => $float_conditions,
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_gb_efct_blur_value',
			[
				'label'     => __( 'Value', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'sizes' => [
						'start' => 0,
						'end'   => 1,
					],
					'unit'  => 'px',
				],
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'labels'    => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_blur_toggle' => 'yes',
					]
				),
			]
		);

		$element->add_control(
			'ha_gb_efct_blur_speed',
			[
				'label'     => __( 'Speed', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'default'   => [
					'size' => 1,
				],
				'condition' => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_blur_toggle' => 'yes',
					]
				),
			]
		);

		$element->end_popover();

	}

	/**
	 * Add grayscale effects controls.
	 */
	public function grayscale_effects_controls( $element ) {
		$float_conditions = [
			'ha_gb_effects'  => 'yes',
			'ha_gb_switcher' => 'yes',
		];

		$element->add_control(
			'ha_gb_efct_grayscale_toggle',
			[
				'label'              => __( 'Grayscale', 'happy-addons-pro' ),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => $float_conditions,
			]
		);

		$element->start_popover();

		$element->add_control(
			'ha_gb_efct_gscale_value',
			[
				'label'     => __( 'Value', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'sizes' => [
						'start' => 0,
						'end'   => 50,
					],
					'unit'  => '%',
				],
				'labels'    => [
					__( 'From', 'happy-addons-pro' ),
					__( 'To', 'happy-addons-pro' ),
				],
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_grayscale_toggle' => 'yes',
					]
				),
			]
		);

		$element->add_control(
			'ha_gb_efct_gscale_speed',
			[
				'label'     => __( 'Speed', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'default'   => [
					'size' => 1,
				],
				'condition' => array_merge(
					$float_conditions,
					[
						'ha_gb_efct_grayscale_toggle' => 'yes',
					]
				),
			]
		);

		$element->end_popover();

	}

	/**
	 * Render Global badge output in the editor.
	 */
	public function print_template( $template, $element ) {

		if ( ! $template && 'widget' === $element->get_type() ) {
			return;
		}

		$old_template = $template;
		ob_start();
		?>
<#
   var isEnabled='yes'===settings.ha_gb_switcher ? true : false;

   if ( isEnabled ) {

   var text=settings.ha_gb_text,
   iconEnabled='none' !=settings.ha_gb_icon_type ? true : false,
   //svgEnabled='yes'===settings.ha_gb_svg_enabled ? true : false,
   floatingEnabled='yes'===settings.ha_gb_effects ? true : false,
   badgeSettings={
   text : text,
   };

   if ( iconEnabled ) {
   var type=settings.ha_gb_icon_type,
   icon={};

   badgeSettings.iconType=type;

   switch( type ) {
   case 'icon' :
   icon=settings.ha_gb_icon;
   break;

   case 'image' :
   icon.url=settings.ha_gb_img.url;
   break;

   case 'lottie' :
   icon.url=settings.ha_gb_lottie_url;
   icon.loop=settings.ha_gb_loop;
   icon.reverse=settings.ha_gb_reverse;
   break;

   default:
   icon=false;
   }
   } else {
   icon=false;
   }

   badgeSettings.icon=icon;

   if ( floatingEnabled ) {
   var floatingSettings={},
   filtersEnabled='yes'===settings.ha_gb_efct_blur_toggle || 'yes'===settings.ha_gb_efct_grayscale_toggle ? true : false;

   if ( 'yes'===settings.ha_gb_efct_translate_toggle ) {

   var translateSettings={
   x_param_from: settings.ha_gb_efct_translatex.sizes.start,
   x_param_to: settings.ha_gb_efct_translatex.sizes.end,
   y_param_from: settings.ha_gb_efct_translatey.sizes.start,
   y_param_to: settings.ha_gb_efct_translatey.sizes.end,
   speed: settings.ha_gb_efct_translate_speed.size * 1000,
   };

   floatingSettings.translate=translateSettings;
   }

   if ( 'yes'===settings.ha_gb_efct_rotate_toggle ) {

   var rotateSettings={
   x_param_from: settings.ha_gb_efct_rotatex.sizes.start,
   x_param_to: settings.ha_gb_efct_rotatex.sizes.end,
   y_param_from: settings.ha_gb_efct_rotatey.sizes.start,
   y_param_to: settings.ha_gb_efct_rotatey.sizes.end,
   z_param_from: settings.ha_gb_efct_rotatez.sizes.start,
   z_param_to: settings.ha_gb_efct_rotatez.sizes.end,
   speed: settings.ha_gb_efct_rotate_speed.size * 1000,
   };

   floatingSettings.rotate=rotateSettings;
   }

   if ( 'yes'===settings.ha_gb_efct_opacity_toggle ) {

   var opacitySettings={
   from: settings.ha_gb_efct_opacity_value.sizes.start / 100,
   to: settings.ha_gb_efct_opacity_value.sizes.end / 100,
   speed: settings.ha_gb_efct_opacity_speed.size * 1000,
   };

   floatingSettings.opacity=opacitySettings;
   }

   if ( filtersEnabled ) {
   var filtersSettings={};

   if ( 'yes'===settings.ha_gb_efct_blur_toggle ) {

   var blurSettings={
   from: 'blur(' + settings.ha_gb_efct_blur_value.sizes.start + 'px)' ,
   to: 'blur(' + settings.ha_gb_efct_blur_value.sizes.end + 'px)' ,
   speed: settings.ha_gb_efct_blur_speed.size * 1000,
   };

   filtersSettings.blur=blurSettings;
   }

   if ( 'yes'===settings.ha_gb_efct_grayscale_toggle ) {
   var gscaleSettings={
   from: 'grayscale(' + settings.ha_gb_efct_gscale_value.sizes.start + '%)' ,
   to: 'grayscale(' + settings.ha_gb_efct_gscale_value.sizes.end + '%)' ,
   speed: settings.ha_gb_efct_gscale_speed.size,
   };

   filtersSettings.gscale=gscaleSettings;
   }

   floatingSettings.filters=filtersSettings;
   }

   badgeSettings.floating=floatingSettings;
   }

   view.addRenderAttribute( 'badge_data' , { 'id' : 'ha-gbadge-data-wrap-' + view.getID(), 'class' : 'ha-gb-wrap-wrapper ' + view.container.type, 'data-gbadge' : JSON.stringify( badgeSettings )
   });
   #>
	<div {{{ view.getRenderAttributeString( 'badge_data' ) }}}></div>
	<#
	   }
	   #>

		<?php

		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;

		return $template;
	}

	/**
	 * Render Global badge output on the frontend.
	 */
	public function before_render( $element ) {

		$element_type = $element->get_type();

		$id = $element->get_id();

		$settings = $element->get_settings_for_display();

		$badge_switcher = $settings['ha_gb_switcher'] ?? 'no';

		if ( 'yes' === $badge_switcher ) {

			$text = esc_html( $settings['ha_gb_text'] );
			$floating_enabled = 'yes' === $settings['ha_gb_effects'] ? true : false;
			$badge_settings   = [
				'text' => $text,
			];

			if ( 'none' != $settings['ha_gb_icon_type'] ) {
				$type                       = $settings['ha_gb_icon_type'];
				$badge_settings['iconType'] = $type;

				switch ( $type ) {
					case 'icon':
						$icon = $settings['ha_gb_icon'];
						break;

					case 'image':
						$icon['url'] = $settings['ha_gb_img']['url'];
						$icon['alt'] = Control_Media::get_image_alt( $settings['ha_gb_img'] );
						break;

					case 'lottie':
						$icon['url']  = esc_url( $settings['ha_gb_lottie_url'] );
						$icon['loop'] = $settings['ha_gb_loop'];
						break;

					default:
						$icon = false;
						break;
				}
			} else {
				$icon = false;
			}

			$badge_settings['icon'] = $icon;

			if ( $floating_enabled ) {
				$floating_settings = [];
				$filters_enabled = 'yes' === $settings['ha_gb_efct_blur_toggle'] || 'yes' === $settings['ha_gb_efct_grayscale_toggle'] ? true : false;

				if ( 'yes' === $settings['ha_gb_efct_translate_toggle'] ) {

					$translate_settings = [
						'x_param_from' => $settings['ha_gb_efct_translatex']['sizes']['start'],
						'x_param_to'   => $settings['ha_gb_efct_translatex']['sizes']['end'],
						'y_param_from' => $settings['ha_gb_efct_translatey']['sizes']['start'],
						'y_param_to'   => $settings['ha_gb_efct_translatey']['sizes']['end'],
						'speed'        => $settings['ha_gb_efct_translate_speed']['size'] * 1000,
					];

					$floating_settings['translate'] = $translate_settings;
				}

				if ( 'yes' === $settings['ha_gb_efct_scale_toggle'] ) {

					$translate_settings = [
						'x_param_from' => $settings['ha_gb_efct_scalex']['sizes']['from'],
						'x_param_to'   => $settings['ha_gb_efct_scalex']['sizes']['to'],
						'y_param_from' => $settings['ha_gb_efct_scaley']['sizes']['from'],
						'y_param_to'   => $settings['ha_gb_efct_scaley']['sizes']['to'],
						'speed'        => $settings['ha_gb_efct_scale_speed']['size'] * 1000,
					];

					$floating_settings['scale'] = $translate_settings;
				}

				if ( 'yes' === $settings['ha_gb_efct_rotate_toggle'] ) {

					$rotate_settings = [
						'x_param_from' => $settings['ha_gb_efct_rotatex']['sizes']['start'],
						'x_param_to'   => $settings['ha_gb_efct_rotatex']['sizes']['end'],
						'y_param_from' => $settings['ha_gb_efct_rotatey']['sizes']['start'],
						'y_param_to'   => $settings['ha_gb_efct_rotatey']['sizes']['end'],
						'z_param_from' => $settings['ha_gb_efct_rotatez']['sizes']['start'],
						'z_param_to'   => $settings['ha_gb_efct_rotatez']['sizes']['end'],
						'speed'        => $settings['ha_gb_efct_rotate_speed']['size'] * 1000,
					];

					$floating_settings['rotate'] = $rotate_settings;
				}

				if ( 'yes' === $settings['ha_gb_efct_opacity_toggle'] ) {

					$opacity_settings = [
						'from'  => $settings['ha_gb_efct_opacity_value']['sizes']['start'] / 100,
						'to'    => $settings['ha_gb_efct_opacity_value']['sizes']['end'] / 100,
						'speed' => $settings['ha_gb_efct_opacity_speed']['size'] * 1000,
					];

					$floating_settings['opacity'] = $opacity_settings;
				}

				if ( $filters_enabled ) {
					$filters_settings = [];
					if ( 'yes' === $settings['ha_gb_efct_blur_toggle'] ) {

						$blur_settings = [
							'from'  => 'blur(' . $settings['ha_gb_efct_blur_value']['sizes']['start'] . 'px)',
							'to'    => 'blur(' . $settings['ha_gb_efct_blur_value']['sizes']['end'] . 'px)',
							'speed' => $settings['ha_gb_efct_blur_speed']['size'] * 1000,
						];

						$filters_settings['blur'] = $blur_settings;
					}

					if ( 'yes' === $settings['ha_gb_efct_grayscale_toggle'] ) {
						$gscale_settings = [
							'from'  => 'grayscale(' . $settings['ha_gb_efct_gscale_value']['sizes']['start'] . '%)',
							'to'    => 'grayscale(' . $settings['ha_gb_efct_gscale_value']['sizes']['end'] . '%)',
							'speed' => $settings['ha_gb_efct_gscale_speed']['size'],
						];

						$filters_settings['gscale'] = $gscale_settings;
					}

					$floating_settings['filters'] = $filters_settings;
				}

				$badge_settings['floating'] = $floating_settings;
			}

			$element->add_render_attribute( '_wrapper', 'data-gbadge', wp_json_encode( $badge_settings ) );

			if ( 'widget' === $element_type && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				?>
		<!-- ha-gb-wrap-temp- -->
		<div id='ha-gbadge-data-wrap-temp-<?php echo esc_html( $id ); ?>' data-gbadge='<?php echo wp_json_encode( $badge_settings ); ?>'></div>
		<?php
			}
		}
	}
}
Global_Badge::instance()->init();