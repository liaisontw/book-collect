<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/liaisontw
 * @since             1.0.0
 * @package           book_collect
 *
 * @wordpress-plugin
 * Plugin Name:       Book Collect
 * Plugin URI:        https://github.com/liaisontw/book-collect
 * Description:       This is a description of the book collect plugin.
 * Version:           1.0.0
 * Author:            liaison
 * Author URI:        https://github.com/liaisontw/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       book-collect
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BOOK_COLLECT_VERSION', '1.0.0' );
define( 'BOOK_COLLECT_TAXONOMY', 'genres' );
define( 'BOOK_COLLECT_CPT', 'book-collect' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-book-collect-activator.php
 */
function activate_book_collect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-book-collect-activator.php';
	book_collect_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-book-collect-deactivator.php
 */
function deactivate_book_collect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-book-collect-deactivator.php';
	book_collect_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_book_collect' );
register_deactivation_hook( __FILE__, 'deactivate_book_collect' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-book-collect.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_book_collect() {

	$plugin = new book_collect();
	$plugin->run();

}
run_book_collect();
