<?php
/**
 * App
 * @package App
 */

namespace WP_Modules\Db;

/**
 * Interface I_DAO
 * @package App
 */
interface I_DAO {

	/**
	 * Function insert
	 *
	 * @param array $data Data.
	 *
	 * @param array $format Format.
	 *
	 * @return mixed
	 */
	public function insert( $data, $format = null );

	/**
	 * Function update
	 *
	 * @param array $data Data.
	 *
	 * @param array $where Where.
	 *
	 * @param array $format Format.
	 *
	 * @param array $where_format Where format.
	 *
	 * @return mixed
	 */
	public function update( $data, $where, $format = null, $where_format = null );

	/**
	 * Function delete
	 *
	 * @param array $where Where.
	 *
	 * @param array $where_format Where format.
	 *
	 * @return mixed
	 */
	public function delete( $where, $where_format = null );

	/**
	 * Function replace
	 *
	 * @param array $data Data.
	 *
	 * @param array $format Format.
	 *
	 * @return mixed
	 */
	public function replace( $data, $format = null );

	/**
	 * Function replace
	 *
	 * @param array  $fields Fields.
	 *
	 * @param array  $order_by Order.
	 *
	 * @param string $direction Direction.
	 *
	 * @param string $group_by Group.
	 *
	 * @param int    $limit Limit.
	 *
	 * @param string $output Output.
	 *
	 * @return mixed
	 */
	public function get_all( $fields = array(), $order_by = null, $direction = null, $group_by = null, $limit = null, $output = OBJECT );

	/**
	 * Function get_by
	 *
	 * @param array  $condition_value Condition value.
	 *
	 * @param string $condition Condition.
	 *
	 * @param bool   $return_single_row Single row.
	 *
	 * @param string $select_columns Select column.
	 *
	 * @param array  $order_by Order.
	 *
	 * @param string $direction Direction.
	 *
	 * @param string $group_by Group.
	 *
	 * @param int    $limit Limit.
	 *
	 * @param string $output Output.
	 *
	 * @return mixed
	 */
	public function get_by( array $condition_value, $condition = '=', $return_single_row = false, $select_columns = '*', $order_by = null, $direction = null, $group_by = null, $limit = null, $output = OBJECT );

	/**
	 * Function set_field_to_null
	 *
	 * @param $field_name
	 * @param array $where
	 *
	 * @return bool|false|int
	 */
	public function set_field_to_null( $field_name, $where = [] );
}

/**
 * Class DAO
 * This class should be used to extend other classes with basic CRUD methods.
 *
 * This class is based on https://codex.wordpress.org/Class_Reference/wpdb
 *
 * @version 0.0.1
 * @author Kuflievskiy Aleksey <kuflievskiy@gmail.com>
 *
 * @property \wpdb $db
 * @property string $table_name
 * */
abstract class DAO implements I_DAO {

	/**
	 * DAO
	 * @var \wpdb
	 */
	protected $db;

	/**
	 * DAO
	 * @var string
	 */
	protected $table_name;

	/**
	 * Function __construct
	 * This function is used to instantiate class properties.
	 *
	 * @param string $table_name Table name.
	 *
	 * @param \wpdb  $db DB.
	 */
	public function __construct( $table_name, \wpdb $db = null ) {
		if ( null === $db ) {
			global $wpdb;
			$this->db = $wpdb;
		} else {
			$this->db = $db;
		}

		$this->table_name = $table_name;
	}

	/**
	 * Function insert
	 *
	 * @param array  $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 *
	 * @param string $format Format.
	 *
	 * @return int|bool returns row id or `false`
	 * */
	public function insert( $data, $format = null ) {
		$result = $this->db->insert( $this->table_name, $data, $format );
		return ( $result ) ? $this->db->insert_id : false;
	}

	/**
	 * Function update
	 *
	 * @param array        $data Data to update (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped). This means that if you are using GET or POST data you may need to use stripslashes() to avoid slashes ending up in the database.
	 * @param array        $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will be joined with ANDs. Both $where columns and $where values should be "raw".
	 * @param array|string $format (optional) An array of formats to be mapped to each of the values in $data. If string, that format will be used for all of the values in $data.
	 * @param array|string $where_format A format is one of '%d', '%f', '%s' (integer, float, string; see below for more information).
	 *
	 * @return bool|int This function returns the number of rows updated, or false if there is an error.
	 * */
	public function update( $data, $where, $format = null, $where_format = null ) {
		if ( empty( $data ) ) {
			return false;
		}

		return $this->db->update( $this->table_name, $data, $where, $format, $where_format );
	}

	/**
	 * Function delete
	 *
	 * @param array $where A named array of WHERE clauses (in column -> value pairs). Multiple clauses will be joined with ANDs. Both $where columns and $where values should be 'raw'.
	 * @param array $where_format A format is one of '%d', '%f', '%s' (integer, float, string; see below for more information).
	 *
	 * @return bool|int  It returns the number of rows updated, or false on error.
	 * */
	public function delete( $where, $where_format = null ) {
		if ( empty( $where ) ) {
			return false;
		}
		return $this->db->delete( $this->table_name, $where, $where_format = null );
	}

	/**
	 * Function replace
	 * Replace a row in a table if it exists or insert a new row in a table if the row did not already exist.
	 *
	 * @link : https://codex.wordpress.org/Class_Reference/wpdb#REPLACE_row
	 *
	 * @param array      $data Data.
	 * @param null|array $format Format.
	 *
	 * @return bool|int
	 * */
	public function replace( $data, $format = null ) {
		if ( empty( $data ) ) {
			return false;
		}
		return $this->db->replace( $this->table_name, $data, $format );
	}

