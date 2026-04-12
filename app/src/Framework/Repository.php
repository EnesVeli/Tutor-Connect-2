<?php

namespace App\Framework;

use App\Config;
use PDO;
use PDOException;

class Repository
{
    protected PDO $db;

    public function __construct()
    {
        $dsn = "mysql:host=" . Config::dbHost() . ";dbname=" . Config::dbName() . ";charset=utf8mb4";
        try {
            $this->db = new PDO($dsn, Config::dbUser(), Config::dbPass());
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \RuntimeException("Database Connection Failed: " . $e->getMessage());
        }
    }
}
