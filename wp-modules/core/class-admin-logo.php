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
		$logo_URL = site_url( '/wp-admin/images/wordpress-logo.svg' );				
		$logo_ABSPATH = ABSPATH . 'wp-admin/images/wordpress-logo.svg';

		if ( file_exists( $logo_ABSPATH ) ) {			
			$this->logo = $logo_URL;
			add_action( 'login_head', [ $this, 'custom_login_logo' ] );
			// Changing link wordpress.org.
			add_filter( 'login_headerurl', function() { return get_home_url(); } );
			// Changing title.
			add_filter( 'login_headertitle', function() { return get_bloginfo( 'name' ); } );
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
