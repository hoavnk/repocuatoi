<?php

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname(dirname(dirname( __FILE__ ))) . '/' );
}

require(ABSPATH . "wp-config.php");

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
// Check connection
if ($conn->connect_error) {
    die(json_encode([
        "error" => true,
        "msg" => "Connection failed",
        "data" => []
    ]));
}

$conn->autocommit(FALSE);

$tableSchema = [
    "setting_id" => "INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "setting_key" => "VARCHAR(255) NULL",
    "setting_value" => "LONGTEXT NOT NULL"
];
