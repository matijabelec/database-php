<?php

use MatijaBelec\Database\Database;

class ErrorTest extends PHPUnit_Framework_TestCase {
  public function testErrorMethod()
  {
    $config = parse_ini_file('config.ini');

    $database = new Database();
    $this->assertTrue($database->connect($config) !== false, 'Database connection failed');

    $database->query('DROP TABLE IF EXISTS users');
    $database->query('CREATE TABLE users(
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(50) NOT NULL,
      status TINYINT(1) NOT NULL DEFAULT 1
    )');

    if($database->query('INSERT INTO users(username2) VALUES(:username)', array('username' => 'mbelec')) === false){
      $error = $database->error();
    }

    $this->assertTrue(isset($error) && is_array($error), 'Error should have been arrised');
  }
}
