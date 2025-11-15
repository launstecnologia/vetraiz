<?php
/**
 * Title Tips widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Schemes\Typography;

defined( 'ABSPATH' ) || die();

class Title_Tips extends Base {
	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Title Tips', 'happy-addons-pro' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor-pro/happy-effects-pro/title-tips/';
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
		return 'hm hm-title-tips-vector-path';
	}

	public function get_keywords() {
		return ['title tips', 'text hover effect', 'text', 'hover', 'effect'];
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {

		$this->start_controls_section(
			'_section_title_tips_item',
			[
				'label' => __( 'Item', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'text_style',
			[
				'label'   => __( 'Text Style', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'underline' => __( 'Underline', 'happy-addons-pro' ),
					'flip'      => __( 'Flip', 'happy-addons-pro' ),
				],
				'default' => 'underline',
			]
		);

		$this->add_control(
			'image_style',
			[
				'label'   => __( 'Image Style', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'happy-addons-pro' ),
					'swap'      => __( 'Swap', 'happy-addons-pro' ),
				],
				'default' => 'default',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => __( 'Title', 'happy-addons-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Default title', 'happy-addons-pro' ),
				'placeholder' => __( 'Type your title here', 'happy-addons-pro' ),
				'dynamic'     => [ 'active' => true],
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'       => __( 'Description', 'happy-addons-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'placeholder' => __( 'Type your description here', 'happy-addons-pro' ),
				'default'     => __( 'Incredibly powerful widget pack for Elementor.', 'happy-addons-pro' ),
				'dynamic'     => [ 'active' => true],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Choose Image', 'happy-addons-pro' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'         => __( 'Link', 'happy-addons-pro' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'happy-addons-pro' ),
				'show_external' => true,
				'dynamic'       => [ 'active' => true],
			]
		);

		$this->add_control(
			'text_item_data',
			[
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[
						'title' => __( 'ThemeBucket', 'happy-addons-pro' ),
					],
					[
						'title' => __( 'HappyAddons', 'happy-addons-pro' ),
					],
					[
						'title' => __( 'EazyPlugins', 'happy-addons-pro' ),
					],
				],
			]
		);

		$this->add_control(
			'image_position',
			[
				'label'                => __( 'Image Position', 'happy-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'options'              => [
					'left'  => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-chevron-left',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-chevron-right',
					],
				],
				'toggle'               => false,
				'prefix_class'         => 'ha_title_tips_img_pos--',
				'default'              => 'right',
				'selectors_dictionary' => [
					'left'  => 'left: auto; right: 100%;',
					'right' => 'left: 100%; right: auto;',
				],
				'selectors'            => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-img-wrap' => '{{VALUE}};',
				],
				// 'condition' => [
				// 	'style!' => 'swap',
				// ],
			]
		);

		$this->add_control(
			'ha_title_tips_animation',
			[
				'label'       => esc_html__( 'Animation', 'textdomain' ),
				'none'        => true,
				'type'        => \Elementor\Controls_Manager::ANIMATION,
				'render_type' => 'template',
				// 'prefix_class' => 'animated ',
				'condition' => [
					'image_style!' => 'swap',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->_text_item_style_controls();
		$this->_image_style_controls();
	}

	protected function _text_item_style_controls() {

		$this->start_controls_section(
			'_section_text_item_style',
			[
				'label' => __( 'Text Item', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'text_gap',
			[
				'label'      => __( 'Item Gap', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				// 'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
					// '%'  => [
					// 	'min' => 0,
					// 	'max' => 100,
					// ],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item:not(:last-of-type)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_gap',
			[
				'label'      => __( 'Description Gap', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				// 'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
					// '%'  => [
					// 	'min' => 0,
					// 	'max' => 100,
					// ],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item .ha-title-tips-desc' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => __( 'Title Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item .ha-title-tips-txt',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => __( 'Description Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item .ha-title-tips-desc',
			]
		);

		$this->start_controls_tabs( '_tabs_underline' );

		$this->start_controls_tab(
			'_tab_content_under_line_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'text_color_normal',
			[
				'label'     => __( 'Title Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item a .ha-title-tips-txt' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'desc_color_normal',
			[
				'label'     => __( 'Description Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item a .ha-title-tips-desc' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'under_line_color_normal',
			[
				'label'     => __( 'Underline Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item a .ha-title-tips-txt::after' => 'background: {{VALUE}}',
				],
				'condition' => [
					'text_style' => 'underline',
				],
			]
		);

		$this->add_responsive_control(
			'under_line_height_normal',
			[
				'label'      => __( 'Underline Height', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item a .ha-title-tips-txt::after' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'text_style' => 'underline',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_content_under_line_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label'     => __( 'Title Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item a:hover .ha-title-tips-txt' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'desc_color_hover',
			[
				'label'     => __( 'Description Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item a:hover .ha-title-tips-desc' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'under_line_color_hover',
			[
				'label'     => __( 'Underline Color', 'happy-addons-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item a:hover .ha-title-tips-txt::after' => 'background: {{VALUE}}',
				],
				'condition' => [
					'text_style' => 'underline',
				],
			]
		);

		$this->add_responsive_control(
			'under_line_height_hover',
			[
				'label'      => __( 'Underline Height', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-hover-item a:hover .ha-title-tips-txt::after' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'text_style' => 'underline',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function _image_style_controls() {

		$this->start_controls_section(
			'_section_image_style',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_box_margin',
			[
				'label' => __( 'Margin', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-title-tips-img-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'image_style!' => 'swap',
				],
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label'      => __( 'Height', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				// 'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					// '%'  => [
					// 	'min' => 0,
					// 	'max' => 100,
					// ],
				],
				'selectors'  => [
					// '{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-img' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-title-tips-hover span.ha-title-tips-img-wrap' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} span.ha-title-tips-img-wrap' => '--ha-title-tips-image-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'image_style!' => 'swap',
				],
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'      => __( 'Width', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				// 'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					// '%'  => [
					// 	'min' => 0,
					// 	'max' => 100,
					// ],
				],
				'selectors'  => [
					// '{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-title-tips-hover span.ha-title-tips-img-wrap' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-title-tips-hover figure' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-title-tips-hover figure img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'label'    => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-img, {{WRAPPER}} .ha-title-tips-hover figure img',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-img, {{WRAPPER}} .ha-title-tips-hover figure img',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .ha-title-tips-hover .ha-title-tips-img img, {{WRAPPER}} .ha-title-tips-hover figure img',
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$animation = '';
		if ( $settings['ha_title_tips_animation'] || 'none' == $settings['ha_title_tips_animation'] ) {
			$animation = "style= --ha-title-tips-image-animation:" . esc_attr( $settings['ha_title_tips_animation'] ) . ";";
		}
		$wrap_animation = ( 'none' == $settings['ha_title_tips_animation'] ) ? "style=transition:none" : '';

		$this->add_render_attribute(
			'hover-wrap',
			'class',
			[
				'ha-title-tips-hover',
				$settings['text_style'],
				$settings['image_style'],
			]
		);
		?>
		<div <?php $this->print_render_attribute_string( 'hover-wrap' ); ?>>
			<ul>
				<?php foreach ( $settings['text_item_data'] as $key => $item ) : ?>
					<li class="ha-title-tips-hover-item <?php echo $item['image']['url'] ? 'have-image': 'no-image'; ?>">
						<a <?php
							echo ( $item['link']['url'] ? ' href="'. esc_url( $item['link']['url'] ) .'"' : '' );
							echo ( $item['link']['is_external'] ? ' target="_blank"' : '' );
							echo ( $item['link']['nofollow'] ? ' rel="nofollow"' : '' );
							echo ( $item['image']['id'] ? ' data-filter-id="'. esc_attr( $item['image']['id'] ) .'"' : ' data-filter-id="'. esc_attr( $key ) .'"' ); ?>
							>
							<span class="ha-title-tips-txt">
								<?php $this->{'_' . $settings['text_style']}( $item['title'] ); ?>

								<?php if ( 'swap' != $settings['image_style'] ) : ?>
								<span class="ha-title-tips-img-wrap" <?php echo esc_html( $wrap_animation ); ?>>
									<span class="ha-title-tips-img-inner" <?php echo esc_html( $animation ); ?>>
										<span class="ha-title-tips-img">
											<img src="<?php echo esc_url( $item['image']['url'] ); ?>" alt="">
										</span>
									</span>
								</span>
								<?php endif; ?>

							</span>

							<?php if ( ! empty( $item['description'] ) ) : ?>
								<p class="ha-title-tips-desc"><?php echo esc_html( $item['description'] ); ?></p>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( 'swap' == $settings['image_style'] ) : ?>
				<figure style="transform: translate3d(0, 0, 0);">
					<?php foreach ( $settings['text_item_data'] as $key => $item ) : ?>
						<?php if ( $item['image']['url'] ) : ?>
						<?php $filter = $item['image']['id'] ? $item['image']['id'] : $key; ?>
							<img src="<?php echo esc_url( $item['image']['url'] ); ?>" data-filter-id="<?php echo esc_attr( $filter ); ?>">
						<?php endif; ?>
					<?php endforeach; ?>
				</figure>
			<?php endif; ?>
		</div>
		<?php
	}

	public function _underline( $item_title ) {
		echo esc_html( $item_title );
	}

	public function _flip( $item_title ) {
		?>
		<span class="ha-title-tips-txt-flip-aria">
			<span class="ha-title-tips-txt-normal">
				<?php echo esc_html( $item_title ); ?>
			</span>
			<span class="ha-title-tips-txt-flip">
				<?php echo esc_html( $item_title ); ?>
			</span>
		</span>
		<?php
	}

}
