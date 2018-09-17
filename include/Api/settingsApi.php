<?php
/**
 * @package dynamichat
 */
// namespace Inc\Api;

class SettingsApi
{
    public $settings = array();
    public $sections = array();
    public $fields = array();

    public function register()
    {
        if ( ! empty( $this->settings ) ) {
            add_action( 'admin_init', array( $this, 'register_custom_fields' ) );
        }
    }

    function set_settings( array $settings ) {
        $this->settings = $settings;

        return $this;
    }

    function set_sections( array $sections ) {
        $this->sections = $sections;

        return $this;
    }

    function set_fields( array $fields ) {
        $this->fields = $fields;

        return $this;
    }

    public function register_custom_fields() 
    {
        foreach ($this->settings as $setting) 
        {
            register_setting( $setting['option_group'], $setting['option_name'], isset( $setting['callback'] ) ? $setting['callback'] : '' );
        }

        foreach ($this->sections as $section) 
        {
            add_settings_section($section['id'], $section['title'], isset( $section['callback'] ) ? $section['callback'] : '', $section['page']);
        }

        foreach ($this->fields as $field) 
        {
            add_settings_field( $field['id'], $field['title'], isset( $field['callback'] ) ? $field['callback'] : '', $field['page'], $field['section'], isset( $field['args'] ) ? $field['args'] : '' ); 
        }
    }
    
}