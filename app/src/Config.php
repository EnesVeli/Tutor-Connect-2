<?php

namespace App;

class Config
{
    public static function dbHost(): string
    {
        return getenv('DB_HOST') ?: 'mysql';
    }

    public static function dbName(): string
    {
        return getenv('DB_NAME') ?: 'developmentdb';
    }

    public static function dbUser(): string
    {
        return getenv('DB_USER') ?: 'developer';
    }

    public static function dbPass(): string
    {
        return getenv('DB_PASS') ?: 'secret123';
    }

    public static function jwtSecret(): string
    {
        $secret = getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET'] ?? null);
        if (!$secret) {
            throw new \RuntimeException('JWT_SECRET not set');
        }
        return $secret;
    }
}