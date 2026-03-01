<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    protected static PDO $pdo;

    public static function connect()
    {
        if (!isset(self::$pdo)) {
            $config = require __DIR__ . '/../../config/database.php';

            try {
                self::$pdo = new PDO(
                    "mysql:host={$config['host']};dbname={$config['name']}",
                    $config['user'],
                    $config['pass'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }

    public static function query($sql)
    {
        try {
            return self::connect()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException("Query failed: " . $e->getMessage());
        }
    }

    public static function prepare($sql, $params = [])
    {
        try {
            $stmt = self::connect()->prepare($sql);

            foreach ($params as $key => $value) {
                $paramType = self::getParamType($value);
                $stmt->bindValue(is_int($key) ? $key + 1 : $key, $value, $paramType);
            }

            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException("Prepared statement failed: " . $e->getMessage());
        }
    }

    public static function execute($sql, $params = [])
    {
        try {
            $stmt = self::connect()->prepare($sql);

            foreach ($params as $key => $value) {
                $paramType = self::getParamType($value);
                $stmt->bindValue(is_int($key) ? $key + 1 : $key, $value, $paramType);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Execute failed: " . $e->getMessage());
        }
    }

    private static function getParamType($value)
    {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            return PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            return PDO::PARAM_NULL;
        } else {
            return PDO::PARAM_STR;
        }
    }
}
