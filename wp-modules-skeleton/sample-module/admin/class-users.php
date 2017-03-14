<?php
namespace WP_Modules_Skeleton\Sample_Module\Admin;

class Users {

	public $model;

	public function __construct( $model ) {
		$this->model = $model;
		add_action( 'delete_user', [ $this, 'delete_user' ] );
		$meta_type = 'user';
		add_action( "updated_{$meta_type}_meta", [ $this, 'update_user_meta' ], 10, 4 );
	}

	/**
	 * Function delete_user
	 * @param $user_id
	 *
	 * */
	public function delete_user( $user_id ) {
		$this->stop_user_orders( $user_id, 'user_deleted' );
	}

	/**
	 * Function delete_user
	 *
	 * @param $meta_id
	 * @param $object_id
	 * @param $meta_key
	 * @param $_meta_value
	 *
	 * */
	public function update_user_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		global $wpdb;
		if( $wpdb->prefix . 'w3dev_user_banned' == $meta_key && true == $_meta_value ) {
			$this->stop_user_orders( $object_id, 'user_banned' );
		}
	}

	/**
	 * Function stop_user_orders
	 * @param $user_id
	 * @param $action_name
	 *
	 * */
	public function stop_user_orders( $user_id, $action_name = '' ) {
		$group_order_opened = $this->model->get_DAO( 'group-orders' )->get_ext_group_orders( [ 'filter' => [ 'status' => [ 'opened' ,'opened_ready', 'opened_prolongated', 'handling', 'delivering', 'delivering_sms_sent' ] ] ] );
		foreach ( $group_order_opened as $group_order ) {
			$user_orders = $this->model->get_DAO( 'user-orders' )->get_by( [ 'fk_group_order_id' => $group_order->id, 'status' => 'active', 'fk_user_id' => $user_id ] );
			foreach( $user_orders as $user_order ) {
				$comment_message = __( 'User order has been stopped on ' . current_date( 'Y-m-d H:i' ) , 'morfresh' );
				$comment_message .= __( ' Reason : ' . $action_name , 'morfresh' );
				$this->model->get_DAO( 'user-orders' )->update( [ 'status' => 'stopped', 'comment' => $comment_message ], [ 'id' => $user_order->id ] );
			}
		}
	}
}