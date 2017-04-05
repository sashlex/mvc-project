<?php 
// login - unique
// email - unique
// confirm_code - unique
return array (
    'up' => 'CREATE TABLE users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        login VARCHAR(50) NOT NULL,
        email VARCHAR(50) NOT NULL,
        password VARCHAR(256) NOT NULL,
        actons TEXT NOT NULL,
        confirmed TINYINT(1) DEFAULT 0,
        confirm_code VARCHAR(256) NOT NULL
    )',
    'fill' => 'INSERT INTO users (
        login,email,password,actions,confirmed,confirm_code
    )
    VALUES (
        "admin",test@localhost",".0XXpfG68yt/nKrn8E79eu1X0zEFHCW3JYeA6RMTWtqSDBetb2Ql6","","1","abc"
    )',
    'down' => 'DROP TABLE users'
);
