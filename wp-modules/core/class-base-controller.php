<?php
/**
 * Class Base_Controller
 * @package App
 */

namespace WP_Modules\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use WP_Modules\Core\Settings\Settings;

/**
 * Class Base_Controller
 *
 * @package App
 */
abstract class Base_Controller {

	/**
	 * Handle path of module
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Handle request object
	 *
	 * @var object
	 */
	protected $requestHandler;

	/**
	 * Handle model object
	 *
	 * @var object
	 */
	protected $model;

	/**
	 * Handle module scripts
	 *
	 * @var array
	 */
	protected $scripts;

	/**
	 * Handle module settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Initialize module path
	 *
	 */
	function __construct() {

		$module_path = $this->get_module_path();

		$this->path = $module_path;
		$this->scripts = [];
		$this->settings = [];

		// @codingStandardsIgnoreStart
		$this->requestHandler = new Request(
			$_GET,
			$_POST,
			array(),
			$_COOKIE,
			$_FILES,
			$_SERVER
		);
		// @codingStandardsIgnoreEnd

		if ( class_exists( $model_name = static::class . '_Model' ) ) {
			$this->model = new $model_name( get_object_vars( $this ) );
		} else {

			$this->model = null;
		}

		$parsed_url    = parse_url( $module_path );
		$settings_path = $parsed_url['path'] . '/settings.php';

		// Module Settings.
		if ( file_exists( $settings_path ) ) {
			$settings = include $settings_path;
			if ( $settings && is_array( $settings ) ) {
				new Settings( explode( '\\', static::class )[0], $settings );
			}
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 99 );
		add_action( 'plugins_loaded', array( $this, 'register_lang' ) );
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
	 * Function register_ajax
	 * Register module ajax action
	 *
	 * Executes module function
	 *
	 * @param string $action_name handles action name to register.
	 */
	public function register_ajax( $action_name ) {

		$action_name = is_array( $action_name ) ? $action_name : array( $action_name );

		foreach ( $action_name as $action ) {
			add_action( 'wp_ajax_' . $action, array( $this, $action . '_action' ) );
			add_action( 'wp_ajax_nopriv_' . $action, array( $this, $action . '_action' ) );
		}
	}

	/**
	 * Function add_script
	 * Add scripts to handler
	 *
	 * @param string $script_name handles script name.
	 * @param array  $dependencies handles scripts to load before current.
	 * @param string $version handles script version.
	 */
	public function add_script( $script_name, $dependencies = array(), $version = '1.0' ) {

		array_push($this->scripts, array(
			'name' => $script_name,
			'dependencies' => $dependencies,
			'version' => $version,
		));
	}

	/**
	 * Function register_scripts
	 * Register all module scripts
	 */
	public function register_scripts() {

		foreach ( $this->scripts as $script ) {
			$this->enqueue_script( $script['name'], $script['dependencies'], $script['version'] );
		}
	}

	/**
	 * Function enqueue_script
	 * Register module script on client side
	 *
	 * Adding global object with ajax url handling
	 *
	 * @param string $scriptName handles script name.
	 * @param array  $dependencies handles dependencies.
	 * @param string $version handles script version.
	 */
	public function enqueue_script( $scriptName, $dependencies = array(), $version = '1.0' ) {

		wp_enqueue_script(
			$scriptName,
			'/' . strrchr( $this->path, 'wp-content' ) . '/assets/js/' . $scriptName . '.js',
			array_unique( array_merge( array( 'jquery' ), $dependencies ) ),
			$version
		);

		wp_localize_script( $scriptName, 'wp', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php', 'https' ),
		) );
	}

	/**
	 * Function register_lang
	 * Registers module translations
	 */
	public function register_lang() {
		$reflection_class = new \ReflectionClass( static::class );
		$domain          = str_replace( 'class-', '', basename( $reflection_class->getFileName(), '.php' ) );

		load_muplugin_textdomain( $domain, $domain . '/lang/' );
	}

	/**
	 * Function register_style
	 * Register module styles
	 *
	 * @param string     $style_name handles style name.
	 * @param bool|false $dependencies handles dependencies.
	 * @param string     $version handles version.
	 */
	public function register_style( $style_name, $dependencies = false, $version = '1.0' ) {

		wp_enqueue_style(
			$style_name,
			'/' . strrchr( $this->path, 'wp-content' ) . '/assets/css/' . $style_name . '.css',
			$dependencies,
			$version
		);
	}

	/**
	 * Function get_request
	 *
	 * Use methods from this object
	 * get()
	 * set()
	 * has()
	 * filter()
	 * getInt()
	 *
	 * @link http://symfony.com/doc/current/components/http_foundation/introduction.html
	 *
	 * @param string $method handles method to manage request.
	 *
	 * @return Request
	 */
	protected function get_request( $method ) {
		$handler = null;

		// @codingStandardsIgnoreStart
		switch ( strtolower( $method ) ) {
			case 'post' : {
				$handler = $this->requestHandler->request;
			} break;
			case 'get' : {
				$handler = $this->requestHandler->query;
			} break;
			case 'cookies' : {
				$handler = $this->requestHandler->cookies;
			} break;
			case 'files' : {
				$handler = $this->requestHandler->files;
			} break;
			case 'server' : {
				$handler = $this->requestHandler->server;
			} break;
			case 'headers' : {
				$handler = $this->requestHandler->headers;
			} break;
		}
		// @codingStandardsIgnoreEnd

		return $handler;
	}

	/**
	 * Function get_post_data
	 *
	 * Returns request POST data
	 *
	 * @param string $name handles post param name.
	 *
	 * @return mixed
	 */
	protected function get_post_data( $name ) {

		return $this->get_request( 'post' )->get( $name );
	}

	/**
	 * Function get_query_data
	 *
	 * Returns request GET data
	 *
	 * @param string $name handles get param name.
	 *
	 * @return mixed
	 */
	protected function get_query_data( $name ) {

		return $this->get_request( 'get' )->get( $name );
	}

	/**
	 * Function get_server_data
	 *
	 * Returns request SERVER data
	 *
	 * @param string $name handles server param name.
	 *
	 * @return mixed
	 */
	protected function get_server_data( $name ) {

		return $this->get_request( 'server' )->get( $name );
	}

	/**
	 * Function build_response
	 *
	 * Builds response with application/json headers
	 *
	 * @param array $data handles response data.
	 * @param int   $status handles response status.
	 *
	 * @return Response
	 */
	protected function build_response( $data, $status = Response::HTTP_OK ) {

		return new Response(
			wp_json_encode( $data ),
			$status,
			array( 'content-type' => 'application/json' )
		);
	}

	/**
	 * Function send_response
	 *
	 * Send built response
	 *
	 * @param array $data handles response data.
	 */
	protected function send_response( $data ) {

		$this->build_response( $data )->send();
		//wp_send_json( $data );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_die();
		}
	}

	/**
	 * Function get_model
	 * Returns current model
	 *
	 * @return null|object
	 */
	public function get_model() {

		return $this->model;
	}

	/**
	 * Function reset_session
	 *
	 * Destroys session
	 */
	protected function reset_session() {

		session_destroy();
	}

	/**
	 * Function redirect
	 *
	 * Redirects user to provided url
	 *
	 * @param string $url holds redirect url.
	 *
	 * @return RedirectResponse
	 */
	protected function redirect( $url ) {
		return new RedirectResponse( $url );
	}

	/**
	 * Function remove_admin_bar
	 *
	 * Fully removes admin bar
	 */
	public function remove_admin_bar() {

		remove_action( 'wp_head', '_admin_bar_bump_cb' );
	}
}

