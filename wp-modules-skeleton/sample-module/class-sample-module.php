<?php
namespace WP_Modules_Skeleton\Sample_Module;

use WP_Modules\Core\Base_Controller;

class Sample_Module extends Base_Controller {


	public function __construct() {

		$this->base_slug = 'sample-controller';
		parent::__construct();

		// Once actions are working, remove this next line
		add_action( 'init', function() {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();			
		} );

		// $this->register_ajax([
		// 'action_method',
		// ]);
		// $sample_DAO = $this->get_model()->get_DAO( 'sample-table' );
		// $items = $sample_DAO->get_all();
		// var_dump( $items );
	}

	/* Function action_test
	 * /sample-controller/test
	 *
	 */
	public function action_test() {
		$sample_DAO = $this->get_model()->get_DAO( 'sample-table' );
		$items = $sample_DAO->get_all();
		var_dump( $items );
		exit;
	}
}
