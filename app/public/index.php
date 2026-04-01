<?php
require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

use App\Controllers\AuthController;
use FastRoute\RouteCollector;
use App\Controllers\HomeController;
use App\Controllers\AdminController;
use App\Controllers\TutorController;
use App\Controllers\StudentController;
use App\Controllers\BookingController;
use App\Controllers\StudentProfileController;



$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute(['GET', 'POST'], '/login', [AuthController::class, 'login']);
    $r->addRoute(['GET', 'POST'], '/register', [AuthController::class, 'register']);
    $r->addRoute('GET', '/logout', [AuthController::class, 'logout']);
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/admin/users', [AdminController::class, 'users']);
    $r->addRoute('POST', '/admin/users/delete', [AdminController::class, 'deleteUser']);
    $r->addRoute('GET', '/tutors', [StudentController::class, 'index']);
    $r->addRoute('GET', '/book', [BookingController::class, 'create']);
    $r->addRoute('GET', '/bookings', [BookingController::class, 'index']);
    $r->addRoute('POST', '/booking/update', [BookingController::class, 'update']);
    $r->addRoute('POST', '/book/payment', [BookingController::class, 'process']);
    $r->addRoute('POST', '/book/confirm', [BookingController::class, 'store']);  
    $r->addRoute(['GET', 'POST'], '/student/profile', [StudentProfileController::class, 'edit']);
    $r->addRoute(['GET', 'POST'], '/admin/users/edit', [AdminController::class, 'editUser']); 
    $r->addRoute('GET', '/admin/statistics', [AdminController::class, 'statistics']); 
    $r->addRoute('GET', '/profile', [TutorController::class, 'index']); 
    $r->addRoute(['GET', 'POST'], '/profile/edit', [TutorController::class, 'edit']);
    $r->addRoute('POST', '/admin/profiles/delete', [AdminController::class, 'deleteProfile']);
    $r->addRoute('POST', '/book/store', [BookingController::class, 'store']); 
        
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
        echo '404 - Page Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 - Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $class = $handler[0];
        $method = $handler[1];
        $controller = new $class();
        $controller->$method($vars);
        break;
}