<?php
/**
 * @package dynamichat
 */
namespace Inc\Pages;

class ChatClient
{
    private $chats = array();
    public $user = array();
    
    function __construct()
    {
        $this->chats = array(
            array(
                'picture' => 'default_user.png',
                'text' => 'welcome to Dymanichat!',
                'date' => '10.1.18',
                'isSenderSelf' => false,
                'init' => true,
                'id' => 0
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
            register_rest_route( 'chat/v1', '/messages', array(
                    'methods' =>  \WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_chats' )
            ) );
        } );
    }  

    public function send_message() {
        $data = json_decode(file_get_contents('php://input'), true);

        $message = array(
            'message' => $data['message'],
            'from' => $data['user']['id'],
            'to' => 'automatic',
            'date' => $data['date'],
            'status' => 0,
            'id' => 0
        );

        $args = array(
            'method' => \WP_REST_Server::CREATABLE,
            'headers' => array(
                'Content-type' => 'application/json',
                'origin' => 'localhost:15255'
            ),
            'body' => json_encode(
                array(
                    'user' => $this->user,
                    'message' => $message
                )
            )
        );
        $res = wp_remote_post( 'http://localhost:8887/chat/sendSupportMessage', $args );

        if ( $res['response']['code'] == 200 ) {
            $message['status'] = 1;
        }

        return array( 'server' => $res, 'message' => $message );
    }

    function get_message_by_id( $id ) {

    }

    function save_message( $message ) {
        global $wpdb; 

        $tablename = $wpdb->prefix . 'dynamichat_messages';
        if ( $this->user['name'] && $this->user['phone'] )
        {
            $res = $wpdb->insert( $tablename, array(
                    'full_name' => $this->user['name'], 
                    'business_name' => $this->user['business'],
                    'phone' => $this->user['phone'] 
                ), array( '%s', '%s', '%s') 
            );

            return ( $res == 1 ? $wpdb->insert_id : false );
        } 
        else
        {
            return "no user data";
        }
    }

    private function filter_chats( $chat ) {
        $latest_id = $_GET['latestID'];
        return $chat['id'] > $latest_id;
    }

    public function get_chats() {
        return array_filter( $this->chats, array( $this, 'filter_chats' ) );
    }
}