<?php
/**
 * Trait Factory_Method
 * @package App\Traits
 */

namespace WP_Modules\Core\Traits;

trait Factory_Method {

	/**
	 * Factory
	 * @var array
	 */
	public static $instances = array();

	/**
	 * Function __construct
	 */
	private function __construct() {}

	/**
	 * Function __clone
	 */
	private function __clone() {}

	/**
	 * Function __destruct
	 */
	public function __destruct() {}

	/**
	 * Function __wakeup
	 */
	private function __wakeup() {}

	/**
	 * Function __toString
	 */
	public function __toString() {}


	/**
	 * Function build
	 *
	 * @param string $class_name Class name.
	 *
	 * @return If the specified class does not exist.
	 * @throws \Exception If the specified class does not exist.
	 */
	public static function factory( $class_name ) {
		try {
			if ( class_exists( $class_name ) ) {
				// Create instance if needed.
				if ( ! array_key_exists( $class_name, static::$instances ) ) {
					static::$instances[ $class_name ] = new $class_name;
				}
				return static::$instances[ $class_name ];
			} else {
				throw new \Exception( 'Invalid class name given : ' . $class_name . ' in class ' . __CLASS__ );
			}
		} catch ( \Exception $e  ) {
			if ( WP_DEBUG ) {
				echo esc_html( $e->getMessage() );
			}
		}
	}
}
