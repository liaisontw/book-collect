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
 * @package           book-collect
 *
 * @wordpress-plugin
 * Plugin Name:       book-collect
 * Plugin URI:        https://github.com/liaisontw/book-collect
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            liason typed according to "Professional Wordpress Plugin Development"
 * Author URI:        https://github.com/liaisontw/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       log-catcher
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Load custom post type functions
require_once plugin_dir_path( __FILE__ ) . 'post-types.php';

?>