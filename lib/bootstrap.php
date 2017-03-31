<?php

# file for requiring files

define( 'ROOT_DIR', $_SERVER[ 'DOCUMENT_ROOT' ] );
define( 'APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app' );
define( 'MODELS_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'Models' );
define( 'VIEWS_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'Views' );
define( 'CONTROLLERS_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'Controllers' );
define( 'CONFIGS_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'configs' );
define( 'LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib' );
define( 'VENDOR_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' );
define( 'PUBLIC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'public' );

# require lib files, which uses allways
require LIB_DIR . DIRECTORY_SEPARATOR . 'App.php';
require LIB_DIR . DIRECTORY_SEPARATOR . 'Source.php';
require LIB_DIR . DIRECTORY_SEPARATOR . 'Constructor.php';
require LIB_DIR . DIRECTORY_SEPARATOR . 'Model.php'; // REMOVE ??? (by loadClass loaded)
require LIB_DIR . DIRECTORY_SEPARATOR . 'View.php'; // REMOVE ??? (by loadClass loaded)
require LIB_DIR . DIRECTORY_SEPARATOR . 'Controller.php'; // REMOVE ??? (by loadClass loaded)
# vendor files can be required with composer autoloading or require directly
require VENDOR_DIR . DIRECTORY_SEPARATOR . 'Orm.php';
require VENDOR_DIR . DIRECTORY_SEPARATOR . 'Framework.php';
require VENDOR_DIR . DIRECTORY_SEPARATOR . 'TemplateEngine.php';
require VENDOR_DIR . DIRECTORY_SEPARATOR . 'Auth.php';
require VENDOR_DIR . DIRECTORY_SEPARATOR . 'Validate.php';

# autoloader ( not recomended ) - it's magic -> learn and check PSR autoloader !!!
# maybe create file -> thats be a file register ( or required file stack )
function loadClass( $class ) {

    $file = strrchr( $class, '\\' );

    if ( $file ) $file = substr( $file, 1 ) . '.php';
    else $file = $class . '.php';

    if ( strpos( $file, 'Model.php' ) !== false ) {
        require MODELS_DIR . DIRECTORY_SEPARATOR . $file;
    }
    else if ( strpos( $file, 'View.php' ) !== false ) {
        require VIEWS_DIR . DIRECTORY_SEPARATOR . $file;
    }
    else if ( strpos( $file, 'Controller.php' ) !== false ) {
        require CONTROLLERS_DIR . DIRECTORY_SEPARATOR . $file;
    }
};
spl_autoload_register( 'loadClass' );

# Maintenance functions

function loadFile( $path ) { 
    return require $path;
}

function check( $var ) {
    echo '<pre>', var_dump( $var ), '</pre>';
}

# Комментарии:
# - Использовать autoloading не желательно, лучше подгружать по известному пути, без проверки на возможность создания класса
