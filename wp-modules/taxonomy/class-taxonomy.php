<?php
namespace WP_Modules\Taxonomy;

interface I_Taxonomy {

}
/*
 * Class Taxonomy
 * @version 1.0
 * @author <kuflievskiy@gmail.com>
 */

class Taxonomy implements I_Taxonomy{

	public $plural_name;
	public $singular_name;
    
    public $custom_fields;
    
    public function __construct( $taxonomy_name, $object_type, $args, $custom_fields = [] ) {

		$singular_name_lowercased = strtolower( $this->singular_name );
		$plural_name_lowercased = strtolower( $this->plural_name );

		$labels = array(
				'name'                       => $this->plural_name,
				'singular_name'              => $this->singular_name,
				'search_items'               => __( 'Search ' . $this->plural_name ),
				'popular_items'              => __( 'Popular ' . $this->plural_name ),
				'all_items'                  => __( 'All ' . $this->plural_name ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit ' . $this->singular_name ),
				'update_item'                => __( 'Update ' . $this->singular_name ),
				'add_new_item'               => __( 'Add New ' . $this->singular_name ),
				'new_item_name'              => __( 'New ' . $this->singular_name . ' Name' ),
				'separate_items_with_commas' => __( 'Separate ' . $plural_name_lowercased . ' with commas' ),
				'add_or_remove_items'        => __( 'Add or remove ' . $plural_name_lowercased ),
				'choose_from_most_used'      => __( 'Choose from the most used ' . $plural_name_lowercased ),
				'not_found'                  => __( 'No ' . $plural_name_lowercased . ' found.' ),
				'menu_name'                  => $this->plural_name,
			);

		$default_args = array(
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => [ 'slug' => $singular_name_lowercased ],
		);
		
		/**
		 * Parse incoming $args into an array and merge it with $defaults
		 */ 
		$args = array_merge_recursive( $args, $default_args );

		$this->taxonomy_name = $taxonomy_name;
		$this->object_type = $object_type;
		$this->args = $args;
		$this->custom_fields = $custom_fields;
		
		add_action( 'init', [ $this, 'init' ] );
	}
	
	public function init() {
		register_taxonomy( $this->taxonomy_name, $this->object_type, $this->args );		
	}
}