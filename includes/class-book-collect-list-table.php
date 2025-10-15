<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( trailingslashit( dirname( __FILE__ ) ) . 'class-wp-list-table-copy.php' );
}

class Book_Collect_List_Table extends WP_List_Table {

	function __construct( $items ) {
		$this->items       = isset( $items['entries'] ) ? $items['entries'] : array();
		$this->total_items = isset( $items['count'] ) ? intval( $items['count'] ) : 0;

		parent::__construct(
			array(
				'singular'  => esc_html__( 'book', 'book-collect' ),
				'plural'    => esc_html__( 'books', 'book-collect' ),
				'ajax'      => false
			)
		);

	}

	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$data = $this->table_data();

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_items,
				'per_page'    => 20
			)
		);

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $data;
	}

	function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	public function get_columns() {
		$columns = array(
			'book_name'        => esc_html__( 'Book Name', 'book-collect' ),
			'book_description' => esc_html__( 'Description', 'book-collect' ),
			'book_genre'       => esc_html__( 'Genre', 'book-collect' ),
			'book_date'        => esc_html__( 'Date', 'book-collect' ),
		);
		return $columns;
	}

	public function get_hidden_columns() {
		return array();
	}

	public function get_sortable_columns() {
		return array(
			'book_name'        => array( 'book_name', false ),
			'book_description' => array( 'book_description', false ),
			'book_genre'       => array( 'book_genre', false ),
			'book_date'        => array( 'book_date', false ),
		);
	}

	private function table_data() {
		$data = array();
		if ( ! empty( $this->items ) ) {
			foreach ( $this->items as $item ) {
				$data[] = array(
					'id'           => isset( $item->the_ID ) ? $item->the_ID : '',
					'book_description' => isset( $item->book_description ) ? $item->book_description : '',
					'book_name'      => isset( $item->book_name ) ? $item->book_name : '',
					'book_date'     => isset( $item->book_date ) ? $item->book_date : '',
					'book_genre'   => isset( $item->book_genre ) ? $item->book_genre : '',
					'session'      => isset( $item->session ) ? $item->session : 0
				);
			}
		}
		return $data;
	}

	public function column_id( $item ) {
		return esc_html( $item['id'] );
	}

	public function column_book_name( $item ) {
		if ( 1 == $item['session'] ) {
			$session_url = esc_url( admin_url( 'admin.php?page=book_collect_book_list&session-select=' . intval( $item['id'] ) ) );
			$message = "<a href='{$session_url}' class='thickbox'>" . esc_html( $item['book_name'] ) . "</a>";
		} else {
			$message = esc_html( $item['book_name'] );
		}
		return $message;
	}

	public function column_book_date( $item ) {
		return esc_html( $item['book_date'] );
	}

	public function column_book_genre( $item ) {
		return esc_html( $item['book_genre'] );
	}

	public function column_book_description( $item ) {
		return esc_html( $item['book_description'] );
	}

}
