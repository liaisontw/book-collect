<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/liaisontw
 * @since      1.0.0
 *
 * @package    book_collect
 * @subpackage book_collect/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    book_collect
 * @subpackage book_collect/admin
 * @author     liason <liaison.tw@gmail.com>
 */
class book_collect_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	const TAXONOMY = BOOK_COLLECT_TAXONOMY;
	const CPT      = BOOK_COLLECT_CPT;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		require_once( trailingslashit( dirname( __FILE__ ) ) . 'partials/book-collect-admin-post-meta.php' );
		$book_collect_post_meta  = new book_collect_Admin_post_meta( );
		add_action( 'init'					, array($book_collect_post_meta, 'bocol_book_collection_post_types') );
		add_action( 'init'					, array($book_collect_post_meta, 'bocol_books_register_meta') );
		add_action( 'init'					, array($book_collect_post_meta, 'bocol_Genres_register_taxonomies') );

		add_action( 'admin_menu'			, array($this, 'admin_menu') );
		
		add_action( 'add_meta_boxes_book'	, array($this, 'bocol_book_register_meta_boxes') );
		add_action( 'save_post_book'		, array($this, 'bocol_book_save_post'), 10, 2);
		

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in book_collect_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The book_collect_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/book-collect-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in book_collect_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The book_collect_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/book-collect-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
     * book_collect_menu_settings function.
     * Add a menu item
     * @access public
     * @return void
     */

	public function admin_menu() {
		add_options_page( 'Book Collect Options', 
						  'Book Collect', 
						  'manage_options', 
						  'book_collect_options', 
						  array(&$this, 'book_collect_menu_options')				  
		);

		add_menu_page(
			esc_html__( 'Bocol Logger', 'book-collect' ),
			esc_html__( 'Bocol Logger', 'book-collect' ),
			'update_core',
			'book_collect_log_list',
			array( $this, 'generate_menu_page' ),
			'dashicons-editor-help',
			100
		);
	}

	public function book_collect_menu_options() {
		

	}

	public function generate_menu_page() {
		$book_collect_sample_text = '';
		require_once( trailingslashit( dirname( __FILE__ ) ) . '../includes/class-book-collect-list-table.php' );

		// $plugin_select = isset( $_POST['plugin-select'] ) ? $_POST['plugin-select'] : false;
		// $log_id        = isset( $_POST['log-select'] ) ? $_POST['log-select'] : false;
		// $session_id    = isset( $_POST['session-select'] ) ? $_POST['session-select'] : false;
		// $search        = isset( $_POST['search'] ) ? $_POST['search'] : '';
		// $hide_form     = isset( $_COOKIE['wp_logger_hide_form'] ) ? 'hide-form' : '';

		$logger_table  = new Book_Collect_List_Table( $this->get_entries() );
		$logger_table->prepare_items();
		require_once( trailingslashit( dirname( __FILE__ ) ) . 'partials/book-collect-admin-display.php' );
		//$this->admin_menu_setting_check();
	}
	

    function bocol_book_register_meta_boxes() {
        add_meta_box( 
            'pdev-book-details',
            'Book Details',
            array(&$this, 'bocol_book_details_meta_boxes'),
            'book',
            'advanced',
            'high'
        );
    }

    function bocol_book_details_meta_boxes( $post ) {
        $author = get_post_meta( $post->ID, 'book_author',  true);

        wp_nonce_field( basename( __FILE__ ), 'pdev-book-details' ); ?>

        <p>
            <label>
                Book Author:
                <br/>
                <input type="text" name="pdev-book-author"
                value="<?php echo esc_attr( $author ) ?>"
                />
            </label>
        </p>
    <?php }

    

    function bocol_book_save_post( $post_id, $post ) {
        
        // Verify the nonce before proceeding
        if (
            ! isset( $_POST['pdev-book-details'] ) ||
            ! wp_verify_nonce( $_POST['pdev-book-details'], basename( __FILE__ ) )
        ){
            return;
        }

        // Bail if user doesn't have permission to edit the post
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Bail if this is an Ajax request, autosave, or revision
        if (
            wp_is_doing_ajax() ||
            wp_is_post_autosave( $post_id ) ||
            wp_is_post_revision( $post_id )
        ) {
            return;
        }

        // Get the existing book author if the value exists.
        // If no existing book author, value is empty string
        $old_author = get_post_meta( $post_id, 'book_author',  true);

        // Strip all tags from posted book author
        // If no value is passed from the form, set to empty string.
        $new_author = isset( $_POST['pdev-book-author'] )
                      ? wp_strip_all_tags( $_POST['pdev-book-author'] )
                    : '';

        // If there's an old value but not a new value, delete old value
        if ( ! $new_author & $old_author ) {
            delete_post_meta( $post_id, 'book_author' );
        
        // If the new value doesn't match the new value, add/update
        } elseif ( $new_author !== $old_author ) {
            update_post_meta( $post_id, 'book_author', $new_value );
        }
    }

	

	

	/* get entries used for list table */
	private function get_entries( $limit = 20 ) {
		global $wpdb;
		$post_where    = "post_type = '" . esc_sql( self::CPT ) . "' AND post_parent != 0";
		$comment_where = "comment_approved = '" . esc_sql( self::CPT ) . "'";
		//$post_where    = "post_type = ' ' AND post_parent != 0";
		//$comment_where = "comment_approved = ' ' ";

		if ( ! empty( $_POST['session-select'] ) ) {
			$comment_where .= $wpdb->prepare( " AND comment_post_ID = %d AND comment_parent = 1", intval( $_POST['session-select'] ) );
		} else {
			$comment_where .= ' AND comment_parent = 0';
		}

		$args = array();

		// ordering
		if ( isset( $_GET['orderby'] ) ) {
			if ( 'log_plugin' === $_GET['orderby'] ) {
				$args['orderby'] = 'log_plugin';
			} elseif ( 'log_date' === $_GET['orderby'] ) {
				$args['orderby'] = 'the_date';
			} elseif ( 'log_severity' === $_GET['orderby'] ) {
				$args['orderby'] = 'severity';
			}
		} else {
			$args['orderby'] = 'the_date';
		}
		$args['order'] = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'desc';

		// search
		if ( ! empty( $_POST['search'] ) ) {
			$search = '%' . $wpdb->esc_like( wp_unslash( $_POST['search'] ) ) . '%';
			$post_where .= $wpdb->prepare( " AND ( {$wpdb->posts}.post_title LIKE %s )", $search );
			$comment_where .= $wpdb->prepare( " AND ( {$wpdb->comments}.comment_content LIKE %s OR {$wpdb->comments}.comment_author LIKE %s )", $search, $search );
		}

		$args['join'] = '';

		if ( ! empty( $_POST['plugin-select'] ) ) {
			$term = get_term_by( 'slug', $this->prefix_slug( sanitize_text_field( wp_unslash( $_POST['plugin-select'] ) ) ), self::TAXONOMY );
			if ( $term ) {
				$post_where .= $wpdb->prepare( " AND (wp_term_relationships.term_taxonomy_id IN (%d))", intval( $term->term_id ) );
				$comment_where .= $wpdb->prepare( " AND comment_author = %s", sanitize_text_field( wp_unslash( $_POST['plugin-select'] ) ) );
				$args['join'] = 'INNER JOIN ' . $wpdb->term_relationships . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->term_relationships . '.object_id ';
			}
		}

		if ( ! empty( $_POST['log-select'] ) && empty( $_POST['session-select'] ) ) {
			$comment_where .= $wpdb->prepare( " AND comment_post_ID = %d", intval( $_POST['log-select'] ) );
			$post_where .= $wpdb->prepare( " AND post_parent = %d", intval( $_POST['log-select'] ) );
		}

		if ( isset( $_GET['paged'] ) && intval( $_GET['paged'] ) > 1 ) {
			$args['limit'] = $wpdb->prepare( " LIMIT 20 OFFSET %d", ( intval( $_GET['paged'] ) - 1 ) * 20 );
		} else {
			$args['limit'] = " LIMIT $limit";
		}

		$session_select = "SELECT
				ID AS the_ID,
				menu_order AS severity,
				post_title AS message,
				post_date AS the_date,
				post_excerpt AS log_plugin,
				1 AS session
			FROM
				{$wpdb->posts}
			{$args['join']}
			WHERE
				{$post_where}
			UNION ";

		$sql = "SELECT
				comment_ID AS the_ID,
				user_ID AS severity,
				comment_content AS message,
				comment_date AS the_date,
				comment_author AS log_plugin,
				0 AS session
			FROM
				{$wpdb->comments}
			WHERE
				{$comment_where}
			ORDER BY
			{$args['orderby']}
			{$args['order']}, the_ID ASC";

		if ( empty( $_POST['session-select'] ) ) {
			$sql = $session_select . $sql;
		}

		$rows   = $wpdb->get_results( "{$sql} {$args['limit']}" );
		$count  = $wpdb->get_results( $sql ); // use get_results to count (legacy approach)
		$countn = is_array( $count ) ? count( $count ) : 0;

		return array(
			'entries' => $rows,
			'count'   => $countn,
		);
	}
}
