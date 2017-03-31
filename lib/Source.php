<?php

# class Configs
# uses object as data source

namespace Lib;

class Source {

    public function __construct( $data = array() ) {
        foreach ( $data as $key => $value ) {
            $this->$key = $value;
        }
    }

    # Get parameter
    public function get ( $key, $default = null ) {
        if ( isset ( $this->$key ) ) {
            return $this->$key;
        }
        return $default;
    }

    # Set parameter
    public function set ( $key, $value = null ) {
        $this->$key = $value;
    }

}
