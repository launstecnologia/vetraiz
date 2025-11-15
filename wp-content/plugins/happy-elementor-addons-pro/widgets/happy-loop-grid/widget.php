<?php

/**
 * Happy Loop Grid widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Widget;

use WP_Query;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Happy_Addons_Pro\Lazy_Query_Manager;
use Happy_Addons_Pro\Controls\Lazy_Select;
use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use Happy_Addons_Pro\Traits\Post_Grid_Markup_New;

defined( 'ABSPATH' ) || die();

class Happy_Loop_Grid extends Base {
	public $query;

	use Lazy_Query_Builder;
	use Post_Grid_Markup_New;

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Happy Loop Grid', 'happy-addons-pro' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor-pro/happy-effects-pro/happy-loop-grid/';
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
		return 'hm hm-loop-grid-content-slider';
	}

	public function get_keywords() {
		return ['post', 'posts', 'portfolio', 'grid', 'tiles', 'query', 'blog', 'loop'];
	}

	// public function get_categories() {
    //     return [ 'happy_addons_pro_category', 'happy_addons_theme_builder' ];
    // }

	/**
	 * Register content related controls
	 */
	protected function register_content_controls() {

		//Layout
		$this->layout_content_tab_controls();

		//Query content
		$this->query_content_tab_controls();

		//Paginations content
		$this->pagination_content_tab_controls();
	}

	/**
	 * Layout content controls
	 */
	protected function layout_content_tab_controls() {

		$this->start_controls_section(
			'_section_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT
			]
		);

		$this->add_control(
			'template_post_type',
			[
				'label'   => esc_html__( 'Template Post Type', 'happy-addons-pro' ),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'ha_library'
			]
		);

		$this->add_control(
			'template_id',
			[
				'label'       => __( 'Choose a Loop Template', 'happy-addons-pro' ),
				'description' => sprintf(
					__( 'Wondering what is loop template or need to create one? Please click %1$shere%2$s', 'happy-addons-pro' ),
					'<a target="_blank" href="' . esc_url( admin_url( 'edit.php?post_type=ha_library&ha_library_type=loop-template' ) ) . '">',
					'</a>'
				),
				'type'        => Lazy_Select::TYPE,
				'multiple'    => false,
				'label_block' => true,
				'placeholder' => __( 'Type and select loop template', 'happy-addons-pro' ),
				'lazy_args'   => [
					'query'         => Lazy_Query_Manager::QUERY_TEMPLATES,
					'widget_props'  => [
						'post_type' => 'template_post_type'
					],
					'template_type' => 'loop-template'
				]
			]
		);

		$this->add_control(
			'ha_template_create_btn',
			[
				'label'       => esc_html__( 'Create Template', 'happy-addons-pro' ),
				'show_label'  => false,
				'label_block' => true,
				'type'        => Controls_Manager::RAW_HTML,
				'raw'         => '<a target="_blank" class="elementor-button elementor-button-default" href="' . esc_url( admin_url( 'edit.php?post_type=ha_library&ha_library_type=loop-template' ) ) . '">' . esc_html__( 'Create Template', 'happy-addons-pro' ) . '</a>',
				'condition'   => [
					'template_id' => ['', null]
				]
			]
		);

		$this->add_control(
			'ha_template_edit_btn',
			[
				'label'       => esc_html__( 'Edit Template', 'happy-addons-pro' ),
				'show_label'  => false,
				'label_block' => true,
				'type'        => Controls_Manager::BUTTON,
				'button_type' => 'default',
				'text'        => esc_html__( 'Edit Template', 'happy-addons-pro' ),
				'event'       => 'happyAddons:Template:edit',
				'condition'   => [
					'template_id!' => ['', null]
				]
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'              => __( 'Columns', 'happy-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6'
				],
				'prefix_class'       => 'ha-hlg-grid%s-',
				'frontend_available' => true,
				'selectors'          => [
					'{{WRAPPER}} .ha-hlg-grid-wrap' => 'grid-template-columns: repeat( {{VALUE}}, 1fr );'
				]
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => __( 'Posts Per Page', 'happy-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3
			]
		);

		$this->add_control(
			'equal_height',
			[
				'label' => esc_html__( 'Equal height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'happy-addons-pro' ),
				'label_on' => esc_html__( 'On', 'happy-addons-pro' ),
				'condition' => [
					'columns!' => '1',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-grid-wrap' => 'grid-auto-rows: 1fr',
					'{{WRAPPER}} .ha-hlg-grid-wrap .ha-hlg-grid-item > .elementor, {{WRAPPER}} .ha-hlg-grid-wrap .ha-hlg-grid-item > .elementor .elementor-section, {{WRAPPER}} .ha-hlg-grid-wrap .ha-hlg-grid-item > .elementor .elementor-container, {{WRAPPER}} .ha-hlg-grid-wrap .ha-hlg-grid-item > .elementor .e-con' => 'height: 100%',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Query content controls
	 */
	protected function query_content_tab_controls() {

		//Query
		$this->start_controls_section(
			'_section_query',
			[
				'label' => __( 'Query', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT
			]
		);

		$this->register_query_controls();

		$this->add_control(
			'query_id',
			[
				'label'       => __( 'Query ID', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'dynamic'     => ['active' => true],
				'description' => __( 'Give your Query a custom unique id to allow server side filtering.', 'happy-addons-pro' )
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Paginations content controls
	 */
	protected function pagination_content_tab_controls() {

		//Pagination
		$this->start_controls_section(
			'_section_pagination',
			[
				'label' => __( 'Pagination & Load More', 'happy-addons-pro' )
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label'   => __( 'Pagination', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''                      => __( 'None', 'happy-addons-pro' ),
					'numbers'               => __( 'Numbers', 'happy-addons-pro' ),
					'prev_next'             => __( 'Previous/Next', 'happy-addons-pro' ),
					'numbers_and_prev_next' => __( 'Numbers', 'happy-addons-pro' ) . ' + ' . __( 'Previous/Next', 'happy-addons-pro' )
				]
			]
		);

		$this->add_control(
			'pagination_page_limit',
			[
				'label'     => __( 'Page Limit', 'happy-addons-pro' ),
				'default'   => '5',
				'condition' => [
					'pagination_type!' => ''
				]
			]
		);

		$this->add_control(
			'pagination_numbers_shorten',
			[
				'label'     => __( 'Shorten', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'condition' => [
					'pagination_type' => [
						'numbers',
						'numbers_and_prev_next'
					]
				]
			]
		);

		$this->add_control(
			'pagination_prev_label',
			[
				'label'     => __( 'Previous Label', 'happy-addons-pro' ),
				'default'   => __( '&laquo; Previous', 'happy-addons-pro' ),
				'condition' => [
					'pagination_type' => [
						'prev_next',
						'numbers_and_prev_next'
					]
				]
			]
		);

		$this->add_control(
			'pagination_next_label',
			[
				'label'     => __( 'Next Label', 'happy-addons-pro' ),
				'default'   => __( 'Next &raquo;', 'happy-addons-pro' ),
				'condition' => [
					'pagination_type' => [
						'prev_next',
						'numbers_and_prev_next'
					]
				]
			]
		);

		$this->add_control(
			'loadmore',
			[
				'label'        => __( 'Load More Button', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-addons-pro' ),
				'label_off'    => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'pagination_type' => ''
				]
			]
		);

		$this->add_control(
			'loadmore_text',
			[
				'label'     => __( 'Load More Text', 'happy-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'happy-addons-pro' ),
				'condition' => [
					'pagination_type' => '',
					'loadmore'        => 'yes'
				]
			]
		);

		$this->add_control(
			'pagination_align',
			[
				'label'      => __( 'Alignment', 'happy-addons-pro' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left'
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center'
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right'
					]
				],
				// 'default' => 'center',
				'selectors'  => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap'   => 'text-align: {{VALUE}};'
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'pagination_type',
							'operator' => '!=',
							'value'    => ''

						],
						[
							'name'     => 'loadmore',
							'operator' => '===',
							'value'    => 'yes'

						]
					]
				]
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register styles related controls
	 */
	protected function register_style_controls() {

		//Laout Style Start
		$this->layout_style_tab_controls();

		$this->item_box_style_tab_controls();

		//Pagination Style Start
		$this->pagination_style_tab_controls();
	}

	/**
	 * Layout Style controls
	 */
	protected function layout_style_tab_controls() {

		$this->start_controls_section(
			'_section_layout_style',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => __( 'Columns Gap', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 30
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-grid-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}}'
				]
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => __( 'Rows Gap', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 35
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-grid-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}}'
				]
			]
		);

		$this->end_controls_section();
	}

	protected function item_box_style_tab_controls() {
		$this->start_controls_section(
			'_section_item_box_style',
			[
				'label' => __( 'Item Box', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'item_padding',
			[
				'label' => esc_html__( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-grid-wrap .ha-hlg-grid-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'item_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-hlg-grid-wrap .ha-hlg-grid-item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .ha-hlg-grid-wrap .ha-hlg-grid-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_border',
				'selector' => '{{WRAPPER}} .ha-hlg-grid-wrap .ha-hlg-grid-item',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-grid-wrap .ha-hlg-grid-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();
	}

	/**
	 * Paginations Style controls
	 */
	protected function pagination_style_tab_controls() {

		$this->start_controls_section(
			'_section_pagination_style',
			[
				'label'      => __( 'Pagination', 'happy-addons-pro' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'pagination_type',
							'operator' => '!=',
							'value'    => ''

						],
						[
							'name'     => 'loadmore',
							'operator' => '===',
							'value'    => 'yes'

						]
					]
				]
			]
		);

		$this->add_responsive_control(
			'pagination_margin',
			[
				'label'      => __( 'Margin', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap'   => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'pagination_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap .ha-hlg-loadmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'pagination_spacing',
			[
				'label'     => __( 'Space Between', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				// 'separator' => 'before',
				'default'   => [
					'size' => 10
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'pagination_type!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_typography',
				'selector' => '{{WRAPPER}} .ha-hlg-pagination-wrap, {{WRAPPER}} .ha-hlg-loadmore-wrap'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'pagination_border',
				'label'    => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers, {{WRAPPER}} .ha-hlg-loadmore-wrap .ha-hlg-loadmore',
				'exclude'  => ['color']
			]
		);

		$this->add_responsive_control(
			'pagination_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap .ha-hlg-loadmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->start_controls_tabs( 'pagination_tabs' );

		$this->start_controls_tab(
			'pagination_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' )
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap .ha-hlg-loadmore' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'pagination_bg_color',
			[
				'label'     => __( 'Background', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap .ha-hlg-loadmore' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'pagination_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap .ha-hlg-loadmore' => 'border-color: {{VALUE}}'
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_hover',
			[
				'label' => __( 'Hover & Active', 'happy-addons-pro' )
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers:not([class~=dots]):hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers.current'                  => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap .ha-hlg-loadmore:hover'                    => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'pagination_bg_hover_color',
			[
				'label'     => __( 'Background', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers:not([class~=dots]):hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers.current'                  => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap .ha-hlg-loadmore:hover'                    => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'pagination_border_hover_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers:not([class~=dots]):hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-hlg-pagination-wrap .page-numbers.current'                  => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-hlg-loadmore-wrap .ha-hlg-loadmore:hover'                    => 'border-color: {{VALUE}}'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get Query
	 *
	 * @param array $args
	 * @return void
	 */
	public function get_query( $args = [] ) {

		$default = $this->get_post_query_args();
		$args    = array_merge( $default, $args );

		$this->query = new WP_Query( $args );

		return $this->query;
	}

	/**
	 * Get post query arguments
	 *
	 * @return function
	 */
	public function get_post_query_args() {

		return $this->get_query_args();
	}

	/**
	 * Get current page number
	 *
	 * @return init
	 */
	public function get_current_page() {
		if ( '' === $this->get_settings_for_display( 'pagination_type' ) ) {
			return 1;
		}

		return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
	}

	/**
	 * Get page number link
	 *
	 * @param [init] $i
	 * @return string
	 */
	private function get_wp_link_page( $i ) {
		if ( ! is_singular() || is_front_page() ) {
			return get_pagenum_link( $i );
		}

		// Based on wp-includes/post-template.php:957 `_wp_link_page`.
		global $wp_rewrite;
		$post       = get_post();
		$query_args = [];
		$url        = get_permalink();

		if ( $i > 1 ) {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, ['draft', 'pending'] ) ) {
				$url = add_query_arg( 'page', $i, $url );
			} else if ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( $url ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
			} else {
				$url = trailingslashit( $url ) . user_trailingslashit( 'page' . $i, 'single_paged' ); // Change Occurs For Fixing Pagination Issue.
			}
		}

		if ( is_preview() ) {
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
				$query_args['preview_id']    = wp_unslash( $_GET['preview_id'] );
				$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
			}

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;
	}

	/**
	 * Get post navigation link
	 *
	 * @param [init] $page_limit
	 * @return string
	 */
	public function get_posts_nav_link( $page_limit = null ) {
		if ( ! $page_limit ) {
			// return;
			$page_limit = $this->query->max_num_pages; // Change Occurs For Fixing Pagination Issue.
		}

		$return = [];

		// $paged = $this->get_current_page();
		$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

		$link_template     = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;
			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', $this->get_wp_link_page( $next_page ), $this->get_settings_for_display( 'pagination_prev_label' ) );
		}

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', $this->get_wp_link_page( $next_page ), $this->get_settings_for_display( 'pagination_next_label' ) );
		}

		return $return;
	}

	/**
	 * Pagination render
	 *
	 * @param [array] $_query
	 * @return void
	 */
	public function pagination_render( $_query ) {

		$parent_settings = $this->get_settings_for_display();
		if ( '' === $parent_settings['pagination_type'] ) {
			return;
		}

		$page_limit = $_query->max_num_pages;
		if ( '' !== $parent_settings['pagination_page_limit'] ) {
			$page_limit = min( $parent_settings['pagination_page_limit'], $page_limit );
		}

		if ( 2 > $page_limit ) {
			return;
		}

		$has_numbers   = in_array( $parent_settings['pagination_type'], ['numbers', 'numbers_and_prev_next'] );
		$has_prev_next = in_array( $parent_settings['pagination_type'], ['prev_next', 'numbers_and_prev_next'] );

		$links = [];

		if ( $has_numbers ) {
			$paginate_args = [
				'type'               => 'array',
				'current'            => $this->get_current_page(),
				'total'              => $page_limit,
				'prev_next'          => false,
				'show_all'           => 'yes' !== $parent_settings['pagination_numbers_shorten'],
				'before_page_number' => '<span class="elementor-screen-only">' . __( 'Page', 'happy-addons-pro' ) . '</span>'
			];

			if ( is_singular() && ! is_front_page() ) {
				global $wp_rewrite;
				if ( $wp_rewrite->using_permalinks() ) {
					$paginate_args['base']   = trailingslashit( get_permalink() ) . '%_%';
					$paginate_args['format'] = user_trailingslashit( 'page%#%', 'single_paged' ); // Change Occurs For Fixing Pagination Issue.
				} else {
					$paginate_args['format'] = '?page=%#%';
				}
			}

			$links = paginate_links( $paginate_args );
		}

		if ( $has_prev_next ) {
			$prev_next = $this->get_posts_nav_link( $page_limit );
			if ( isset( $prev_next['prev'] ) ) {
				array_unshift( $links, $prev_next['prev'] );
			}
			if ( isset( $prev_next['next'] ) ) {
				$links[] = $prev_next['next'];
			}
		}

	?>
		<nav class="ha-hlg-pagination-wrap" role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'happy-addons-pro' );?>">
			<?php echo implode( PHP_EOL, $links ); ?>
		</nav>
	<?php
	}

	/**
	 * Load more render
	 *
	 * @param [array] $query_settings
	 * @return void
	 */
	public function load_more_render( $query_settings ) {

		$settings = $this->get_settings_for_display();
		if ( empty( $settings['loadmore'] ) || empty( $settings['loadmore_text'] ) ) {
			return;
		}
	?>
		<div class="ha-hlg-loadmore-wrap">
			<button class="ha-hlg-loadmore" data-settings="<?php echo esc_attr( $query_settings ); ?>">
				<?php echo esc_html( $settings['loadmore_text'] ); ?>
				<i class="eicon-loading eicon-animation-spin"></i>
			</button>
		</div>
	<?php
	}

	/**
	 * Render content
	 */
	public function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'grid-wrapper',
			'class',
			[
				'ha-hlg-wrapper',
				'ha-hlg-default'
			]
		);
		// $args = $this->get_query_args();
		$args = $this->get_post_query_args();

		$args['posts_per_page'] = $settings['posts_per_page'];
		if ( '' !== $settings['pagination_type'] ) {
			$args['paged'] = $this->get_current_page();
		}

		//define ha Happy Loop Grid custom query filter hook
		if ( ! empty( $settings['query_id'] ) ) {
			$args = apply_filters( "happyaddons/happy-loop-grid/{$settings['query_id']}", $args );
		}

		if( empty( $settings['template_id'] ) ) {
			echo '<div style="display:flex;justify-content:space-between;align-items:center;margin:1rem;padding:1rem 1.25rem;border-left:5px solid #f5c848;color:#856404;background-color:#fff3cd;">';
			echo '<span>'. esc_html__( 'Happy Loop Grid requires a Loop Template to work. Please choose a loop template.', 'happy-addons-pro' ) .'</span>';
			echo '<a class="hlg-create-template" style="background:#f5c848;color:#000;padding:10px 20px;border-radius:0.25rem;font-weight:500;" href="'.esc_url( admin_url( 'edit.php?post_type=ha_library&ha_library_type=loop-template' ) ).'">'. esc_html__( 'Create Template', 'happy-addons-pro' ) .'</a>';
			echo '</div>';
			return;
		}

		$_query = new WP_Query( $args );

		$query_settings = $this->query_settings( $settings, $args );
		$secondary['template_id'] = apply_filters('wpml_object_id', $settings['template_id'], 'ha_library');

		if ( $_query->have_posts() ) { ?>
			<div <?php $this->print_render_attribute_string( 'grid-wrapper' );?>>
				<div class="ha-hlg-grid-wrap">
					<?php
					while ( $_query->have_posts() ) {
						$_query->the_post();
						echo "<div class='ha-hlg-grid-item'>";
						echo ha_elementor()->frontend->get_builder_content_for_display( $settings['template_id'] );
						echo "</div>";
					}
					wp_reset_postdata();
					?>
				</div>
				<?php
				if ( '' === $settings['pagination_type'] ) {
					$this->load_more_render( $query_settings );
				} else {
					$this->pagination_render( $_query );
				}
				?>
			</div>
		<?php
		}
	}

	public function query_settings( $settings, $args ) {

		$query_settings = [
			'args'            => $args,
			'posts_post_type' => $settings['posts_post_type'],
			'template_id'     => $settings['template_id'],
		];

		$query_settings = json_encode( $query_settings, true );

		return $query_settings;
	}
}
