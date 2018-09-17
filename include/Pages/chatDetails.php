<?php
/**
 * @package dynamichat
 */
// namespace Inc\Pages;

// use Inc\Types\SupportUser;

_require(INC_TYPES, 'supportUser');

class ChatDetails
{
    public $support = array();

    function __construct()
    {}

    function register()
    {
        add_action( 'rest_api_init', function () {
            register_rest_route( 'chat/v1', '/openSupport', array(
                    'methods' =>  \WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'open_support' )
            ) );
        } );
    }

    public static function open_support()
    {
        $userDetails = json_decode( file_get_contents('php://input'), true );

        $user = new SupportUser( $userDetails['name'], $userDetails['phone'] );
        $user->set_business( $userDetails['business'] );

        $chatDetails = new self();
        $chatDetails->support = $chatDetails::send_user( $user );

        return $chatDetails;
    }

    private static function send_user( $user )
    {
        $args = array(
            'method' => \WP_REST_Server::CREATABLE,
            'headers' => array(
                'Content-type' => 'application/json',
                'origin' => 'localhost'
            ),
            'timeout' => 1000,
            'body' => json_encode(
                array(
                    'user' => array(
                        'name' => $user->get_name(),
                        'phone' => $user->get_phone(),
                        'business' => $user->get_business()
                    )
                )
            )
        );

        $url = get_option( 'server_uri' ) . '/support/openSupport';
        $res = wp_remote_post( $url , $args );

        if ( is_wp_error( $res ) ) {
            $error_string = $res->get_error_message();
            echo '<div id="message" class="error"><p>' . $url . '</p><p>' . $error_string . '</p></div>';
            return;
        }

        // TODO: is available rep
        return json_decode( $res['body'] )->support;
    }
}