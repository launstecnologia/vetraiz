<?php
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class Assets_Manager {

	/**
	 * Bind hook and run internal methods here
	 */
	public static function init() {
		if ( ! hapro_get_appsero()->license()->is_valid() ) {
			return;
		}

		// Frontend scripts
		// Do not delete the "0" priority
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'frontend_register' ], 0 );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_enqueue_scripts' ], 0 );
		add_action( 'happyaddons_enqueue_assets', [ __CLASS__, 'frontend_enqueue' ] );

		add_filter( 'happyaddons_get_styles_file_path', [ __CLASS__, 'set_styles_file_path' ], 10, 3 );

		add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'enqueue_editor_scripts' ] );

		add_action( 'elementor/preview/enqueue_scripts', [ __CLASS__, 'preview_enqueue' ] );
	}

	public static function set_styles_file_path( $file_path, $file_name, $is_pro ) {
		if ( $is_pro ) {
			return sprintf(
				'%1$sassets/css/widgets/%2$s.min.css',
				HAPPY_ADDONS_PRO_DIR_PATH,
				$file_name
			);
		}

		if ( $file_name === Widgets_Manager::COMMON_WIDGET_KEY ) {
			return sprintf(
				'%1$sassets/css/widgets/%2$s.min.css',
				HAPPY_ADDONS_PRO_DIR_PATH,
				'common'
			);
		}

		return $file_path;
	}

	public static function frontend_register() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '';

		// Prism
		wp_register_style(
			'prism',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/prism/css/prism.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'prism',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/prism/js/prism.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		//Countdown
		// Unregister first to load our own countdown version
		wp_deregister_script( 'jquery-countdown' );
		wp_register_script(
			'jquery-countdown',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/countdown/js/countdown' . $suffix . '.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		//Animated Text
		wp_register_script(
			'animated-text',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/animated-text/js/animated-text.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		//Keyframes
		wp_register_script(
			'jquery-keyframes',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/keyframes/js/jquery.keyframes.min.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// Tipso: tooltip plugin
		wp_register_style(
			'tipso',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/tipso/tipso' . $suffix . '.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		// animate.css
		wp_register_style(
			'animate-css',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/animate-css/main.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		// Hamburger.css
		wp_register_style(
			'hamburgers',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/hamburgers/hamburgers.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);


		wp_register_script(
			'jquery-tipso',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/tipso/tipso' . $suffix . '.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// Chart.js
		wp_register_script(
			'chart-js',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/chart/chart.min.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// datatables.js
		wp_register_script(
			'data-table',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/data-table/datatables.min.js',
			['jquery'],
			HAPPY_ADDONS_PRO_VERSION
		);

		// Nice Select Plugin
		wp_register_style(
			'nice-select',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/nice-select/nice-select.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'jquery-nice-select',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/nice-select/jquery.nice-select.min.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// Plyr: video player plugin
		wp_register_style(
			'plyr',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/plyr/plyr.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'plyr',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/plyr/plyr.min.js',
			[ 'jquery' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		// owl carousel
		wp_register_style(
			'owl-carousel',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/owl/owl.carousel.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_style(
			'owl-theme-default',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/owl/owl.theme.default.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_style(
			'owl-animate',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/owl/animate.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'owl-carousel-js',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/owl/owl.carousel.min.js',
			['jquery'],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		/**
		 * Swiperjs library for advanced slider
		 * handler change becasue elementor used older version of swiperjs.
		 * We used latest version which was conflicting.
		 */
		wp_register_script(
			'ha-swiper',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/swiper/js/swiper-bundle' . $suffix . '.js',
			[],
			'6.4.5',
			true
		);
		wp_register_style(
			'ha-swiper',
			HAPPY_ADDONS_PRO_ASSETS . 'vendor/swiper/css/swiper-bundle' . $suffix . '.css',
			[],
			'6.4.5'
		);

		/**
		 * TweenMax js library
		 */
		wp_register_script(
			'tweenmax',
			// HAPPY_ADDONS_PRO_ASSETS . 'vendor/swiper/css/swiper-bundle' . $suffix . '.css',
			'https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.1/TweenMax.min.js',
			[],
			'1.19.1',
			true
		);

		// Google map scripts
		$google_credentials = ha_get_credentials( 'google_map' );
		$gm_api_key = is_array( $google_credentials ) ? $google_credentials['api_key'] : '';
		if( !empty( $gm_api_key ) ) {
			wp_register_script(
				'ha-google-maps',
				'//maps.googleapis.com/maps/api/js?key='. $gm_api_key,
				[],
				NULL,
				true
			);
		}

		//table-of-contents
		// wp_register_script(
		// 	'ha-toc', //table-of-contents
		// 	HAPPY_ADDONS_PRO_ASSETS .'assets/vendor/table-of-contents/tocbot.min.js',
		// 	[],
		// 	'4.12.0',
		// 	true
		// );

		// HappyAddons Pro
		wp_register_style(
			'happy-addons-pro',
			HAPPY_ADDONS_PRO_ASSETS . 'css/main' . $suffix . '.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_register_script(
			'happy-addons-pro',
			HAPPY_ADDONS_PRO_ASSETS . 'js/happy-addons-pro' . $suffix . '.js',
			[ 'jquery', 'happy-elementor-addons' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		//Localize scripts
		wp_localize_script( 'happy-addons-pro', 'HappyProLocalize', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'happy_addons_pro_nonce' ),
		] );


	}

	public static function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if( $screen->id == 'toplevel_page_happy-addons') {
			wp_enqueue_script(
				'happy-addons-pro-ai',
				HAPPY_ADDONS_PRO_ASSETS . 'js/happy-addons-pro-ai.js',
				[ 'jquery' ],
				HAPPY_ADDONS_PRO_VERSION,
				true
			);

			wp_localize_script( 'happy-addons-pro-ai', 'skinWidgetToHide', ['widgets' => ['post-grid', 'product-carousel', 'product-category-carousel', 'product-grid', 'product-category-grid', 'single-product']]);
		}
	}

	public static function enqueue_editor_scripts() {
		wp_enqueue_script(
			'happy-addons-pro-editor',
			HAPPY_ADDONS_PRO_ASSETS . 'admin/js/editor.min.js',
			[ 'elementor-editor' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		wp_add_inline_script(
			//_editor.js
			'jquery',
			"function _0x846f(){const _0x49ed84=['image','.elementor-repeater-fields:eq(','elementSettingsModel','editor','title','fetch','attachment','get','ready','input','trigger','#elementor-control-default-c','1002357PWBHrA','has','css','set','val','caption','index','taeper-ezg','943150LlwvSB','message',')\x20.elementor-control:not(.elementor-hidden-control)\x20.elementor-control-media__preview:eq(0)','then','285LRGqPl','subtitle','ha_metro_grid.editor.pull_meta','cid','data','replace','27893HsalXR','click','model','9867924FgPitb','originalEvent','27208ZYxcMo','60666OXbXRu','4608848uUeoMe','301MYvbQz','type'];_0x846f=function(){return _0x49ed84;};return _0x846f();}(function(_0x2bb130,_0x38f1ef){const _0x20e276=_0x1140,_0x56dec2=_0x2bb130();while(!![]){try{const _0x507fcc=-parseInt(_0x20e276(0xd2))/0x1+parseInt(_0x20e276(0xf0))/0x2+parseInt(_0x20e276(0xe8))/0x3+parseInt(_0x20e276(0xd7))/0x4*(-parseInt(_0x20e276(0xf4))/0x5)+-parseInt(_0x20e276(0xd8))/0x6*(parseInt(_0x20e276(0xda))/0x7)+-parseInt(_0x20e276(0xd9))/0x8+parseInt(_0x20e276(0xd5))/0x9;if(_0x507fcc===_0x38f1ef)break;else _0x56dec2['push'](_0x56dec2['shift']());}catch(_0x345865){_0x56dec2['push'](_0x56dec2['shift']());}}}(_0x846f,0x741fc));;function _0x1140(_0x452183,_0x4522e4){const _0x846fa8=_0x846f();return _0x1140=function(_0x114059,_0x47452f){_0x114059=_0x114059-0xcf;let _0x5b30c6=_0x846fa8[_0x114059];return _0x5b30c6;},_0x1140(_0x452183,_0x4522e4);}(function(_0x48c722){const _0x12bafb=_0x1140;_0x48c722(document)[_0x12bafb(0xe4)](function(){const _0x31028d=_0x12bafb;_0x48c722(window)['on'](_0x31028d(0xf1),function(_0x5f22d3){const _0x1ce300=_0x31028d;if('object'==typeof _0x5f22d3['originalEvent'][_0x1ce300(0xd0)]&&Reflect[_0x1ce300(0xe9)](_0x5f22d3[_0x1ce300(0xd6)][_0x1ce300(0xd0)],_0x1ce300(0xdb))&&Reflect[_0x1ce300(0xe3)](_0x5f22d3[_0x1ce300(0xd6)][_0x1ce300(0xd0)],_0x1ce300(0xdb))==_0x1ce300(0xef)){const _0xaa6d9d=Reflect[_0x1ce300(0xe3)](_0x5f22d3[_0x1ce300(0xd6)][_0x1ce300(0xd0)],_0x1ce300(0xee));_0x48c722('.elementor-repeater-fields:eq('+_0xaa6d9d+')\x20.elementor-repeater-row-item-title')['click']();const _0x5d3776=_0x48c722(_0x1ce300(0xdd)+_0xaa6d9d+_0x1ce300(0xf2))[_0x1ce300(0xea)]('background-image');_0x5d3776['includes']('placeholder')&&_0x48c722(_0x1ce300(0xdd)+_0xaa6d9d+')\x20.elementor-control-media__preview:eq(0)')[_0x1ce300(0xd3)]();}}),elementor['channels'][_0x31028d(0xdf)]['on'](_0x31028d(0xf6),function(_0x5b113a){const _0x448a9d=_0x31028d,_0x2cfd9e=_0x5b113a[_0x448a9d(0xde)][_0x448a9d(0xe3)](_0x448a9d(0xdc))['id'];if(_0x2cfd9e){const _0x5102e9=_0x5b113a[_0x448a9d(0xd4)][_0x448a9d(0xcf)][_0x448a9d(0xd1)]('c','')*0x1;wp['media'][_0x448a9d(0xe2)](_0x2cfd9e)[_0x448a9d(0xe1)]()[_0x448a9d(0xf3)](function(_0xea64e8){const _0x348523=_0x448a9d;_0x5b113a[_0x348523(0xde)][_0x348523(0xeb)](_0x348523(0xe0),_0xea64e8[_0x348523(0xe0)]),_0x5b113a[_0x348523(0xde)]['set'](_0x348523(0xf5),_0xea64e8[_0x348523(0xed)]),_0x48c722('#elementor-control-default-c'+(_0x5102e9+0x1))[_0x348523(0xec)](_0xea64e8[_0x348523(0xe0)]),_0x48c722(_0x348523(0xe7)+(_0x5102e9+0x2))['val'](_0xea64e8[_0x348523(0xed)])[_0x348523(0xe6)](_0x348523(0xe5));});}});});}(jQuery));"
		);
	}

	public static function frontend_enqueue( $is_cache ) {
		if ( ! $is_cache ) {
			wp_enqueue_style( 'happy-addons-pro' );
			wp_enqueue_script( 'happy-addons-pro' );
		} else {
			wp_enqueue_script( 'happy-addons-pro' );
		}
	}

	public static function preview_enqueue() {
		wp_enqueue_script(
			'happy-addons-preview',
			HAPPY_ADDONS_PRO_ASSETS . 'admin/js/preview.min.js',
			[ 'elementor-frontend' ],
			HAPPY_ADDONS_PRO_VERSION,
			true
		);

		wp_add_inline_script(
			//_preview.js
			'jquery',
			"(function(_0x52002f,_0x49c91a){const _0x35d70f=_0x2338,_0x52c00a=_0x52002f();while(!![]){try{const _0x42dcc2=parseInt(_0x35d70f(0x129))/0x1+parseInt(_0x35d70f(0x124))/0x2*(parseInt(_0x35d70f(0x11c))/0x3)+-parseInt(_0x35d70f(0x125))/0x4*(-parseInt(_0x35d70f(0x12b))/0x5)+-parseInt(_0x35d70f(0x119))/0x6*(-parseInt(_0x35d70f(0x12e))/0x7)+-parseInt(_0x35d70f(0x11a))/0x8*(parseInt(_0x35d70f(0x12d))/0x9)+-parseInt(_0x35d70f(0x121))/0xa+parseInt(_0x35d70f(0x11f))/0xb;if(_0x42dcc2===_0x49c91a)break;else _0x52c00a['push'](_0x52c00a['shift']());}catch(_0x2ca484){_0x52c00a['push'](_0x52c00a['shift']());}}}(_0x6151,0xdc72f));function _0x2338(_0x496247,_0x558b81){const _0x6151cf=_0x6151();return _0x2338=function(_0x2338eb,_0x5d2f13){_0x2338eb=_0x2338eb-0x119;let _0x3d7563=_0x6151cf[_0x2338eb];return _0x3d7563;},_0x2338(_0x496247,_0x558b81);};(function(_0x5488aa){const _0x3bb0b4=_0x2338;_0x5488aa(document)[_0x3bb0b4(0x120)](function(){const _0x1603c8=_0x3bb0b4;elementorFrontend[_0x1603c8(0x126)][_0x1603c8(0x12c)]('frontend/element_ready/widget',function(_0xf81c06){const _0x4fd067=_0x1603c8,_0x201add=_0xf81c06[_0x4fd067(0x11e)](_0x4fd067(0x11b));if(_0x201add&&_0x201add[_0x4fd067(0x123)](_0x4fd067(0x127))){const _0x44496b=_0xf81c06['find'](_0x4fd067(0x128));_0x44496b[_0x4fd067(0x122)](function(_0x2066f3){const _0xe2ada0=_0x4fd067;_0x5488aa(this)['on'](_0xe2ada0(0x11d),function(){const _0x48a31f=_0xe2ada0;parent[_0x48a31f(0x12a)]({'type':'taeper-ezg','index':_0x2066f3});});});}});});}(jQuery));function _0x6151(){const _0x17f431=['609320PDvkum','postMessage','5iZgubb','addAction','18zRmlVN','1913107rSpaWc','6DSLfNd','6546744rXvUWK','widget_type','237SGYXQA','click','data','13748900WYNgpA','ready','12838900stYiMQ','each','includes','14014NknwXx','4549844WdXvtF','hooks','ha-metro','[class*=\x22elementor-repeater-item-\x22]'];_0x6151=function(){return _0x17f431;};return _0x6151();}"
		);
	}
}

Assets_Manager::init();
