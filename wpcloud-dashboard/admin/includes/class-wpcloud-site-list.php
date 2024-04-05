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

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$sites = WPCLOUD_Site::find_all(owner_id: get_current_user_id(), query: $options);
		if ( is_wp_error( $sites ) ) {
			error_log( $sites->get_error_message() );
			$sites = array();
		}
		$this->items = array_map( fn($site) => (array) $site, $sites );
	}

	public function get_columns() {
		$columns = array(
			'select' => '<input type="checkbox" />',
			'name' => __( 'Site Name', 'wpcloud' ),
			'owner' => __( 'Owner', 'wpcloud' ),
			'status' => __( 'Status', 'wpcloud'),
			'created' => __( 'Created', 'wpcloud' ),
			'tags' => __( 'Tags', 'wpcloud' ),
		);

		return $columns;
	}

	public function column_id( $item ) {
		return $item['id'];
	}

	public function column_name( $item ) {
		$edit_link = add_query_arg(
			array(
				'post' => absint( $item[ 'id' ] ),
				'action' => 'edit',
			),
			menu_page_url( 'wpcloud_admin_new_site', false )
		);

		$view_link = add_query_arg(
			array(
				'post' => absint( $item[ 'id' ] ),
				'action' => 'view',
			),
			menu_page_url( 'wpcloud', false )
		);

		$actions = array(
			'edit' => sprintf( __( '<a href="%s">Edit</a>' ), $edit_link ),
			'view' => sprintf( __( '<a href="%s">View</a>' ), get_permalink( $item[ 'id' ] ) ),
			'delete' => sprintf( __( '<a href="%s">Delete</a>' ), get_delete_post_link( $item[ 'id' ], '', true ) ),
		);

		return sprintf( '%1$s %2$s', $item['name'], $this->row_actions( $actions ) );
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

	public function column_owner( $item ) {
		$owner_id = get_post_field( 'post_author', $item['id'] );
		$owner = get_userdata( $owner_id );
		return $owner->display_name;
	}

	public function column_tags( $item ) {
		$tags = get_the_tags( $item['id'] );
		if ( ! $tags ) {
			return '';
		}
		return implode( ', ', array_map( fn($tag) => $tag->name, $tags ) );
	}

	protected function column_default( $item, $column_name ) {
		return '';
	}
}