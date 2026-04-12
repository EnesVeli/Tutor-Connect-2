<?php

namespace App\Framework;

use PDO;
use PDOException;

require_once __DIR__ . '/../../config/config.php';

class Repository
{
    protected PDO $db;

    public function __construct()
    {
        $dsn = "mysql:host=" . \DB_HOST . ";port=" . \DB_PORT . ";dbname=" . \DB_NAME . ";charset=utf8mb4";
        try {
            $this->db = new PDO($dsn, \DB_USER, \DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \RuntimeException("Database Connection Failed: " . $e->getMessage());
        }
    }
}
