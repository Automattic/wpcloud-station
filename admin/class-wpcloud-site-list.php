<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WPCLOUD_Site_List extends WP_List_Table {

	public function __construct() {
		parent::__construct( array(
			'singular' => 'site',
			'plural' => 'sites',
			'ajax' => false,
		) );
	}

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$defaults = array(
			'post_status' => 'any',
			'posts_per_page' => -1,
			'offset' => 0,
			'orderby' => 'created',
			'order' => 'DESC',
			'post_type' => 'wpcloud_site',
		);

		$q = new WP_Query();

		$results = $q->query( $defaults );

		foreach( (array) $results as $result ) {
			$this->items[] = array(
				'id' => $result->ID,
				'site_name' => $result->post_title,
				'status' => $result->post_status,
				'created' => $result->post_date,
			);
		}
	}

	public function get_columns() {
		$columns = array(
			'select' => '<input type="checkbox" />',
			'site_name' => __( 'Site Name', 'wpcloud' ),
			'status' => __( 'Status', 'wpcloud'),
			'created' => __( 'Created', 'wpcloud' ),
		);

		return $columns;
	}

	public function column_id( $item ) {
		return $item['id'];
	}

	public function column_site_name( $item ) {
		$actions = array(
			'edit' => sprintf( '<a href="%s">Edit</a>', '#' ),
			'delete' => sprintf( '<a href="%s">Delete</a>', '#' ),
		);

		return sprintf( '%1$s %2$s', $item['site_name'], $this->row_actions( $actions ) );
	}

	public function column_status( $item ) {
		if ( 'publish' === $item['status'] ) {
			return '<span style="color:green;">Active</span>';
		}
		if ( 'draft' === $item['status'] ) {
			return '<span style="color:orange;">Provisioning</span>';
		}
	}

	public function column_select( $item ) {
		return sprintf(
			'<input type="checkbox" name="site[]" value="%s" />',
			$item['id']
		);
	}

	public function column_created( $item ) {
		$dt = get_post_datetime($item['id']);
		return $dt->format('Y-m-d H:i:s');
	}

	protected function column_default( $item, $column_name ) {
		return '';
	}
}