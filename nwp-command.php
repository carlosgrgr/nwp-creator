<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

$nwp_autoloader = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $nwp_autoloader ) ) {
	require_once $nwp_autoloader;
}
WP_CLI::add_command( 'nwp', 'Nwp_Command' );