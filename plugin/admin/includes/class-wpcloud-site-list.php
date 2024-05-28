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

	public function prepare_items(array $options = array()) {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$defaults = array(
			'posts_per_page' => 20,
			'post_type' => 'wpcloud_site',
			'post_status' => 'any',
			'orderby' => 'id',
			'order' => 'asc',
		);

		$options = wp_parse_args( $options, $defaults );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$results = new WP_Query( $options );

		if ( is_wp_error( $results ) ) {
			error_log( $results->get_error_message() );
		}

		$this->items = $results->posts; //array_map( fn($site) => (array) $site, $sites );
	}

	public function get_columns() {
		$columns = array(
			'select' => '<input type="checkbox" />',
			'name' => __( 'Name', 'wpcloud' ),
			'owner' => __( 'Owner', 'wpcloud' ),
			'status' => __( 'Status', 'wpcloud'),
			'created' => __( 'Created', 'wpcloud' ),
			'tags' => __( 'Tags', 'wpcloud' ),
		);

		return $columns;
	}

	public function column_id( $item ) {
		return $item->ID;
	}

	public function column_name( $item ) {
		$domain = wpcloud_get_site_detail( $item->ID, 'domain_name' );
		$actions = array(
			'edit' => sprintf( __( '<a href="%s">Edit</a>' ), get_permalink($item) ),
			'delete' => sprintf( __( '<a href="%s">Delete</a>' ), get_delete_post_link( $item->ID, '', true ) ),
		);

		return sprintf( '<a href="https://%1$s" target="_blank">%1s</a> %2$s', $domain, $this->row_actions( $actions ) );
	}

	public function column_status( $item ) {
		return $item->post_status;
	}

	public function column_select( $item ) {
		return sprintf(
			'<input type="checkbox" name="site[]" value="%s" />',
			$item->ID
		);
	}

	public function column_created( $item ) {
		$dt = get_post_datetime($item->ID);
		return $dt->format('Y-m-d H:i:s');
	}

	public function column_owner( $item ) {
		$owner_id = get_post_field( 'post_author', $item->ID );
		$owner = get_userdata( $owner_id );
		return $owner->display_name;
	}

	public function column_tags( $item ) {
		$tags = get_the_tags( $item->ID );
		if ( ! $tags ) {
			return '';
		}
		return implode( ', ', array_map( fn($tag) => $tag->name, $tags ) );
	}

	protected function column_default( $item, $column_name ) {
		return '';
	}
}
