<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/liaisontw
 * @since      1.0.0
 *
 * @package    book_collect
 * @subpackage book_collect/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    book_collect
 * @subpackage book_collect/includes
 * @author     liason <liaison.tw@gmail.com>
 */
class book_collect_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_option( 'book_collect_active', 'yes' );
		add_option( 'book_collect_template_text', 'Read More' );
		add_option( 'book_collect_template_padding', '..' );
	}

}
