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

