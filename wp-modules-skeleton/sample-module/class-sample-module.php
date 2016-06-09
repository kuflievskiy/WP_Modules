<?php 

namespace WP_Modules_Skeleton\Sample_Module;

use WP_Modules\Core\Base_Controller;

class Sample_Module extends Base_Controller {

	public function __construct(){
		parent::__construct();		
		
		//$this->register_ajax([
		//	'action_method',
		//]);
		
		//$sample_DAO = $this->get_model()->get_DAO( 'sample-table' );
		//$items = $sample_DAO->get_all();
		//var_dump( $items );
	}
}