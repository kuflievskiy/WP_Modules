<?php
/**
 * Class Taxonomy_Fields
 *
 * @package App
 * @subpackage Custom_Fields
 *
 * @author  Sergey Zaharchenko <zaharchenko.dev@gmail.com>
 * */

namespace WP_Modules\Core\Custom_Fields;

/**
 * Class Taxonomy_Fields
 * */
abstract class Taxonomy_Fields extends Custom_Fields {

	/**
	 * Taxonomy name
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Taxonomy_Fields constructor.
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @param array  $fields Custom fields.
	 *
	 * @param string $upload_dir Path to upload dir.
	 */
	public function __construct( $taxonomy, $fields, $upload_dir = null ) {

		global $wpdb;
		$this->taxonomy = $taxonomy;
		$table_name = "{$wpdb->prefix}taxonomy_{$taxonomy}_fields";

		parent::__construct( $table_name, $wpdb, $fields, $upload_dir );

		add_action( 'init', [ $this, 'register_taxonomy' ] );

		if ( $fields ) {
			add_action( 'edited_terms', [ $this, 'save_taxonomy_fields' ], 10, 3 );
			add_action( $taxonomy . '_edit_form_fields', [ $this, 'render_taxonomy_fields' ], 10, 1 );
			add_action( $taxonomy . '_term_edit_form_tag', [ $this, 'edit_form_enctype' ], 10, 1 );
			add_filter( 'terms_clauses', [ $this, 'edit_request' ], 10, 2 );
			add_action( "delete_{$taxonomy}", [ $this, 'delete_field_values' ] );
		}
	}

	/**
	 * Function register_taxonomy
	 */
	public function register_taxonomy() {
		register_taxonomy( $this->taxonomy, $this->post_type, $this->args );
	}

	/**
	 * Function save_option
	 *
	 * @param int    $term_id Term id.
	 * @param string $taxonomy Taxonomy name.
	 */
	public function save_taxonomy_fields( $term_id, $taxonomy ) {
		if ( $taxonomy === $this->taxonomy ) {
			$this->save_fields( $term_id );
		}
	}


	/**
	 * Function render_taxonomy_fields
	 *
	 * @param object $taxonomy Taxonomy.
	 */
	public function render_taxonomy_fields( $taxonomy ) {
		$this->render_fields( $taxonomy->term_id );
	}

	/**
	 * Edit database request. Adds options table.
	 *
	 * @param array $pieces Request pieces.
	 * @param array $taxonomies Taxonomies.
	 *
	 * @return string Modified $string.
	 */
	public function edit_request( $pieces, $taxonomies ) {
		if ( in_array( $this->taxonomy, $taxonomies ) ) {
			$pieces['fields'] .= ', ' . $this->table_name . '.*';
			$pieces['join'] .= ' LEFT JOIN `' . $this->table_name . '` ON `t`.`term_id` = `' . $this->table_name . '`.`entity_id` ';
		}
		return $pieces;
	}
}
