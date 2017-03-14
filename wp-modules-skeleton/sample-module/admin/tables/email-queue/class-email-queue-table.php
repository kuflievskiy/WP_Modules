<?php
namespace WP_Modules_Skeleton\Sample_Module\Admin\Tables\Email_Queue;

use WP_Modules\Core\WP_List_Table_Extended;

/**
 * Class Email_Queue_Table
 *
 * @link   https://codex.wordpress.org/Class_Reference/WP_List_Table
 * */
class Email_Queue_Table extends WP_List_Table_Extended {

	private $singular;
	private $plural;
	private $admin_page_slug = 'scheduled-emails';

	protected $columns;
	protected $template_path;

	public $model;
	public $module_config;
	public $queue_statuses;

	/**
	 * __construct
	 * @param $model
	 *
	 * */
	public function __construct( $model ) {

		$this->plural        = __( 'Scheduled Emails' );
		$this->singular      = __( 'Scheduled Email' );
		$this->module_config = include ( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/config.php';

		/**
		 * @var $queue_statuses_DAO \Morfresh\Shop_Module\DAO\Queue_Statuses
		 * */
		$queue_statuses_DAO = $model->get_DAO( 'queue-statuses' );
		$this->queue_statuses = $queue_statuses_DAO->get_all();

		parent::__construct( [
			'singular' => $this->singular,
			'plural'   => $this->plural,
			'ajax'     => false,
			'screen'   => $this->admin_page_slug,
		] );

		$this->model = $model;

		$this->columns = [
			'id'               => __( 'ID' ),
			'send_at'          => __( 'Scheduled to send at' ),
			'comment'          => __( 'Сomment' ),
			'user_email'       => __( 'Email' ),
			'fk_user_id'       => __( 'User' ),
			'template'         => __( 'Template' ),
			'fk_user_order_id' => __( 'User Order ID' ),
			'status'           => __( 'Status' ),
		];

		$this->template_path = dirname( __FILE__ ) . '/templates/' . $this->admin_page_slug . '.tpl.php';



		$this->register_actions( [
			[
				'name'     => 'admin_menu',
				'function' => 'add_admin_pages',
				'priority' => 11,
			],
		] );

		if ( $this->admin_page_slug == $this->_get_sanitized_request( 'page' ) ) {
			$this->register_actions( [
				[
					'name'     => 'admin_head',
					'function' => 'add_scripts',
					'priority' => 4,
				],
				[
					'name'     => 'admin_head',
					'function' => 'add_styles',
					'priority' => 3,
				],
			] );
		}

		new Email_Queue_Table_Actions( $model );
	}

	public function get_plural() {
		return $this->plural;
	}

	/**
	 * Register all actions from here
	 *
	 * @param $actions
	 */
	private function register_actions( $actions ) {
		foreach ( $actions as $action ) {
			add_action( $action[ 'name' ], array( $this, $action[ 'function' ] ), $action[ 'priority' ] );
		}
	}

	/**
	 * Helper to get sanitized request params
	 *
	 * @param string $param
	 * @param string $method
	 *
	 * @return mixed
	 */
	private function _get_sanitized_request( $param = '', $method = 'get' ) {
		return filter_input( ( ( $method == 'get' ) ? INPUT_GET : INPUT_POST ), $param, FILTER_SANITIZE_STRING );
	}

	/**
	 * Function add_admin_pages
	 */
	public function add_admin_pages() {
		add_menu_page( $this->plural, $this->plural, 'moderate_comments', $this->admin_page_slug, [ $this, 'render_page' ] );
	}

	/**
	 * Function add_styles
	 * */
	public function add_styles() {
		wp_enqueue_style( 'thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', null, '1.0' );
	}

	/**
	 * Function add_styles
	 * */
	public function add_scripts() {
		wp_enqueue_script( 'email-queue', WPMU_PLUGIN_URL . '/wp-modules-skeleton/sample-module/admin/tables/email-queue/js/email-queue.js',[ 'jquery' ] );
		wp_enqueue_script( 'thickbox', null, [ 'jquery' ] );
		wp_print_scripts();
	}

	public function get_sortable_columns() {
		return [
			'id'               => [ 'id', false ],
			'send_at'          => [ 'send_at', false ],
			'comment'          => [ 'comment', false ],
			'user_email'       => [ 'user_email', false ],
			'fk_user_id'       => [ 'fk_user_id', false ],
			'template'         => [ 'template', false ],
			'fk_user_order_id' => [ 'fk_user_order_id', false ],
			'status'           => [ 'status', false ],
		];
	}

	/**
	 * Function prepare_items
	 *
	 * Handles data query and filter, sorting, and pagination.
	 *
	 */
	public function prepare_items() {

		/**
		 * @var $DAO \Morfresh\Shop_Module\DAO\Email_Queue
		 *
		 * */
		$DAO = $this->model->get_DAO( 'email-queue' );

		$columns               = $this->get_columns();
		$hidden                = [];
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = [ $columns, $hidden, $sortable ];
		$input_per_page        = filter_var( $_REQUEST['per_page'], FILTER_SANITIZE_NUMBER_INT );
		if ( ! $input_per_page ) {
			$input_per_page = 20;
		}

		$per_page     = $this->get_items_per_page( 'per_page', $input_per_page );
		$current_page = $this->get_pagenum();

		$filter_get = filter_input_array( INPUT_GET, [
			'orderby' => FILTER_SANITIZE_STRING,
			'order'   => FILTER_SANITIZE_STRING,
		] );

		$orderby = $filter_get['orderby'] ? $filter_get['orderby'] : 'id';
		$order = $filter_get['order'] ? $filter_get['order'] : 'desc';

		$items = $DAO->get_scheduled_emails( [
			'limit'   => $per_page,
			'offset'  => ( $current_page - 1 ) * $per_page,
			'order'   => $order,
			'orderby' => $orderby,
		] );

		$total_items = count( $DAO->get_all() );

		$this->set_pagination_args( [
			'total_items' => $total_items,                      // WE have to calculate the total number of items
			'per_page'    => $per_page,                         // WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page ),  // WE have to calculate the total number of pages
		] );
		$this->items = $items;
	}

	public function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'send_at' :
				return date( 'd.m.Y H:i:s', strtotime( $item->$column_name ) );
			default :
				return $item->$column_name;
		}
	}

	public function column_fk_user_id( $item ) {
		return '<a target="_balnk" href="/wp-admin/user-edit.php?user_id=' . $item->fk_user_id . '">' . $item->user_login . '</a>';
	}

	public function column_status( $item ) {
		if ( ! $item->status ) {
			return 'Ошибка (статус не установлен)';
		}

		$html = '<select class="status" data-id="' . $item->id . '">';
		foreach( $this->queue_statuses as $status ) {
			$is_selected = $item->fk_status_id == $status->id ? 'selected' : '';
			$html .= '<option value="' . $status->id . '" ' . $is_selected . '>' . $this->module_config['queue_statuses'][$status->status] . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
}