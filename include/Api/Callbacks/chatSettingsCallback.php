<?php
/**
 * @package dynamichat
 */
// namespace Inc\Api\Callbacks;

class ChatSettingsCallback
{
    public function server_uri_check( $value ) {
        return $value;
    }

    public function server_uri_html() {
        $value = esc_attr( get_option( 'server_uri' ) );
        echo '<input type="text" class="form-control" name="server_uri" value="' . $value . '" placeholder="Server API URI" />';
    }
}