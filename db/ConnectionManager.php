<?php

class ConnectionManager {
    public function connect_db() {
    $db = new PDO("mysql:host=localhost;dbname=customersRegDB", "jmrosa", "jmrosa");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // throw exceptions
    return $db;
}
}
