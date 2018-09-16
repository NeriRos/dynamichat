<?php
/**
 * @package dynamichat-ws
 */
namespace Inc;

final class Init
{
    /**
     * Store all classes inside an array
     * @return array full list of classes
     */
    public static function get_services()
    {
        $namespaces = [
            Pages\Admin::class,
            Pages\ChatClient::class,
            Pages\ChatDetails::class,
            Base\Enqueue::class,
            Base\Links::class,
        ];

        return $namespaces;
    }

    /**
     * Loop through the classes, initialize them
     * and call the register() method if it exists.
     * @return
     */
    public static function register_services()
    {
        foreach ( self::get_services() as $class ) {
            $service = self::instantiate( $class );
            if ( method_exists( $service, 'register' ) ) {
                $service->register();
            }
        }
    }

    /**
     * Simply initialize the class
     * @param class $class class from the services array
     * @return class instance new instance of the class
     */
    private static function instantiate( $class )
    {
        return new $class();
    }
}