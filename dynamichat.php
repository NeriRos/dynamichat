<?php
/**
 * @package dynamichat
 */

/*
Plugin name: DynamiChat
Plugin URI: https://lightx.co.il/wp-plugins/dynamichat
Description: Chat client that connects with json to any server dynamicly!
Version: 1.0
Author: Neriya Rosner - LightX
Author URI: https://lightx.co.il/
License: GPLv2 or later
*/

( defined( 'ABSPATH' ) and function_exists( 'add_action' ) ) or die;

if( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) 
{
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_NAME', plugin_basename( __FILE__ ) );

function activate() {
    Inc\Base\Activate::activate();
}
function deactivate() {
    Inc\Base\Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'activate' );
register_deactivation_hook( __FILE__, 'deactivate' );

function get_local_file_contents( ) {
    $file_path = PLUGIN_PATH . 'templates\chatClient.php';

    ob_start();
    include $file_path;
    $contents = ob_get_clean();

    return $contents;
}

add_shortcode( 'dynamichat', 'get_local_file_contents' );


if( class_exists( 'Inc\\Init' ) ) 
{
    Inc\Init::register_services();
}