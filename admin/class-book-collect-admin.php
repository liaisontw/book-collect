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
		add_action( 'init'					, array($this, 'bocol_book_collection_post_types') );
		add_action( 'init'					, array($this, 'bocol_books_register_meta') );
		add_action( 'admin_menu'			, array($this, 'admin_menu') );
		add_action( 'add_meta_boxes_book'	, array($this, 'bocol_book_register_meta_boxes') );
		add_action( 'save_post_book'		, array($this, 'bocol_book_save_post'), 10, 2);
		add_action( 'init'					, array($this, 'bocol_Genres_register_taxonomies') );

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
		add_options_page( 'template Stuff Options', 
						  'template Stuff', 
						  'manage_options', 
						  'book_collect_options', 
						  array(&$this, 'book_collect_menu_options')				  
		);
	}

	public function book_collect_menu_options() {
		

	}

	

    function bocol_book_register_meta_boxes() {
        add_meta_box( 
            'pdev-book-details',
            'Book Details',
            array(&$this, 'bocol_'),
            'book',
            'advanced',
            'high'
        );
    }

    function bocol_( $post ) {
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

	

	function bocol_book_collection_post_types() {
		register_post_type( 'book', [
			'public'                => true,
			'publicly_queryable'    => true,
			'show_in_rest'          => true,
			'show_in_nav_menus'     => true,
			'show_in_admin_bar'     => true,
			'exclude_from_search'   => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_icon'             => 'dashicons-book',
			'hierarchical'          => false,
			'has_archive'           => 'books',
			'qeury_var'             => 'book',
			'map_meta_cap'          => true,
			//'capabilities_type'     => 'book',
			'taxonomies'            => [
				'post_tag'
			],

			// The rewrite handles the URL structure.
			'rewrite' => [
				'slug'          => 'books',
				'with_front'    => false,
				'pages'         => true,
				'feeds'         => true,
				'ep_mask'       => EP_PERMALINK,
			],

			// Features the Book type supports.
			'supports' => [
				'title',
				'editor',
				'excerpt',
				'thumbnail'
			],

			// Text labels
			'labels' => [
				'name'                     => 'Books',
				'singular_name'            => 'Book',
				'add_new'                  => 'Add New',
				'add_new_item'             => 'Add New Book',
				'edit_item'                => 'Edit Book',
				'new_item'                 => 'New Book',
				'view_item'                => 'View Book',
				'view_items'               => 'View Books',
				'search_items'             => 'Search Books',
				'not_found'                => 'No books found.',
				'not_found_in_trash'       => 'No books found in Trash.',
				'all_items'                => 'All Books',
				'archives'                 => 'Book Archives',
				'attributes'               => 'Book Attributes',
				'insert_into_item'         => 'Insert into book',
				'uploaded_to_this_item'    => 'Uploaded to this book',
				'featured_image'           => 'Book Image',
				'set_featured_image'       => 'Set book image',
				'remove_featured_image'    => 'Remove book image',
				'use_featured_image'       => 'Use as book image',
				'filter_items_list'        => 'Filter books list',
				'items_list_navigation'    => 'Books list navigation',
				'items_list'               => 'Books list',
				'item_published'           => 'Book published.',
				'item_published_privately' => 'Book published privately.',
				'item_reverted_to_draft'   => 'Book reverted to draft.',
				'item_scheduled'           => 'Book scheduled.',
				'item_updated'             => 'Book updated.'
			], 
	
	/*        
			'capabilities' => [
				'edit_post'                 => 'edit_book',
				'read_post'                 => 'read_book',
				'delete_post'               => 'delete_book',
				'create_post'               => 'create_books',
				'edit_posts'                => 'edit_books',
				'edit_others_posts'         => 'edit_others_books',
				'edit_private_posts'        => 'edit_private_books',
				'edit_published_posts'      => 'edit_published_books',
				'publish_posts'             => 'publish_books',
				'read_private_posts'        => 'read_private_books',
				'read'                      => 'read',
				'delete_posts'              => 'delete_books',
				'delete_others_posts'       => 'delete_others_books',
				'delete_private_posts'      => 'delete_private_books',
				'delete_published_posts'    => 'delete_published_books',
			],
	*/      
		] );
	}

	

    function bocol_books_register_meta() {
        register_post_meta( 'book', 'book_author', [
            'single'               => true,
            'show_in_rest'         => true,
            'sanitize_callback'    => function( $value ) {
                      return wp_strip_all_tags( $value );
            }
        ]);
    }


	function bocol_Genres_register_taxonomies() {
		register_taxonomy( 'genre', 'book', [

			// Taxonomy auguments
			'public'                => true,
			'show_in_rest'          => true,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'show_tagcloud'         => true,
			'show_in_admin_column'  => true,
			'hierarchical'          => true,
			'qeury_var'             => 'genre',

			// The rewrite handles the URL structure.
			'rewrite' => [
				'slug'          => 'genre',
				'with_front'    => false,
				'hierarchical'  => false,
				'ep_mask'       => EP_NONE
			],

			// Text labels
			'labels' => [
				'name'                     => 'Genres',
				'singular_name'            => 'Genre',
				'name_admin_bar'           => 'Genres',
				'search_items'             => 'Search Genres',
				'popular_items'            => 'Popular Genres',
				'all_items'                => 'All Genres',
				'add_new'                  => 'Add New',
				'edit_item'                => 'Edit Genre',
				'view_item'                => 'View Genre',
				'update_item'              => 'Update Genre',
				'add_new_item'             => 'Add New Genre',
				'new_item_name'            => 'New Genre Name',
				'not_found'                => 'No genres found.',
				'no_terms'                 => 'No genres',
				'items_list_navigation'    => 'Genres list navigation',
				'items_list'               => 'Genres list',
				// Hierarchical only
				'select_name'              => 'Select Genre',
				'parent_name'              => 'Parent Genre',
				'parent_name_colon'        => 'Parent Genre:',
			]
		] );
	}

}
