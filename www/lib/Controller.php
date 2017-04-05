<?php 

namespace Lib;

class Controller extends Constructor {

    public function __construct( $params = array(), $items = array( 'base', 'controller' ) ) {
        parent::__construct( $params, $items );
    }

    public function __get( $object ) {

        if ( $object == 'model' ) {
            $class = $this->context . 'Model'; // Model name same as Controller name
            $this->model = new $class;
            return $this->model;
        }

        if ( $object == 'view' ) {
            $class = $this->context . 'View'; // View name same as Controller name */
            $this->view = new $class;
            return $this->view;
        }
    }

}
