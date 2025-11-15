<?php
/**
 * WPML integration and compatibility manager
 */
namespace Happy_Addons_Pro;

use Happy_Addons\Elementor\Widgets_Cache;
use Happy_Addons\Elementor\Assets_Cache;

defined( 'ABSPATH' ) || die();

class WPML_Manager {

	public static function init() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ __CLASS__, 'add_widgets_to_translate' ] );
	}

	public static function load_integration_files() {
		// Load repeatable module class
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'classes/wpml-module-with-items.php' );

		// Load widget integration
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/accordion.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/advanced-tabs.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/animated-text.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/business-hour.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/feature-list.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/hotspots.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/list-group.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/line-chart.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/pie-chart.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/polar-chart.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/price-menu.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/pricing-table.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/radar-chart.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/scrolling-image.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/testimonial-carousel.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/team-carousel.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/timeline.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/toggle.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/advanced-slider.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/creative-slider.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/google-map.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/logo-carousel.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/off-canvas.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/one-page-nav.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/advanced-comparison-table.php' );
	}

	public static function add_widgets_to_translate( $widgets ) {
		self::load_integration_files();

		$widgets_map = [
			'accordion' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Accordion',
			],
			'advanced-data-table' => [
				'fields' => [
					[
						'field' => 'export_table_text',
						'type'        => __( 'Advanced Data Table: Export Table', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				]
			],
			'advanced-heading' => [
				'fields' => [
					[
						'field' => 'heading_before',
						'type'        => __( 'Advanced Heading: Before Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'heading_center',
						'type'        => __( 'Advanced Heading: Center Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'heading_after',
						'type'        => __( 'Advanced Heading: After Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'background_text',
						'type'        => __( 'Advanced Heading: Background Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					'link' => [
						'field'       => 'url',
						'type'        => __( 'Advanced Heading: Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
				]
			],
			'author-list' => [
				'fields' => [
					[
						'field' => 'author_post_count_text',
						'type'        => __( 'Author List: Post Count Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				]
			],
			'breadcrumbs' => [
				'fields' => [
					[
						'field' => 'home',
						'type'        => __( 'Breadcrumbs: Homepage', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'page_title',
						'type'        => __( 'Breadcrumbs: Pages', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'search',
						'type'        => __( 'Breadcrumbs: Search', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'error_404',
						'type'        => __( 'Breadcrumbs: Error 404', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'separator_text',
						'type'        => __( 'Breadcrumbs: Separator', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					]
				]
			],
			'creative-slider' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Creative_Slider',
			],
			'google-map' => [
				'fields' => [
					[
						'field' => 'legend_title',
						'type'        => __( 'Advanced Google Map: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					]
				],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Google_Map_Ha_Gmap_Polylines',
					__NAMESPACE__ . '\\WPML_Google_Map_List_Legend',
					__NAMESPACE__ . '\\WPML_Google_Map_List_Marker',
				]
			],
			'logo-carousel' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Logo_Carousel',
				]
			],
			'off-canvas' => [
				'fields' => [
					[
						'field' => 'button_text',
						'type'        => __( 'Off Canvas: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'burger_label',
						'type'        => __( 'Off Canvas: Label', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'select_close_button_title',
						'type'        => __( 'Off Canvas: Close Icon Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'close_bar_button',
						'type'        => __( 'Off Canvas: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'close_bar_text',
						'type'        => __( 'Off Canvas: Link Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					'close_bar_link' => [
						'field' => 'url',
						'type'        => __( 'Off Canvas: Link for Logo/Button/Text', 'happy-addons-pro' ),
						'editor_type' => 'LINK'
					]
				],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Off_Canvas',
				]
			],
			'one-page-nav' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_One_Page_Nav',
				]
			],
			'edd-cart' => [
				'fields' => [
					[
						'field' => 'btn_text',
						'type'        => __( 'EDD Cart: Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					]
				],
			],
			'edd-category-grid' => [
				'fields' => [
					[
						'field' => 'load_more_text',
						'type'        => __( 'EDD Category Grid: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					]
				],
			],
			'edd-checkout' => [
				'fields' => [
					[
						'field' => 'btn_text',
						'type'        => __( 'EDD Checkout: Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					]
				],
			],
			'edd-login' => [
				'fields' => [
					'redirect_url' => [
						'field'       => 'url',
						'type'        => __( 'EDD Login: Redirect URL After Login', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
				],
			],
			'edd-product-carousel' => [
				'fields' => [
					[
						'field'       => 'add_to_cart_text',
						'type'        => __( 'EDD Product Carousel: Add To Cart Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'product_sale_badge',
						'type'        => __( 'EDD Product Carousel: Badge Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],
			'edd-product-grid' => [
				'fields' => [
					[
						'field'       => 'badge_label',
						'type'        => __( 'EDD Product Grid: Badge label', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'cart_btn_label',
						'type'        => __( 'EDD Product Grid: Add To Cart Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'quick_view_text',
						'type'        => __( 'EDD Product Grid: Quick View Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'load_more_text',
						'type'        => __( 'EDD Product Grid: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					'load_more_link' => [
						'field'       => 'url',
						'type'        => __( 'EDD Product Grid: Button URL', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
				],
			],
			'edd-register' => [
				'fields' => [
					'redirect_url' => [
						'field'       => 'url',
						'type'        => __( 'EDD Register: Redirect URL', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
				],
			],
			'edd-single-product' => [
				'fields' => [
					[
						'field'       => 'badge_text',
						'type'        => __( 'EDD Single Product: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'discount_text',
						'type'        => __( 'EDD Single Product: Discount Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'add_to_cart_text',
						'type'        => __( 'EDD Single Product: Add To Cart Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],
			'product-carousel-new' => [
				'fields' => [
					[
						'field'       => 'add_to_cart_text',
						'type'        => __( 'Product Carousel: Add To Cart Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],
			'product-categroy-grid-new' => [
				'fields' => [
					[
						'field'       => 'load_more_text',
						'type'        => __( 'Product Category Grid: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					'load_more_link' => [
						'field'       => 'url',
						'type'        => __( 'Product Category Grid: Button URL', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					]
				],
			],
			'product-grid-new' => [
				'fields' => [
					[
						'field'       => 'add_to_cart_text',
						'type'        => __( 'Product Grid: Add To Cart Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'quick_view_text',
						'type'        => __( 'Product Grid: Quick View Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'load_more_text',
						'type'        => __( 'Product Grid: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					'load_more_link' => [
						'field'       => 'url',
						'type'        => __( 'Product Grid: Button URL', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
				],
			],
			'single-product-new' => [
				'fields' => [
					[
						'field'       => 'badge_text',
						'type'        => __( 'Single Product: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'discount_text',
						'type'        => __( 'Single Product: Discount Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'add_to_cart_text',
						'type'        => __( 'Single Product: Add To Cart Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],
			'facebook-feed' => [
				'fields' => [
					[
						'field'       => 'read_more_text',
						'type'        => __( 'Facebook Feed: Read More Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'load_more_text',
						'type'        => __( 'Facebook Feed: Load More Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],
			'modal-popup' => [
				'fields' => [
					[
						'field'       => 'button',
						'type'        => __( 'Modal Popup: Button', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'modal_title',
						'type'        => __( 'Modal Popup: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'modal_description',
						'type'        => __( 'Modal Popup: Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					]
				],
			],
			'table-of-contents' => [
				'fields' => [
					[
						'field'       => 'widget_title',
						'type'        => __( 'Table of Contents: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],
			'twitter-carousel' => [
				'fields' => [
					[
						'field'       => 'read_more_text',
						'type'        => __( 'Twitter Feed Carousel: Read More Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],
			'nav-menu' => [
				'fields' => [
					'ha_nav_menu_logo_link' => [
						'field'       => 'url',
						'type'        => __( 'Happy Menu: Custom Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					]
				],
			],
			'sticky-video' => [
				'fields' => [
					'remote_url' => [
						'field'       => 'url',
						'type'        => __( 'Sticky Video: Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					]
				],
			],
			'list-group' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_List_Group',
			],
			'feature-list' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Feature_List',
			],
			'flip-box' => [
				'fields' => [
					[
						'field' => 'front_title',
						'type'        => __( 'Flip Box: Before Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'front_description',
						'type'        => __( 'Flip Box: Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA'
					],
					[
						'field' => 'back_title',
						'type'        => __( 'Flip Box: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'back_description',
						'editor_type' => 'AREA',
						'type'        => __( 'Flip Box: Description', 'happy-addons-pro' ),
					],
					[
						'field' => 'button_text',
						'type'        => __( 'Flip Box: Button', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					'link' => [
						'field'       => 'url',
						'type'        => __( 'Flip Box: Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
				]
			],
			'hotspots' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Hotspots',
			],
			'hover-box' => [
				'fields' => [
					[
						'field' => 'title',
						'type'        => __( 'Hover Box: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'sub_title',
						'type'        => __( 'Hover Box: Sub Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'detail',
						'type'        => __( 'Hover Box: Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA'
					],
				]
			],
			'line-chart' => [
				'fields' => [
					[
						'field' => 'labels',
						'type'        => __( 'Line Chart: Labels', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Line_Chart',
			],
			'pie-chart' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Pie_Chart',
			],
			'polar-chart' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Polar_Chart',
			],
			'promo-box' => [
				'fields' => [
					[
						'field' => 'before_title',
						'type'        => __( 'Promo Box: Before Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'title',
						'type'        => __( 'Promo Box: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					'promo_link' => [
						'field'       => 'url',
						'type'        => __( 'Promo Box: Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
					[
						'field' => 'after_title',
						'type'        => __( 'Promo Box: After Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'description',
						'type'        => __( 'Promo Box: Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA'
					],
					[
						'field' => 'button_text',
						'type'        => __( 'Promo Box: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					'button_link' => [
						'field'       => 'url',
						'type'        => __( 'Promo Box: Button Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
					[
						'field' => 'badge_text_offer',
						'type'        => __( 'Promo Box: Badge Offer', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'badge_text_detail',
						'type'        => __( 'Promo Box: Badge Description', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				]
			],
			'radar-chart' => [
				'fields' => [
					[
						'field' => 'labels',
						'type'        => __( 'Radar Chart: Labels', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Radar_Chart',
			],
			'team-carousel' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Team_Carousel',
			],
			'testimonial-carousel' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Testimonial_Carousel',
			],
			/**
			 * Countdown
			 */
			'countdown' => [
				'fields' => [
					[
						'field' => 'label_days',
						'type' => __( 'Countdown: Label Days', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'label_hours',
						'type' => __( 'Countdown: Label Hours', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'label_minutes',
						'type' => __( 'Countdown: Label Minutes', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'label_seconds',
						'type' => __( 'Countdown: Label Seconds', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'separator',
						'type' => __( 'Countdown: Separator', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'end_message',
						'type' => __( 'Countdown: Countdown End Message', 'happy-addons-pro' ),
						'editor_type' => 'VISUAL'
					],
					[
						'field' => 'end_redirect_link',
						'type' => __( 'Countdown: Redirection Link', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				]
			],

			/**
			 * Animated Text
			 */
			'animated-text' => [
				'fields' => [
					[
						'field'       => 'before_text',
						'type'        => __( 'Animated Text: Before Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'after_text',
						'type'        => __( 'Animated Text: After Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Animated_Text',
			],

			/**
			 * Business Hour
			 */
			'business-hour' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Business Hour: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Business_Hour',
			],

			/**
			 * Instagram Feed
			 */
			'instagram-feed' => [
				'fields' => [
					[
						'field'       => 'title_btn_text',
						'type'        => __( 'Instagram Feed: Title Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'load_more_text',
						'type'        => __( 'Instagram Feed: Load More Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				]
			],

			/**
			 * Single Image Scroll
			 */
			'image-scroller' => [
				'fields' => [
					[
						'field'       => 'badge_text',
						'type'        => __( 'Single Image Scroll: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					'custom_link' => [
						'field'       => 'url',
						'type'        => __( 'Single Image Scroll: Custom Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					]
				]
			],

			/**
			 * Remote Carousel
			 */
			'remote-carousel' => [
				'fields' => [
					[
						'field'       => 'ha_rcc_next_btn_text',
						'type'        => __( 'Remote Carousel: Next Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'ha_rcc_prev_btn_text',
						'type'        => __( 'Remote Carousel: Prev Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				]
			],

			/**
			 * Shipping Bar
			 */
			'shipping-bar' => [
				'fields' => [
					[
						'field'       => 'ha_fsb_announcement',
						'type'        => __( 'Shipping Bar: Announcement', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'ha_fsb_success_message',
						'type'        => __( 'Shipping Bar: Success Message', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'ha_fsb_continue_shopping_text',
						'type'        => __( 'Shipping Bar: Link Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					'ha_fsb_continue_shopping_link' => [
						'field'       => 'url',
						'type'        => __( 'Shipping Bar: Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					]
				]
			],

			/**
			 * Price Menu
			 */
			'price-menu' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Price_Menu',
			],

			/**
			 * Pricing Table
			 */
			'pricing-table' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Pricing Table: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'currency_custom',
						'type'        => __( 'Pricing Table: Custom Symbol', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'price',
						'type'        => __( 'Pricing Table: Price', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'original_price',
						'type'        => __( 'Pricing Table: Original Price', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'period',
						'type'        => __( 'Pricing Table: Period', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'features_title',
						'type'        => __( 'Pricing Table: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'description',
						'type'        => __( 'Pricing Table: Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'button_text',
						'type'        => __( 'Pricing Table: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					'button_link' => [
						'field'       => 'url',
						'type'        => __( 'Pricing Table: Button Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
					[
						'field'       => 'button_attributes',
						'type'        => __( 'Pricing Table: Attributes', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'footer_description',
						'type'        => __( 'Pricing Table: Footer Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'badge_text',
						'type'        => __( 'Pricing Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Pricing_Table',
			],

			/**
			 * Post Grid
			 */
			'post-grid-new' => [
				'fields' => [
					[
						'field'       => 'meta_separator',
						'type'        => __( 'Post Grid: Separator', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'read_more',
						'type'        => __( 'Post Grid: Read More', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'loadmore_text',
						'type'        => __( 'Post Grid: Load More Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],

			/**
			 * Post Tiles
			 */
			'post-tiles' => [
				'fields' => [
					[
						'field'       => 'meta_separator',
						'type'        => __( 'Post Tiles: Separator', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],

			/**
			 * Smart Post List
			 */
			'smart-post-list' => [
				'fields' => [
					[
						'field'       => 'widget_title',
						'type'        => __( 'Smart Post List: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'filter_all_text',
						'type'        => __( 'Smart Post List: All Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					]
				],
			],

			/**
			 * Scrolling Image
			 */
			'scrolling-image' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Scrolling_Image',
			],

			/**
			 * Source Code
			 */
			'source-code' => [
				'fields' => [
					[
						'field'       => 'copy_btn_text',
						'type'        => __( 'Source Code: Copy Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'after_copy_btn_text',
						'type'        => __( 'Source Code: After Copy Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				]
			],

			/**
			 * Unfold
			 */
			'unfold' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Unfold: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'editor',
						'type'        => __( 'Unfold: Content Editor', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'unfold_text',
						'type'        => __( 'Unfold: Unfold Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'fold_text',
						'type'        => __( 'Unfold: Fold Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				]
			],

			/**
			 * Timeline
			 */
			'timeline' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Timeline',
			],

			/**
			 * Advanced Tabs
			 */
			'advanced-tabs' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Advanced_Tabs',
			],

			/**
			 * Advanced Toggle
			 */
			'toggle' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Advanced_Toggle',
			],
			/**
			 * Advanced Slider
			 */
			'advanced-slider' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Advanced_Slider',
			],
			/**
			 * Happy Loop Grid
			 */
			'happy-loop-grid' => [
				'fields' => [
					[
						'field'       => 'loadmore_text',
						'type'        => __( 'Happy Loop Grid: Load More Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'pagination_prev_label',
						'type'        => __( 'Happy Loop Grid: Previous Label', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'pagination_next_label',
						'type'        => __( 'Happy Loop Grid: Next Label', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				]
			],
			/**
			 * Advanced Comparison Table
			 */
			'advanced-comparison-table' => [
				'fields' => [
					[
						'field'       => 'badge_text_2',
						'type'        => __( 'Advanced Comparison Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'badge_text_3',
						'type'        => __( 'Advanced Comparison Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'badge_text_4',
						'type'        => __( 'Advanced Comparison Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'badge_text_5',
						'type'        => __( 'Advanced Comparison Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'badge_text_6',
						'type'        => __( 'Advanced Comparison Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'badge_text_7',
						'type'        => __( 'Advanced Comparison Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'badge_text_8',
						'type'        => __( 'Advanced Comparison Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'badge_text_9',
						'type'        => __( 'Advanced Comparison Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'badge_text_10',
						'type'        => __( 'Advanced Comparison Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_1',
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_2',
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_3',
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_4',
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_5',
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_6',
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_7',
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_8',
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_9',
					__NAMESPACE__ . '\\WPML_Advanced_Comparison_Table_Column_Data_10',
				]
			],

			/**
			 * Title Tips
			 */
			'title-tips' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Title_Tips',
			],

			/**
			 * Metro Grid
			 */
			'metro-grid' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Metro_Grid',
			],
		];

		foreach ( $widgets_map as $key => $data ) {
			$widget_name = 'ha-'.$key;

			$entry = [
				'conditions' => [
					'widgetType' => $widget_name,
				],
				'fields' => $data['fields'],
			];

			if ( isset( $data['integration-class'] ) ) {
				$entry['integration-class'] = $data['integration-class'];
			}

			$widgets[ $widget_name ] = $entry;
		}

		return $widgets;
	}
}

WPML_Manager::init();
