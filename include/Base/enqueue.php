<?php
/**
 * @package dynamichat
 */
namespace Inc\Base;

class Enqueue 
{
    function register() {
        add_action('admin_enqueue_script', array( $this, 'enqueue_backend' ));
        add_action('wp_enqueue_script', array( $this, 'enqueue_frontend' ));
    }
    
    function enqueue_frontend() {
        wp_enqueue_style("main-style", PLUGIN_URL . 'assets/frontend/style.css');
        wp_enqueue_script("main-script", PLUGIN_URL . 'assets/frontend/javascript.js');
    }

    function enqueue_backend() {
        wp_enqueue_style("main-style", PLUGIN_URL . 'assets/backend/style.css');
        wp_enqueue_script("main-script", PLUGIN_URL . 'assets/backend/javascript.js');
    }
}