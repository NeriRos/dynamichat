<?php
/**
 * @package dynamichat
 */
namespace Inc\Base;


class Activate
{
    function __construct() {
        if (false) {}
    }

    public static function activate() {
        // self::create_users_table();
        // self::create_messages_table();

        flush_rewrite_rules();
    }

    private static function create_users_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'dynamichat_users';
        $sql = "CREATE TABLE $table_name (
            id int(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            views int(5) NOT NULL,
            clicks int(5) NOT NULL,
            full_name char(20) NOT NULL,
            business_name char(20),
            phone int(10) NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    private static function create_messages_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'dynamichat_messages';

        $sql = "CREATE TABLE $table_name (
            id int(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            views int(5) NOT NULL,
            clicks int(5) NOT NULL,
            userID int(9) NOT NULL,
            message_text varchar(200) NOT NULL,
            message_date TIMESTAMP(1) NOT NULL,
            isSenderSelf int(1) NOT NULL,
            is_company int(1) NOT NULL,
            message_status int(1) NOT NULL,
            contact int(9) NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}