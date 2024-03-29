<?php
/**
 * @package dynamichat
 */
// namespace Inc\Libs;

class HttpClient {
    public $method;
    public $url;
    public $args;

    function __construct( $method, $url, $args )
    {
        $this->method = $method;
        $this->url = esc_attr( get_option( 'server_uri' ) ) . $url;
        $this->args = $args ? $args : array(
            'method' => \WP_REST_Server::READABLE,
            'headers' => array(
                'origin' => 'localhost:7347'
            )
        );
    }

    public function send( $data ) {
        $res = call_user_func('wp_remote_' . $this->method, $this->url, $this->args );

        if ( is_wp_error( $res ) ) {
            return self::error_handler( $res );
        }

        $body = json_decode( $res['body'] );

        return $body;
    }

    public static function error_handler( $error ) {
        $error_string = $error->get_error_message();

        return (object)[ 'error' => '<div id="message" class="error"><p>' . $error_string . '</p></div>' ];
    }
}

?>