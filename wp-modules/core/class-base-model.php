<?php
/**
 * Class Base_Model
 *
 * @package App
 */

namespace WP_Modules\Core;

use WP_Modules\Core\Traits\Factory_Method;

/**
 * Class Base_Model
 *
 * @package App
 */
abstract class Base_Model {

	use Factory_Method;

	/**
	 * Stores all model DAO classes
	 *
	 * @var array $dao
	 */
	private $dao;

	/**
	 * Prepare module DAO classes
	 * Build namespace of each DAO
	 *
	 * @param array $properties
	 */
	public function __construct( $properties = [] ) {

		$this->dao = array();

		foreach ( $properties as $property_name => $property_value ) {
			if ( property_exists( $this, $property_name ) and null === $this->$property_name ) {
				$this->$property_name = $property_value;
			}
		}

		$module_path = $this->get_module_path();
		$dao_folder_path = $module_path . '/dao';

		$reflector = new \ReflectionClass( get_class( $this ) );
		$model_class_namespace = $reflector->getNamespaceName();

		$iter = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator( $dao_folder_path, \RecursiveDirectoryIterator::SKIP_DOTS ),
			\RecursiveIteratorIterator::SELF_FIRST,
			\RecursiveIteratorIterator::CATCH_GET_CHILD
		);

		foreach ( $iter as $path => $dir ) {
			if ( ! $dir->isDir() ) {

				$DAO_file_name = basename( $path );
				$cleared_file_name = str_replace( [ 'class-', '.php' ], '', $DAO_file_name );
				$DAO_class_name = $this->build_name_DAO( $cleared_file_name );
				$this->dao[ $cleared_file_name ] = $model_class_namespace . '\\DAO\\' . $DAO_class_name;
			}
		}
	}

	/**
	 * Function get_module_path
	 *
	 * @return string real path
	 */
	protected function get_module_path() {
		$reflector = new \ReflectionClass( get_class( $this ) );
		return dirname( $reflector->getFileName() );
	}

	/**
	 * Function get_DAO
	 *
	 * Builds DOA class by name
	 *
	 * @param string $name stores DAO name.
	 *
	 * @return object
	 */
	// @codingStandardsIgnoreStart
	public function get_DAO( $name ) {

		if ( array_key_exists( $name, $this->dao ) ) {

			return self::factory( $this->dao[ $name ] );
		} else {

			return false;
		}
	}
	// @codingStandardsIgnoreEnd

	/**
	 * Function check_presence
	 *
	 * Check for array keys presence
	 *
	 * @param array $target_array array where to check keys.
	 * @param array $comparing_array array of keys to check.
	 *
	 * @return bool
	 */
	public function check_presence( $target_array, $comparing_array ) {
		$response = true;

		foreach ( $comparing_array as $element ) {

			$response = $response && array_key_exists( $element, $target_array );
		}

		return $response;
	}

	/**
	 * Function build_name_DAO
	 *
	 * Builds DAO class name
	 *
	 * @param string $name stores dao file name.
	 *
	 * @return string
	 */
	// @codingStandardsIgnoreStart
	private function build_name_DAO( $name ) {

		return implode( '_', array_map( 'ucfirst', explode( '-', $name ) ) );
	}
	// @codingStandardsIgnoreEnd
}

