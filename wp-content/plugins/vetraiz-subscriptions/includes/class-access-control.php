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
		// First check if this is a video post type
		$video_post_type = get_option( 'vetraiz_video_post_type', 'video' );
		$is_video_post_type = false;
		
		if ( $video_post_type && is_singular( $video_post_type ) ) {
			$is_video_post_type = true;
		}
		
		// If it's a video post type, check if it requires subscription
		if ( $is_video_post_type ) {
			$post_id = get_the_ID();
			
			// Check if we should protect all videos or only marked ones
			$protect_all = get_option( 'vetraiz_protect_all_videos', false );
			
			if ( $protect_all ) {
				// Protect all videos of this post type
				return true;
			}
			
			// Check by meta field (JetEngine) - check for "Assinantes" or similar
			$requires_subscription = false;
			
			// Check common meta field names for subscription requirement
			$meta_fields_to_check = array(
				'_jet_engine_listing_type', // JetEngine listing type
				'video_access_type',
				'access_type',
				'requires_subscription',
				'_vetraiz_requires_subscription',
			);
			
			foreach ( $meta_fields_to_check as $meta_field ) {
				$value = get_post_meta( $post_id, $meta_field, true );
				if ( $value ) {
					// Check if value indicates subscription required
					if ( in_array( strtolower( $value ), array( 'assinantes', 'subscriber', 'subscribers', 'premium', 'paid' ), true ) ) {
						$requires_subscription = true;
						break;
					}
				}
			}
			
			// Check JetEngine meta fields (they might be stored differently)
			if ( function_exists( 'jet_engine' ) ) {
				// Try to get all meta fields
				$all_meta = get_post_meta( $post_id );
				foreach ( $all_meta as $key => $values ) {
					if ( is_array( $values ) && ! empty( $values[0] ) ) {
						$value = strtolower( $values[0] );
						if ( in_array( $value, array( 'assinantes', 'subscriber', 'subscribers', 'premium', 'paid' ), true ) ) {
							$requires_subscription = true;
							break;
						}
					}
				}
			}
			
			// Check by taxonomy "tipos-de-videos" for term "assinantes" (most common case)
			$taxonomies_to_check = array(
				'tipos-de-videos', // JetEngine taxonomy (most common)
				'tipos_de_videos',
				'tipo-de-video',
				'video-type',
			);
			
			foreach ( $taxonomies_to_check as $taxonomy ) {
				if ( taxonomy_exists( $taxonomy ) ) {
					// Get terms by slug first (more reliable)
					$terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							$term_lower = strtolower( $term );
							if ( in_array( $term_lower, array( 'assinantes', 'subscriber', 'subscribers', 'premium', 'paid' ), true ) ) {
								$requires_subscription = true;
								break 2; // Break both loops
							}
						}
					}
					
					// Also check by term name (in case slug is different)
					$terms_by_name = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );
					if ( ! is_wp_error( $terms_by_name ) && ! empty( $terms_by_name ) ) {
						foreach ( $terms_by_name as $term_name ) {
							$term_name_lower = strtolower( $term_name );
							if ( in_array( $term_name_lower, array( 'assinantes', 'subscriber', 'subscribers', 'premium', 'paid' ), true ) ) {
								$requires_subscription = true;
								break 2; // Break both loops
							}
						}
					}
					
					// Also check by has_term for "assinantes"
					if ( has_term( 'assinantes', $taxonomy, $post_id ) ) {
						$requires_subscription = true;
						break;
					}
				}
			}
			
			// Check by category/taxonomy
			$video_category = get_option( 'vetraiz_video_category', '' );
			if ( $video_category && has_term( $video_category, 'category' ) ) {
				$requires_subscription = true;
			}
			
			// Check by custom meta field
			$is_video_meta = get_post_meta( $post_id, '_vetraiz_is_video', true );
			if ( $is_video_meta ) {
				$requires_subscription = true;
			}
			
			// If video post type but no subscription requirement found, don't protect
			if ( ! $requires_subscription ) {
				return false;
			}
			
			return true;
		}
		
		// Check by URL pattern (only if not a video post type)
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
					// If URL matches pattern, check if it's a video post type that requires subscription
					if ( is_singular() ) {
						$post_id = get_the_ID();
						// Check if this specific post requires subscription
						$requires_subscription = get_post_meta( $post_id, '_vetraiz_requires_subscription', true );
						if ( ! $requires_subscription ) {
							// Check JetEngine fields
							$all_meta = get_post_meta( $post_id );
							$found_subscription = false;
							foreach ( $all_meta as $key => $values ) {
								if ( is_array( $values ) && ! empty( $values[0] ) ) {
									$value = strtolower( $values[0] );
									if ( in_array( $value, array( 'assinantes', 'subscriber', 'subscribers', 'premium', 'paid' ), true ) ) {
										$found_subscription = true;
										break;
									}
								}
							}
							if ( ! $found_subscription ) {
								return false; // URL matches but post doesn't require subscription
							}
						}
					}
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

