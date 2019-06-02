<?php
/**
 * Composer class autoloader.
 *
 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since  2019-06-01
 */

$autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

if ( ! is_readable( $autoload ) ) {
	return;
}

require_once $autoload;
