<?php

namespace WP_Modules_Skeleton\Sample_Module\Admin\Widgets;

class Dashboard_Widget {

    public $model;

	function __construct( $model ) {
	    $this->model = $model;
		add_action( 'wp_dashboard_setup', [ $this, 'dashboard_widget' ] );
	}

	/**
	 * Add a widget to the dashboard.
	 *
	 * This function is hooked into the 'wp_dashboard_setup' action below.
	 */
	function dashboard_widget() {

		if ( 'dashboard' == get_current_screen()->base ) {
			add_filter( 'wp_modules_need_post_type_fields', function () {
				return true;
			} );
            // add assets for our graphics
			//add_action( 'admin_head', [ $this, 'add_scripts' ], 4 );
			//add_action( 'admin_head', [ $this, 'add_styles' ], 4 );
		}

		wp_add_dashboard_widget( 'widget_name', get_bloginfo( 'name' ) . ' widget_name', [ $this, 'test_widget_handler' ] );
	}
	
	function test_widget_handler() {
		
	}
}
