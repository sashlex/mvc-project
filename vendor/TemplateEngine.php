<?php

# vendors Template Engine

class TemplateEngine {

    private $layouts = 'Layouts'; // layouts dir
    public $theme; // theme dir
    private $data = array(); // template data

    public function __construct ( $configs = array() ) {
        foreach( $configs as $key => $value ) {
            $this->$key = $value;
        }
    }

    # реализовать кеширование
    public function render ( $layout = '', $file = '', $data = array(), $generate = false ) { // может сделать более универсальной и для js и css

    $file = implode( DIRECTORY_SEPARATOR, explode( '/', $file ) );
    extract( $data );
    extract( $this->data );
    ob_start();
    require $this->theme . DIRECTORY_SEPARATOR . $file . '.php';
    $content = ob_get_clean();

    ob_start();
    require $this->theme . DIRECTORY_SEPARATOR . $this->layouts . DIRECTORY_SEPARATOR . $layout . '.php';
    $result =  ob_get_clean();
    echo $result;
}

    public function set ( $data = array() ) {
        foreach( $data as $key => $value ) {
            $this->$key = $value;
        }
    }

}
