<?php

namespace WP_CLI_Nwp;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

\WP_CLI::add_command( 'nwp', 'WP_CLI_Nwp\Nwp_Command' );