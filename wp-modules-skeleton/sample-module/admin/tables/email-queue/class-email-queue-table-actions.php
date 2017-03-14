<?php
namespace WP_Modules_Skeleton\Sample_Module\Admin\Tables\Email_Queue;

class Email_Queue_Table_Actions {

	public $model;

	/**
	 * __construct
	 * @param $model
	 *
	 * */
	public function __construct( $model ) {

		$this->model = $model;

		add_action( 'wp_ajax_update_email_queue_item', [ $this, 'update_email_queue_item_action' ] );
	}

	public function update_email_queue_item_action() {
		$post_data = filter_input_array( INPUT_POST,[
			'data' => [
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_REQUIRE_ARRAY,
			]
		]);

		$dao = $this->model->get_DAO( 'email-queue' );
		$num_rows_updated = $dao->update( [ 'fk_status_id' => $post_data['data']['status'] ], [ 'id' => $post_data['data']['id'] ] );
		if ( $num_rows_updated ) {
			wp_send_json_success( [ 'message' => __( 'Status has been change successfully' ) ] );
		}else{
			wp_send_json_error( [ 'message' => __( 'Error while changing status' ) ] );
		}
	}
}