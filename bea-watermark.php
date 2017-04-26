<?php
/*
 Plugin Name: BEA Watermark
 Version: 1.0.2
 Description: Add watermark to the images if needed
 Author: BeApi
 Author URI: http://www.beapi.fr
 Domain Path: languages
 Text Domain: bea-watermark
 ----

 Copyright 2015 Beapi (human@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


// Plugin constants
define( 'BEA_WM_VERSION', '1.0.2' );
define( 'BEA_WM_MIN_PHP_VERSION', '5.4' );
define( 'BEA_WM_VIEWS_FOLDER_NAME', 'bea-watermark' );
define( 'BEA_WM_META_NAME', 'bea_watermark' );

// Plugin URL and PATH
define( 'BEA_WM_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_WM_DIR', plugin_dir_path( __FILE__ ) );

// Check PHP min version
if ( version_compare( PHP_VERSION, BEA_WM_MIN_PHP_VERSION, '<' ) ) {
	require_once( BEA_WM_DIR . 'compat.php' );

	// possibly display a notice, trigger error
	add_action( 'admin_init', array( 'BEA_WM\Compatibility', 'admin_init' ) );

	// stop execution of this file
	return;
}

/**
 * Autoload all the things \o/
 */
require_once BEA_WM_DIR . 'autoload.php';

add_action( 'plugins_loaded', 'init_bea_watermark_plugin' );
function init_bea_watermark_plugin() {
	if ( is_admin() ) {
		new BEA\WM\Admin\Main();
	}

	new BEA\WM\Main();
}