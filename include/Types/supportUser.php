<?php
/**
 * @package dynamichat
 */
// namespace Inc\Types;

class SupportUser
{
    public $id;
    public $name;
    public $phone;
    public $business;

    private $USERS_TABLE = 'dynamichat_users';

    public function __construct($name, $phone)
    {
        $this->name = $name;
        $this->phone = $phone;
    }

    public function save_user( $user = NULL ) {
        global $wpdb;

        if ( !$user )
        {
            $user = $this;
        }

        $tablename = $wpdb->prefix . $this->USERS_TABLE;
        if ( $user->name && $user->phone )
        {
            $res = $wpdb->insert( $tablename, array(
                    'full_name' => $user->name,
                    'business_name' => $user->business,
                    'phone' => $user->phone
                ), array( '%s', '%s', '%s')
            );

            if( $res == 1 )
                $user->id = $wpdb->insert_id;

            return $res;
        }
        else
        {
            return "no user data";
        }
    }
    public function get_id(){
		return $this->id;
	}

    private function set_id( $id ) {
		$this->id = $id;
	}

	public function get_name(){
		return $this->name;
	}

	public function set_name( $name ){
		$this->name = $name;
	}

	public function get_phone(){
		return $this->phone;
	}

	public function set_phone( $phone ){
		$this->phone = $phone;
	}

	public function get_business(){
		return $this->business;
    }

	public function set_business($business){
		$this->business = $business;
	}
}
