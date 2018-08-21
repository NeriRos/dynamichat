<?php
/**
 * @package dynamichat
 */
namespace Inc\Pages;

class ChatClient
{
    public $chats = array();
    public $support = array();
    public $message = array();
    
    function __construct()
    {        

    }
    
    function register() 
    {    
        add_action( 'rest_api_init', function () {
            register_rest_route( 'chat/v1', '/init', array(
                    'methods' =>  \WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'init' )
            ) );
        } );

        add_action( 'rest_api_init', function () {
            register_rest_route( 'chat/v1', '/messages', array(
                    'methods' =>  \WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_chats' )
            ) );
        } );

        add_action( 'rest_api_init', function () {
            register_rest_route( 'chat/v1', '/new_message', array(
                    'methods' =>  \WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'send_message' )
            ) );
        } );
    }  

    function init( $data ) {
        $data = json_decode(file_get_contents('php://input'), true);
    
        $client = new self();
        $client->support = $data['support'];

        return $client;
    }

    public function get_chats() {
        $args = array(
            'method' => \WP_REST_Server::READABLE,
            'headers' => array(
                'origin' => 'localhost:15255'
            )
        );
        
        $res = wp_remote_post( 'http://localhost:8887/support/getChats/' . $_GET['chatID'] . '?id=' . $_GET['userID'], $args );
        
        if ( is_wp_error( $res ) ) {
            $error_string = $res->get_error_message();
            echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
            return;
        }

        $body = json_decode( $res['body'] );
        
        $chats = array_merge( $this->chats, $body->chats );
        $chats = array_map( array( $this, 'map_chats' ), $chats );
        // $chats = array_filter( $chats, array( $this, 'filter_chats' ) );

        return array( 'chats' => $chats, 'isAvailableRep' => $body->isAvailableRep );
    }

    public function send_message() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $this->message = array(
            'user' => $data['user'],
            'message' => $data['message'],
            'from' => $data['from'],
            'contact' => 'automatic',
            'date' => $data['date'],
            'isSenderSelf' => $data['isSenderSelf'],
            'status' => 0,
            'id' => $data['id']
        );

        $args = array(
            'method' => \WP_REST_Server::CREATABLE,
            'headers' => array(
                'Content-type' => 'application/json',
                'origin' => 'localhost:15255'
            ),
            'body' => json_encode( $this->message )
        );

        $res = wp_remote_post( 'http://localhost:8887/support/sendMessage', $args );
        
        if ( $res['response']['code'] == 200 ) {
            $this->message['status'] = 1;
            $this->message['to'] = json_decode( $res['body'] )->status;
        }

        $this->save_message( $this->message );

        return array( 'server' => $res, 'message' => $this->message );
    }

    function save_message( $message ) {
        global $wpdb; 

        $tablename = $wpdb->prefix . 'dynamichat_messages';
        if ( $message['message'] )
        {
            $res = $wpdb->insert( $tablename, array(
                    'userID' => $message['id'], 
                    'message_text' => $message['message'],
                    'message_date' => $message['date'],
                    'isSenderSelf' => ( $message['isSenderSelf'] ? 1 : 0),
                    'contact' => $message['to'],
                    'is_company' => 1,
                ), array( '%s', '%s', '%s', '%s', '%s', '%s') 
            );

            return ( $res == 1 ? $wpdb->insert_id : false );
        } 
        else
        {
            return "no user data";
        }
    }

    function get_message_by_id( $id ) {
        global $wpdb; 

        $tablename = $wpdb->prefix . 'dynamichat_messages';
        if ( $id )
        {
            $res = $wpdb->get_results( 'SELECT * FROM ' . $tablename . ' WHERE id="' . $id . '";' ); 
            
            return $res;
        } 
        else
        {
            return "no user data";
        }
    }

    private function map_chats( $chat ) {
        return json_decode(json_encode($chat), true);
    }

    private function filter_chats( $chat ) {
        // $latest_id = $_GET['latestID'];
        // return $chat['id'] > $latest_id;
        return true;
    }
}
