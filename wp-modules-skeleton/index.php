<?php
/**
 * Main App file
 * @package SMD Application
 * */

use WP_Modules\Core\App;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * WordPress Must-Use Plugins AutoLoading Section
 *
 * You should use WordPress Coding Standards in order to have your classes autoloaded
 *
 * @link https://make.wordpress.org/core/handbook/coding-standards/php/
 * */
 

require_once ABSPATH . 'wp-content/mu-plugins/wp-modules/bootstrap.php';

/**
 * Init Application
*/
App::init();

/**
 * Get DI container
 */
$container = App::get_container_builder();

/**
 * Init WordPress MU Plugins section
 *
 * Here you can register modules.
 * */
$loader = new YamlFileLoader( $container, new FileLocator( __DIR__ ) );
$loader->load( 'services.yml' );

if ( is_admin() ) {
	// @todo init modules depending on templates
	// Init modules only on the backend side.
} else {
	// Init modules only on the frontend side.
}

$container->get( 'sample-module' );