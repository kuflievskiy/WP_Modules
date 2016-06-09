<?php
/**
 * Cron
 * @package Cron
 */

namespace WP_Modules\Cron;

/**
 * Class Cron
 *
 * @package Cron
 */
abstract class Cron {

	/**
	 * Function __construct
	 */
	public function __construct() {
		// @codingStandardsIgnoreStart
		add_filter( 'cron_schedules', array( $this, 'cron_intervals' ) );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Function cron_intervals
	 *
	 * Adds additional cron intervals
	 *
	 * @return mixed
	 */
	public function cron_intervals() {
		// Add a '5 min' interval.
		$schedules['each_5_min'] = array(
			'interval' => 300,
			'display' => __( 'Each 5 min' ),
		);
		// Add a '10 min' interval.
		$schedules['each_10_min'] = array(
			'interval' => 600,
			'display' => __( 'Each 10 min' ),
		);
		// Add a '15 min' interval.
		$schedules['each_15_min'] = array(
			'interval' => 900,
			'display' => __( 'Each 15 min' ),
		);
		// Add a '30 min' interval.
		$schedules['each_30_min'] = array(
			'interval' => 1800,
			'display' => __( 'Each 30 min' ),
		);

		return $schedules;
	}

	/**
	 * Function register_cron
	 *
	 * Registers cron event
	 *
	 * @param string $schedule holds cron interval.
	 * @param string $task_hook holds task hook name.
	 * @param array  $action holds function to call and context.
	 */
	public function register_cron( $schedule, $task_hook, $action ) {
		if ( ! wp_next_scheduled( $task_hook ) ) {

			wp_schedule_event( time(), $schedule, $task_hook );
		}

		add_action( $task_hook, $action );
	}

	/**
	 * Function unset_cron
	 *
	 * Unsets cron
	 *
	 * @param string $action_hook holds task hook name.
	 */
	public function unset_cron( $action_hook ) {

		wp_clear_scheduled_hook( $action_hook );
	}
}
