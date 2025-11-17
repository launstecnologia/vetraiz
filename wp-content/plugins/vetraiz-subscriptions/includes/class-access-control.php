<?php
/**
 * Access control for videos
 *
 * @package Vetraiz_Subscriptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Vetraiz_Subscriptions_Access_Control {
	
	/**
	 * Instance
	 *
	 * @var Vetraiz_Subscriptions_Access_Control
	 */
	private static $instance = null;
	
	/**
	 * Get instance
	 *
	 * @return Vetraiz_Subscriptions_Access_Control
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		// Shortcode to protect video content
		add_shortcode( 'vetraiz_video', array( $this, 'video_shortcode' ) );
		
		// Filter to check access
		add_filter( 'vetraiz_user_has_access', array( $this, 'check_user_access' ), 10, 1 );
		
		// Protect video pages
		add_action( 'template_redirect', array( $this, 'protect_video_pages' ) );
		
		// Protect JetEngine custom post types (if video post type exists)
		add_action( 'template_redirect', array( $this, 'protect_jetengine_videos' ) );
	}
	
	/**
	 * Check if user has access
	 *
	 * @param int $user_id User ID (optional, defaults to current user).
	 * @return bool
	 */
	public function check_user_access( $user_id = null ) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}
		
		if ( ! $user_id ) {
			return false;
		}
		
		// Check if user has active subscription
		return Vetraiz_Subscriptions_Subscription::user_has_active_subscription( $user_id );
	}
	
	/**
	 * Video shortcode
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function video_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'id'    => '',
			'url'   => '',
			'title' => 'Conteúdo exclusivo para assinantes',
		), $atts );
		
		if ( ! is_user_logged_in() ) {
			return $this->get_access_denied_message( 'Você precisa estar logado para acessar este conteúdo.', true );
		}
		
		if ( ! $this->check_user_access() ) {
			return $this->get_access_denied_message( $atts['title'] );
		}
		
		// User has access, show video
		$video_html = '';
		
		if ( ! empty( $atts['url'] ) ) {
			$video_html = '<div class="vetraiz-video-container">';
			$video_html .= '<video controls width="100%">';
			$video_html .= '<source src="' . esc_url( $atts['url'] ) . '" type="video/mp4">';
			$video_html .= 'Seu navegador não suporta o elemento de vídeo.';
			$video_html .= '</video>';
			$video_html .= '</div>';
		} elseif ( ! empty( $atts['id'] ) ) {
			// If using video ID, you can implement custom video player here
			$video_html = '<div class="vetraiz-video-container">';
			$video_html .= '<p>Vídeo ID: ' . esc_html( $atts['id'] ) . '</p>';
			$video_html .= '</div>';
		}
		
		return $video_html;
	}
	
	/**
	 * Protect video pages
	 */
	public function protect_video_pages() {
		// Check if this is a video page (you can customize this logic)
		$is_video_page = $this->is_video_page();
		
		if ( ! $is_video_page ) {
			return;
		}
		
		// Check if user has access
		if ( ! $this->check_user_access() ) {
			$subscribe_url = get_option( 'vetraiz_subscribe_page_id' ) ? get_permalink( get_option( 'vetraiz_subscribe_page_id' ) ) : home_url( '/assinar' );
			
			// Store redirect URL for after login/subscription
			if ( ! is_user_logged_in() ) {
				$redirect_url = add_query_arg( 'redirect_to', urlencode( get_permalink() ), wp_login_url() );
			} else {
				$redirect_url = add_query_arg( 'redirect_to', urlencode( get_permalink() ), $subscribe_url );
			}
			
			wp_redirect( $redirect_url );
			exit;
		}
	}
	
	/**
	 * Protect JetEngine video posts
	 */
	public function protect_jetengine_videos() {
		// Check if JetEngine is active
		if ( ! class_exists( 'Jet_Engine' ) ) {
			return;
		}
		
		// Get video post type (you can configure this in settings)
		$video_post_type = get_option( 'vetraiz_video_post_type', 'video' );
		
		if ( ! is_singular( $video_post_type ) ) {
			return;
		}
		
		// Check if user has access
		if ( ! $this->check_user_access() ) {
			$subscribe_url = get_option( 'vetraiz_subscribe_page_id' ) ? get_permalink( get_option( 'vetraiz_subscribe_page_id' ) ) : home_url( '/assinar' );
			
			// Store redirect URL
			if ( ! is_user_logged_in() ) {
				$redirect_url = add_query_arg( 'redirect_to', urlencode( get_permalink() ), wp_login_url() );
			} else {
				$redirect_url = add_query_arg( 'redirect_to', urlencode( get_permalink() ), $subscribe_url );
			}
			
			wp_redirect( $redirect_url );
			exit;
		}
	}
	
	/**
	 * Check if current page is a video page
	 *
	 * @return bool
	 */
	private function is_video_page() {
		// Check by post type
		$video_post_type = get_option( 'vetraiz_video_post_type', 'video' );
		if ( $video_post_type && is_singular( $video_post_type ) ) {
			return true;
		}
		
		// Check by category/taxonomy
		$video_category = get_option( 'vetraiz_video_category', '' );
		if ( $video_category && has_term( $video_category, 'category' ) ) {
			return true;
		}
		
		// Check by meta field (JetEngine)
		if ( function_exists( 'jet_engine' ) && is_singular() ) {
			$is_video = get_post_meta( get_the_ID(), '_vetraiz_is_video', true );
			if ( $is_video ) {
				return true;
			}
		}
		
		// Check by URL pattern
		$url_patterns = get_option( 'vetraiz_video_url_patterns', array() );
		if ( ! is_array( $url_patterns ) && ! empty( $url_patterns ) ) {
			$url_patterns = explode( "\n", $url_patterns );
			$url_patterns = array_map( 'trim', $url_patterns );
			$url_patterns = array_filter( $url_patterns );
		}
		
		if ( ! empty( $url_patterns ) ) {
			$current_url = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
			foreach ( $url_patterns as $pattern ) {
				if ( ! empty( $pattern ) && false !== strpos( $current_url, $pattern ) ) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Get access denied message
	 *
	 * @param string $message Message.
	 * @param bool   $show_login Show login link.
	 * @return string
	 */
	private function get_access_denied_message( $message, $show_login = false ) {
		$html = '<div class="vetraiz-access-denied" style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px; margin: 20px 0;">';
		$html .= '<h3 style="color: #d32f2f; margin-bottom: 20px;">🔒 Acesso Restrito</h3>';
		$html .= '<p style="font-size: 16px; margin-bottom: 20px;">' . esc_html( $message ) . '</p>';
		
		if ( $show_login ) {
			$html .= '<a href="' . esc_url( wp_login_url( get_permalink() ) ) . '" class="button" style="display: inline-block; padding: 12px 24px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px;">Fazer Login</a>';
		} else {
			$subscribe_url = get_permalink( get_option( 'vetraiz_subscribe_page_id' ) );
			if ( $subscribe_url ) {
				$html .= '<a href="' . esc_url( $subscribe_url ) . '" class="button" style="display: inline-block; padding: 12px 24px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px; margin-top: 10px;">Assinar Agora</a>';
			}
		}
		
		$html .= '</div>';
		
		return $html;
	}
}

