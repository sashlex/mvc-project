<?php

class DefaultController extends AppController {

    public function index() {
        $this->view->render( 'default', 'default/index', array ( 'message' => 'This is Default Index page!!!' ) );
    }

}

