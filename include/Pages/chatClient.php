<?php
/**
 * @package dynamichat
 */
// namespace Inc\Pages;

// use Inc\Libs\HttpClient;

_require(INC_LIBS, 'httpClient');

class ChatClient {

    public $chats = array();
    public $support = array();
    public $message = array();
    public $nodeConnectionId;

    function __construct( )
    { }

    function register()
    {
        add_action( 'rest_api_init', function () {
            register_rest_route( 'chat/v1', '/init', array(
                    'methods' =>  \WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'init' )
            ) );
        } );

        add_action( 'rest_api_init', function () {
            register_rest_route( 'chat/v1', '/getChats', array(
                    'methods' =>  \WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_chats' )
            ) );
        } );
    }

    /**
     * init chatClient with support, and return to server.
     */
    public function init()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $client = new self();
        $client->support = $data['support'];

        return $client;
    }

    public function get_chats()
    {
        $url = '/support/getChats/' . $_GET['chatID'] . '?id=' . $_GET['userID'];
        $httpClient = new HttpClient( \WP_REST_Server::READABLE, $url, null );

        $res = $httpClient->send( null );

        if ( !$res ) {
            return null;
        }

        if ( property_exists( $res, 'error' ) ) {
            return $res->error;
        }

        $chats = array_merge( $this->chats, $res->chats );
        $chats = array_map( array( $this, 'map_chats' ), $chats );
        // $chats = array_filter( $chats, array( $this, 'filter_chats' ) );

        return array( 'chats' => $chats, 'isAvailableRep' => $res->isAvailableRep, 'representative' => ( $res->isAvailableRep ? $res->representative : false ) );
    }

    public static function send_message( $data )
    {
        $url = '/support/sendMessage';
        $httpClient = new HttpClient( \WP_REST_Server::CREATABLE, $url, null );

        $res = $httpClient->send( $data );

        if ( property_exists( $res, 'error' ) ) {
            return array( 'error' => $res->error );
        }
        // $chats = array_filter( $chats, array( $this, 'filter_chats' ) );

        return array( 'chats' => $chats, 'isAvailableRep' => $res->isAvailableRep, 'representative' => ( $res->isAvailableRep ? $res->representative : false ) );
    }

    private function map_chats( $chat )
    {
        return json_decode(json_encode($chat), true);
    }

    private function filter_chats( $chat )
    {
        $latest_id = $_GET['latestID'];
        return $chat['id'] > $latest_id;
        return true;
    }
}
