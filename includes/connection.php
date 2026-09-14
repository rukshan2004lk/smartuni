<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database
{
    public static $connection;

    public static function setUpConnection()
    {
        if (!isset(Database::$connection)) {
            Database::$connection = new mysqli(
                "localhost",
                "root",
                "rukshan123",
                "smartuni_db",
                3306
            );

            if (Database::$connection->connect_error) {
                die("Database Connection Failed: " . Database::$connection->connect_error);
            }
        }
    }

    public static function escape($str)
    {
        Database::setUpConnection();
        return Database::$connection->real_escape_string($str ?? '');
    }

    public static function iud($q)
    {
        Database::setUpConnection();
        $result = Database::$connection->query($q);
        
        // If query fails, print the exact MySQL error so you can see why it didn't save
        if (!$result) {
            die("SQL Error: " . Database::$connection->error . " | Query: " . $q);
        }
        
        return $result;
    }

    public static function search($q)
    {
        Database::setUpConnection();
        $result = Database::$connection->query($q);

        if (!$result) {
            die("SQL Error: " . Database::$connection->error . " | Query: " . $q);
        }

        return $result;
    }
}
?>