<?php

class Auth {

    private $pdo; // PDO instance
    private $confirmPage; // registration confirm url
    private $table; // database table name
    private $identifier; // pasword hash identifier
    private $domain; // domain name
    public $userAgents; // How agents can has per user
    public $allowedActions; // actions allowed to user



    /**
     * @param object $params - required parameters:
     * $params->db - PDO instance
     * $params->confirmPage - 'http://mvc/confirmation/' ( page-method that handle account confirmation )
     * $params->table - table name
     * $params->salt - salt hash
     */

    public function __construct( $params = array() ) {
        foreach( $params as $key => $value ) {
            $this->$key = $value;
        }
        $this->sessionName = 'SESSION';
        $this->identifier = '$2y$12$';
        $this->domain = $_SERVER[ 'SERVER_NAME' ];
        $this->userAgents = 2;
        if ( !empty( $_SESSION[ 'LOGIN' ] ) ) $this->user = $_SESSION[ 'LOGIN' ];
        if ( !empty( $_SESSION[ 'ALLOWED_ACTIONS' ] ) ) $this->allowedActions = $_SESSION[ 'ALLOWED_ACTIONS' ];
    }



    /**
     * todo
     */

    public function login( $login = '', $password = '' ) {
        // destroy created session
        if ( !empty( session_id() ) ) session_destroy();
        // check login
        if ( preg_match( '#[^a-zA-Z0-9_-]+#', $login ) ) return array ( 'success' => false, 'message' => 'Login not valid!' );
        $clearLogin = filter_var( $login, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
        if ( $clearLogin !== $login )  return array ( 'success' => false, 'message' => 'Login not valid!' );
        $result = $this->getWhere( array( 'login' ), array( $login ) );
        if ( empty( $result[ 'result' ] ) ) {
            return array ( 'success' => false, 'message' => 'Login or password not valid!' );
        }
        if ( count( $result[ 'result' ] ) !== 1 ) {
            return array ( 'success' => false, 'message' => 'More than one users founded!' );
        }
        $isLogin =  password_verify( hash( 'sha512', $this->salt . $password . $this->salt ), $this->identifier . $result['result']['password'] );
        if ( !$isLogin ) return array ( 'success' => false, 'message' => 'Login or password not valid!' );
        if ( empty( $result['result']['confirmed'] ) ) {
            return array ( 'success' => false, 'message' => 'Account not confirmed!' );
        } else {
            $this->sessionStart( $this->sessionName );
            $_SESSION[ 'LOGIN' ] = $result['result'][ 'login' ];
            $_SESSION[ 'ALLOWED_ACTIONS' ] = $result['result'][ 'actions' ];
            return array ( 'success' => true, 'message' => 'Logged in!' );
        }
    }



    /**
     * todo
     */

    public function hasAccess ( $action ) {
        session_name( $this->sessionName );
        session_start();
        //session_regenerate_id(true); // ?????????????????????
        if ( in_array ( $_SESSION[ 'TICKET' ], $_SESSION[ 'TICKETS' ] ) ) {
            session_destroy();
            return array ( 'success' => false, 'message' => 'No access! Token was used.' );
        }
        if ( $_SESSION[ 'TICKET' ] !== $_COOKIE[ 'TICKET' ] ) {
            session_destroy();
            return array ( 'success' => false, 'message' => 'No access! Tokens no matched.' );
        }
        $ip = md5( $this->getIp() );
        $userAgent = md5( $_SERVER[ 'HTTP_USER_AGENT' ] );
        if ( $ip !== $_SESSION[ 'IP' ] && $userAgent !== $_SESSION[ 'USER_AGENT' ] ) {
            session_destroy();
            return array ( 'success' => false, 'message' => 'No access! User ip and agent changed at moment.' );
        }
        if ( !in_array( $_SESSION[ 'USER_AGENT' ], $_SESSION[ 'USER_AGENTS' ] ) ) array_push( $_SESSION[ 'USER_AGENTS' ], $_SESSION[ 'USER_AGENT' ] ); // save users agent
        if ( count( $_SESSION[ 'USER_AGENTS' ] ) > $this->userAgents ) {
            session_destroy();
            return array ( 'success' => false, 'message' => 'No access! User often change user agent.' );
        }
        array_push( $_SESSION[ 'TICKETS' ], $_SESSION[ 'TICKET' ] ); // save access tokens history

        $_SESSION[ 'TICKET' ] = $this->generateCode(); // актуальный токен доступа
        setcookie( 'TICKET', $_SESSION[ 'TICKET' ], 0, '/', $this->domain, false, true ); // токен доступа юзеру
        
        if ( in_array( md5( $this->salt . $action . $this->salt ), $this->allowedActions ) ) return array ( 'success' => true, 'message' => 'Access allowed!' );
        return array ( 'success' => false, 'message' => 'No access! Permisson not allowed..' );
    }



    /**
     * todo
     */

    public function logout () {
        if ( !empty( session_id() ) ) session_destroy();
        if ( isset( $_COOKIE[ 'TICKET' ] ) ) {
            unset( $_COOKIE[ 'TICKET' ] );
            setcookie( 'TICKET', '', time() - 3600, '/', $this->domain, false, true );
        }
        return array ( 'success' => true, 'message' => 'You have successfully logged out!' );
    }



    /**
     * todo
     */

    public function sessionStart( $name = '' ) {
        if ( !empty( $_SESSION[ 'TICKET' ] ) ) return array ( 'success' => false, 'message' => 'Session was started!' );
        ini_set( 'session.cookie_lifetime', 0 );
        ini_set( 'session.use_cookies', 1 );
        ini_set( 'session.use_only_cookies', 1 );
        ini_set( 'session.use_strict_mode', 1 );
        ini_set( 'session.cookie_httponly', 1 );
        ini_set( 'session.use_trans_sid', 0 );
        ini_set( 'session.cache_limiter', 'nocache' );
        ini_set( 'session.hash_function', 'sha512' );
        session_name( $name );
        session_start();
        $_SESSION[ 'TICKET' ] = $this->generateCode(); // актуальный токен доступа
        $_SESSION[ 'TICKETS' ] = array(); // история токенов доступа
        setcookie( 'TICKET', $_SESSION[ 'TICKET' ], 0, '/', $this->domain, false, true ); // токен доступа юзеру
        $_SESSION[ 'IP' ] = md5( $this->getIp() );
        $_SESSION[ 'USER_AGENT' ] = md5( $_SERVER[ 'HTTP_USER_AGENT' ] );
        $_SESSION[ 'USER_AGENTS' ] = array();
        array_push( $_SESSION[ 'USER_AGENTS' ], $_SESSION[ 'USER_AGENT' ] ); // save users agent
    }



    /**
     * todo
     */

    public function generateCode(  ) {
        return hash( 'sha512', microtime() . uniqid( rand( 10000000000, 99999999999 ), true ) );
    }



    /**
     * todo
     */

    public function register ( $login, $email, $password, $confirmPassword ) {
        // check login
        if ( preg_match( '#[^a-zA-Z0-9_-]+#', $login ) ) return array ( 'success' => false, 'message' => 'Login not valid!' );
        $clearLogin = filter_var( $login, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
        if ( $clearLogin !== $login ) return array ( 'success' => false, 'message' => 'Login not valid!' );
        //$email = filter_var( $email, FILTER_VALIDATE_EMAIL );
        if ( empty( $email ) ) return array ( 'success' => false, 'message' => 'Email not valid!' );
        if ( !empty( $this->getWhere( array( 'login' ), array( $login ) )[ 'result' ] ) ) {
            return array ( 'success' => false, 'message' => 'Login has been taken!' );
        }
        //if ( !empty( $this->getWhere( array( 'email' ), array( $email ) )[ 'result' ] ) ) {
        //  return array ( 'success' => false, 'message' => 'Email has been taken!' );
        //}
        $originalPassword =  hash( 'md5', $password );
        $confirmPassword = hash( 'md5', $confirmPassword );
        if ( empty( $originalPassword ) || ( $originalPassword !== $confirmPassword ) ) return array ( 'success' => false, 'message' => 'Passwords not valid!' );
        // generate confirmation message 
        $options = array( 'cost' => 12 );
        $password =  password_hash( hash( 'sha512', $this->salt . $password . $this->salt ), PASSWORD_DEFAULT, $options );
        $password = substr( $password, strlen( $this->identifier ) ); /* remove identifier $2y$12$ */
        $confirm_code = hash( 'md5', password_hash(  microtime(), PASSWORD_DEFAULT ) );
        $headers = "From: confirm@me.net\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8";
        $message = $login . " пройдите по ссылке для поддверждения регистрации:" . "\r\n\r\n" . $this->confirmPage . $confirm_code;
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // UPDATE create new user and other code
        $result = $this->insert( array( 'login', 'email', 'password', 'confirm_code' ), array( $login, $email, $password, $confirm_code ) );
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        if ( $result[ 'success' ] ) {
            if ( mail( $email, 'Подтверждение регистрации.', $message, $headers ) ) {
                return array ( 'success' => true, 'message' => 'На ваш email отправлено письмо, подтврердите регистрацию пройдя по ссылке!' );
            }
            return array ( 'success' => true, 'message' => 'You must confirm registration!' );
        }
        return array ( 'success' => false, 'message' => $result[ 'message' ] );
    }



    /**
     * todo
     */

    public function confirm( $confirm_code ) {
        // check code
        if ( !preg_match( '#^[a-f0-9]{32}$#', $confirm_code ) ) return array ( 'success' => false, 'message' => 'Wrong confirmation code!' );
        // stupid anti kids
        /* usleep( 2000 ); */
        if ( !empty( $this->getWhere( array( 'confirm_code' ), array( $confirm_code ) )[ 'result' ] ) ) {
            if ( $this->update( array( 'confirmed' ), array( 1 ), array( 'confirm_code' ), array( $confirm_code ) )[ 'success' ] ) {
                return array ( 'success' => true, 'message' => 'Your account confirmed!' );
            } return array ( 'success' => false, 'message' => 'An error has occurred! Confirmation not completed.' );
        } else return array ( 'success' => false, 'message' => 'Wrong confirmation code!' );
    }



    /**
     * todo
     */

    public function forgot ( $email ) {
        //$email = filter_var( $email, FILTER_VALIDATE_EMAIL );
        if ( empty( $email ) ) return array ( 'success' => false, 'message' => 'Email not valid!' );
        if ( empty( $this->getWhere( array( 'email' ), array( $email ) )[ 'result' ] ) ) {
            return array ( 'success' => false, 'message' => 'Email not found!' );
        }
        $string = '';
        for ( $i = 0; $i < 7; $i ++ ) $string .= substr ( 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKMNOPQRSTUVWXYZ1234567890', rand ( 0 , 59 ), 1 );
        $options = array( 'cost' => 12 );
        $password =  password_hash( hash( 'sha512', $this->salt . $string . $this->salt ), PASSWORD_DEFAULT, $options );
        $password = substr( $password, strlen( $this->identifier ) ); /* remove identifier $2y$12$ */
        $headers = "From: restore@me.net\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8";
        $message = "Ваш новый пароль на сайте " . $this->domain . ":" . "\r\n\r\n" . $string;
        // save new password
        $result = $this->update( array( 'password' ), array( $password ), array( 'email' ), array( $email ) );
        if ( $result[ 'success' ] ) {
            if ( mail( $email, 'Пароль изменен.', $message, $headers ) ) {
                return array ( 'success' => true, 'message' => 'На ваш email отправлен новый пароль! Рекомендуем его изменить на более безопасный.' );
            }
            return array ( 'success' => true, 'message' => 'Произошла ошибка попробуйте еще раз!' );
        }
        return array ( 'success' => false, 'message' => $result[ 'message' ] );
    }



    /**
     * todo
     */

    public function insert( $keys, $values ) {
        if ( count( $keys ) !== count( $values ) ) return array( 'success' => false, 'message' => 'Keys and values not equals!' );
        $sql = 'INSERT INTO ' . $this->table . ' (' . implode( ',', $keys ) . ') VALUES (:' . implode( ',:', $keys ) . ');';
        $stmt = $this->pdo->prepare( $sql );
        foreach( $keys as $key => $value ) {
            $data[ $value ] = $values[ $key ];
        }
        if ( $stmt->execute( $data ) ) {
            return array ( 'success' => true, 'message' => 'New user created!' );
        } else {
            return array ( 'success' => false, 'message' => 'An error has occurred! User not created.' );
        }
    }



    /**
     * This function get records from database
     * @param array $keys the fields which will be updated
     * @param array $values values for update fields
     * @throws nothing
     * @return array with keys: 'success' and 'message';
     *         where 'success' mean - is done right
     *         where 'message' message - for user with result description
     */

    public function getWhere( $keys, $values ) {
        if ( count( $keys ) !== count( $values ) ) return array( 'success' => false, 'message' => 'Keys and values not equals!' );
        $str = '';
        foreach( $keys as $key => $value ) {
            $str .= $value . '=:' . $value . ' AND ';
            $data[ $value ] = $values[ $key ];
        }
        $str = substr( $str, 0, -5 ); // minus last letter ' AND '
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $str;
        $stmt = $this->pdo->prepare( $sql );
        if ( $stmt->execute( $data ) ) {
            return array( 'success' => true, 'message' => '', 'result' => $stmt->fetch( PDO::FETCH_LAZY ) );
        } else {
            return array( 'success' => false, 'message' => 'Произошла ошибка!', 'result' => null );
        }
    }



    /**
     * This function update records in database
     * e.g. update $keys=>'value' where equals $whereKeys=>'whereValue'
     * @param array $keys the fields which will be updated
     * @param array $values values for update fields
     * @param array $whereKeys the fields keys where store condition values 
     * @param array $whereValues values for check conditions
     * @throws nothing
     * @return array with keys: 'success' and 'message';
     *         where 'success' mean - is done right
     *         where 'message' message - for user with result description
     */

    public function update( $keys, $values, $whereKeys, $whereValues ) {
        if ( count( $keys ) !== count( $values ) || count( $whereKeys ) !== count( $whereValues ) ) return array( 'success' => false, 'message' => 'Keys and values not equals!' );
        $strData = '';
        $strWhere = '';
        foreach( $keys as $key => $value ) {
            $strData .= $value . '=:' . $value . ',';
            $data[ $value ] = $values[ $key ];
        }
        $strData = substr( $strData, 0, -1 ); // minus last ','
        foreach( $whereKeys as $key => $value ) {
            $strWhere .= $value . '=:' . $value . ',';
            $data[ $value ] = $whereValues[ $key ];
        }
        $strWhere = substr( $strWhere, 0, -1 ); // minus last ','
        $sql = 'UPDATE ' . $this->table . ' SET ' . $strData . ' WHERE ' . $strWhere;
        $stmt = $this->pdo->prepare( $sql );
        if ( $stmt->execute( $data ) ) {
            return array( 'success' => true, 'message' => 'Update success!' );
        } else {
            return array( 'success' => false, 'message' => 'An error has occurred! Try again later.' );
        }
    }



    /**
     * This function return user IP address
     * @return string ip address
     */

    public function getIp (  ) {
        if ( function_exists ( 'apache_request_headers' ) ) {
            $headers = apache_request_headers (  );
        } else {
            $headers = $_SERVER;
        }
        if ( array_key_exists ( 'X-Forwarded-For', $headers ) && filter_var ( $headers [ 'X-Forwarded-For' ], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            $the_ip = $headers [ 'X-Forwarded-For' ];
        } elseif ( array_key_exists ( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var ( $headers [ 'HTTP_X_FORWARDED_FOR' ], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            $the_ip = $headers [ 'HTTP_X_FORWARDED_FOR' ];
        } else {
            $the_ip = filter_var ( $_SERVER [ 'REMOTE_ADDR' ], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
        }
        return $the_ip;
    }



    /**
     * This method add new allowed actions to user
     * @param string $user to append actions
     * @param array $actions to allow for user
     * @return array with keys: 'success' and 'message';
     *         where 'success' mean - is done right
     *         where 'message' message - for user with result description
     */
    
    public function addActions( $user, $action ) {
        if ( $this->hasAccess( 'addActions' ) ) {

        }
    }



    /**
     * This method remove actions from user
     * @param string $user for remove actions
     * @param array $actions to remove from user
     * @return array with keys: 'success' and 'message';
     *         where 'success' mean - is done right
     *         where 'message' message - for user with result description
     */

    public function removeActions( $user, $action ) {
        if ( $this->hasAccess( 'removeActions' ) ) {

        }
    }



    /**
     * This method add groups to user
     * @param string $user to append groups
     * @param array $groups to append to user
     * @return array with keys: 'success' and 'message';
     *         where 'success' mean - is done right
     *         where 'message' message - for user with result description
     */
    
    public function addGroups( $user, $action ) {
        if ( $this->hasAccess( 'addGroups' ) ) {

        }
    }



    /**
     * This method remove groups from user
     * @param string $user to remove groups
     * @param array $groups remove from user
     * @return array with keys: 'success' and 'message';
     *         where 'success' mean - is done right
     *         where 'message' message - for user with result description
     */
    
    public function removeGroups( $user, $action ) {
        if ( $this->hasAccess( 'removeGroups' ) ) {

        }
    }




    /**
     * This method create table in database
     * note: execute this method once first on setup Auth module
     */

    public function createTable() {
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // СОЗДАТЬ 2 ТАБЛИЦЫ actions и groups
        //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        if ( array_search( $this->table, $this->pdo->query( 'SHOW TABLES' )->fetch() ) !== false ) {
            return array( 'success' => false, 'message' => 'Can\'t create database, database already exists!' );
        }
        $createTable = 'CREATE TABLE ' . $this->table . ' (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      login VARCHAR(50) NOT NULL,
      email VARCHAR(50) NOT NULL,
      password VARCHAR(256) NOT NULL,
      groups TEXT NOT NULL,
      actons TEXT NOT NULL,
      created_at DATETIME NOT NULL,
      confirmed TINYINT(1) DEFAULT 0,
      confirmed_at DATETIME NOT NULL,
      confirm_code VARCHAR(256) NOT NULL
    )';
        $stmt = $this->pdo->prepare( $createTable );
        if ( $stmt->execute() ) return array( 'success' => true, 'message' => 'Table creation success!' );
        else return array( 'success' => false, 'message' => 'Table creation failed!' );
    }


    /**
     * This method fill table
     * note: execute this method once second, after createTabele on setup Auth module
     * create default user with admin privileges:
     * login: admin, email: this@email.net, password: 123, group: "", actions: ["addActions","removeActions","addGroups","removeGroups"], created_at: 2012-12-12 12:12:12, confirmed: 1, confirmed_at: 2012-12-12 12:12:12, confirm_code: ""
     */

    public function fillTable() {
        $options = array( 'cost' => 12 );
        $password =  password_hash( hash( 'sha512', $this->salt . '123' . $this->salt ), PASSWORD_DEFAULT, $options );
        $groups = json_encode( array(
            md5( $this->salt . 'addActions' . $this->salt ),
            md5( $this->salt . 'removeActions' . $this->salt ),
            md5( $this->salt . 'addGroups' . $this->salt ),
            md5( $this->salt . 'removeGroups' . $this->salt )
        ) );
        $fillTable = 'INSERT INTO ' . $this->table . ' (
      login,email,password,groups,actions,created_at,confirmed,confirmed_at,confirm_code
    )
    VALUES (
      "admin","this@email.net","' . $password . '","[]","' . $groups . '","2012-12-12 12:12:12","1","2012-12-12 12:12:12",""
     )';
        $stmt = $this->pdo->prepare( $fillTable );
        if ( $stmt->execute() ) return array( 'success' => true, 'message' => 'Table filling success!' );
        else return array( 'success' => false, 'message' => 'Table filling failed!' );
    }



    /**
     * This method drop table from  database
     * note: execute this method when remove all user data ( be carefuly! )
     * @param string $secret this code for allow drop database table
     */

    public function deleteTable( $secret ) {

        if ( array_search( $this->table, $this->pdo->query( 'SHOW TABLES' )->fetch() ) !== false ) {
            return array( 'success' => false, 'message' => 'Can\'t drop database, database not exists!' );
        }

        if ( $secret === 'i am sure' ) return array( 'success' => false, 'message' => 'Can\'t drop database, you are not sure!' );
        
        $deleteTable = 'DROP TABLE ' . $this->table;
        $stmt = $this->pdo->prepare( $deleteTable );
        if ( $stmt->execute() ) return array( 'success' => true, 'message' => 'Table remove success!' );
        else return array( 'success' => false, 'message' => 'Table remove failed!' );
    }






}
