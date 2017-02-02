<?php /*
	Plugin Name: WP Normalize
	Plugin URI: https://github.com/bramwillemse/wp-normalize
	Description: Set of base functions
	Version: 1.0.6
	Author: Bram Willemse
	Author URI: http://www.bramwillemse.nl
	Tested up to: 4.6.1
*/

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

$normalizeWP = new normalizeWP();

?>