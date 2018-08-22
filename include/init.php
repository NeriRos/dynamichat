<?php
/**
 * @package dynamichat
 */
// namespace Inc;

final class Init
{
    private static $IS_LEGACY = true;

    /**
     * Store all classes inside an array
     * @return array full list of classes
     */
    public static function get_services()
    {
        $namespaces = [
            // Pages\Admin::class,
            // Pages\ChatClient::class,
            // Pages\ChatDetails::class,
            // Base\Enqueue::class,
            // Base\Links::class,
        ];

        $legacy = [
            array( 'path' => INC_PAGES, 'file' => 'admin', 'class' => 'Admin' ),
            array( 'path' => INC_PAGES, 'file' => 'chatClient', 'class' => 'ChatClient' ),
            array( 'path' => INC_PAGES, 'file' => 'chatDetails', 'class' => 'ChatDetails' ),
            array( 'path' => INC_BASE, 'file' => 'enqueue', 'class' => 'Enqueue' ),
            array( 'path' => INC_BASE, 'file' => 'links', 'class' => 'Links' ),
        ];

        return self::$IS_LEGACY ? $legacy : $namespaces;
    }

    /**
     * Loop through the classes, initialize them
     * and call the register() method if it exists.
     * @return
     */
    public static function register_services()
    {
        foreach ( self::get_services() as $class ) {
            $isOK = false;

            if ( self::$IS_LEGACY ) {
                $isOK = _require( $class['path'], $class['file'] );
                $class = $class['class'];
            } else {
                $isOK = true;
            }

            if ( $isOK ) {
                $service = self::instantiate( $class );
                if ( method_exists( $service, 'register' ) ) {
                    $service->register();
                }
            } else {
                echo "ERORR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
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