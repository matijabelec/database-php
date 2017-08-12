<?php

/**
 * Database wrapper
 *
 * Database wrapper class that uses PDO for DB connection.
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

/**
 * Database class that uses PDO as connector to database.
 * It includes standard queries and transactions.
 */
class Database {
  /**
   * Connection object.
   * @var PDO
   */
  protected static $connection;

  /**
   * Connect to database.
   * @param  boolean $config optional, if supplied should be in form of array:
   *                         ['host' => '..',
   *                          'database' => '..',
   *                          'charset' => 'utf8',
   *                          'username' => '..',
   *                          'password' => '..',
   *                         ]
   * @return PDO|false       on success return PDO object or false on error/connection
   *                         is not successfully established
   */
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

  /**
   * Runs query against database.
   * @param  string $query       query string with placeholders for PDO prepare statement
   * @param  array  $arguments   arguments array for prepared statement ($query argument)
   * @return PDOStatement|false  returns PDOStatement on success or false on error
   */
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

  /**
   * Runs query against database with return set.
   * @param  string $query      query string with placeholders for PDO prepare statement
   * @param  array  $arguments  arguments array for prepared statement ($query argument)
   * @return array              returns array of rows on success or false on error
   */
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

  /**
   * Start transaction (BEGIN).
   * @return boolean  returns boolean that represents success of opening transaction
   */
  public function transaction()
  {
    $connection = $this->connect();
    if($connection === false)
      return false;
    return $connection->beginTransaction();
  }

  /**
   * Close successfull transaction.
   * @return boolean  returns boolean that represents success of closing transaction
   */
  public function commit()
  {
    $connection = $this->connect();
    if($connection === false)
      return false;
    return $connection->commit();
  }

  /**
   * Rollback transaction.
   * @return boolean  returns boolean that represents success of rollback transaction
   */
  public function rollback()
  {
    $connection = $this->connect();
    if($connection === false)
      return false;
    return $connection->rollBack();
  }

  /**
   * Returns last error.
   * @return array|false  returns array with error details or false if no error
   */
  public function error()
  {
    $connection = $this->connect();
    if($connection === false)
      return array(-1, -1, 'Connection error');
    return $connection->errorInfo();
  }
}
