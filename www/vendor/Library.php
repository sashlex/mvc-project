<?php

# vendors php Library

class Library {

    public function __construct ( $config = array () ) {
        foreach( $config as $key => $value ) {
            $this->$key = $value;
        }
    }

    public function testLibrary ( $var ) {

    }

}
