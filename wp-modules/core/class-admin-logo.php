<?php
namespace WP_Modules\Core;

/**
 * Class Admin_Logo
 * This class is used to customize wp logo on the admin login page.
 */
final class Admin_Logo {

	public $logo;

	/**
	 * Construct
	 * */
	public function __construct() {
		if ( is_admin() or 'wp-login.php' == basename( filter_input( INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING ) ) ) {
			add_action( 'login_init', array( $this, 'init' ) );
		}
	}

	/**
	 * Function init
	 * This function is used to add wp hooks.
	 * */
	public function init() {
		// @todo : we should fetch logo URL from the option, get rid of the hardcoded path.
		$logo_relative_path = '/images/minisite/logo.png';
		$theme_root = get_template_directory();

		$logo_URL = get_bloginfo( 'template_directory' ) . $logo_relative_path;
		$logo_ABSPATH = $theme_root . $logo_relative_path;
		if ( file_exists( $logo_ABSPATH ) ) {
			$this->logo = $logo_URL;
			add_action( 'login_head', array( $this, 'custom_login_logo' ) );
			// changing link wordpress.org
			add_filter( 'login_headerurl', create_function( '', 'return get_home_url();' ) );
			// remove title
			add_filter( 'login_headertitle', create_function( '', 'return false;' ) );
		}
	}

	/**
	 * Function custom_login_logo
	 * This function is used to replace default wp-logo with nix-framework logo.
	 *
	 * @return void
	 * */
	public function custom_login_logo() {
		if ( ini_get( 'allow_url_fopen' ) ) {
			list( $width, $height ) = getimagesize( $this->logo );
			echo "<style type='text/css'>
				#login{
					min-width: {$width}px;
				}
				#login h1 a{
					margin: 0 auto;
					height:{$height}px;
					width: {$width}px;
					background: url($this->logo) no-repeat 0 0 !important;
				}
				#lostpasswordform{
					height:100px;
				}
				#lostpasswordform .button-primary{
					text-shadow: 0 1px 1px #666;
				}
				.login #login #nav{
					display: none;
				}
	        </style>";
		}
	}
}
