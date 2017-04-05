<?php

class FileController extends AppController {

    protected $css;

    public function index( $args = array() ) {

        $path =  $this->view->getCssPath() . DIRECTORY_SEPARATOR . $args[ 'file' ] ?? ''; // getThemePath
        $this->view->put( $this->model->getCss( $path ) );
        header('Content-Type: text/css'); // wrap this !!!

    }

}
