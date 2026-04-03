<?php

namespace App\Framework;

abstract class Controller
{
    /**
     * Send a JSON response and terminate.
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    /**
     * Read and decode the JSON request body.
     */
    protected function getBody(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
}