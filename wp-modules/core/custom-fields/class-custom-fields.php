<?php
/**
 * Class Custom_Fields
 *
 * @package App
 * @subpackage Custom_Fields
 *
 * @author  Sergey Zaharchenko <zaharchenko.dev@gmail.com>
 *
 * @codingStandardsIgnoreFile
 * */

namespace WP_Modules\Core\Custom_Fields;

use \WP_Modules\Db\DAO;

/**
 * Class Custom_Fields
 *
 * */
abstract class Custom_Fields extends DAO {

	/**
	 * Path to upload dir.
	 * @var null|string
	 */
	protected $upload_dir;

	/**
	 * Custom fields.
	 * @var array
	 */
	protected $fields;


	/**
	 * Table name.
	 * @var string
	 */
	protected $table_name;

	/**
	 * Custom_Fields constructor.
	 *
	 * @param string $table_name Table name.
	 * @param object $wpdb Wpdb.
	 * @param array  $fields Custom fields.
	 * @param string $upload_dir Path to upload dir.
	 */
	public function __construct( $table_name, $wpdb, $fields, $upload_dir = null ) {
		$this->table_name = $table_name;
		parent::__construct( $table_name, $wpdb );
		add_action('admin_init', [$this, 'enqueue_assets']);
		
		$this->fields     = $fields;
		$this->upload_dir = $upload_dir ? $upload_dir : 'wp-content/uploads';
	}

	/**
	 * Function enqueue_assets Enqueues style and js if needed (now needs only for image field)
	 */
	function enqueue_assets () {
		foreach ( $this->fields as $field ) {
			if ( 'image' === $field['type'] ) {
				$dir = strstr( __DIR__, 'wp-content' );
				wp_enqueue_style( 'custom-fields-style', get_site_url() . '/' . $dir . '/assets/custom-fields.css' );
				wp_enqueue_script( 'custom-fields-js', get_site_url() . '/' . $dir . '/assets/custom-fields.js' );
				break;
			}
		}
	}

	/**
	 * Function render_fields
	 *
	 * @param int $entity_id Entity id (post id or taxonomy id).
	 */
	public function render_fields( $entity_id ) {
		include 'templates/nonce.tpl.php';
		$field_values = $this->get_field_values( $entity_id );
		foreach ( $this->fields as $field_name => $field_settings ) {
			$field_value = $field_values[ $field_name ];
			if ( 'image' === $field_settings['type'] ) {
				$file_path = get_site_url() . '/' . $this->upload_dir . '/' . $field_value;
			}
			include "templates/{$field_settings['type']}.tpl.php";
		}
	}


	/**
	 * Function save_fields
	 *
	 * @param int $entity_id Entity id (post id or taxonomy id).
	 */
	public function save_fields( $entity_id ) {
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'custom_fields_nonce' ), 'custom_fields_nonce' ) ) {
			return;
		}
		$fields_to_save = array();
		$field_values = $this->get_field_values( $entity_id );
		foreach ( $this->fields as $field_name => $field_settings ) {

			$old_value = $field_values[ $field_name ];

			if ( 'image' === $field_settings['type'] ) {
				$delete = filter_input( INPUT_POST, $field_name . '_delete', FILTER_SANITIZE_STRING );

				$new_value = filter_var( wp_unslash( $_FILES[ $field_name ]['name'] ), FILTER_SANITIZE_STRING );

				if ( $_FILES[ $field_name ]['error'] ) { // No file has been sent.
					if ( 'delete' === $delete ) {
						$new_value = '';
					} else {
						$new_value = $old_value;
					}
				}
			} else {
				$new_value = filter_input( INPUT_POST, $field_name, FILTER_SANITIZE_STRING );
			}

			if ( $old_value != $new_value ) {
				if ( 'image' == $field_settings['type'] ) {
					if ( $new_value ) {
						$this->save_file( $field_name, $old_value, $new_value );
					} else {
						$this->delete_file( $old_value );
					}
				}
				$fields_to_save[ $field_name ] = $new_value;
			}
		}
		if ( count( $fields_to_save ) ) {
			$where = array( 'entity_id' => $entity_id );

			if ( count( $field_values ) ) {
				$this->update( $fields_to_save, $where );
			} else {
				$fields_to_save['entity_id'] = $entity_id;
				$this->insert( $fields_to_save );
			}
		}
	}



	/**
	 * Function save_file
	 *
	 * @param string $field_name Field name.
	 * @param string $old_value Old value.
	 * @param string $new_value New value.
	 *
	 * @return bool
	 */
	public function save_file( $field_name, $old_value, $new_value ) {
		$dir_path = ABSPATH . $this->upload_dir;
		if ( ! is_dir( $dir_path ) ) {
			$this->make_dir( $dir_path );
		}
		if ( move_uploaded_file( $_FILES[ $field_name ]['tmp_name'], $dir_path . '/' . $new_value ) ) {
			chmod( $dir_path . '/' . $new_value, 0666 );
			if ( $old_value ) {
				unlink( $dir_path . '/' . $old_value );
			}

			return true;
		} else {

			return false;
		}
	}

	/**
	 * Function delete_file
	 *
	 * @param string $file_name File name.
	 *
	 * @return bool
	 */
	public function delete_file( $file_name ) {
		$dir_path = ABSPATH . $this->upload_dir;
		if ( $file_name ) {
			return unlink( $dir_path . '/' . $file_name );
		} else {
			return false;
		}
	}



	/**
	 * Function make_dir
	 * @param string $dir_path Path to dir.
	 */
	public function make_dir( $dir_path ) {
		mkdir( $dir_path, 0666, true );

		if ( false !== strpos( $dir_path, 'uploads' ) ) {
			$uploads  = strstr( $dir_path, 'uploads', true ) . 'uploads';
			$to_chmod = str_replace( $uploads, '', $dir_path );
			while ( $to_chmod ) {
				chmod( $uploads . $to_chmod, 0666 );
				$rpos      = strrpos( $to_chmod, '/' );
				$to_delete = substr( $to_chmod, $rpos );
				$to_chmod  = str_replace( $to_delete, '', $to_chmod );
			}
		}
	}


	/**
	 * Function get_field
	 *
	 * @param int    $entity_id Entity id.
	 * @param string $field_name Field name.
	 *
	 * @return string|bool
	 */
	public function get_field_value( $entity_id, $field_name ) {
		$field_values = $this->get_field_values( $entity_id );
		return ( is_array( $field_values ) and array_key_exists( $field_name, $field_values ) ) ? $field_values['$field_name'] : false;
	}

	/**
	 * Function get_field
	 *
	 * @param int $entity_id Entity id.
	 *
	 * @return array|bool
	 */
	public function get_field_values( $entity_id ) {
		return $this->get_by( array( 'entity_id' => $entity_id ) , '=', true, null, null, null, null, null, ARRAY_A );
	}


	/**
	 * Function edit_form_tag
	 *
	 * @return void
	 * */
	public function edit_form_enctype() {
		echo ' enctype="multipart/form-data" ';
	}

	/**
	 * Function delete_field_values
	 *
	 * @param $entity_id
	 *
	 * @return bool|int  It returns the number of rows updated, or false on error.
	 */
	public function delete_field_values( $entity_id ) {
		$where = [ 'entity_id' => $entity_id ];

		return $this->delete( $where );
	}
}
