<?php
/**
 * @package dynamichat-ws
 */
namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Api\Callbacks\ChatSettingsCallback;

class Admin
{
    public $settings;
    public $callbacks;

    public function register()
    {
        add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );

        $this->settings = new SettingsApi();
        $this->callbacks = new ChatSettingsCallback();

        $this->set_settings();
        $this->set_sections();
        $this->set_fields();

        $this->settings->register();
    }

    public function add_admin_pages()
    {
        add_menu_page("DynamiChat Plugin", "DynamiChat", "manage_options", "dynamichat_settings", array( $this, 'admin_index' ), 'dashicons-format-chat', 110);
    }

    public function admin_index()
    {
        require_once PLUGIN_PATH . 'templates/admin.php' ;
    }

    public function set_settings()
    {
        $args = array(
            array(
                'option_group' => 'chat_options_group',
                'option_name' => 'server_uri',
                'callback' => array( $this->callbacks, 'server_uri_check' )
            )
        );

        $this->settings->set_settings( $args );
    }

    public function set_sections()
    {
        $args = array(
            array( // chat_settings_section
                'id' => 'chat_settings_section',
                'title' => 'Settings',
                'page' => 'dynamichat_settings'
            )
        );

        $this->settings->set_sections( $args );
    }

    public function set_fields() 
    {
        $args = array(
            array( // chat_server_uri_input
                'id' => 'server_uri',
                'title' => 'Server API URI:',
                'callback' => array( $this->callbacks, 'server_uri_html' ),
                'page' => 'dynamichat_settings',
                'section' => 'chat_settings_section',
                'args' => array(
                    'label_for' => 'server_uri',
                    'class' => 'settings_input'
                )
            )
        );

        $this->settings->set_fields( $args );
    }
}