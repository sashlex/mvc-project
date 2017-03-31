<?php

# vendors php Framework

class Framework {

    public function __construct ( $config = array () ) {
        foreach( $config as $key => $value ) {
            $this->$key = $value;
        }
    }

    public function testFramework ( $var ) {

    }

}
