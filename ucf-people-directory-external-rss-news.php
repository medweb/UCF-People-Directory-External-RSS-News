<?php
/*
Plugin Name: UCF People Directory - External RSS News
Description: Modifies single-person from the theme to bring in news posted on another WordPress site, based on a user-defined slug for each profile.
Version: 1.3.1
Author: Stephen Schrauger
Plugin URI: https://github.com/medweb/UCF-People-Directory-External-RSS-News
Github Plugin URI: medweb/UCF-People-Directory-External-RSS-News

 * Created by IntelliJ IDEA.
 * User: stephen
 * Date: 2021-10-20
 * Time: 1:43 PM
 */

namespace ucf_people_directory_external_rss_news;

if ( ! defined( 'WPINC' ) ) {
	die;
}

include_once __DIR__ . '/includes/acf-pro-fields.php'; // defines ACF fields for editor page
include_once __DIR__ . '/includes/simple_html_dom.php'; // parse rss feed
include_once __DIR__ . '/includes/main.php'; // fetch rss feed and print out news on single-profile template


// #### Boilerplate code to add js/css if needed, and activation hooks if needed.

// plugin css/js
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\add_css' );
function add_css() {
	if ( file_exists( plugin_dir_path( __FILE__ ) . '/includes/plugin.css' ) ) {
		wp_enqueue_style(
			__NAMESPACE__ . '-style',
			plugin_dir_url( __FILE__ ) . '/includes/plugin.css',
			false,
			filemtime( plugin_dir_path( __FILE__ ) . '/includes/plugin.css' )
		);
	}
}

add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\add_js' );
function add_js() {
	if ( file_exists( plugin_dir_path( __FILE__ ) . '/includes/plugin.js' ) ) {
		wp_enqueue_script(
			__NAMESPACE__ . '-script',
			plugin_dir_url( __FILE__ ) . 'includes/plugin.js',
			false,
			filemtime( plugin_dir_path( __FILE__ ) . '/includes/plugin.js' ),
			true
		);
	}
}

// run on plugin activation
register_activation_hook( __FILE__, __NAMESPACE__ . '\\activation' );
function activation() {
}

// run on plugin deactivation
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\deactivation' );
function deactivation() {
}

// run on plugin complete uninstall
register_uninstall_hook( __FILE__, __NAMESPACE__ . '\\deactivation' );
function uninstall() {
}

