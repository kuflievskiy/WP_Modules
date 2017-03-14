<?php
/**
 * Class Post_Type_Fields
 * Class for adding custom fields for post types
 *
 * @package App
 * @subpackage Custom_Fields
 *
 * @author Sergey Zaharchenko
 * */

namespace WP_Modules\Core\Custom_Fields;

/**
 * Class Post_Type_Fields
 */
abstract class Post_Type_Fields extends Custom_Fields {

	/**
	 * Post type
	 * @var string
	 */
	protected $post_type;


	/**
	 * Array of meta boxes
	 * @var array
	 */
	protected $meta_boxes = array();

	/**
	 * Post_Type_Settings constructor.
	 *
	 * @param string $post_type Post type.
	 * @param array  $fields Custom fields.
	 * @param string $upload_dir Upload dir.
	 */
	public function __construct( $post_type, $fields, $args, $upload_dir = null ) {

		global $wpdb;
		$this->post_type = $post_type;
		$this->args = $args;
		$table_name = $wpdb->prefix . 'post_type_' . $post_type . '_fields';


		add_action( 'admin_menu', [ $this, 'add_custom_boxes' ] );
		add_action( 'init', [ $this, 'init' ] );

		parent::__construct( $table_name, $wpdb , $fields, $upload_dir );
	}

	public function init() {

		register_post_type( $this->post_type, $this->args );

		// Dealing with data.
		add_action( 'save_post', [ $this, 'save_post_fields' ], 10, 3 );
		add_action( 'deleted_post', [ $this, 'delete_field_values' ] );
		if ( ! empty( $this->fields ) ) {
			add_filter( 'posts_clauses_request', [ $this, 'edit_request' ], 10, 3 );
		}

	}

	/**
	 * Function add_meta_box
	 *
	 * @param string $id Meta box id.
	 * @param string $label Meta box label.
	 * @param array  $form Form.
	 *
	 * @return void
	 * */
	public function add_meta_box( $id, $label, $form ) {
		$new_box = [
			'id'    => $id,
			'label' => $label,
			'form'  => $form,
		];

		array_push( $this->meta_boxes, $new_box );
	}


	/**
	 * Function add_custom_boxes
	 *
	 * @return void
	 * */
	public function add_custom_boxes() {
		foreach ( $this->meta_boxes as $meta_box ) {
			add_meta_box( $meta_box['id'], $meta_box['label'], [ $this, 'render_post_fields' ], $this->post_type, 'normal', 'default' );
		}
	}


	/**
	 * Function render_post_fields
	 *
	 * @param object $post Post.
	 *
	 * @return void
	 * */
	public function render_post_fields( $post ) {
		echo '<table class="form-table">';
		$this->render_fields( $post->ID );
		echo '</table>';
	}


	/**
	 * Function save_post_fields
	 *
	 * @param int    $post_id Post id.
	 * @param object $post Post.
	 *
	 * @return void
	 * */
	public function save_post_fields( $post_id, $post ) {
		if ( $this->check_post_data( $post_id, $post ) ) {
			$this->save_fields( $post_id );
		}
	}


	/**
	 * Function check_post_data
	 *
	 * @param int    $post_id Post id.
	 * @param object $post Post.
	 *
	 * @return bool
	 */
	public function check_post_data( $post_id, $post ) {
		if ( $this->post_type !== $post->post_type ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Edit database request. Adds options table.
	 *
	 * @param array     $pieces Pieces.
	 * @param \WP_Query $wp_query WP_Query.
	 *
	 * @return string Modified $string.
	 */
	public function edit_request( $pieces, $wp_query ) {
		global $wpdb;
		if ( $this->need_fields() ) {
			if( $this->post_type === $wp_query->queried_object->post_type or  $this->post_type === $wp_query->query['post_type'] ) {
				$pieces['fields'] .= ', ' . $this->table_name . '.*';
				$pieces['join'] .= ' LEFT JOIN `' . $this->table_name . "` ON `$wpdb->posts`.`ID` = `" . $this->table_name . '`.`entity_id` ';
			}
		}

		return $pieces;
	}

	/**
	 * @return bool
	 */
	protected function need_fields() {
		if( is_admin() ) {
			$screen = get_current_screen();

			$return = ( $this->post_type == $screen->post_type and in_array( $screen->base, [ 'post', 'edit' ] ) ) ? true : false;
		} else {
			$return = true;
		}

		return apply_filters( 'wp_modules_need_post_type_fields', $return );
	}
}
