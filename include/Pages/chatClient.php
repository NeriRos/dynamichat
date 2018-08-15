<?php
/**
 * @package dynamichat
 */
namespace Inc\Pages;

class ChatClient
{
    private $chats = array();

    function __construct()
    {
        $this->chats = array(
            array(
                'picture' => 'default_user.png',
                'text' => 'welcome to dymanichat!',
                'date' => '10.1.18',
                'isSenderSelf' => false
            ),
            array(
                'picture' => 'default_user.png',
                'text' => 'thanks!',
                'date' => '11.1.18',
                'isSenderSelf' => true
            )
        );
    }

    function register() 
    {    
        add_action( 'rest_api_init', function () {
            register_rest_route( 'chat/v1', '/new_message', array(
                    'methods' =>  \WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'send_message' )
            ) );
        } );
        
        add_action( 'rest_api_init', function () {
            register_rest_route( 'chat/v1', '/new_message', array(
                    'methods' =>  \WP_REST_Server::READABLE,
                    'callback' => array( $this, 'send_message' )
            ) );
        } );

    }

    public function send_message() {
        $args = array();
        $res = \WP_Http::request( 'http://localhost/chat/new_message', $args );
    }

    public function get_chats() {
        return $this->chats;
    }
}