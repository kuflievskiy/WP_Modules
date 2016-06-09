<?php
namespace WP_Modules_Skeleton\Sample_Module\DAO;

use WP_Modules\Db\DAO;

/**
 * Class Orders_DAO
 *
 * @package Checkout
 *
 * @property $wpdb_securedb
 * @property $table_orders
 */
class Sample_Table extends DAO {

	protected $wpdb_securedb;
	protected $table_name = 'wp_users';

	/**
	 * __construct
	 * */
	public function __construct() {
		parent::__construct( $this->table_name );
	}
}
