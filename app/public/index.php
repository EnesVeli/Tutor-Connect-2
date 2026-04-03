<?php

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS headers for Vue dev server
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Global Error Handlers to enforce JSON responses
set_exception_handler(function (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    exit;
});

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

use App\Controllers\AuthController;
use App\Controllers\TutorController;
use App\Controllers\BookingController;
use App\Controllers\AdminController;
use App\Controllers\StudentProfileController;
use App\Controllers\ReviewController;
use FastRoute\RouteCollector;

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('POST', '/api/auth/register', [AuthController::class, 'register']);
    $r->addRoute('POST', '/api/auth/login', [AuthController::class, 'login']);
    $r->addRoute('GET', '/api/auth/me', [AuthController::class, 'me']);
    $r->addRoute('GET', '/api/tutors', [TutorController::class, 'list']);
    $r->addRoute('GET', '/api/tutors/{id:\d+}', [TutorController::class, 'show']);
    $r->addRoute('GET', '/api/tutors/{id:\d+}/reviews', [TutorController::class, 'getReviews']);
    $r->addRoute('POST', '/api/tutors/{id:\d+}/reviews', [TutorController::class, 'createReview']);
    $r->addRoute('GET', '/api/tutor/profiles', [TutorController::class, 'myProfiles']);
    $r->addRoute('POST', '/api/tutor/profiles', [TutorController::class, 'create']);
    $r->addRoute('PUT', '/api/tutor/profiles/{id:\d+}', [TutorController::class, 'update']);
    $r->addRoute('DELETE', '/api/tutor/profiles/{id:\d+}', [TutorController::class, 'delete']);
    $r->addRoute('GET', '/api/bookings', [BookingController::class, 'index']);
    $r->addRoute('POST', '/api/bookings', [BookingController::class, 'create']);
    $r->addRoute('PUT', '/api/bookings/{id:\d+}', [BookingController::class, 'update']);
    $r->addRoute('GET', '/api/student/profile', [StudentProfileController::class, 'show']);
    $r->addRoute('PUT', '/api/student/profile', [StudentProfileController::class, 'update']);
    $r->addRoute('PUT', '/api/reviews/{id:\d+}', [ReviewController::class, 'update']);
    $r->addRoute('DELETE', '/api/reviews/{id:\d+}', [ReviewController::class, 'delete']);
    $r->addRoute('GET', '/api/admin/users', [AdminController::class, 'listUsers']);
    $r->addRoute('GET', '/api/admin/users/{id:\d+}', [AdminController::class, 'getUser']);
    $r->addRoute('PUT', '/api/admin/users/{id:\d+}', [AdminController::class, 'updateUser']);
    $r->addRoute('DELETE', '/api/admin/users/{id:\d+}', [AdminController::class, 'deleteUser']);
    $r->addRoute('DELETE', '/api/admin/tutor-profiles/{id:\d+}', [AdminController::class, 'deleteTutorProfile']);
    $r->addRoute('GET', '/api/admin/stats', [AdminController::class, 'stats']);
    $r->addRoute('GET', '/api/admin/reviews', [AdminController::class, 'reviews']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $class = $handler[0];
        $method = $handler[1];
        try {
            $controller = new $class();
            $controller->$method($vars);
        } catch (\RuntimeException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server Error', 'message' => $e->getMessage()]);
        }
        break;
}