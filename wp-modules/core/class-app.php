<?php
/**
 * App
 * @package App
 */

namespace WP_Modules\Core;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use WP_Modules\Core\Traits\Factory_Method;
use WP_Modules\Core\Fix\Fix;
/**
 * Class App
 *
 * @author: Volgust Max, Kuflievskiy Aleksey <kuflievskiy@gmail.com>
 * @version : 1.0.1
 * @date: 5/29/2015
 * @package App
 */
class App {

	use Factory_Method;

	/**
	 * App
	 * @var object
	 */
	public static $container_builder;

	/**
	 * App
	 * @var $db_connections
	 */
	private static $db_connections = [];

	/**
	 * Function __construct
	 */
	private function __construct() {}

	/**
	 * Function get_container_builder
	 * @link http://symfony.com/doc/current/components/dependency_injection/introduction.html
	 * @link https://github.com/symfony/dependency-injection
	 * http://symfony.com/doc/current/components/dependency_injection/compilation.html
	 */
	public static function get_container_builder() {
		if ( null === self::$container_builder  ) {
			self::$container_builder = new ContainerBuilder();
		}
		return self::$container_builder;
	}


	/**
	 * Function init
	 */
	public static function init() {
		new Admin_Logo;
		new Fix;
	}

	/**
	 * Function get_db_connection
	 *
	 * @param string $db_user holds db user.
	 * @param string $db_password holds db password.
	 * @param string $db_name holds db name.
	 * @param string $db_host holds db host.
	 *
	 * @return \wpdb
	 */
	public static function get_db_connection( $db_user, $db_password, $db_name, $db_host ) {
		if ( array_key_exists( $db_name, self::$db_connections )  ) {
			return self::$db_connections[ $db_name ];
		} else {
			self::$db_connections[ $db_name ] = new \wpdb( $db_user, $db_password, $db_name, $db_host );
			return self::$db_connections[ $db_name ];
		}
	}
}
