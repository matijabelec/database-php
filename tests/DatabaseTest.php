<?php

use MatijaBelec\Database\Database;

class DatabaseTest extends PHPUnit_Framework_TestCase {
  public function testConnection()
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

    $usernames = array('mbelec', 'ibelec', 'tester');
    foreach($usernames as $username)
      $database->query('INSERT INTO users(username) VALUES(:username)', array('username' => $username));

    $users = $database->select('SELECT * FROM users');
    $this->assertTrue($users !== false, 'Error arised on users fetching');

    $this->assertTrue(count($users) === count($usernames),
                      'There should be exactly ' . count($usernames) . ' users but returned ' . count($users));
  }
}
