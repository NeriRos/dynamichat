<?php

/**
 * trigger this file on uninstall
 * 
 * @package dynamichat
 */

defined( 'WP_UNINSTALL_PLUGIN' ) or die;

// $chats = get_posts( array( 'post_type' => 'dynamicchat', 'numberposts' => -1 ) );

// foreach ($chats as $key => $chat) {
//     wp_delete_post( $chat->ID, true );
// }

global $wpdb;

$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'dynamicchat'");
$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)");
$wpdb->query("DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)");