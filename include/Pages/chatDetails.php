<?php
/**
 * @package dynamichat
 */
namespace Inc\Pages;

class ChatDetails
{
    public $user = array();
    
    function __construct()
    {}
    
    function register() 
    {    
        add_action( 'rest_api_init', function () {
            register_rest_route( 'chat/v1', '/details', array(
                    'methods' =>  \WP_REST_Server::CREATABLE,
                    'callback' => array( $this, 'create_account' )
            ) );
        } );
    }

    public function create_account() {
        global $wpdb;

        $tmp_user = json_decode( file_get_contents('php://input'), true );
        
        $this->user = array(
            'name' => $tmp_user['name'],
            'business' => $tmp_user['business'],
            'phone' => $tmp_user['phone']
        );
        $user_id = $this->save_user($this->user);
        $this->user['id'] = $user_id;

        return $user_id;
    } 

    private function save_user($user) {
        global $wpdb;

        $tablename = $wpdb->prefix . 'dynamichat_users';
        if ( $user['name'] && $user['phone'] )
        {
            $res = $wpdb->insert( $tablename, array(
                    'full_name' => $user['name'], 
                    'business_name' => $user['business'],
                    'phone' => $user['phone'] 
                ), array( '%s', '%s', '%s') 
            );

            return ( $res == 1 ? $wpdb->insert_id : false );
        } 
        else
        {
            return "no user data";
        }
    }
    
    public function get_user_id() {
        return $this->user['id'];
     }
} 