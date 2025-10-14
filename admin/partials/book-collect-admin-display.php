<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/liaisontw
 * @since      1.0.0
 *
 * @package    book_collect
 * @subpackage book_collect/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap book-collect-wrap">
	<h1><?php esc_html_e( 'Plugin Logs', 'book-collect' ); ?></h1>

	<!-- <form method="post" id="logger-form" action=""> -->
	<form id="book-collect-form" method="post" action="">
		<?php wp_nonce_field( 'book_collect_generate_report', 'book_collect_form_nonce' ); ?>
		

		<input type="hidden" name="book_collect_submit_hidden" value="Y">
		<div class="alignleft actions">
			<label for="add log entry">
				<?php 
					//esc_html_e( 'Add log entry:', 'book-collect' ); 
				?>
			</label>
			<?php submit_button( __( 'Add Log', 'book-collect' ), 'primary', 'add_log_button', false ); ?>

			<input type="text" id="catcher_log_sample_text" 
				   name="catcher_log_sample_text" 
				   value="<?php echo $catcher_log_sample_text?>"></input>
		</div>

		<div class="tablenav top">

			<div class="alignleft actions">
				<label for="plugin-select">
					<?php 
					//esc_html_e( 'Plugin:', 'book-collect' ); 
					?>
				</label>
				<select name="plugin-select" id="plugin-select">
					<option value="">
						<?php esc_html_e( 'All Plugins', 'book-collect' ); ?>
					</option>
					<?php
					// foreach ( $this->get_plugins() as $plugin ) {
					// 	printf(
					// 		'<option value="%s"%s>%s</option>',
					// 		esc_attr( $plugin->name ),
					// 		selected( $plugin->name, $plugin_select, false ),
					// 		esc_html( $plugin->name )
					// 	);
					// }
					?>
				</select>

				<label for="log-select">
					<?php 
					//esc_html_e( 'Log:', 'book-collect' ); 
					?>
				</label>
				<?php
					// AJAX will rerender，call here first time
					//$this->build_log_select( $plugin_select, $log_id );
				?>

				<input type="hidden" id="session-select" name="session-select" value="<?php echo $session_id; ?>">
				<?php if ( ! empty( $session_id ) ) : ?>
					<div class="tagchecklist">
						<span>
							<a class="clear-session ntdelbutton">X</a>&nbsp;<?php echo get_the_title( $session_id ); ?>
						</span>
					</div>
				<?php endif; ?>

				
				<label for="session-select">
					<?php 
						//esc_html_e( 'Session:', 'book-collect' ); 
					?>
				</label>
				<!-- <select name="session-select" id="session-select">
					<option value=""> -->
						<?php 
							//esc_html_e( 'All Sessions', 'book-collect' ); 
						?>
					<!-- </option>
				</select> -->

				<input type="search" name="search" value="<?php echo isset( $_POST['search'] ) ? esc_attr( $_POST['search'] ) : ''; ?>" placeholder="<?php esc_attr_e( 'Search logs…', 'book-collect' ); ?>" />

				<?php submit_button( __( 'Filter', 'book-collect' ), '', 'filter_action', false ); ?>
			</div>

			<br class="clear" />
			<br class="clear" />
			<br class="clear" />

			<div class="alignleft actions">
				<label for="plugin-select">
					<?php esc_html_e( 'Log Clear for Plugin:', 'book-collect' ); ?>
				</label>
				<br class="clear" />
				<br class="clear" />
				<select name="log_clear_plugin_select" id="log_clear_plugin_select">
					<option value=""><?php esc_html_e( 'All Plugins', 'book-collect' ); ?></option>
					<?php
					// foreach ( $this->get_plugins() as $plugin ) {
					// 	printf(
					// 		'<option value="%s"%s>%s</option>',
					// 		esc_attr( $plugin->name ),
					// 		selected( $plugin->name, $log_clear_plugin_select, false ),
					// 		esc_html( $plugin->name )
					// 	);
					// }
					?>
				</select>
				<?php submit_button( __( 'Clear', 'book-collect' ), '', 'clear_action', false ); ?>
			</div>

			<br class="clear" />
		</div>

		<?php
			//$logger_table->display();
		?>
	</form>
	
	<div id="email-response"></div>
	<div class="alignleft actions">
		<label for="email-results">Email</label>
		<input type="email" name="email-logs" id="email-results" 
			value="<?php 
			//echo esc_attr( $this->get_plugin_email( $plugin_select ) ); 
			?>"
			placeholder="<?php esc_attr_e( 'Developer Email', 'book-collect' ); ?>" />
		<button id="send-logger-email" class="button"><?php esc_html_e( 'Send', 'book-collect' ); ?></button>
	</div>	
</div>
