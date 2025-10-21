<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/liaisontw
 * @since      1.0.0
 *
 * @package    book_collect
 * @subpackage book_collect/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    book_collect
 * @subpackage book_collect/includes
 * @author     liason <liaison.tw@gmail.com>
 */
class book_collect {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      book_collect_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BOOK_COLLECT_VERSION' ) ) {
			$this->version = BOOK_COLLECT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'book-collect';

		$this->load_dependencies();
		//$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// Load custom post type functions
		//require_once( trailingslashit( dirname( __FILE__ ) ) . 'includes/class-book-collect-post-meta.php' );
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-book-collect-post-meta.php';
		$book_collect_post_meta  = new book_collect_Admin_post_meta( );
		add_action( 'init'					, array($book_collect_post_meta, 'bocol_book_collection_post_types') );
		add_action( 'init'					, array($book_collect_post_meta, 'bocol_books_register_meta') );
		add_action( 'init'					, array($book_collect_post_meta, 'bocol_Genres_register_taxonomies') );


	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - book_collect_Loader. Orchestrates the hooks of the plugin.
	 * - book_collect_i18n. Defines internationalization functionality.
	 * - book_collect_Admin. Defines all hooks for the admin area.
	 * - book_collect_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-book-collect-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-book-collect-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-book-collect-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-book-collect-public.php';

		$this->loader = new book_collect_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the book_collect_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		//$plugin_i18n = new book_collect_i18n();
		//$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
			/*
			* load_plugin_textdomain() has been discouraged 
			* since WordPress version 4.6. 
			* When your plugin is hosted on WordPress.org, 
			* you no longer need to manually include this 
			* function call for translations under your plugin slug. 
			* WordPress will automatically load the translations 
			* for you as needed.
			*/

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new book_collect_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new book_collect_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init'              , $plugin_public, 'init_book_collect' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    book_collect_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/*
	public function add_log_entry( $plugin_name, $log, $message, $severity = 1 ) {
		$plugin_name = sanitize_text_field( (string) $plugin_name );
		$log         = sanitize_text_field( (string) $log );
		$message     = (string) $message;
		$severity    = intval( $severity );

		if ( self::$session_post ) {
			$post_id = self::$session_post;
		} else {
			$post_id = $this->check_existing_log( $plugin_name, $log );
			if ( false == $post_id ) {
				$post_id = $this->create_post_with_terms( $plugin_name, $log );
				if ( false == $post_id ) {
					return false;
				}
			}
		}

		$comment_data = array(
			'comment_post_ID'      => $post_id,
			'comment_content'      => wp_kses_post( $message ),
			'comment_author'       => $plugin_name,
			'comment_approved'     => self::CPT,
			'comment_author_IP'    => '',
			'comment_author_url'   => '',
			'comment_author_email' => '',
			'user_id'              => $severity,
		);

		if ( self::$session_post ) {
			$comment_data['comment_parent'] = 1;
		}

		$comment_id = wp_insert_comment( wp_filter_comment( $comment_data ) );

		if ( ! self::$session_post ) {
			$this->limit_plugin_logs( $plugin_name, $log, $post_id );
		}

		return (bool) $comment_id;
	}

	//limit plugin logs with filter wp_logger_limit_{plugin_name} 
	private function limit_plugin_logs( $plugin_name, $log_name, $log_id ) {
		global $wpdb;

		$limit = apply_filters( 'wp_logger_limit_' . $plugin_name, 20, $log_name );

		$comments = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $wpdb->comments WHERE comment_approved = %s AND comment_author = %s AND comment_post_ID = %d ORDER BY comment_date ASC",
				self::CPT,
				$plugin_name,
				$log_id
			)
		);

		$count = $wpdb->num_rows;

		if ( $count > $limit ) {
			$diff = $count - $limit;
			for ( $i = 0; $i < $diff; $i++ ) {
				wp_delete_comment( $comments[ $i ]->comment_ID, true );
			}
		}
	}
	
	public function clear_logs($plugin_name ) {
		$logs = $this->plugin_admin->get_logs( $plugin_name, 'purge' );
		if ( $logs->have_posts() ) {
			while ( $logs->have_posts() ) {
				$logs->the_post();
				wp_delete_post( get_the_ID(), true );
			}
			wp_reset_postdata();
		}
	}

	// ensure a taxonomy term exists for plugin and return slug 
	private function do_plugin_term( $plugin_name ) {
		$prefixed_term = $this->plugin_admin->prefix_slug( $plugin_name );

		if ( ! term_exists( $prefixed_term, self::TAXONOMY ) ) {
			$registered = wp_insert_term(
				$plugin_name,
				self::TAXONOMY,
				array( 'slug' => $prefixed_term )
			);
			if ( is_wp_error( $registered ) ) {
				return false;
			}
		}
		return $prefixed_term;
	}

	// helper: check existing log post 
	private function check_existing_log( $plugin_name, $log ) {
		$prefixed_term = $this->do_plugin_term( $plugin_name );

		$log_exists = new WP_Query(
			array(
				'post_type' => self::CPT,
				'name'      => $this->plugin_admin->prefix_slug( $log, $plugin_name ),
				'tax_query' => array(
					array(
						'taxonomy' => self::TAXONOMY,
						'field'    => 'slug',
						'terms'    => $prefixed_term
					)
				)
			)
		);

		if ( $log_exists->have_posts() ) {
			$log_exists->the_post();
			$id = get_the_ID();
			wp_reset_postdata();
			return $id;
		}

		return false;
	}

	// create CPT + assign plugin taxonomy term 
	private function create_post_with_terms( $plugin_name, $log, $session_title = '', $severity = 0 ) {
		$prefixed_term = $this->do_plugin_term( $plugin_name );

		$args = array(
			'post_title'     => sanitize_text_field( $log ),
			'post_name'      => $this->plugin_admin->prefix_slug( $log, $plugin_name ),
			'post_type'      => self::CPT,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_status'    => 'publish',
		);

		if ( ! empty( $session_title ) ) {
			$existing_log = $this->check_existing_log( $plugin_name, $log );
			if ( false == $existing_log ) {
				$existing_log = $this->create_post_with_terms( $plugin_name, $log );
			}
			$args['post_parent']  = $existing_log;
			$args['post_title']   = sanitize_text_field( $session_title );
			$args['post_excerpt'] = sanitize_text_field( $plugin_name );
			$args['menu_order']   = intval( $severity );
		}

		$post_id = wp_insert_post( $args );

		if ( 0 == $post_id ) {
			return false;
		}

		$add_terms = wp_set_post_terms(
			$post_id,
			$prefixed_term,
			self::TAXONOMY
		);

		if ( ! is_array( $add_terms ) ) {
			return false;
		}

		return $post_id;		
	}
	*/
}
