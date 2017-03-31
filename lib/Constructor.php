<?php

namespace Lib;

abstract class Constructor {

    static $source; // Данные подключаем 1 раз при компиляции
    private $libs;

    public function __construct( $params = array(), $items = array() ) {

        # set params
        foreach( $params as $key => $value ) {
            $this->$key = $value;
        }

        #set libs
        if ( !isset( self::$source ) ) {
            $data = require CONFIGS_DIR . DIRECTORY_SEPARATOR . 'sources.php'; 
            self::$source = new Source( $data );
        }

        foreach ( $items as $item ) {
            foreach ( self::$source->$item as $key => $value ) {
                $this->$key = $value;
                $this->libs[] = $key;
            }
        }
    }

    public function __call( $method, $args ) // search called method in avaliable libs
    {
        foreach ( $this->libs as $lib )
        {
            if ( method_exists( $this->{ $lib }, $method ) )
            {
                return $this->{ $lib }->{ $method } ( ...$args ); // lib ??? or what?
            }
        }
        echo 'There are no method: "', $method, '" in ', static::class, ' class!'; // ERROR HANDLER PLEASE !!!
    }

    public function __get( $property ) { // search called property in avaliable libs
        foreach ( $this->libs as $lib ) {
            if ( isset( $this->{ $lib }->{ $property } ) ) {
                return $this->{ $lib }->{ $property }; // lib ???
            }
        }
    }

}
