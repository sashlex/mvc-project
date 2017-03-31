<?php

class ErrorsController extends AppController {

    public function _404 (  ) {
        $this->view->render( 'default', '_404' );
    }


}
