<?php

/**
 * Database wrapper
 *
 * Database wrapper class that uses PDO for DB connection
 *
 * PHP5.*, PHP7
 *
 * LICENSE: MIT (details available at this package github page
 *          https://github.com/matijabelec/database-php)
 *
 * @package    database-php
 * @author     Matija Belec <matijabelec1@gmail.com>
 * @copyright  2017 Matija Belec
 * @license    MIT
 * @link       https://github.com/matijabelec/database-php
 */

namespace MatijaBelec\Database;
use PDO;

class Database {
  protected static $connection;

  public function connect($config=false)
  {
    if($config === false){
      if(!isset(self::$connection))
        self::$connection = false;
      return self::$connection;
    }
    if(!isset(self::$connection)) {
      $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
      $opt = [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_SILENT,//ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES   => false,
      ];
      self::$connection = new PDO($dsn, $config['username'], $config['password'], $opt);
    }
    if(self::$connection === false) {
      return false;
    }
    return self::$connection;
  }

  public function query($query, $arguments=[])
  {
    $connection = $this->connect();
    if($connection === false)
      return false;
    $stmt = $connection->prepare($query);
    if($stmt === false)
      return false;
    $result = $stmt->execute($arguments);
    if($result === false)
      return false;
    return $stmt;
  }

  public function select($query, $arguments=[])
  {
    $rows = array();
    $result = $this->query($query, $arguments);
    if($result === false)
      return false;
    while($row = $result->fetch())
      $rows[] = $row;
    return $rows;
  }

  public function transaction()
  {
    $connection = $this->connect();
    if($connection === false)
      return false;
    return $connection->beginTransaction();
  }

  public function commit()
  {
    $connection = $this->connect();
    if($connection === false)
      return false;
    return $connection->commit();
  }

  public function rollback()
  {
    $connection = $this->connect();
    if($connection === false)
      return false;
    return $connection->rollBack();
  }

  public function error()
  {
    $connection = $this->connect();
    if($connection === false)
      return false;
    return $connection->errorInfo();
  }
}
