<?php

class Output {
    static $verbosity = 0;

    static function error( $message, ...$args ) {
        if ( self::verbosity >= 3 ) {
            echo "\n[!] $message \n\t" . self::parseArgs( $args );
        }
    }
    static function warning( $message, ...$args ) {
        if ( self::verbosity >= 2 ) {
            echo "\n[-] $message \n\t" . self::parseArgs( $args );
        }
    }
    static function info( $message, ...$args ) {
        if ( self::verbosity >= 1 ) {
            echo "\n[+] $message \n\t" . self::parseArgs( $args );
        }
    }
    static function verbose( $message, ...$args ) {
        if ( self::verbosity >= 0 ) {
            echo "\n[*] $message \n\t" . self::parseArgs( $args );
        }
    }

    static function parseArgs( $args ) {
        $message = "";

        if ( $args ) {
            foreach ($args as $arg) {
                $message .= json_encode( $args ) + ", ";
            }
        }

        return $message;
    }
}