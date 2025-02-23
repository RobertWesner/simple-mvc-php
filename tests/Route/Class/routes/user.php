<?php

declare(strict_types=1);

use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Tests\Route\Class\Controller\UserController;

$controller = new UserController();
Route::get('/api/users', $controller->all(...));
Route::get('/api/users/(?<userId>\d+)', $controller->get(...));
Route::post('/api/users', $controller->create(...));
Route::delete('/api/users/(?<userId>\d+)', $controller->delete(...));
