<?php

use App\Nwp_Command;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

$nwp_autoloader = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $nwp_autoloader ) ) {
	require_once $nwp_autoloader;
}
var_dump(WP_CLI::add_command( 'nwp', 'App\Nwp_Command' ));