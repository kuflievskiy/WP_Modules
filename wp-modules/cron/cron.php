<?php
/**
 * Your server crontab could now look something like: * * * * * wget --no-check-certificate -O /dev/null https://%SITE_DOMAIN%/wp-content/mu-plugins/app/cron.php?secret_key=%SMD_SECRET_KEY% >/dev/null 2>&1
 * */

require_once strstr( __FILE__, 'wp-content', true ) . 'wp-load.php';

if ( AUTH_KEY !== filter_input( INPUT_GET, 'key', FILTER_SANITIZE_STRING ) ) {
	exit;
}

$site_url  = site_url();
$cron_jobs = include 'cron-config.php';
$resolver  = new \Cron\Resolver\ArrayResolver();

foreach ( $cron_jobs as $cron_job ) {
	foreach ( $cron_job as $schedule => $command ) {
		$job = new \Cron\Job\ShellJob();
		$job->setCommand( $command );
		$job->setSchedule( new \Cron\Schedule\CrontabSchedule( $schedule ) );
		$resolver->addJob( $job );
	}
}

$cron = new \Cron\Cron();
$cron->setExecutor( new \Cron\Executor\Executor() );
$cron->setResolver( $resolver );
$cron->run();