	/**
	 * Function get_all
	 *
	 * @param array  $fields Fields.
	 * @param string $order_by Order.
	 * @param string $direction Direction.
	 * @param string $group_by Group.
	 * @param int    $limit Limit.
	 * @param string $output Output
	 * OBJECT 	- result will be output as a numerically indexed array of row objects.
	 * OBJECT_K - result will be output as an associative array of row objects, using first column's values as keys (duplicates will be discarded).
	 * ARRAY_A 	- result will be output as a numerically indexed array of associative arrays, using column names as keys.
	 * ARRAY_N 	- result will be output as a numerically indexed array of numerically indexed arrays.
	 *
	 * @return array
	 * If no matching rows are found, or if there is a database error, the return value will be an empty array.
	 * If your $query string is empty, or you pass an invalid $output_type, NULL will be returned.
	 * */
	public function get_all( $fields = array(), $order_by = null, $direction = null, $group_by = null, $limit = null, $output = OBJECT ) {
		if ( ! count( $fields ) ) {
			$field_names = '*';
		} else {
			$field_names = implode( ', ', $fields );
		}
		$sql = 'SELECT ' . $field_names .  ' FROM `'.$this->table_name.'`';

		if ( ! empty( $group_by ) ) {
			$sql .= ' GROUP BY ' . $group_by;
		}
		if ( ! empty( $order_by ) ) {
			$sql .= ' ORDER BY ' . $order_by;
			if ( ! empty( $direction ) ) {
				$sql .= ' ' . $direction;
			}
		}

		if ( ! empty( $limit ) ) {
			$sql .= ' LIMIT ' . $limit;
		}

		$all = $this->db->get_results( $sql, $output );
		return $all;
	}


	/**
	 * Function get_by
	 *
	 * This function is used to get a value by a condition
	 *
	 * @param array        $condition_value A key value pair of the conditions you want to search on.
	 * @param string       $condition A string value for the condition of the query default to equals.
	 * @param bool         $return_single_row Single row.
	 * @param array|string $fields Fields.
	 * @param null|string  $order_by Order.
	 * @param null|string  $direction Direction.
	 * @param null|string  $group_by Group.
	 * @param null|string  $limit Limit.
	 * @param null|string  $output Output.
	 * @throws \Exception Exception.
	 *
	 * @return object|array|bool result
	 */
	public function get_by( array $condition_value, $condition = '=', $return_single_row = false, $fields = array(), $order_by = null, $direction = null, $group_by = null, $limit = null, $output = OBJECT ) {
		try {
			$sql = 'SELECT ';

			if ( ! count( $fields ) ) {
				$field_names = '*';
			} else {
				$field_names = implode( ', ', $fields );
			}

			$sql .= $field_names;

			$sql .= ' FROM `' . $this->table_name . '`';

			if ( ! empty( $condition_value ) && is_array( $condition_value ) ) {
				$sql .= ' WHERE ';
				$condition_counter = 1;
				foreach ( $condition_value as $field => $value ) {
					if ( $condition_counter > 1 ) {
						$sql .= ' AND ';
					}

					switch ( strtolower( $condition ) ) {
						case 'in':
							if ( ! is_array( $value ) ) {
								throw new \Exception( 'Values for IN query must be an array.', 1 );
							} else {
								foreach ( $value as $k => $v ) {
									$value[ $k ] = $this->db->prepare( '%s', $v );
								}
								$value = '(' . implode( ',', $value ) . ')';
							}
							$sql .= '`' . $field . '` IN ' . $value;
							break;
						default:
							$sql .= $this->db->prepare( '`' . $field . '` ' . $condition . ' %s', $value );
							break;
					}
					$condition_counter++;
				}
			}

			if ( ! empty( $group_by ) ) {
				$sql .= ' GROUP BY ' . $group_by;
			}

			if ( ! empty( $order_by ) ) {
				$sql .= ' ORDER BY ' . $order_by;
				if ( ! empty( $direction ) ) {
					$sql .= ' ' . $direction;
				}
			}

			if ( ! empty( $limit ) ) {
				$sql .= ' LIMIT ' . $limit;
			}

			$result = $this->db->get_results( $sql, $output );

			// As this will always return an array of results if you only want to return one record make $return_single_row TRUE.
			if ( count( $result ) && $return_single_row ) {
				$result = $result[0];
			}

			return $result;
		} catch ( \Exception $ex ) {
			return false;
		}
	}

	/**
	 * Function set_field_to_null
	 *
	 * @param $field_name
	 * @param array $where
	 *
	 * @return bool|false|int
	 */
	public function set_field_to_null( $field_name, $where = [] ) {

		if ( empty( $field_name ) ) {
			return false;
		} else {
			$field_name = filter_var( $field_name, FILTER_SANITIZE_STRING );
		}

		if ( ! empty( $where ) && is_array( $where ) ) {

			$sql = 'UPDATE `' . $this->table_name . '` SET `' . $field_name . '` = NULL ';
			$condition = '=';
			$sql_where = '';

			$condition_counter = 1;
			foreach ( $where as $field => $value ) {
				if ( $condition_counter > 1 ) {
					$sql_where .= ' AND ';
				}
				$sql_where .= $this->db->prepare( '`' . $field . '` ' . $condition . ' %s', $value );
				$condition_counter++;
			}
		} else {
			return false;
		}

		if ( $sql_where ) {
			$sql .= 'WHERE ' . $sql_where;
		}

		return $this->db->query( $sql );
	}
}
