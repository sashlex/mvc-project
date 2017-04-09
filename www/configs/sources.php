<?php

$orm = new Orm( array(
    'user' => 'root_user',
    'password' => 'password',
    'host' => 'localhost',
    'dbName' => 'mvc',
    'encoding' => 'utf8'
) );

$templateEngine = new TemplateEngine( array(
    'theme' => ROOT_DIR . DIRECTORY_SEPARATOR . 'themes/userTheme',
    'cache' => ROOT_DIR . DIRECTORY_SEPARATOR . 'themes/cache',
) );

$library = new Library( array(
    'lib' => rand()
) );

$auth = new Auth ( array(
    'db' => $orm->pdo,
    'confirmPage' => 'http://mvc/confirmation/',
    'table' => 'users',
    'salt' => 'cCtAzmnIZ9FWvEhzzKZjmSnLhmT8VaXCXtRYenbSj6zRMr08tFC65kRXwv5mD4v3'
) );

// Добавление экземляров только в одном файле, где создаются все экземпляры ( иначе теряется контроль )
// Статическое добавление экземпляров
# $obj_merged = (object) array_merge((array) $obj1, (array) $obj2); ?????
# ( непойдет методы смешиваются и могут быть одинаковыми, а для чего еще ?? )
return ( object ) array(
    'base' => array( // target -> all instances
        'some' => 'everywhere parameters'
    ),
    'controller' => array( // target -> controller instances
        'lib' => $library,
        'auth' => $auth
    ),
    'model' => array( // target -> model instances
        'lib' => $orm
    ),
    'view' => array( // target -> view instances
        'lib' => $templateEngine
    )
);
