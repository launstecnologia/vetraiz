<?php

/**
 * Advanced_Comparison_Table widget class
 *
 * @package Happy_Addons_Pro
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined( 'ABSPATH' ) || die();

class Advanced_Comparison_Table extends Base {
	/**
	 * Get widget title.
	 *
	 * @access public
	 *
	 * @since 2.24.2
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Advanced Comparison Table', 'happy-addons-pro' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/advanced-comparison-table/';
	}

	/**
	 * Get widget icon.
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-link-box';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @access public
	 *
	 * @since 1.0.10
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['advanced comparison table','comparison table', 'table', 'comparison'];
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {
		$this->_section_general();
		$this->_section_columns();
	}

	protected function _section_general() {

		$this->start_controls_section(
			'section_general',
			[
				'label' => __( 'General', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'column_count',
			[
				'label'       => __( 'Column Number', 'happy-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Minimum column item 2 and maximum column item 10', 'happy-addons-pro' ),
				'default'     => 3,
				'min'         => 2,
				'max'         => 10,
			]
		);

		$this->add_control(
			'heading_icon_position',
			[
				'label'   => __( 'Heading Icon Position', 'happy-addons-pro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'  => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default' => 'left',
				'toggle'  => true,
				'prefix_class' => 'ha-adct-heading-icon-position-',
				'selectors'    => [
					'{{WRAPPER}} .ha-adct-heading i' => '{{VALUE}}',
					'{{WRAPPER}} .ha-adct-heading svg' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left' => 'order: -1',
					'right' => 'order: 0',
				],
			]
		);

		$this->add_control(
			'collapse_close_icon',
			[
				'label'       => __( 'Collapse Icon', 'happy-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => true,
				'skin' => 'inline',
				'exclude_inline_options' => [ 'svg' ],
				'default'     => [
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'collapse_open_icon',
			[
				'label'       => __( 'Expand Icon', 'happy-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => true,
				'skin' => 'inline',
				'exclude_inline_options' => [ 'svg' ],
				'default'     => [
					'value'   => 'fas fa-minus',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'child_expand',
			[
				'type'         => Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Child item expand?', 'happy-addons-pro' ),
				'description' => __( 'Child item expand on 1st load.', 'happy-addons-pro' ),
				'label_on'     => esc_html__( 'Yes', 'happy-addons-pro' ),
				'label_off'    => esc_html__( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'nav_badge',
			[
				'type'         => Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Responsive Nav Badge?', 'happy-addons-pro' ),
				'label_on'     => esc_html__( 'Yes', 'happy-addons-pro' ),
				'label_off'    => esc_html__( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'sticky_header',
			[
				'type'         => Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Sticky Header?', 'happy-addons-pro' ),
				'label_on'     => esc_html__( 'Yes', 'happy-addons-pro' ),
				'label_off'    => esc_html__( 'No', 'happy-addons-pro' ),
				'prefix_class' => 'ha-adct-sticky-header-',
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();
	}

	protected function repeater_fields() {

		$repeater = new Repeater();

		$repeater->add_control(
			'row_type',
			[
				'label'   => __( 'Content Position', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'parent',
				'options' => [
					'parent'    => __( 'Parent', 'happy-addons-pro' ),
					'child' => __( 'Child', 'happy-addons-pro' ),
				],
			]
		);

		$repeater->add_control(
			'content_type',
			[
				'label'       => __( 'Content Type', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'toggle'      => false,
				'default'     => 'text',
				'options'     => [
					'blank' => [
						'title' => __( 'Blank', 'happy-addons-pro' ),
						'icon'  => 'eicon-editor-close',
					],
					'heading'  => [
						'title' => __( 'Heading', 'happy-addons-pro' ),
						'icon'  => 'eicon-heading',
					],
					'text'  => [
						'title' => __( 'Text', 'happy-addons-pro' ),
						'icon'  => 'eicon-t-letter-bold',
					],
					'icon'  => [
						'title' => __( 'Icon', 'happy-addons-pro' ),
						'icon'  => 'eicon-info-circle',
					],
					'image' => [
						'title' => __( 'Image', 'happy-addons-pro' ),
						'icon'  => 'eicon-image',
					],
					'button' => [
						'title' => __( 'Button', 'happy-addons-pro' ),
						'icon'  => 'eicon-editor-bold',
					],
					'price' => [
						'title' => __( 'Price', 'happy-addons-pro' ),
						'icon'  => 'fas fa-dollar-sign',
					],
				],
			]
		);

		$repeater->add_control(
			'title_tag',
			[
				'label'   => __( 'Title HTML Tag', 'happy-addons-pro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'h1' => [
						'title' => __( 'H1', 'happy-addons-pro' ),
						'icon'  => 'eicon-editor-h1',
					],
					'h2' => [
						'title' => __( 'H2', 'happy-addons-pro' ),
						'icon'  => 'eicon-editor-h2',
					],
					'h3' => [
						'title' => __( 'H3', 'happy-addons-pro' ),
						'icon'  => 'eicon-editor-h3',
					],
					'h4' => [
						'title' => __( 'H4', 'happy-addons-pro' ),
						'icon'  => 'eicon-editor-h4',
					],
					'h5' => [
						'title' => __( 'H5', 'happy-addons-pro' ),
						'icon'  => 'eicon-editor-h5',
					],
					'h6' => [
						'title' => __( 'H6', 'happy-addons-pro' ),
						'icon'  => 'eicon-editor-h6',
					],
				],
				'default' => 'h5',
				'toggle'  => false,
				'condition'   => [
					'content_type' => 'heading',
				],
			]
		);

		$repeater->add_control(
			'heading_icon',
			[
				'label'       => __( 'Heading Icon', 'happy-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => true,
				'skin' => 'inline',
				'exclude_inline_options' => [ 'svg' ],
				'default'     => [
					'value'   => 'fas fa-clipboard-list',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'content_type' => 'heading',
				],
			]
		);

		$repeater->add_control(
			'column_text',
			[
				'label'       => __( 'Title', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Title', 'happy-addons-pro' ),
				'default' => __( 'Title', 'happy-addons-pro' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'content_type' => ['text','heading'],
				],
			]
		);

		$repeater->add_control(
			'column_image',
			[
				'label'       => __( 'Image', 'happy-addons-pro' ),
				'type'        => Controls_Manager::MEDIA,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'   => [
					'content_type' => 'image',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'exclude'   => [ 'custom' ],
				'include'   => [],
				'default'   => 'thumbnail',
				'condition' => [
					'content_type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'column_icon',
			[
				'label'       => __( 'Icon', 'happy-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => true,
				'default'     => [
					'value'   => 'fas fa-check-circle',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'content_type' => 'icon',
				],
			]
		);

		$repeater->add_control(
			'desc_or_tooltip',
			[
				'label'   => __( 'Description/Tooltip', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''    => __( 'None', 'happy-addons-pro' ),
					'description'    => __( 'Description', 'happy-addons-pro' ),
					'tooltip'    => __( 'Tooltip', 'happy-addons-pro' ),
				],
				'condition'   => [
					'content_type' => ['heading','text'],
				],
			]
		);

		// Button Content Start
		$repeater->add_control(
			'btn_text',
			[
				'label'       => __( 'Button Title', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Buy Now', 'happy-addons-pro' ),
				'placeholder' => __( 'Type your title here', 'happy-addons-pro' ),
				'condition'   => [
					'content_type'    => 'button',
				],
			]
		);

		$repeater->add_control(
			'btn_link',
			[
				'label'         => __( 'Link', 'happy-addons-pro' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'happy-addons-pro' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => true,
					'nofollow'    => true,
				],
				'dynamic' => [
					'active' => true,
				],
				'condition'   => [
					'content_type'    => 'button',
				],
			]
		);

		// Price Content Start
		$repeater->add_control(
			'currency',
			[
				'label' => __( 'Currency', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					''             => __( 'None', 'happy-addons-pro' ),
					'baht'         => '&#3647; ' . _x( 'Baht', 'Currency Symbol', 'happy-addons-pro' ),
					'bdt'          => '&#2547; ' . _x( 'BD Taka', 'Currency Symbol', 'happy-addons-pro' ),
					'dollar'       => '&#36; ' . _x( 'Dollar', 'Currency Symbol', 'happy-addons-pro' ),
					'euro'         => '&#128; ' . _x( 'Euro', 'Currency Symbol', 'happy-addons-pro' ),
					'franc'        => '&#8355; ' . _x( 'Franc', 'Currency Symbol', 'happy-addons-pro' ),
					'guilder'      => '&fnof; ' . _x( 'Guilder', 'Currency Symbol', 'happy-addons-pro' ),
					'krona'        => 'kr ' . _x( 'Krona', 'Currency Symbol', 'happy-addons-pro' ),
					'lira'         => '&#8356; ' . _x( 'Lira', 'Currency Symbol', 'happy-addons-pro' ),
					'peseta'       => '&#8359 ' . _x( 'Peseta', 'Currency Symbol', 'happy-addons-pro' ),
					'peso'         => '&#8369; ' . _x( 'Peso', 'Currency Symbol', 'happy-addons-pro' ),
					'pound'        => '&#163; ' . _x( 'Pound Sterling', 'Currency Symbol', 'happy-addons-pro' ),
					'real'         => 'R$ ' . _x( 'Real', 'Currency Symbol', 'happy-addons-pro' ),
					'ruble'        => '&#8381; ' . _x( 'Ruble', 'Currency Symbol', 'happy-addons-pro' ),
					'rupee'        => '&#8360; ' . _x( 'Rupee', 'Currency Symbol', 'happy-addons-pro' ),
					'indian_rupee' => '&#8377; ' . _x( 'Rupee (Indian)', 'Currency Symbol', 'happy-addons-pro' ),
					'shekel'       => '&#8362; ' . _x( 'Shekel', 'Currency Symbol', 'happy-addons-pro' ),
					'won'          => '&#8361; ' . _x( 'Won', 'Currency Symbol', 'happy-addons-pro' ),
					'yen'          => '&#165; ' . _x( 'Yen/Yuan', 'Currency Symbol', 'happy-addons-pro' ),
					'custom'       => __( 'Custom', 'happy-addons-pro' ),
				],
				'default' => 'dollar',
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'content_type' => 'price',
				],
			]
		);

		$repeater->add_control(
			'currency_custom',
			[
				'label' => __( 'Custom Symbol', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'content_type' => 'price',
					'currency' => 'custom',
				],
			]
		);

		$repeater->add_control(
			'price',
			[
				'label'       => __( 'Price', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( '39.99', 'happy-addons-pro' ),
				'placeholder' => __( '39.99', 'happy-addons-pro' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'content_type' => 'price',
				],
			]
		);

		$repeater->add_control(
			'original_price',
			[
				'label'       => __( 'Original Price', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( '49.99', 'happy-addons-pro' ),
				'default'     => __( '49.99', 'happy-addons-pro' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'content_type' => 'price',
				],
			]
		);

		$repeater->add_control(
			'currency_side',
			[
				'label' => __( 'Currency Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => __( 'Left', 'happy-addons-pro' ),
					'right'  => __( 'Right', 'happy-addons-pro' ),
				],
				'condition'   => [
					'content_type' => 'price',
				],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'period',
			[
				'label'       => __( 'Timespan', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( '/Per Year', 'happy-addons-pro' ),
				'placeholder' => __( 'Per Year', 'happy-addons-pro' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'content_type' => 'price',
				],
			]
		);
		// Price Content End

		$repeater->add_responsive_control(
			'single_item_icon_size',
			[
				'label' => __('Icon Size', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 250,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-heading i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-heading svg' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}}.ha-adct-column-content-type-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}}.ha-adct-column-content-type-icon svg' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'content_type'    => ['heading','icon'],
				],
			]
		);

		$repeater->add_control(
			'single_item_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-heading' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-heading i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-heading svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}}.ha-adct-column-content-type-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}}.ha-adct-column-content-type-icon svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-btn' => 'color: {{VALUE}}',
				],
				'condition' => [
					'content_type'    => ['heading','text','icon','button'],
				],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'single_item_bg',
			[
				'label' => __('Background', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-heading' => 'background: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-btn' => 'background: {{VALUE}}',
				],
				'condition' => [
					'content_type'    => ['heading','button'],
				],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'button_text_color_hover',
			[
				'label'     => __( 'Color (Hover)', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-btn:hover' => 'color: {{VALUE}}',
				],
				'style_transfer' => true,
				'condition' => [
					'content_type'    => 'button',
				],
			]
		);

		$repeater->add_control(
			'button_bg_color_hover',
			[
				'label'     => __( 'Background (Hover)', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-btn:hover' => 'background: {{VALUE}}',
				],
				'style_transfer' => true,
				'condition' => [
					'content_type'    => 'button',
				],
			]
		);

		//Tooltip
		$repeater->add_control(
			'tooltip',
			[
				'label'       => __( 'Tooltip', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'placeholder' => __( 'Tooltip', 'happy-addons-pro' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'content_type' => ['heading','text'],
					'desc_or_tooltip'    => 'tooltip',
				],
			]
		);

		//Description
		$repeater->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'happy-addons-pro' ),
				'type'        => Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Type your description here', 'happy-addons-pro' ),
				'condition'   => [
					'content_type' => ['heading','text'],
					'desc_or_tooltip'    => 'description',
				],
			]
		);

		//Description bottom padding
		$repeater->add_control(
			'description_bottom_space',
			[
				'label'     => __( 'Description Bottom Space', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}} .ha-adct-description' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'content_type' => ['heading','text'],
					'desc_or_tooltip'    => 'description',
				],
			]
		);

		//cell background
		$repeater->add_control(
			'cell_background',
			[
				'label'     => __( 'Cell Background', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}}.ha-adct-column' => 'background: {{VALUE}}',
				],
				'style_transfer' => true,
			]
		);

		//cell border
		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'cell_border',
				'selector' => '{{WRAPPER}} .ha-adct-table {{CURRENT_ITEM}}.ha-adct-column',
			]
		);

		return $repeater;

	}

	protected function _section_columns() {

		$repeater = $this->repeater_fields();

		for ( $i = 1; $i < 11; $i++ ) {

			$this->start_controls_section(
				'_section_column_' . $i,
				[
					'label'     => sprintf( esc_html__( 'Column %s', 'happy-addons-pro' ), $i ),
					'operator'  => '>',
					'condition' => [
						'column_count' => $this->condition_value_add( $i ),
					],
				]
			);

			if ( 1 != $i ) {

				$this->add_control(
					'show_column_' . $i,
					[
						'label'        => __( 'Show/Hide Column?', 'happy-addons-pro' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Show', 'happy-addons-pro' ),
						'label_off' => esc_html__( 'Hide', 'happy-addons-pro' ),
						'default'      => 'yes',
						'return_value' => 'yes',
						'render_type'  => 'template',
						'style_transfer' => true,
					]
				);
			}

			if ( 1 == $i ) {

				$this->add_responsive_control(
					'column_width_' . $i,
					[
						'label'          => __( 'Column Width', 'happy-addons-pro' ),
						'type'           => Controls_Manager::SLIDER,
						'size_units'     => ['%'],
						'range'          => [
							'%'  => [
								'min' => 0,
								'max' => 100,
							],
						],
						'devices' => [ 'desktop', 'tablet', 'mobile' ],
						'desktop_default' => [
							'unit' => '%',
							'size' => '',
						],
						'tablet_default' => [
							'unit' => '%',
							'size' => '',
						],
						'mobile_default' => [
							'unit' => '%',
							'size' => '',
						],
						'selectors'      => [
							'{{WRAPPER}} .ha-adct-table ' => '--ha-adct-1st-column: {{SIZE}}%;',
							// '{{WRAPPER}} .ha-adct-table ' => '--ha-adct-1st-column: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'column_data_' . $i,
					[
						'type'         => Controls_Manager::REPEATER,
						'fields'       => $repeater->get_controls(),
						'item_actions' => [
							'sort' => true,
						],
						'default'      => $this->default_value('column_1'),
						'title_field'  => '{{{ content_type == "heading" || content_type == "text" ?  column_text.replace(/(<([^>]+)>)/ig,"").replace(/\r\n|\r|\n/g,"<br />") : content_type.charAt(0).toUpperCase() +  content_type.slice(1) }}}',
					]
				);

				$this->add_control(
					'column_align_' . $i,
					[
						'label'     => __( 'Alignment', 'happy-addons-pro' ),
						'type'      => Controls_Manager::CHOOSE,
						'separator' => 'before',
						'options'   => [
							'flex-start'   => [
								'title' => __( 'Left', 'happy-addons-pro' ),
								'icon'  => 'eicon-text-align-left',
							],
							'center' => [
								'title' => __( 'Center', 'happy-addons-pro' ),
								'icon'  => 'eicon-text-align-center',
							],
							'flex-end'  => [
								'title' => __( 'Right', 'happy-addons-pro' ),
								'icon'  => 'eicon-text-align-right',
							],
						],
						'default'   => (1 == $i) ? 'flex-start' : 'center',
						'toggle'    => true,
						'selectors_dictionary' => [
							'flex-start' => 'justify-content: flex-start; text-align: left;',
							'center' => 'justify-content: center; text-align: center;',
							'flex-end' => 'justify-content: flex-end; text-align: right;',
						],
						'selectors'    => [
							'{{WRAPPER}} .ha-adct-table .ha-adct-column-'.$i => '{{VALUE}}',
							'{{WRAPPER}} .ha-adct-table .ha-adct-column-'.$i.' .ha-adct-heading' => '{{VALUE}}',
						],
						'render_type' => 'template',
					]
				);

			} else {

				$this->add_control(
					'column_data_' . $i,
					[
						'type'         => Controls_Manager::REPEATER,
						'fields'       => $repeater->get_controls(),
						'item_actions' => [
							'sort' => true,
						],
						'default'      => ($i <= 3) ? $this->default_value('column_' . $i) : $this->default_value('column_others'),
						'title_field'  => '{{{ content_type == "heading" || content_type == "text" ?  column_text.replace(/(<([^>]+)>)/ig,"").replace(/\r\n|\r|\n/g,"<br />") : content_type.charAt(0).toUpperCase() +  content_type.slice(1) }}}',
						'condition'   => [
							'show_column_' . $i   => 'yes',
						],
					]
				);

				$this->add_control(
					'column_align_' . $i,
					[
						'label'     => __( 'Alignment', 'happy-addons-pro' ),
						'type'      => Controls_Manager::CHOOSE,
						'separator' => 'before',
						'options'   => [
							'flex-start'   => [
								'title' => __( 'Left', 'happy-addons-pro' ),
								'icon'  => 'eicon-text-align-left',
							],
							'center' => [
								'title' => __( 'Center', 'happy-addons-pro' ),
								'icon'  => 'eicon-text-align-center',
							],
							'flex-end'  => [
								'title' => __( 'Right', 'happy-addons-pro' ),
								'icon'  => 'eicon-text-align-right',
							],
						],
						'default'   => (1 == $i) ? 'flex-start' : 'center',
						'toggle'    => true,
						'selectors_dictionary' => [
							'flex-start' => 'justify-content: flex-start; text-align: left;',
							'center' => 'justify-content: center; text-align: center;',
							'flex-end' => 'justify-content: flex-end; text-align: right;',
						],
						'selectors'    => [
							'{{WRAPPER}} .ha-adct-table .ha-adct-column-'.$i => '{{VALUE}}',
						],
						'render_type' => 'template',
						'condition'   => [
							'show_column_' . $i   => 'yes',
						],
					]
				);
			}

			// Custom Style Start
			if ( 1 != $i ) {

				$this->add_control(
					'badge_text_' . $i,
					[
						'label'       => __( 'Badge Text', 'happy-addons-pro' ),
						'type'        => Controls_Manager::TEXT,
						'label_block' => true,
						'placeholder' => __( 'Badge Text', 'happy-addons-pro' ),
						'dynamic'     => [
							'active' => true,
						],
						'condition'   => [
							'show_column_' . $i   => 'yes',
						],
					]
				);

				$this->add_control(
					'enable_badge_custom_style_' . $i,
					[
						'label'        => __( 'Badge Custom Style?', 'happy-addons-pro' ),
						'type'         => Controls_Manager::SWITCHER,
						'default'      => '',
						'return_value' => 'yes',
						'render_type'  => 'ui',
						'condition'   => [
							'show_column_' . $i   => 'yes',
							'badge_text_' . $i .'!'    => '',
						],
						'style_transfer' => true,
					]
				);

				$this->add_control(
					'badge_color_' . $i,
					[
						'label' => __( 'Text Color', 'happy-addons-pro' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ha-adct-column-'.$i.' .ha-adct-badge' => 'color: {{VALUE}};',
						],
						'condition'   => [
							'show_column_' . $i   => 'yes',
							'enable_badge_custom_style_' . $i => 'yes',
						],
						'style_transfer' => true,
					]
				);

				$this->add_control(
					'badge_bg_color_' . $i,
					[
						'label' => __( 'Background Color', 'happy-addons-pro' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ha-adct-column-'.$i.' .ha-adct-badge' => 'background-color: {{VALUE}};',
						],
						'condition'   => [
							'show_column_' . $i   => 'yes',
							'enable_badge_custom_style_' . $i => 'yes',
						],
						'style_transfer' => true,
					]
				);

			}

			$this->end_controls_section();
		}

	}

	protected function default_value( $number ) {

		$value = [
			'column_1' => [
				[
					'row_type'     => 'parent',
					'content_type'     => 'blank',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'heading',
					'column_text'         => __( 'Price', 'happy-addons-pro' ),
					'heading_icon'     => [
						'value'   => '',
						'library' => '',
					],
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'text',
					'column_text'         => __( 'All Feature List', 'happy-addons-pro' ),
				],
				[
					'row_type'     => 'child',
					'column_text'         => __( 'Cross Domain Copy Paste', 'happy-addons-pro' ),
					'single_item_color'     => '#6F6F6FFC',
				],
				[
					'row_type'     => 'child',
					'column_text'         => __( 'On Demand Asset Loading', 'happy-addons-pro' ),
					'single_item_color'     => '#6F6F6FFC',
				],
				[
					'row_type'     => 'parent',
					'column_text'         => __( 'Display Condition', 'happy-addons-pro' ),
					'desc_or_tooltip'         => 'tooltip',
					'tooltip' => __( 'Tooltip', 'happy-addons-pro' ),
				],
				[
					'row_type'     => 'parent',
					'column_text'         => __( 'Unlimited Section Nesting', 'happy-addons-pro' ),
					'desc_or_tooltip'         => 'description',
					'description' => __( 'You wished, we heard. Now you can create designs which were previously impossible with stock elementor sections and columns by creating unlimited nested sections.', 'happy-addons-pro' ),
				],
				[
					'row_type'     => 'parent',
					'column_text'         => __( 'Theme Builder', 'happy-addons-pro' ),
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'blank',
				],
			],
			'column_2' => [
				[
					'row_type'     => 'parent',
					'content_type'     => 'heading',
					'column_text'      => __( 'Starter', 'happy-addons-pro' ),
					'heading_icon'     => [
						'value'   => 'hm hm-anchor',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#FFFFFF',
					'single_item_bg'     => '#FF649F',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'price',
					'currency'         => 'dollar',
					'price'         =>  __( '39.99', 'happy-addons-pro' ),
					'original_price'         =>  '',
					'currency_side'         =>  'left',
					'period'         =>  '',
				],
				[
					'row_type'     => 'parent',
					'column_text'         => __( 'No', 'happy-addons-pro' ),
				],
				[
					'row_type'     => 'child',
					'content_type'     => 'icon',
					'column_icon'     => [
						'value'   => 'hm hm-cross-circle',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#FF2323',
				],
				[
					'row_type'     => 'child',
					'content_type'     => 'icon',
					'column_icon'     => [
						'value'   => 'hm hm-cross-circle',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#FF2323',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'icon',
					'column_icon'     => [
						'value'   => 'hm hm-tick-circle',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#FF2323',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'blank',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'blank',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'button',
					'btn_text'     => __( 'Buy Now', 'happy-addons-pro' ),
					'btn_link'     => [
						'url'         => '#',
						'is_external' => true,
						'nofollow'    => true,
					],
					'single_item_color'     => '#FFFFFF',
					'single_item_bg'     => '#29B4E2',
					'button_bg_color_hover'     => '#000000',
				],
			],
			'column_3' => [
				[
					'row_type'     => 'parent',
					'content_type'     => 'heading',
					'column_text'      => __( 'Professional', 'happy-addons-pro' ),
					'heading_icon'     => [
						'value'   => 'hm hm-badge1',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#FFFFFF',
					'single_item_bg'     => '#4C8FF9',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'price',
					'currency'         => 'dollar',
					'price'         =>  __( '89.99', 'happy-addons-pro' ),
					'original_price'         =>  '',
					'currency_side'         =>  'left',
					'period'         =>  '',
				],
				[
					'row_type'     => 'parent',
					'column_text'         => __( 'Yes', 'happy-addons-pro' ),
				],
				[
					'row_type'     => 'child',
					'content_type'     => 'icon',
					'column_icon'     => [
						'value'   => 'hm hm-tick-circle',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#29B4E2',
				],
				[
					'row_type'     => 'child',
					'content_type'     => 'icon',
					'column_icon'     => [
						'value'   => 'hm hm-tick-circle',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#29B4E2',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'icon',
					'column_icon'     => [
						'value'   => 'hm hm-tick-circle',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#29B4E2',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'icon',
					'column_icon'     => [
						'value'   => 'hm hm-tick-circle',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#29B4E2',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'icon',
					'column_icon'     => [
						'value'   => 'hm hm-tick-circle',
						'library' => 'happy-icons',
					],
					'single_item_color'     => '#29B4E2',
				],
				[
					'row_type'     => 'parent',
					'content_type'     => 'button',
					'btn_text'     => __( 'Buy Now', 'happy-addons-pro' ),
					'btn_link'     => [
						'url'         => '#',
						'is_external' => true,
						'nofollow'    => true,
					],
					'single_item_color'     => '#FFFFFF',
					'single_item_bg'     => '#4BAC6E',
					'button_bg_color_hover'     => '#F37676',
				],
			],
			'column_others' => [
				[
					'row_type'     => 'parent',
					'content_type'     => 'text',
					'column_text'         => __( 'Title', 'happy-addons-pro' ),
				],
			],
		];

		return $value[$number];
	}

	protected function condition_value_add( $j ) {
		$value = [];
		for ( $i = $j; $i < 11; $i++ ) {
			$value[] = $i;
		}

		return $value;
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->__table_column_style_controls();
		$this->__table_heading_and_text_style_controls();
		$this->__table_description_tooltip_style_controls();
		$this->__table_collapse_icon_style_controls();
		$this->__table_image_icon_style_controls();
		$this->__table_pricing_style_controls();
		$this->__table_btn_style_controls();
		$this->__table_badge_style_controls();
		$this->__table_nav_style_controls();
	}

	protected function __table_column_style_controls() {

		$this->start_controls_section(
			'_section_column_style',
			[
				'label' => __( 'Column', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-adct-column' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_background_color_even',
			[
				'label'     => __( 'Background Color (Even)', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default' => '#f7f7f7',
				'selectors' => [
					'{{WRAPPER}} .ha-adct-row-wrap:nth-child(even)' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'row_background_color_odd',
			[
				'label'     => __( 'Background Color (Odd)', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ha-adct-row-wrap:nth-child(odd)' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'child_row_background_color_odd',
			[
				'label'     => __( 'Background Color (child)', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ha-adct-child-row-wrap' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'column_border',
				'selector' => '{{WRAPPER}} .ha-adct-table .ha-adct-column',
			]
		);

		$this->add_control(
			'column_border_top',
			[
				'label'        => esc_html__( 'Table Top Border', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'happy-addons-pro' ),
				'label_off'    => esc_html__( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors_dictionary' => [
					'' => 'border-top: none;',
				],
				'selectors'    => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-row-wrap:first-child .ha-adct-column' => '{{VALUE}}',
				],
				'condition'   => [
					'column_border_border!' => ['','none'],
				],
			]
		);

		$this->add_control(
			'column_border_bottom',
			[
				'label'        => esc_html__( 'Table Bottom Border', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'happy-addons-pro' ),
				'label_off'    => esc_html__( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors_dictionary' => [
					'' => 'border-bottom: none;',
				],
				'selectors'    => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-row-wrap:last-child .ha-adct-row .ha-adct-column' => '{{VALUE}}',
					'{{WRAPPER}} .ha-adct-table .ha-adct-row-wrap:last-child .ha-adct-child-row:last-child .ha-adct-column' => '{{VALUE}}',
				],
				'condition'   => [
					'column_border_border!' => ['','none'],
				],
			]
		);

		$this->add_control(
			'column_border_left',
			[
				'label'        => esc_html__( 'Table Left Border', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'happy-addons-pro' ),
				'label_off'    => esc_html__( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors_dictionary' => [
					'' => 'border-left: none;',
				],
				'selectors'    => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-column:first-child' => '{{VALUE}}',
				],
				'condition'   => [
					'column_border_border!' => ['','none'],
				],
			]
		);

		$this->add_control(
			'column_border_right',
			[
				'label'        => esc_html__( 'Table Right Border', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'happy-addons-pro' ),
				'label_off'    => esc_html__( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors_dictionary' => [
					'' => 'border-right: none;',
				],
				'selectors'    => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-column:last-child' => '{{VALUE}}',
				],
				'condition'   => [
					'column_border_border!' => ['','none'],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __table_heading_and_text_style_controls() {

		$this->start_controls_section(
			'_section_heading_and_text_style',
			[
				'label' => __( 'Heading & Text', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_title',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Heading', 'happy-addons-pro' ),
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .ha-adct-heading .ha-adct-text',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'fields_options' => [
					'typography' => [
						'default' => 'yes'
					],
					'font_weight' => [
						'default' => '700', // 100, 200, 300, 400, 500, 600, 700, 800, 900, normal, bold
					],
				],
			]
		);

		$this->add_control(
			'heading_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-heading .ha-adct-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-heading > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-heading > svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_background',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-heading' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'heading_icon_size',
			[
				'label'     => __( 'Icon Size', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-heading i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-adct-heading svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_icon_spacing',
			[
				'label'      => __( 'Icon Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-adct-heading i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-adct-heading svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-adct-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'heading_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-adct-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Text', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .ha-adct-text',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-tooltip-text i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'child_text_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Child Text', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'child_text_typography',
				'selector' => '{{WRAPPER}} .ha-adct-child-row .ha-adct-text',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'fields_options' => [
					'typography' => [
						'default' => 'yes'
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '400', // 100, 200, 300, 400, 500, 600, 700, 800, 900, normal, bold
					],
					'font_size' => [
						'default' => [
							'unit' => 'px', // px, em, rem, vh
							'size' => '14', // any number
						],
					],
				],
			]
		);

		$this->add_control(
			'child_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-child-row .ha-adct-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-child-row .ha-adct-tooltip-text i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'child_text_left_padding',
			[
				'label'      => __( 'Left Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'html:not([dir="rtl"]) {{WRAPPER}} .ha-adct-child-row .ha-adct-text' => 'padding-left: {{SIZE}}{{UNIT}};',
					'html[dir="rtl"] {{WRAPPER}} .ha-adct-child-row .ha-adct-text' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __table_description_tooltip_style_controls() {

		$this->start_controls_section(
			'_section_description_style',
			[
				'label' => __( 'Description & Tooltip', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_description_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Description', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .ha-adct-description',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'fields_options' => [
					'typography' => [
						'default' => 'yes'
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '400', // 100, 200, 300, 400, 500, 600, 700, 800, 900, normal, bold
					],
					'font_size' => [
						'default' => [
							'unit' => 'px', // px, em, rem, vh
							'size' => '14', // any number
						],
					],
				],
			]
		);

		$this->add_control(
			'description_text_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#979797',
				'selectors' => [
					'{{WRAPPER}} .ha-adct-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'description_text_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default' => [
					'top' => 10,
					'right' => 10,
					'bottom' => 10,
					'left' => 0,
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-adct-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_tooltip_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Tooltip', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tooltip_width',
			[
				'label'          => __( 'Width', 'happy-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				// 'size_units'     => ['%'],
				'range'          => [
					'px'  => [
						'min' => 0,
						'max' => 250,
						'step' => 1,
					],
				],
				// 'devices' => [ 'desktop', 'tablet', 'mobile' ],
				// 'desktop_default' => [
				// 	'unit' => '%',
				// 	'size' => '',
				// ],
				// 'tablet_default' => [
				// 	'unit' => '%',
				// 	'size' => '',
				// ],
				// 'mobile_default' => [
				// 	'unit' => '%',
				// 	'size' => '',
				// ],
				'selectors'      => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-tooltip-text span' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tooltip_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-adct-table .ha-adct-tooltip-text span',
			]
		);

		$this->add_control(
			'tooltip_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-tooltip-text span' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'tooltip_icon_color',
			[
				'label' => __( 'Icon Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-tooltip-text i' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'tooltip_background',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-tooltip-text span' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-adct-table .ha-adct-tooltip-text span:after' => 'border-top-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'tooltip_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-tooltip-text span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tooltip_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-table .ha-adct-tooltip-text span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __table_collapse_icon_style_controls() {

		$this->start_controls_section(
			'_section_collapse_icon_style',
			[
				'label' => __( 'Collapse Icon', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'collapse_icon_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-collapse-icon-wrap i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-collapse-icon-wrap svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'collapse_icon_size',
			[
				'label'     => __( 'Icon Size', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-collapse-icon-wrap i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-adct-collapse-icon-wrap svg' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'collapse_icon_spacing',
			[
				'label'      => __( 'Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-adct-collapse-icon-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __table_image_icon_style_controls() {

		$this->start_controls_section(
			'_section_image_icon_style',
			[
				'label' => __( 'Image & Icon', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Image', 'happy-addons-pro' ),
				'separator' => 'after',
			]
		);

		$this->add_control(
			'image_width',
			[
				'label'     => __( 'Width', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-column.ha-adct-column-content-type-image img'  => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_height',
			[
				'label'     => __( 'Height', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-column.ha-adct-column-content-type-image img'  => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label'     => __( 'Border Radius', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-column.ha-adct-column-content-type-image img'  => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Icon', 'happy-addons-pro' ),
				'separator' => 'after',
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label'     => __( 'Icon Size', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-column.ha-adct-column-content-type-icon i'  => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-adct-column.ha-adct-column-content-type-icon svg'  => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-column.ha-adct-column-content-type-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-adct-column.ha-adct-column-content-type-icon svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __table_pricing_style_controls() {

		$this->start_controls_section(
			'_section_pricing_style',
			[
				'label' => __( 'Pricing', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_price',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Price', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'selector' => '{{WRAPPER}} .ha-adct-price-text',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-price-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'price_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-price-tag' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_heading_currency',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Currency', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'currency_typography',
				'selector' => '{{WRAPPER}} .ha-adct-currency',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'currency_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-currency' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'currency_spacing',
			[
				'label' => __( 'Side Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-currency' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-adct-currency.right-side' => 'margin-left: {{SIZE}}{{UNIT}};margin-right:0;',
				],
			]
		);

		$this->add_responsive_control(
			'currency_position',
			[
				'label' => __( 'Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-current-price .ha-adct-currency' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_heading_original_price',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Original Price', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'original_price_typography',
				'selector' => '{{WRAPPER}} .ha-adct-original-price .ha-adct-currency,{{WRAPPER}} .ha-adct-original-price .ha-adct-price-text',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'original_price_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-original-price .ha-adct-currency' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-adct-original-price .ha-adct-price-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'original_price_spacing',
			[
				'label' => __( 'Side Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-original-price' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'original_price_position',
			[
				'label' => __( 'Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-original-price .ha-adct-currency,'
				   .'{{WRAPPER}} .ha-adct-original-price .ha-adct-price-text' => 'top: {{SIZE}}{{UNIT}};position:relative;',
				],
			]
		);

		$this->add_control(
			'_heading_period',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Period', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'period_typography',
				'selector' => '{{WRAPPER}} .ha-adct-price-period',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'period_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-price-period' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __table_btn_style_controls() {

		$this->start_controls_section(
			'_section_btn_style',
			[
				'label' => __( 'Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn_text_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-adct-btn',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'btn_border_radius',
			[
				'label'     => __( 'Border Radius', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-btn'  => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn_padding',
			[
				'label'     => __( 'Padding', 'happy-addons-pro' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'default' => [
					'top' => 5,
					'right' => 10,
					'bottom' => 5,
					'left' => 10,
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-btn'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'btn_border',
				'selector' => '{{WRAPPER}} .ha-adct-btn',
			]
		);

		$this->start_controls_tabs( '_tabs_button' );
		$this->start_controls_tab(
			'_tab_button_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-btn' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-btn' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .ha-adct-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tabs_button_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'button_hover_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-btn:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-btn:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_border_hover_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-btn:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} .ha-adct-btn:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __table_badge_style_controls() {

		$this->start_controls_section(
			'_section_style_badge',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'badge_width',
			[
				'label'     => __( 'Width', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-badge'  => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-adct-badge',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-adct-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'selector' => '{{WRAPPER}} .ha-adct-badge',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_box_shadow',
				'selector' => '{{WRAPPER}} .ha-adct-badge',
			]
		);

		$this->add_control(
			'badge_translate_toggle',
			[
				'label' => __( 'Offset', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'badge_translate_x',
			[
				'label' => __( 'Offset X', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'(desktop){{WRAPPER}} .ha-adct-badge' => 'left: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'badge_translate_y',
			[
				'label' => __( 'Offset Y', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-adct-badge' => 'top: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	protected function __table_nav_style_controls() {

		$this->start_controls_section(
			'_section_nav_style',
			[
				'label' => __( 'Responsive Nav Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'nav_text_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} ul.ha-adct-table-responsive-nav button',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_responsive_control(
			'nav_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'nav_margin',
			[
				'label'      => __( 'Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'nav_border',
				'selector' => '{{WRAPPER}} ul.ha-adct-table-responsive-nav button',
			]
		);

		$this->start_controls_tabs( '_tabs_nav' );
		$this->start_controls_tab(
			'_tab_nav_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'nav_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'nav_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav button' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'_tab_nav_active',
			[
				'label' => __( 'Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'nav_active_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav li.active button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'nav_active_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav li.active button' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'nav_active_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav li.active button' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'_nav_badge_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'separator' => 'before',
				'condition'   => [
					'nav_badge' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'nav_badge_text_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} ul.ha-adct-table-responsive-nav .ha-adct-badge',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition'   => [
					'nav_badge' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_nav_badge' );
		$this->start_controls_tab(
			'_tab_nav_badge_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
				'condition'   => [
					'nav_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'nav_badge_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav .ha-adct-badge' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'nav_badge_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav .ha-adct-badge' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'_tab_nav_badge_active',
			[
				'label' => __( 'Active', 'happy-addons-pro' ),
				'condition'   => [
					'nav_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'nav_active_badge_color',
			[
				'label'     => __( 'Badge Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav li.active .ha-adct-badge' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'nav_active_badge_bg_color',
			[
				'label'     => __( 'Badge Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ha-adct-table-responsive-nav li.active .ha-adct-badge' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function data_map() {
		$settings     = $this->get_settings_for_display();
		$column_1    = is_array( $settings['column_data_1'] ) ? $settings['column_data_1'] : [];

		$table_data = [];
		$row_number = 0;
		$child_row_number = 0;
		foreach ( $column_1 as $key => $value ) {
			$value['column_number'] = 1;
			if( 'parent' == $value['row_type'] ) {
				$row_number +=1;
				$child_row_number = 0;
				$row = [];
				array_push($row, $value);
				$table_data['row_'.$row_number]['parent'] = $row;

			} elseif ( 'child' == $value['row_type'] ) {
				$child_row_number +=1;
				$table_data['row_'.$row_number]['child']['child_row_'.$child_row_number] = [$value];
			}
		}

		$columns_data = [];
		$nav_data = [];
		for ($i = 2; $i <= $settings['column_count']; $i++) {
			$row_number = 0;
			$child_row_number = 0;
			if( is_array( $settings['column_data_' . $i] ) && 'yes' == $settings['show_column_' . $i] ) {
				foreach ($settings['column_data_' . $i] as $key => $value ) {
					$value['column_number'] = $i;
					if( 'parent' == $value['row_type'] ) {
						$row_number +=1;
						$child_row_number = 0;
						if( 1 == $row_number && $settings['badge_text_'.$i] ) {
							// $value['badge_text'] = $settings['badge_text'];
							$value['badge_text'] = $settings['badge_text_'.$i];
						}
						if( 1 == $row_number ) {
							$nav_data[] = [
								'title' => $value['column_text'] ? $value['column_text'] : esc_html__( ('Column ' . $i), 'happy-addons-pro'),
								'_id' => $value['_id'],
								'badge_text' => isset($value['badge_text']) ? $value['badge_text'] : '',
							];
						}
						$row = [];
						array_push($row, $value);
						$columns_data['column_data_'.$i]['row_'.$row_number]['parent'] = $row;

					} elseif ( 'child' == $value['row_type'] ) {
						$child_row_number +=1;
						$columns_data['column_data_'.$i]['row_'.$row_number]['child']['child_row_'.$child_row_number] = [$value];
					}
				}
			}
		}


		foreach ( $table_data as $key => $value ) {

			foreach ( $columns_data as $columns_key => $single_columns ) { // column's data

				$table_data[$key]['parent'][] = isset( $single_columns[$key]['parent'][0] ) ? $single_columns[$key]['parent'][0] : [];

				if( array_key_exists( 'child', $value ) ) {
					foreach ( $value['child'] as $child_key => $child_value ) {

						$table_data[$key]['child'][$child_key][] = isset( $single_columns[$key]['child'][$child_key] ) ? $single_columns[$key]['child'][$child_key][0]: [];

					}

					$table_data[$key]['parent'][0]['collapse'] = [
						'open' => $settings['collapse_open_icon'],
						'close' => $settings['collapse_close_icon']
					];
				}
			}

		}

		return [
			'nav' => $nav_data,
			'table' => $table_data
		];
	}

	protected function get_currency_symbol( $symbol_name ) {
		$symbols = [
			'baht'         => '&#3647;',
			'bdt'          => '&#2547;',
			'dollar'       => '&#36;',
			'euro'         => '&#128;',
			'franc'        => '&#8355;',
			'guilder'      => '&fnof;',
			'indian_rupee' => '&#8377;',
			'pound'        => '&#163;',
			'peso'         => '&#8369;',
			'peseta'       => '&#8359',
			'lira'         => '&#8356;',
			'ruble'        => '&#8381;',
			'shekel'       => '&#8362;',
			'rupee'        => '&#8360;',
			'real'         => 'R$',
			'krona'        => 'kr',
			'won'          => '&#8361;',
			'yen'          => '&#165;',
		];

		return isset( $symbols[ $symbol_name ] ) ? $symbols[ $symbol_name ] : '';
	}

	protected function get_column_number( $settings ) {

		$column_number = $settings['column_count'] ? ( $settings['column_count'] - 1 ) : '2';

		if ( 'yes' != $settings['show_column_2'] && $settings['column_count'] >= '2' ) {
			$column_number = $column_number - 1;
		}
		if ( 'yes' != $settings['show_column_3'] && $settings['column_count'] >= '3' ) {
			$column_number = $column_number - 1;
		}
		if ( 'yes' != $settings['show_column_4'] && $settings['column_count'] >= '4' ) {
			$column_number = $column_number - 1;
		}
		if ( 'yes' != $settings['show_column_5'] && $settings['column_count'] >= '5' ) {
			$column_number = $column_number - 1;
		}
		if ( 'yes' != $settings['show_column_6'] && $settings['column_count'] >= '6' ) {
			$column_number = $column_number - 1;
		}
		if ( 'yes' != $settings['show_column_7'] && $settings['column_count'] >= '7' ) {
			$column_number = $column_number - 1;
		}
		if ( 'yes' != $settings['show_column_8'] && $settings['column_count'] >= '8' ) {
			$column_number = $column_number - 1;
		}
		if ( 'yes' != $settings['show_column_9'] && $settings['column_count'] >= '9' ) {
			$column_number = $column_number - 1;
		}
		if ( 'yes' != $settings['show_column_10'] && $settings['column_count'] >= '10' ) {
			$column_number = $column_number - 1;
		}

		$column_number = ( 1 == $column_number ) ? 2 : $column_number;

		return $column_number;
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();

		$data = $this->data_map();
		$table_data = $data['table'];
		$nav_data = $data['nav'];

		$column_number = $this->get_column_number( $settings );
		$style = '';
		$style .= '--ha-adct-column-count:' . esc_attr( $column_number );
		?>

		<!-- responsive nav start -->
		<ul class="ha-adct-table-responsive-nav">
			<?php foreach( $nav_data as $key => $li ) : ?>
				<li class="<?php echo (0 == $key ? esc_attr('active') : '') ?>">
					<?php
					if ( 'yes' === $settings['nav_badge'] && isset( $li['badge_text'] ) && $li['badge_text'] ) {
						printf(
							'<div class="ha-adct-badge">%s</div>',
							esc_html( $li['badge_text'] ),
						);
					}
					if ( isset( $li['title'] ) && $li['title'] ) {
						printf(
							'<button>%s</button>',
							esc_html( $li['title'] ),
						);
					}
					?>
				</li>
			<?php endforeach;?>
		</ul>
		<!-- responsive nav end -->


		<div class="ha-adct-table" style="<?php echo $style;?>">
		<?php foreach( $table_data as $key => $row ) : ?>


			<div class="ha-adct-row-wrap ha-adct-head-row ha-adct-<?php echo esc_attr($key);?>">

				<?php if ( isset( $row['parent'] ) && !empty( $row['parent'] ) ) : ?>
				<div class="ha-adct-row">
					<?php foreach ( $row['parent'] as $parent_key => $parent ) : ?>
						<?php
						$class = "ha-adct-column";
						$class .= !empty($parent) ? " ha-adct-column-" . esc_attr($parent['column_number']) : '';
						$class .= isset($parent['_id']) ? " elementor-repeater-item-" . esc_attr($parent['_id']) : '';
						$class .= isset($parent['content_type']) ? " ha-adct-column-content-type-" . esc_attr($parent['content_type']) : '';
						$class .= !isset($parent['content_type']) ? " ha-adct-column-empty" : '';
						$class .= ( !empty( $parent['desc_or_tooltip'] ) && 'description' == $parent['desc_or_tooltip'] && !empty( $parent['description'] ) ) ? " ha-adct-column-align-" . $settings['column_align_' . esc_attr($parent['column_number']) ] : '';
						?>
						<div class="<?php echo esc_attr($class);?>">
							<?php if ( !empty($parent) && 'blank' != $parent['content_type'] ) : ?>

								<?php if ( isset( $parent['badge_text'] ) && !empty( $parent['badge_text'] ) ) : ?>
									<div class="ha-adct-badge"><?php echo esc_html( $parent['badge_text'] );?></div>
								<?php endif; ?>

								<?php if ( isset( $parent['collapse'] ) && !empty( $parent['collapse'] ) ) : ?>
								<?php $has_icon = ( $settings['collapse_close_icon']['value'] && $settings['collapse_open_icon']['value'] ); ?>
									<span class="ha-adct-collapse-icon-wrap <?php echo esc_attr(('yes' == $settings['child_expand'] ? 'open': ''));?>">
										<span class="ha-adct-collapse-close">
											<?php Icons_Manager::render_icon( $settings['collapse_close_icon'] ); ?>
										</span>
										<span class="ha-adct-collapse-open">
											<?php Icons_Manager::render_icon( $settings['collapse_open_icon'] ); ?>
										</span>
									</span>
								<?php endif; ?>

								<?php $this->inner_column_markup($parent); ?>

							<?php endif; ?>

						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>


				<?php if ( isset( $row['child'] ) && !empty( $row['child'] ) ) : ?>
				<div class="ha-adct-child-row-wrap" style="<?php echo esc_attr(('yes' != $settings['child_expand'] ? 'display: none;': ''));?>">

					<?php foreach ( $row['child'] as $child_row_key => $child_row ) : ?>

						<?php if ( !empty( $child_row ) ) : ?>
							<div class="ha-adct-child-row ha-adct-child-<?php echo esc_attr($child_row_key);?>">
								<?php foreach ( $child_row as $child_column_key => $child_column ) : ?>
									<?php
									$child_class = "ha-adct-column";
									$child_class .= !empty($child_column) ? " ha-adct-column-" . esc_attr($child_column['column_number']) : '';
									$child_class .= isset($child_column['_id']) ? " elementor-repeater-item-" . esc_attr($child_column['_id']) : '';
									$child_class .= isset($child_column['content_type']) ? " ha-adct-column-content-type-" . esc_attr($child_column['content_type']) : '';
									$child_class .= !isset($child_column['content_type']) ? " ha-adct-column-empty" : '';
									$child_class .= ( !empty( $parent['desc_or_tooltip'] ) && 'description' == $child_column['desc_or_tooltip'] && !empty( $child_column['description'] ) ) ? " ha-adct-column-align-" . $settings['column_align_' . esc_attr($child_column['column_number']) ] : '';
									?>
									<div class="<?php echo esc_attr($child_class);?>">
										<?php if ( !empty($child_column) && 'blank' != $child_column['content_type'] ) {$this->inner_column_markup($child_column);} ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

					<?php endforeach; ?>

				</div>
				<?php endif; ?>

			</div>

		<?php endforeach;?>

		</div>

		<?php


	}

	protected function inner_column_markup($item) {
		?>
		<!-- Content type heading -->
		<?php if ( 'heading' == $item['content_type'] && !empty( $item['column_text'] ) ) : ?>
			<div class="ha-adct-heading-wrap">
				<<?php echo ha_escape_tags($item['title_tag']);?> class="ha-adct-heading">
					<?php
						printf(
							'<span class="ha-adct-text ">%s</span>',
							esc_html( $item['column_text'] ),
						);
						if( $item['heading_icon'] ) {
							Icons_Manager::render_icon( $item['heading_icon'] );
						}
					?>
				</<?php echo ha_escape_tags($item['title_tag']);?>>

				<!-- description text -->
				<?php if ( 'description' == $item['desc_or_tooltip'] && !empty( $item['description'] ) ) : ?>
					<?php
						printf(
							'<div class="ha-adct-description">%s</div>',
							wp_kses_post( $item['description'] ),
						);
					?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Content type text -->
		<?php if ( 'text' == $item['content_type'] && !empty( $item['column_text'] ) ) : ?>
			<div class="ha-adct-text-wrap">
				<?php
					printf(
						'<span class="ha-adct-text ">%s</span>',
						esc_html( $item['column_text'] ),
					);
				?>

				<!-- description text -->
				<?php if ( 'description' == $item['desc_or_tooltip'] && !empty( $item['description'] ) ) : ?>
					<?php
						printf(
							'<div class="ha-adct-description">%s</div>',
							wp_kses_post( $item['description'] ),
						);
					?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Content type icon -->
		<?php if ( 'icon' == $item['content_type'] && !empty( $item['column_icon'] ) ) : ?>
			<?php Icons_Manager::render_icon( $item['column_icon'] ); ?>
		<?php endif; ?>

		<!-- Content type image -->
		<?php if ( 'image' == $item['content_type'] && !empty( $item['column_image'] ) ) : ?>
			<?php
				if ( $item['column_image']['id'] ) {
					echo wp_get_attachment_image( $item['column_image']['id'], $item['thumbnail_size'] );
				} else {
					printf(
						'<img src="%s" alt="">',
						esc_url( $item['column_image']['url'] ),
					);
				}
			?>
		<?php endif; ?>

		<!-- Content type price -->
		<?php if ( 'price' == $item['content_type'] && $item['price'] ) : ?>
			<?php
				if( 'custom' === $item['currency'] ) {
					$currency = $item['currency_custom'];
				} else {
					$currency = $this->get_currency_symbol( $item['currency'] );
				}
			?>
			<div class="ha-adct-price">
				<div class="ha-adct-price-tag">
					<?php if ( $item['original_price'] || '0' == $item['original_price'] ):?>
						<div class="ha-adct-original-price">
							<?php if ( 'left' == $item['currency_side'] ):?>
								<span class="ha-adct-currency left-side"><?php echo esc_html( $currency ); ?></span>
							<?php endif ?>
							<span class="ha-adct-price-text"><?php echo ha_kses_basic( $item['original_price'] ); ?></span>
							<?php if ( 'right' == $item['currency_side'] ):?>
								<span class="ha-adct-currency right-side"><?php echo esc_html( $currency ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="ha-adct-current-price">
						<?php if ( 'left' == $item['currency_side'] ):?>
							<span class="ha-adct-currency left-side"><?php echo esc_html( $currency ); ?></span>
						<?php endif ?>
						<span class="ha-adct-price-text"><?php echo ha_kses_basic( $item['price'] ); ?></span>
						<?php if ( 'right' == $item['currency_side'] ):?>
							<span class="ha-adct-currency right-side"><?php echo esc_html( $currency ); ?></span>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( $item['period'] ) : ?>
					<div class="ha-adct-price-period"><?php echo ha_kses_basic( $item['period'] ); ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Content type button -->
		<?php if ( 'button' == $item['content_type'] && ! empty( $item['btn_text'] ) ) : ?>
			<?php
				$this->add_render_attribute( 'button', 'class', ['ha-adct-btn'] );
				if ( $item['btn_link']['url'] ) {
					$this->add_link_attributes( 'button', $item['btn_link'] );
				}
				printf(
					'<a %s>%s</a>',
					$this->get_render_attribute_string( 'button' ),
					esc_html( $item['btn_text'] ),
				);
				$this->remove_render_attribute( 'button' );
			?>
		<?php endif; ?>

		<!-- tooltip text -->
		<?php if ( ( 'heading' == $item['content_type'] || 'text' == $item['content_type'] ) && 'tooltip' == $item['desc_or_tooltip'] && !empty( $item['tooltip'] ) ) : ?>
			<?php
				printf(
					'<span class="ha-adct-tooltip-text" style=""><i class="hm hm-info"></i><span>%s</span></span>',
					esc_html( $item['tooltip'] ),
				);
			?>
		<?php endif; ?>

		<!-- description text -->
		<?php if ( false && ( 'heading' == $item['content_type'] || 'text' == $item['content_type'] ) && 'description' == $item['desc_or_tooltip'] && !empty( $item['description'] ) ) : ?>
			<?php
				printf(
					'<div class="ha-adct-description">%s</div>',
					wp_kses_post( $item['description'] ),
				);
			?>
		<?php endif; ?>

		<?php
	}

}
