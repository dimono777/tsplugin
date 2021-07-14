<?php
/**
 * @author Alexandr Tomenko <alexandr.tomenko@tstechpro.com>
 */

spl_autoload_register( 'tradersoft_namespace_autoload' );

function tradersoft_namespace_autoload( $class_name )
{
    if ( false === strpos( $class_name, 'tradersoft' ) ) {
        return;
    }

    $file_parts = explode( '\\', $class_name );
    $namespace = '';
    $file_name = '';

    for ( $i = count( $file_parts ) - 1; $i > 0; $i-- ) {
        $current = strtolower( $file_parts[ $i ] );

        if ( count( $file_parts ) - 1 === $i ) {
            $file_name = "$current.php";
        } else {
            $namespace = DIRECTORY_SEPARATOR . $current . $namespace;
        }
    }

    $file_path  = dirname( dirname( __FILE__ ) ) . $namespace . DIRECTORY_SEPARATOR ;
    $file_path .= $file_name;

    if ( file_exists( $file_path ) && is_file($file_path)) {
        include_once( $file_path );
    } else {
        throw new Exception(esc_html( "The file attempting to be loaded at $file_path does not exist." ));
    }
}