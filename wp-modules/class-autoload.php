<?php
/**
 * Autoload MU Plugins
 *
 * @package App
 */

namespace WP_Modules;

if ( ! class_exists( 'Autoload' ) ) {

	/**
	 * Class Autoload
	 * This class is used as a generic Autoloader for MU plugins
	 *
	 * @author : Kuflievskiy Aleksey <kuflievskiy@gmail.com>
	 * @version : 1.0.0
	 */
	class Autoload {

		/**
		 * Factory
		 *
		 * @var array
		 */
		public $mu_plugins;

		/**
		 * Function __construct
		 *
		 * @param string $dir Directory name.
		 */
		function __construct( $dir = '' ) {
			spl_autoload_register( array( $this, 'spl_autoload_register' ) );
		}

		/**
		 * Function spl_autoload_register
		 *
		 * @param string $class_name Class name.
		 */
		function spl_autoload_register( $class_name ) {
			$class_name = ltrim( $class_name, '\\' );
			$file_name  = '';
			if ( $lastNsPos = strrpos( $class_name, '\\' ) ) {
				$namespace = substr( $class_name, 0, $lastNsPos );
				$class_name = substr( $class_name, $lastNsPos + 1 );
				$file_name  = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
			}
			$file_name .= 'class-' . $class_name . '.php';

			$class_path = strtolower( str_replace( '_', '-', $file_name ) );

			if ( file_exists( WPMU_PLUGIN_DIR . '/' . $class_path ) ) {
				include_once WPMU_PLUGIN_DIR . '/' . $class_path;
			}
		}

		/**
		 * Function get_mu_plugins
		 * More faster variant of the wp_get_mu_plugins
		 * */
		function get_mu_plugins() {
			$directories = array();
			$objDI = new \DirectoryIterator( WPMU_PLUGIN_DIR );
			foreach ( $objDI as $file_info ) {
				// If it's a directory, but not '.', or '..'.
				if ( $file_info->isDir() && ! $file_info->isDot() ) {
					$directories[] = array(
						'base_name' => $file_info->getFilename(),
						'path'      => WPMU_PLUGIN_DIR . '/' . $file_info->getFilename(),
					);
				}
			}
			return $directories;
		}
	}

	new Autoload;

}
