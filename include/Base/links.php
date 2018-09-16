<?php
/**
 * @package dynamichat-ws
 */
namespace Inc\Base;

class Links 
{

    function register() {
        add_filter( 'plugin_action_links_'. PLUGIN_NAME, array( $this, 'settings_link') );
    }

    function settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=dynamichat_settings">Settings</a>';
        
        array_push($links, $settings_link);

        return $links;
    }
}