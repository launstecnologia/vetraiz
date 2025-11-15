<?php

/**
 * Metro Grid widget class
 *
 * @package Happy_Addons_Pro
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Embed;
use Elementor\Core\Schemes\Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Happy_Addons_Pro\Controls\Image_Selector;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined( 'ABSPATH' ) || die();

class Metro_Grid extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Metro Grid', 'happy-addons-pro' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor-pro/happy-effects-pro/metro-grid/';
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-metro-grid-text-outline';
	}

	public function get_keywords() {
		return [ 'metro-grid', 'metro', 'grid' ];
	}

	public function free_grids() {
		$free_grids = [
			'lily'        => __( 'Lily', 'happy-addons-pro' ),
			'daffodil'    => __( 'Daffodil', 'happy-addons-pro' ),
			'lavender'    => __( 'Lavender', 'happy-addons-pro' ),
			'orchid'      => __( 'Orchid', 'happy-addons-pro' ),
			'wild-orchid' => __( 'Wild Orchid', 'happy-addons-pro' ),
			'poppy'       => __( 'Poppy', 'happy-addons-pro' ),
			'rachel'      => __( 'Rachel', 'happy-addons-pro' ),
			'pippin'      => __( 'Pippin', 'happy-addons-pro' ),
			'windy'       => __( 'Windy', 'happy-addons-pro' ),
			'breezy'      => __( 'Breezy', 'happy-addons-pro' ),
			'cathreen'    => __( 'Cathreen', 'happy-addons-pro' ),
			'capricorn'   => __( 'Capricorn', 'happy-addons-pro' ),
			'europa'      => __( 'Europa', 'happy-addons-pro' ),
			'rondeletia'  => __( 'Rondeletia', 'happy-addons-pro' ),
			'bletilla'    => __( 'Bletilla', 'happy-addons-pro' ),
			'crepuscular' => __( 'Crepuscular', 'happy-addons-pro' ),

			'clianthus'   => __( 'Clianthus', 'happy-addons-pro' ),
			'dandelion'   => __( 'Dandelion', 'happy-addons-pro' ),
			'lupin'       => __( 'Lupin', 'happy-addons-pro' ),
		];
		return $free_grids;

	}

	/**
	 * Hover style
	 */
	public function hover_styles() {
		$hover_styles = [
			'slide-up' => __( 'Slide Up', 'happy-addons-pro' ),
			'fade-in'  => __( 'Fade In', 'happy-addons-pro' ),
			'zoom-in'  => __( 'Zoom In', 'happy-addons-pro' ),
			'lilly'    => __( 'Lilly', 'happy-addons-pro' ),
			'kindred'  => __( 'Kindred', 'happy-addons-pro' ),
		];
		return $hover_styles;
	}


	/**
	 * Register content controls
	 */
	public function register_content_controls() {
		$this->__content_controls();
		$this->__settings_content_controls();
	}

	public function __content_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'           => __( 'Grid Layout', 'happy-addons-pro' ),
				'type'            => Image_Selector::TYPE,
				'label_block'     => true,
				'default'         => 'lily',
				'options'         => ha_metro_grid_layout_image_list( $this->free_grids() ),
				'content_classes' => 'column-4',
				'column_height'   => '200px',
			]
		);

		$this->add_control(
			'on_click',
			[
				'label'   => __( 'On Click', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'lightbox',
				'options' => [
					'lightbox' => __( 'Display Lightbox', 'happy-addons-pro' ),
					'link'     => __( 'Open Link', 'happy-addons-pro' ),
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'media_type',
			[
				'label'       => __( 'Media Type', 'happy-addons-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'image' => [
						'title' => __( 'Image', 'happy-addons-pro' ),
						'icon'  => 'eicon-image',
					],
					'video' => [
						'title' => __( 'Video', 'happy-addons-pro' ),
						'icon'  => 'eicon-video-playlist',
					],
				],
				'toggle'      => false,
				'default'     => 'image',
			]
		);

		$this->__repeater_controls_image( $repeater );
		$this->__repeater_controls_video( $repeater );
		$this->__repeater_controls_title_subtitle( $repeater );

		$repeater_defaults = [
			[
				'title'    => __( 'Item #1', 'happy-addons-pro' ),
				'subtitle' => __( 'Place your subtitle here.', 'happy-addons-pro' ),
			],
			[
				'title'    => __( 'Item #2', 'happy-addons-pro' ),
				'subtitle' => __( 'Place your subtitle here.', 'happy-addons-pro' ),
			],
			[
				'title'    => __( 'Item #3', 'happy-addons-pro' ),
				'subtitle' => __( 'Place your subtitle here.', 'happy-addons-pro' ),
			],
			[
				'title'    => __( 'Item #4', 'happy-addons-pro' ),
				'subtitle' => __( 'Place your subtitle here.', 'happy-addons-pro' ),
			],
			[
				'title'    => __( 'Item #5', 'happy-addons-pro' ),
				'subtitle' => __( 'Place your subtitle here.', 'happy-addons-pro' ),
			],
			[
				'title'    => __( 'Item #6', 'happy-addons-pro' ),
				'subtitle' => __( 'Place your subtitle here.', 'happy-addons-pro' ),
			],
			[
				'title'    => __( 'Item #7', 'happy-addons-pro' ),
				'subtitle' => __( 'Place your subtitle here.', 'happy-addons-pro' ),
			],
			[
				'title'    => __( 'Item #8', 'happy-addons-pro' ),
				'subtitle' => __( 'Place your subtitle here.', 'happy-addons-pro' ),
			],
		];

		$this->add_control(
			'media_list',
			[
				'label'       => __( 'Grid List', 'happy-addons-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $repeater_defaults,
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	public function __repeater_controls_image( $repeater ) {
		$repeater->add_control(
			'image',
			[
				'label'     => __( 'Image', 'happy-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic'   => [
					'active' => false,
				],
				'condition' => [
					'media_type' => 'image',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'default'   => 'large',
				'exclude'   => [
					'custom',
				],
				'condition' => [
					'media_type' => 'image',
				],
			]
		);
	}

	public function __repeater_controls_video( $repeater ) {
		$repeater->add_control(
			'video_type',
			[
				'label'              => esc_html__( 'Source', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'youtube',
				'options'            => [
					'youtube'     => esc_html__( 'YouTube', 'happy-addons-pro' ),
					'vimeo'       => esc_html__( 'Vimeo', 'happy-addons-pro' ),
					'hosted'      => esc_html__( 'Self Hosted', 'happy-addons-pro' ),
				],
				'frontend_available' => true,
				'condition'          => [
					'media_type' => 'video',
				],
			]
		);

		$repeater->add_control(
			'youtube_url',
			[
				'label'              => esc_html__( 'Link', 'happy-addons-pro' ),
				'type'               => Controls_Manager::URL,
				'dynamic'            => [
					'active'     => false,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder'        => esc_html__( 'Enter your URL', 'happy-addons-pro' ) . ' (YouTube)',
				'default' => [
					'url' => 'https://www.youtube.com/watch?v=3U67Cw2YoeQ',
				],
				'label_block'        => true,
				'options' => false,
				'condition'          => [
					'media_type' => 'video',
					'video_type' => 'youtube',
				],
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'vimeo_url',
			[
				'label'       => esc_html__( 'Link', 'happy-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active'     => false,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL', 'happy-addons-pro' ) . ' (Vimeo)',
				'default' => [
					'url' => 'https://vimeo.com/235215203',
				],
				'label_block' => true,
				'options' => false,
				'condition'   => [
					'media_type' => 'video',
					'video_type' => 'vimeo',
				],
			]
		);

		$repeater->add_control(
			'hosted_url',
			[
				'label'      => esc_html__( 'Choose File', 'happy-addons-pro' ),
				'type'       => Controls_Manager::MEDIA,
				'media_type' => [ 'video' ],
				'dynamic'    => [
					'active'     => true,
					'categories' => [
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'condition'  => [
					'media_type' => 'video',
					'video_type' => 'hosted',
				],
			]
		);

		$repeater->add_control(
			'image_overlay',
			[
				'label'              => esc_html__( 'Custom Video Poster Image', 'happy-addons-pro' ),
				'type'               => Controls_Manager::MEDIA,
				'default'            => [
					'url' => '',
				],
				'dynamic'            => [
					'active' => false,
				],
				'condition'          => [
					'media_type' => 'video',
				],
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'start',
			[
				'label'              => esc_html__( 'Start Time', 'happy-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => esc_html__( 'Specify a start time (in seconds)', 'happy-addons-pro' ),
				'frontend_available' => true,
				'condition'          => [
					'media_type' => 'video',
				],
			]
		);

		$repeater->add_control(
			'end',
			[
				'label'              => esc_html__( 'End Time', 'happy-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => esc_html__( 'Specify an end time (in seconds)', 'happy-addons-pro' ),
				'condition'          => [
					'media_type' => 'video',
					'video_type' => [ 'youtube', 'hosted' ],
				],
				'frontend_available' => true,
			]
		);
	}

	public function __repeater_controls_title_subtitle( $repeater ) {
		$repeater->add_control(
			'pull_meta',
			[
				'label'     => esc_html__( 'Image Meta', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::BUTTON,
				'separator' => 'before',
				// 'button_type' => 'success',
				'text'      => esc_html__( 'Pull Image Meta', 'happy-addons-pro' ),
				'event'     => 'ha_metro_grid.editor.pull_meta',
				'condition' => [
					'media_type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => __( 'Title', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'New Grid Item', 'happy-addons-pro' ),
				'separator'   => 'before',
				'placeholder' => __( 'Type your title here', 'happy-addons-pro' ),
			]
		);

		$repeater->add_control(
			'subtitle',
			[
				'label'       => __( 'Subtitle', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default'     => esc_html__( 'Place your subtitle here.', 'happy-addons-pro' ),
				'placeholder' => __( 'Type your subtitle here', 'happy-addons-pro' ),
			]
		);

		$repeater->add_control(
			'url',
			[
				'label'       => __( 'Link URL', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'show_label'  => false,
				'input_type'  => 'url',
				'description' => __( 'Works only when "On Click" is set to "Open Link"', 'happy-addons-pro' ),
				'default'     => '',
				'placeholder' => __( 'Link URL', 'happy-addons-pro' ),
				'condition'   => [
					'media_type' => 'image',
				],
			]
		);
	}

	public function __settings_content_controls() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_hover',
			[
				'label'        => __( 'Enable Hover', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'happy-addons-pro' ),
				'label_off'    => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'enable_hover_tablet',
			[
				'label'        => __( 'Enable Hover For Tablet', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'happy-addons-pro' ),
				'label_off'    => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'enable_hover' => 'yes',
				],
			]
		);

		$this->add_control(
			'enable_hover_mobile',
			[
				'label'        => __( 'Enable Hover For Mobile', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'happy-addons-pro' ),
				'label_off'    => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'enable_hover' => 'yes',
				],
			]
		);

		$this->add_control(
			'hover_style',
			[
				'label'     => __( 'Hover Style', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'slide-up',
				'options'   => $this->hover_styles(),
				'condition' => [
					'enable_hover' => 'yes',
				],
			]
		);

		$this->add_control(
			'hover_text_align',
			[
				'label'     => __( 'Title Alignment', 'happy-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'enable_hover' => 'yes',
				],
			]
		);

		$this->add_control(
			'video_item_headding',
			[
				'label'     => esc_html__( 'Video', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'label'              => esc_html__( 'Aspect Ratio', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					'169' => '16:9',
					'219' => '21:9',
					'43'  => '4:3',
					'32'  => '3:2',
					'11'  => '1:1',
					'916' => '9:16',
				],
				'default'            => '169',
				'prefix_class'       => 'elementor-aspect-ratio-',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'vid_play_icon',
			[
				'label'                  => __( 'Icon', 'happy-addons-pro' ),
				'type'                   => Controls_Manager::ICONS,
				'default'                => [
					'value'   => 'hm hm-play-button',
					'library' => 'happy-icons',
				],
				'recommended'            => [
					'fa-solid'   => [
						'play',
						'play-circle',
					],
					'fa-regular' => [
						'play-circle',
					],
				],
				'skin'                   => 'inline',
				'label_block'            => false,
				'exclude_inline_options' => ['svg'],
			]
		);

		$this->add_control(
			'vid_enable_overlay',
			[
				'label'        => esc_html__( 'Enable Hover Overlay', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'happy-addons-pro' ),
				'label_off'    => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register style controls
	 */
	public function register_style_controls() {
		$this->__general_style_controls();
		$this->__hover_1_style_controls();
		$this->__hover_2_style_controls();
		$this->__hover_3_style_controls();
		$this->__hover_4_style_controls();
		$this->__hover_5_style_controls();
		$this->__video_style_controls();
	}

	public function __general_style_controls() {
		$this->start_controls_section(
			'general_style',
			[
				'label' => __( 'General', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'grid_height',
			[
				'label'       => __( 'Grid Height', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'devices'     => ['desktop', 'tablet'],
				'size_units'  => ['px', 'vh'],
				'range'       => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				// 'default'     => [
				// 	'unit' => 'px',
				// 	'size' => '200',
				// ],
				'selectors'   => [
					'{{WRAPPER}} .ha-metor-grid-wrap .ha-metor-grid-content' => 'grid-auto-rows: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'gutter_width',
			[
				'label'       => __( 'Gutter Width', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'devices'     => ['desktop', 'tablet'],
				'size_units'  => ['px'],
				'default'     => [
					'unit' => 'px',
					'size' => '5',
				],
				'selectors'   => [
					'{{WRAPPER}} .ha-metor-grid-wrap .ha-metor-grid-content ' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Image Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-metor-grid--item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function __hover_1_style_controls() {
		$this->start_controls_section(
			'grid_hover_style',
			[
				'label'     => __( 'Hover', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_hover' => 'yes',
					'hover_style'  => 'slide-up',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_margin',
			[
				'label'      => __( 'Overlay Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-metor-grid--overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_padding',
			[
				'label'      => __( 'Overlay Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_title_typography',
				'label'          => __( 'Title Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '600',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '20',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title',
			]
		);

		$this->add_responsive_control(
			'title_btm_space',
			[
				'label'       => __( 'Bottom Space', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'devices'     => ['desktop', 'tablet', 'mobile'],
				'size_units'  => ['px'],
				'range'       => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => '0',
				],
				'selectors'   => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_desc_typography',
				'label'          => __( 'Description Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '400',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '14',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-desc',
			]
		);

		$this->add_control(
			'hover_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--hover-slide-up .ha-metor-grid--overlay-inner' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'hover_style' => 'slide-up',
				],
			]
		);

		$this->add_control(
			'hover_overlay_color',
			[
				'label'     => __( 'Overlay Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-metor-grid--overlay-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-metor-grid--overlay-title:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_description_color',
			[
				'label'     => __( 'Description Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-metor-grid--overlay-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function __hover_2_style_controls() {
		$this->start_controls_section(
			'grid_hover_style_2',
			[
				'label'     => __( 'Hover', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_hover' => 'yes',
					'hover_style'  => 'fade-in',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_margin_2',
			[
				'label'      => __( 'Overlay Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-metor-grid--overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_padding_2',
			[
				'label'      => __( 'Overlay Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_title_typography_2',
				'label'          => __( 'Title Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '600',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '20',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title',
			]
		);

		$this->add_responsive_control(
			'title_btm_space_2',
			[
				'label'       => __( 'Bottom Space', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'devices'     => ['desktop', 'tablet', 'mobile'],
				'size_units'  => ['px'],
				'range'       => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => '0',
				],
				'selectors'   => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_desc_typography_2',
				'label'          => __( 'Description Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '400',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '14',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-desc',
			]
		);

		$this->add_control(
			'hover_background_color_2',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--hover-slide-up .ha-metor-grid--overlay-inner' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'hover_style' => 'slide-up',
				],
			]
		);

		$this->add_control(
			'hover_overlay_color_2',
			[
				'label'     => __( 'Overlay Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_text_color_2',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-metor-grid--overlay-title:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_description_color_2',
			[
				'label'     => __( 'Description Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function __hover_3_style_controls() {
		$this->start_controls_section(
			'grid_hover_style_3',
			[
				'label'     => __( 'Hover', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_hover' => 'yes',
					'hover_style'  => 'zoom-in',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_margin_3',
			[
				'label'      => __( 'Overlay Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-metor-grid--overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_padding_3',
			[
				'label'      => __( 'Overlay Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_title_typography_3',
				'label'          => __( 'Title Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '600',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '20',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title',
			]
		);

		$this->add_responsive_control(
			'title_btm_space_3',
			[
				'label'       => __( 'Bottom Space', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'devices'     => ['desktop', 'tablet', 'mobile'],
				'size_units'  => ['px'],
				'range'       => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => '0',
				],
				'selectors'   => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_desc_typography_3',
				'label'          => __( 'Description Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '400',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '14',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-desc',
			]
		);

		$this->add_control(
			'hover_background_color_3',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--hover-slide-up .ha-metor-grid--overlay-inner' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'hover_style' => 'slide-up',
				],
			]
		);

		$this->add_control(
			'hover_overlay_color_3',
			[
				'label'     => __( 'Overlay Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_text_color_3',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-metor-grid--overlay-title:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_description_color_3',
			[
				'label'     => __( 'Description Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function __hover_4_style_controls() {
		$this->start_controls_section(
			'grid_hover_style_4',
			[
				'label'     => __( 'Hover', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_hover' => 'yes',
					'hover_style'  => 'lilly',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_padding_4',
			[
				'label'      => __( 'Overlay Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_title_typography_4',
				'label'          => __( 'Title Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '600',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '20',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title',
			]
		);

		$this->add_responsive_control(
			'title_btm_space_4',
			[
				'label'       => __( 'Bottom Space', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'devices'     => ['desktop', 'tablet', 'mobile'],
				'size_units'  => ['px'],
				'range'       => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => '0',
				],
				'selectors'   => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_desc_typography_4',
				'label'          => __( 'Description Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '400',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '14',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-desc',
			]
		);

		$this->add_control(
			'hover_overlay_color_4',
			[
				'label'     => __( 'Overlay Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_text_color_4',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-metor-grid--overlay-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-metor-grid--overlay-title:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_description_color_4',
			[
				'label'     => __( 'Description Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function __hover_5_style_controls() {
		$this->start_controls_section(
			'grid_hover_style_5',
			[
				'label'     => __( 'Hover', 'happy-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_hover' => 'yes',
					'hover_style'  => 'kindred',
				],
			]
		);

		$this->add_responsive_control(
			'overlay_padding_5',
			[
				'label'      => __( 'Overlay Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_title_typography_5',
				'label'          => __( 'Title Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '600',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '20',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title',
			]
		);

		$this->add_responsive_control(
			'title_btm_space_5',
			[
				'label'       => __( 'Bottom Space', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'label_block' => true,
				'devices'     => ['desktop', 'tablet', 'mobile'],
				'size_units'  => ['px'],
				'range'       => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => '0',
				],
				'selectors'   => [
					'{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'hover_desc_typography_5',
				'label'          => __( 'Description Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Roboto',
					],
					'font_weight' => [
						'default' => '400',
					],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => '14',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .ha-metor-grid--overlay-inner .ha-metor-grid--overlay-desc',
			]
		);

		$this->add_control(
			'hover_overlay_color_5',
			[
				'label'     => __( 'Overlay Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--hover-kindred:hover .ha-metor-grid--overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_text_color_5',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [

					'{{WRAPPER}} .ha-metor-grid--overlay-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-metor-grid--overlay-title:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_description_color_5',
			[
				'label'     => __( 'Description Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-metor-grid--overlay-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_overlay_border_color_5',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-metor-grid--hover-kindred .ha-metor-grid--overlay::before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-metor-grid--hover-kindred .ha-metor-grid--overlay::after' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function __video_style_controls() {
		$this->start_controls_section(
			'section_video_style',
			[
				'label' => esc_html__( 'Video', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'play_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-custom-embed-play i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-custom-embed-play svg' => 'fill: {{VALUE}}',
				],
				'default'   => '#DBDDDE',
			]
		);

		$this->add_control(
			'play_icon_hover_color',
			[
				'label'     => esc_html__( 'Hover Icon Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-custom-embed-play:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-custom-embed-play:hover svg' => 'fill: {{VALUE}}',
				],
				'default'   => '#DBDDDE',
			]
		);

		$this->add_responsive_control(
			'play_icon_size',
			[
				'label'     => esc_html__( 'Icon Size', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 300,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 60,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-custom-embed-play i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-custom-embed-play svg' => 'height: {{SIZE}}{{UNIT}};width:auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'           => 'play_icon_text_shadow',
				'selector'       => '{{WRAPPER}} .elementor-custom-embed-play i,{{WRAPPER}} .elementor-custom-embed-play svg',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => _x( 'Shadow', 'Text Shadow Control', 'happy-addons-pro' ),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	private function get_video_settings( $video_link, $video_type ) {
		$video_url = null;
		$video_settings = [
			'type' => 'video'
		];
		if ( 'hosted' == $video_type ) {
			$video_url = $video_link;
			$video_settings['videoParams'] = [
				'controls' => 'yes',
				'preload' => 'metadata',
				'muted' => 'muted',
				'controlsList' => 'nodownload',
			];
		} else {
			$video_url = $video_link;
		}

		if ( null === $video_url ) {
			return '';
		}

		$video_settings['videoType'] = $video_type;
		$video_settings['url'] = $video_url;

		return $video_settings;
	}

	private function get_embed_options( $grid_item ) {
		$embed_options = [];

		if ( 'youtube' === $grid_item['video_type'] ) {
			$embed_options['privacy'] = true;
		} elseif ( 'vimeo' === $grid_item['video_type'] ) {
			$embed_options['start'] = $grid_item['start'];
		}

		$embed_options['lazy_load'] = true;

		return $embed_options;
	}

	private function get_hosted_video_url( $grid_item ) {
		$video_url = $grid_item['hosted_url']['url'];

		if ( empty( $video_url ) ) {
			return '';
		}

		if ( $grid_item['start'] || $grid_item['end'] ) {
			$video_url .= '#t=';
		}

		if ( $grid_item['start'] ) {
			$video_url .= $grid_item['start'];
		}

		if ( $grid_item['end'] ) {
			$video_url .= ',' . $grid_item['end'];
		}

		return $video_url;
	}

	private function get_video_peram( $grid_item, $settings ) {
		$lightbox_url = '';
		$video_settings = [];
		$video_url = $grid_item[ $grid_item['video_type'] . '_url' ];

		if( 'hosted' === $grid_item['video_type'] && $grid_item['hosted_url']['url'] ) {
			$lightbox_url = $this->get_hosted_video_url( $grid_item );
			$video_url = $this->get_hosted_video_url( $grid_item );
			$video_settings = $this->get_video_settings( $lightbox_url, $grid_item['video_type'] );
		}
		elseif ( 'youtube' === $grid_item['video_type'] && $grid_item['youtube_url']['url'] ) {
			$video_url = $grid_item['youtube_url']['url'];
			$start = $grid_item['start'];
			if ( ! $grid_item['start'] ) {
				$property = explode("t=", $grid_item['youtube_url']['url']);
				$start = isset( $property[1] ) ? $property[1] : $start;
			}
			$embed_url_params = [
				'start' => $start,
				'end' => $grid_item['end'],
				'autoplay' => 1,
				'rel' => 0,
				'controls' => 1,
			];
			$embed_options = $this->get_embed_options( $grid_item );
			$lightbox_url = Embed::get_embed_url( $grid_item['youtube_url']['url'], $embed_url_params, $embed_options );
			$video_settings = $this->get_video_settings( $lightbox_url, $grid_item['video_type'] );

		}
		elseif ( 'vimeo' === $grid_item['video_type'] && $grid_item['vimeo_url']['url'] ) {
			$video_url = $grid_item['vimeo_url']['url'];
			$embed_url_params = [
				'loop' => 0,
				'dnt' => true,
				'muted' => 0,
				'title' => 1,
				'portrait' => 1,
				'byline' => 1,
				'autoplay' => 1,
				'rel' => 0,
				'controls' => 1,
			];
			$embed_options = $this->get_embed_options( $grid_item );
			$lightbox_url = Embed::get_embed_url( $grid_item['vimeo_url']['url'], $embed_url_params, $embed_options );
			$video_settings = $this->get_video_settings( $lightbox_url, $grid_item['video_type'] );
		}

		if( ! empty($video_settings) ){
			$video_settings['modalOptions'] = [
				'id' => 'elementor-lightbox-' . $grid_item['_id'],
				'videoAspectRatio' => $settings['aspect_ratio']
			];
		}

		return [
			'lightbox_url' => $lightbox_url,
			'video_url' => $video_url,
			'video_settings' => $video_settings
		];
	}

	public function get_hover_overlay_markup( $title, $subtitle ) {
		?>
			<div class="ha-metor-grid--overlay">
				<div class="ha-metor-grid--overlay-inner">
					<h2 class="ha-metor-grid--overlay-title">
						<?php echo esc_html( $title ); ?>
					</h2>
					<p class="ha-metor-grid--overlay-desc">
						<?php echo esc_html( $subtitle ); ?>
					</p>
				</div>
			</div>
		<?php
	}

	public function get_image_item_options( $image, $settings ) {
		$options = [];
		if ( empty( $image['url'] ) ) {
			$url = $image['image']['id'] ? wp_get_attachment_image_src( $image['image']['id'], 'full' )[0] : $image['image']['url'];
		} else {
			$url = $image['url'];
		}

		$lightbox_image_url = ( 'lightbox' === $settings['on_click'] && $image['image']['id'] ) ? wp_get_attachment_image_src( $image['image']['id'], 'full' )[0] : $url;

		if ( ! empty( $image['image']['id'] ) ) {
			$image_url = wp_get_attachment_image_src( $image['image']['id'], $image['thumbnail_size'] )[0];
		} else {
			$image_url = $image['image']['url'];
		}

		$options = [
			'image_url'          => $image_url,
			'lightbox_image_url' => $lightbox_image_url,
		];

		return $options;
	}

	public function get_attachment_image_html( $src, $alt = '', $class = '' ) {
		if ( $src ) {
			echo sprintf( '<img class="%s" src="%s" alt="%s">', esc_attr( $class ), esc_url( $src ), esc_attr( $alt ) );
		}
	}



	private function get_grid_definitions() {
		$grid_classes = [];
		//$grid_class['name'] = 'container pattern_name h-flip v-flip grid-item-number'; //h-flip=yes/no
		$grid_classes['lily']        = 'ha-metor-grid--pattern4x2 ha-metor-grid--layout-lily yes no 6';
		$grid_classes['daffodil']    = 'ha-metor-grid--pattern4x2 ha-metor-grid--layout-daffodil yes no 5';
		$grid_classes['lavender']    = 'ha-metor-grid--pattern4x2 ha-metor-grid--layout-lavender no no 5';
		$grid_classes['orchid']      = 'ha-metor-grid--pattern5x2 ha-metor-grid--layout-orchid no no 4';
		$grid_classes['wild-orchid'] = 'ha-metor-grid--pattern5x2 ha-metor-grid--layout-wild-orchid yes no 4';
		$grid_classes['poppy']       = 'ha-metor-grid--pattern5x2 ha-metor-grid--layout-poppy no no 7';
		$grid_classes['rachel']      = 'ha-metor-grid--pattern3x2 ha-metor-grid--layout-rachel yes no 3';
		$grid_classes['pippin']      = 'ha-metor-grid--pattern4x1 ha-metor-grid--layout-pippin yes no 3';
		$grid_classes['windy']       = 'ha-metor-grid--pattern4x4 ha-metor-grid--layout-windy yes no 8';
		$grid_classes['breezy']      = 'ha-metor-grid--pattern4x4 ha-metor-grid--layout-breezy no yes 5';
		$grid_classes['cathreen']    = 'ha-metor-grid--pattern3x3 ha-metor-grid--layout-cathreen no yes 5';
		$grid_classes['capricorn']   = 'ha-metor-grid--pattern10x4 ha-metor-grid--layout-capricorn yes yes 8';
		$grid_classes['europa']      = 'ha-metor-grid--pattern10x2 ha-metor-grid--layout-europa yes yes 6';
		$grid_classes['rondeletia']  = 'ha-metor-grid--pattern4x2 ha-metor-grid--layout-rondeletia yes yes 4';
		$grid_classes['bletilla']    = 'ha-metor-grid--pattern4x2 ha-metor-grid--layout-bletilla yes yes 5';
		$grid_classes['crepuscular'] = 'ha-metor-grid--pattern4x2 ha-metor-grid--layout-crepuscular yes yes 6';
		$grid_classes['clianthus']   = 'ha-metor-grid--pattern3x2 ha-metor-grid--layout-clianthus yes yes 3';
		$grid_classes['dandelion']   = 'ha-metor-grid--pattern8x3 ha-metor-grid--layout-dandelion yes yes 4';
		$grid_classes['lupin']       = 'ha-metor-grid--pattern4x3 ha-metor-grid--layout-lupin yes yes 8';

		return $grid_classes;
	}

	public function get_grid_class_name( $layout, $settings ) {
		$grid_classes      = $this->get_grid_definitions();
		$grid_class_parts  = explode( ' ', $grid_classes[ $layout ] );
		$grid_class        = $grid_class_parts[0] . ' ' . $grid_class_parts[1];

		return $grid_class;
	}

	public function get_grid_element_count( $layout ) {
		$grid_classes     = $this->get_grid_definitions();
		$grid_class_parts = explode( ' ', $grid_classes[ $layout ] );

		return $grid_class_parts[4];
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$layout     = $settings['layout'];
		$grid_class = $this->get_grid_class_name( $layout, $settings );

		$hover_disable = '';
		if ( 'yes' == $settings['enable_hover'] && 'yes' !== $settings['enable_hover_tablet'] ) {
			$hover_disable .= 'ha-metor-grid-tablet-hover-disable ';
		}
		if ( 'yes' == $settings['enable_hover'] && 'yes' !== $settings['enable_hover_mobile'] ) {
			$hover_disable .= 'ha-metor-grid-mobile-hover-disable';
		}

		$hover_class = 'ha-metor-grid--hover-' . $settings['hover_style'];

		$lightbox_policy = 'yes';
		if ( ha_elementor()->editor->is_edit_mode() || 'link' == $settings['on_click'] ) {
			$lightbox_policy = 'no';
		}

		$this->add_render_attribute('grid', [
			'class'   => [
				'ha-metor-grid-wrap',
				esc_attr( $hover_disable ),
			],
			'data-oc' => esc_attr( $settings['on_click'] ),
		] );
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'grid' ) ); ?>>
			<div class="ha-metor-grid-content <?php echo esc_attr( $grid_class ); ?>">
				<?php
				$counter            = 0;
				$number_of_elements = $this->get_grid_element_count( $layout ) * 1;

				foreach ( $settings['media_list'] as $image ) :

					$counter++;

					if ( 'video' == $image['media_type'] ) {
						$this->get_video_item_markup( $image, $settings, $lightbox_policy, $counter, $hover_class, $number_of_elements );
					} else {
						$this->get_image_item_markup( $image, $settings, $lightbox_policy, $counter, $hover_class, $number_of_elements );
					}
					$this->remove_render_attribute( 'image-overlay' );
				endforeach;
				?>
			</div>
		</div>
		<?php
	}

	public function grid_repeater_item( $counter, $number_of_elements ) {
		$repeat_num = (int) ( ( $counter - 1 ) / $number_of_elements );
		return 'style="--ha_metor_grid_repeat: ' . esc_attr( $repeat_num ) . '"';
	}

	public function get_video_item_markup( $image, $settings, $lightbox_policy, $counter, $hover_class, $number_of_elements ) {
				$options = $this->get_video_peram( $image, $settings );
				if ( ! empty( $options['lightbox_url'] ) && ! empty( $options['video_settings'] ) ) {
					$this->add_render_attribute( 'image-overlay', [
						'href' => esc_url( $options['video_url'] ),
						'target' => '__blank',
						'data-elementor-open-lightbox' => $lightbox_policy,
						'data-elementor-lightbox-video' => $options['lightbox_url'],
						'data-elementor-lightbox' => wp_json_encode($options['video_settings'] ),
						'data-e-action-hash' => \Elementor\Plugin::instance()->frontend->create_action_hash( 'lightbox', $options['video_settings'] ),
					] );
				}
		?>
			<article class="ha-metor-grid--item<?php echo esc_attr( $counter ); ?> <?php echo esc_attr( $hover_class ); ?> ha-metor-grid--item elementor-repeater-item-<?php echo esc_attr( $image['_id'] ); ?>" <?php echo( $this->grid_repeater_item( $counter, $number_of_elements ) ); ?> >
				<?php if ( ! empty( $options['lightbox_url'] ) && ! empty( $options['video_settings'] ) ) : ?>
					<a  <?php $this->print_render_attribute_string( 'image-overlay' ); ?>>
						<?php
						if ( $image['image_overlay'] && ( ! empty( $image['image_overlay']['url'] ) || ! empty( $image['image_overlay']['id'] ) ) ) :
							Group_Control_Image_Size::print_attachment_image_html( $image, 'image_overlay' );
						else :
							$this->get_attachment_image_html( ha_metro_grid_video_thumb( $image['video_type'], $options['video_url'] ) );
						endif;
						?>

						<div class="elementor-custom-embed-play" role="button">
							<?php Icons_Manager::render_icon( $settings['vid_play_icon'], [ 'aria-hidden' => 'true' ] ); ?>
							<span class="elementor-screen-only"><?php echo esc_html__( 'Play Video', 'happy-addons-pro' ); ?></span>
						</div>

						<?php
						if ( $settings['enable_hover'] && $settings['vid_enable_overlay'] == 'yes' ) {
							$this->get_hover_overlay_markup( $image['title'], $image['subtitle'] );
						}
						?>
					</a>
				<?php else: echo '<b>Please check that you have provided the correct URL.</b>';?>
				<?php endif; ?>
			</article>
		<?php
	}

	public function get_image_item_markup( $image, $settings, $lightbox_policy, $counter, $hover_class, $number_of_elements ) {
				$options = $this->get_image_item_options( $image, $settings );
		?>
			<article class="ha-metor-grid--item<?php echo esc_attr( $counter ); ?> <?php echo esc_attr( $hover_class ); ?> ha-metor-grid--item elementor-repeater-item-<?php echo esc_attr( $image['_id'] ); ?>" <?php echo( $this->grid_repeater_item( $counter, $number_of_elements ) ); ?> >
				<?php if ( $options['lightbox_image_url'] ) : ?>
					<?php
					$this->add_render_attribute( 'image-overlay', [
						'class'                        => 'ha-metor-grid--item-inner ha-metor-grid--trigger ' . esc_attr( $this->get_id() ),
						'target'                       => '__blank',
						'href'                         => esc_url( $options['lightbox_image_url'] ),
						'title'                        => esc_attr( $image['title'] ),
						'data-elementor-open-lightbox' => $lightbox_policy,
					] );
					?>
					<a <?php $this->print_render_attribute_string( 'image-overlay' ); ?>>
						<?php
						$this->get_attachment_image_html( $options['image_url'], $image['title'] );
						if ( $settings['enable_hover'] ) {
							$this->get_hover_overlay_markup( $image['title'], $image['subtitle'] );
						}
						?>
					</a>
				<?php endif; ?>
				</article>
		<?php
	}

}
