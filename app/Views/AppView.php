<?php # здесь реализация методов вида, с помощью подключенных библиотек
class AppView extends \Lib\View {

    public function put ( $string  ) {
        echo  $string;
    }

    public function getCssPath (  ) {
        return $this->lib->theme . DIRECTORY_SEPARATOR . 'css';
    }

    /* public function render( $layout = '', $view = '', $data = array (  ) ) {

       if ( !empty( $this->message ) ) $data[ 'message' ] = $this->message;
       else $data[ 'message' ] = '';

       $this->lib->render ( $layout, $view, $data );
       } */


    /* public function message( $message = '' ) {
       $this->message = $message;
       } */






}
