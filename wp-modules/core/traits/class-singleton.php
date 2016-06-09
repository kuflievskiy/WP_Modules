<?php
/**
 * Trait Singleton
 * @package App\Traits
 */

namespace WP_Modules\Core\Traits;

trait Singleton {

	/**
	 * Static singleton instance
	 * @var object $instance
	 */
	protected static $instance;

	/**
	 * Get instance
	 *
	 * @return static
	 */
	final public static function get_instance() {
		return isset( static::$instance )
			? static::$instance
			: static::$instance = new static;
	}

	/**
	 * Disabled by access level
	 */
	protected function __construct() {}

	/**
	 * Disabled by access level
	 */
	protected function __clone() {}

	/**
	 * Function __destruct
	 */
	protected function __destruct() {}
}
