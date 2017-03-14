<?php
/**
 * Settings
 *
 * @package Settings
 */

namespace WP_Modules\Core\Settings;

/**
 * Class Settings
 *
 * This module adds settings to admin panel and can be used by other modules to store data.
 *
 * @author  : Sergey Zaharchenko <zahardoc@gmail.com>, Kuflievskiy Aleksey <kuflievskiy@gmail.com>
 * @version : 1.1.0
 * @package Settings
 */
class Settings {

	/**
	 * Page slug
	 *
	 * @var object
	 */
	private $page_slug;

	/**
	 * Settings
	 *
	 * @var object
	 */
	private $config;

	/**
	 * Function __construct
	 *
	 * @param $page_slug
	 * @param $config
	 */
	public function __construct( $page_slug, $config ) {
		if ( ( isset( $page_slug ) && $page_slug ) &&
		     ( isset( $config ) && $config ) ) {
			$this->config = $config;
			$this->page_slug = $page_slug;
			add_action( 'admin_menu', array( $this, 'add_settings' ) );
		}
	}

	/**
	 * Function add_settings
	 */
	public function add_settings() {

		// Adding settings page.
		add_options_page( 'Settings Page',   // Page title.
			$this->page_slug,                 // Menu title.
			'manage_options',               // Apability.
			$this->page_slug . '.php',             // Menu_slug.
			array( $this, 'settings_form' ) // Callback function.
		);

		foreach ( $this->config as $section_name => $fields ) {

			$section_title = str_replace( '_', ' ', ucfirst( $section_name ) );

			// Adding settings section.
			add_settings_section( $section_name,  // ID used to identify this section and with which to register options.
				$section_title,                   // Title to be displayed on the administration page.
				'',                               // Callback used to render the description of the section.
				$this->page_slug                  // Page on which to add this section of options.
			);

			foreach ( $fields as $field_name => $field ) {
				// Adding settings field.
				add_settings_field( $field_name, // ID used to identify the field throughout the theme.
					$field['title'],             // The label to the left of the option interface element.
					array(
						$this,
						'render_field',
					),              // The name of the function responsible for rendering the option interface.
					$this->page_slug, // The page on which this option will be displayed.
					$section_name,  // The name of the section to which this field belongs.
					array(
						$section_name,
						$field_name,
					)               // The array of arguments to pass to the callback. In this case, just a description.
				);
				register_setting( $this->page_slug, $field_name );
			}
		}
	}

	/**
	 * Function settings_form
	 * */
	public function settings_form() {
		include dirname( __FILE__ ) . '/templates/form.tpl.php';
	}

	/**
	 * Function render_field
	 *
	 * @param array $args Arguments.
	 */
	public function render_field( $args ) {
		$section_name = $args[0];
		$field_name   = $args[1];
		if ( isset( $this->config[ $section_name ][ $field_name ]['type'] ) ) {
			$field = $this->config[ $section_name ][ $field_name ];
			$path  = dirname( __FILE__ ) . '/templates/' . $field['type'] . '.tpl.php';
			if ( file_exists( $path ) ) {
				include $path;
			}
		}
	}
}
