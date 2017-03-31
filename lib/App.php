<?php

class App {

    public function __construct() {
        $request = require CONFIGS_DIR . DIRECTORY_SEPARATOR . 'routes.php';
        $this->response( $request );
    }

    private function response( $request ) {

        $class = $request->controller . 'Controller';
        $controller = new $class( $request->params ); // Call Controller
        $controller->context = $request->controller; // this is param for create model or view instance, in current controller ( lazzy load )
        $controller->{ $request->method }( $request->args ); // Call method

    }

}
