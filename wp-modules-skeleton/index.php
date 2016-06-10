<?php
/**
 * Main Application file
 *
 * @package WP_Modules Skeleton Application
 * 
 * */

use WP_Modules\Core\App;


require_once ABSPATH . 'wp-content/mu-plugins/wp-modules/bootstrap.php';

// Init Application.
$container = App::get_container_builder();

// Load Dependency Injection config.
App::load_config( dirname( __FILE__ ) . '/services.yml' );


$container->get( 'sample-module' );
