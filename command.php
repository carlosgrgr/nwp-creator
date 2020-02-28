<?php

namespace WP_CLI_Nwp;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

// $nwp_autoloader = __DIR__ . '/vendor/autoload.php';

\WP_CLI::add_command( 'nwp', 'WP_CLI_Nwp\Nwp_Command' );