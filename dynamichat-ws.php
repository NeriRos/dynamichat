<?php
/**
 * @package dynamichat-ws
 */

/*
Plugin name: DynamiChat WebSockets
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

// require_once dirname( __FILE__ ) . '/chat-server.php';

// Global vars
define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_NAME', plugin_basename( __FILE__ ) );

use Inc\Base\Activate;
use Inc\Base\Deactivate;

// De/activate plugin methods
function activate() {
    Activate::activate();
}
function deactivate() {
    Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'activate' );
register_deactivation_hook( __FILE__, 'deactivate' );

// Shortcodes
function get_content($file_path)
{
    ob_start();
    include $file_path;
    $contents = ob_get_clean();

    return $contents;
}

function chat_client_content( )
{
    return get_content(PLUGIN_PATH . 'templates/chatClient.php');
}

function chat_details_content( )
{
    return get_content(PLUGIN_PATH . 'templates/chatDetails.php');
}

add_shortcode( 'dynamichat', 'chat_client_content' );
add_shortcode( 'dynamichat_details', 'chat_details_content' );


// Init call
Inc\Init::register_services();
