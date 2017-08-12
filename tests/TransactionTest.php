<?php

use MatijaBelec\Database\Database;

class TransactionTest extends PHPUnit_Framework_TestCase {
  public function testTransactionRollback()
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
    $database->transaction();
    foreach($usernames as $username)
      $database->query('INSERT INTO users(username) VALUES(:username)', array('username' => $username));
    $database->rollback();

    $users = $database->select('SELECT * FROM users');
    $this->assertTrue($users !== false, 'Error arised on users fetching');

    $this->assertTrue(count($users) === 0,
                      'There should be no users in relation but result returned ' . count($users) . ' users');
  }

  public function testTransactionCommit()
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
    $database->transaction();
    foreach($usernames as $username)
      $database->query('INSERT INTO users(username) VALUES(:username)', array('username' => $username));
    $database->commit();

    $users = $database->select('SELECT * FROM users');
    $this->assertTrue($users !== false, 'Error arised on users fetching');

    $this->assertTrue(count($users) === count($usernames),
                      'There should be exactly ' . count($usernames) . ' users but returned ' . count($users));
  }
}
