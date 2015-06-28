<?php
namespace BEA\WM;

use BEA\WM\API as API;

/**
 * Class Client
 */
class Main {

	/**
	 * The meta name for the watermark
	 */
	const watermark_meta_name = "bea_watermark";

	/**
	 *
	 */
	function __construct() {
		// Load translation
		add_action( 'init', array( __CLASS__, 'init' ) );

		// Add the js
		add_action( 'template_redirect', array( __CLASS__, 'register_assets' ) );

		// Add js templates
		add_action( 'wp_head', array( __CLASS__, 'enqueue_assets' ) );

		// Add js templates
		add_action( 'wp_footer', array( __CLASS__, 'add_js_templates' ) );

		// Add watermark on content
		add_filter( 'the_content', array( __CLASS__, 'the_content' ) );
	}

	/**
	 * Load the translation file
	 */
	public static function init() {
		// Load translations
		load_plugin_textdomain( 'bea-plugin-boilerplate', false, BEA_WM_DIR . 'languages' );
	}

	/**
	 * Add the attachment attributes if needed
	 *
	 * @param array $attributes
	 * @param int $att
	 *
	 * @author Nicolas Juen
	 * @return array
	 */
	public static function wp_get_attachment_image_attributes( $attributes = array(), $att = 0 ) {
		;
		if ( ! self::is_image_eligible( $att->ID ) ) {
			return $attributes;
		}

		$attributes['data-watermark'] = $att->{self::watermark_meta_name};

		return $attributes;
	}

	/**
	 * Check if the image is eligible for the watermark
	 *
	 * @param int $id
	 *
	 * @author Nicolas Juen
	 * @return bool
	 */
	public static function is_image_eligible( $id ) {
		$bea_rewrite = get_post_meta( $id, self::watermark_meta_name, true );

		return ! empty( $bea_rewrite );
	}

	/**
	 * Register the assets
	 *
	 * @param void
	 *
	 * @return void
	 * @author Nicolas Juen
	 */
	public static function register_assets() {
		// Suffix for the files
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ? '' : '.min' ;

		// Script
		wp_register_script( 'bea-watermark', BEA_WM_URL . 'assets/js/front'.$suffix.'.js', array(
			'jquery',
			'underscore'
		), BEA_WM_VERSION, true );

		// Style
		wp_register_style( 'bea-watermark', BEA_WM_URL . 'assets/css/style'.$suffix.'.css' );

	}

	/**
	 * Enqueue the script on the front office
	 *
	 * @param void
	 *
	 * @return void
	 * @author Nicolas Juen
	 */
	public static function enqueue_assets() {
		// Add the element in footer
		wp_enqueue_script( 'bea-watermark' );

		// Style
		wp_enqueue_style( 'bea-watermark' );
	}

	/**
	 * Add the underscore template
	 *
	 * @param void
	 *
	 * @return void
	 * @author Nicolas Juen
	 */
	public static function add_js_templates() {
		API::load_template( 'js-template' );
	}

	/**
	 * Add watermark attributes if needed
	 *
	 * @param string $content
	 *
	 * @return mixed|string
	 * @author Zainoudine Soulé
	 */
	public static function the_content( $content = '' ) {
		/**
		 * @var $wpdb \wpdb
		 */
		global $wpdb;

		if ( ! is_single() && ! is_page() ) {
			return $content;
		}

		// Get imgs
		$imgs = preg_match_all( '|<img([^>]+)\>|', $content, $img_contents );

		if ( $imgs === false ) {
			return $content;
		}

		//Add watermark attributes for any img
		foreach ( $img_contents[1] as $img_content ) {
			// Check there is an image id on the classes
			$id_match = preg_match( '|wp-image-([0-9]*)|', $img_content, $id_attrs );

			// Get image src
			preg_match( '|src="([^"]*)|', $img_content, $src );

			// If not go trough the Path
			if ( $id_match !== 1 ) {

				// Get path info
				$src_no_cache = pathinfo( $src[1] );

				// If file ok
				if ( ! empty( $src_no_cache['dirname'] ) ) {
					// Explode the path
					$parts = explode( '/', $src_no_cache['dirname'] );

					// Get the last 3 parts
					$final_parts[] = array_pop( $parts );
					$final_parts[] = array_pop( $parts );
					$final_parts[] = array_pop( $parts );

					// Re-implode the elements
					$filename = implode( '/', array_reverse( $final_parts ) );

					// Get the attachment id from the database and filename
					$post_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value LIKE '$filename%%'", '_wp_attached_file' ) );

					if ( ! empty( $post_id ) && ! is_null( $post_id ) ) {
						$id_match = 1;
						$id_attrs[1] = $post_id;
					}
				}
			}

			if ( $id_match !== 1 || ! self::is_image_eligible( $id_attrs[1] ) ) {
				continue;
			}

			//New content with watermark add
			$content = str_replace(
				'src="' . $src[1] . '"',
				'src="' . $src[1] . '" ' . sprintf( 'data-watermark="%s"', esc_html( get_post_meta( $id_attrs[1], self::watermark_meta_name, true ) ) ),
				$content );
		}

		return $content;
	}
}
