<?php

// Automatically load necessary classes via Composer
require 'vendor/autoload.php';

// Import necessary classes
use App\Router;
use App\Controllers\UserController;
use App\Controllers\{AuthController, BookController, LoanController, AuthorController};

// List of controllers to register in the router
$controllers = [
    UserController::class,
    AuthController::class,
    BookController::class,
    LoanController::class,
    AuthorController::class
];

// Create an instance of the router
$router = new Router();

// Register the controllers in the router
$router->registerControllers($controllers);

// Run the router to handle incoming requests
$router->run();