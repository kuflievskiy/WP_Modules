<?php

namespace WP_Modules\Core;
use WP_List_Table;

/**
 * Class WP_List_Table_Extended
 *
 * */
abstract class WP_List_Table_Extended extends WP_List_Table {

	protected $columns;
	protected $template_path;

	public function __construct( $args ) {
		parent::__construct( $args );
	}

	public function get_columns(){
		return $this->columns;
	}

	/**
	 * Function column_default
	 *
	 * @param $item
	 * @param $column_name
	 *
	 * @return string|void
	 */
	public function column_default( $item, $column_name ) {
		return $item->$column_name; // Show the whole array for troubleshooting purposes: print_r($item,true)
	}


	/**
	 * Function get_bulk_actions
	 * */
	public function get_bulk_actions() {
		return array();
	}

	/**
	 * Function get_sortable_columns
	 * */
	public function get_sortable_columns() {
		return [ ];
	}

	/**
	 * Funciton render_page
	 *
	 * */
	public function render_page() {
		if ( file_exists( $this->template_path ) ) {
			include $this->template_path;
		}
	}

}