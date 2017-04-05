<?php

class UsersController extends AppController {

    public function index() {
        $this->view->render( 'default', 'users/index', array ( 'message' => 'This is Users Index page!!!' ) );
    }

    public function login() {
        /* if ( isset( $_POST[ 'submit' ] ) ) {
           $result = $this->auth->login( $_POST[ 'login' ], $_POST[ 'password' ] ); 
           $this->view->message( $result[ 'message' ] );
           if ( $result[ 'success' ] ) $this->redirect( '/admin/users' );
           }
           $this->view->render( 'default', 'users/login' ); */
    }

    public function registration() {
        if ( isset( $_POST[ 'submit' ] ) ) {
            $result = $this->auth->register( $_POST[ 'login' ], $_POST[ 'email' ], $_POST[ 'password' ], $_POST[ 'confirmPassword' ] );
            $this->view->message( $result[ 'message' ] );
        }
        $this->view->render( 'default', 'users/registration', array ( 'message' => 'This is Users Registration page!!!' ) );
    }

    public function confirmation( $args = array() ) {
        /* $result = $this->auth->confirm( $args[ 'confirm_code' ] );
           $this->view->message( $result[ 'message' ] );
           $this->view->render( 'default', 'confirmation' ); */
    }

    public function restore( $args = array() ) {
        /* if ( isset( $_POST[ 'submit' ] ) ) {
           $result = $this->auth->forgot( $_POST[ 'email' ] );
           $this->view->message( $result[ 'message' ] );
           }
           $this->view->render( 'default', 'restore' ); */
    }

    public function indexAdmin() {
        /*     if ( !$this->auth->hasAccess( 'index' ) ) $this->redirect( '/404' ); */
        /* echo $this->auth->user; */
        /*     $this->view->render( 'admin', 'indexAdmin', array( 'user' => $this->auth->user ) ); */
    }


}
