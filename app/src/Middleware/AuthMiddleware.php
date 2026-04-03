<?php

namespace App\Middleware;

use App\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    /**
     * Validate the JWT bearer token from the Authorization header.
     * Optionally check if the user has the required role.
     *
     * Returns the decoded user payload on success.
     * Sends 401/403 JSON and exits on failure.
     */
    public static function validate(?string $requiredRole = null): object
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!str_starts_with($header, 'Bearer ')) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $token = substr($header, 7);

        try {
            $decoded = JWT::decode($token, new Key(Config::JWT_SECRET, 'HS256'));
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized', 'message' => 'Invalid or expired token']);
            exit;
        }

        $userData = $decoded->data;

        if ($requiredRole && $userData->role !== $requiredRole) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden', 'message' => 'Insufficient permissions']);
            exit;
        }

        return $userData;
    }
}
