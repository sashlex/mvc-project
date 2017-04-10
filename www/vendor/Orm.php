<?php

# vendors Orm Engine

class Orm {

    protected $migrationsPath;
    public $pdo;

    public function __construct ( $configs = array() ) { // array or object ????
        foreach( $configs as $key => $value ) {
            $this->$key = $value;
        }
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName . ';charset=' . $this->encoding;
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => TRUE
        );
        $this->pdo = new PDO( $dsn, $this->user, $this->password, $options );
    }

    public function migrateUp( $migration ) {
        $sql = require $this->migrationsPath . $migration . '.php';
        $stmt = $this->pdo->prepare( $sql[ 'up' ] );
        if ( $stmt->execute() ) return array( 'success' => true, 'message' => 'Migration success!' );
        else return array( 'success' => false, 'message' => 'Migration failed!' );
    }

    public function migrateDown( $migration ) {
        $sql = require $this->migrationsPath . $migration . '.php';
        $stmt = $this->pdo->prepare( $sql[ 'down' ] );
        if ( $stmt->execute() ) return array( 'success' => true, 'message' => 'Migration success!' );
        else return array( 'success' => false, 'message' => 'Migration failed!' );
    }

    public function setMigrationsPath( $path ) {
        $this->migrationsPath = $path;
    }

}
