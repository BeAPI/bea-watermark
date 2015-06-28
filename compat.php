<?php
namespace BEA\WM;
class Compatibility {
	/**
	 * admin_init hook callback
	 *
	 * @since 0.1
	 */
	public static function admin_init() {
		// Not on ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Check activation
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Load the textdomain
		load_plugin_textdomain( 'bea-watermark', false, BEA_WM_DIR . 'languages' );

		trigger_error( sprintf( __( 'Plugin Boilerplate requires PHP version %s or greater to be activated.', 'bea-watermark' ), BEA_WM_MIN_PHP_VERSION ) );

		// Deactive self
		deactivate_plugins( BEA_WM_DIR . 'bea-watermark.php' );

		unset( $_GET['activate'] );

		add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
	}

	/**
	 * Notify the user about the incompatibility issue.
	 */
	public static function admin_notice() {
		echo '<div class="notice error is-dismissible">';
		echo '<p>' . esc_html( sprintf( __( 'Bea Watermark require PHP version %s or greater to be activated. Your server is currently running PHP version %s.', 'bea-watermark' ), BEA_WM_MIN_PHP_VERSION, PHP_VERSION ) ) . '</p>';
		echo '</div>';
	}
}