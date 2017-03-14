<?php 

namespace WP_Modules_Skeleton\Sample_Module\Taxonomy;

use WP_Modules\Taxonomy\Taxonomy;


class Sections extends Taxonomy {
	
	public $plural_name;
	public $singular_name;

	public function __construct() {

		$this->plural_name = _x( 'Sections', 'taxonomy general name' );
		$this->singular_name = _x( 'Section', 'taxonomy general name' );
		
		$args = [];
		$custom_fields = [];

		parent::__construct( 'sections', [ 'post' ],  $args, $custom_fields );
	}
}