<?php

namespace WP_Modules\Core\Fix;

use WP_List_Table;

class Fix {
	
	public function __construct(){		
		if( is_admin() ) {
			$this->fix_wp_list_table();
		}
	}
	
	public function fix_wp_list_table(){
		// Include WP's list table class.
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
			require_once ABSPATH . 'wp-admin/includes/screen.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			require_once ABSPATH . 'wp-admin/includes/template.php';
		}
	}
}