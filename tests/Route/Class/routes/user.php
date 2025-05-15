<?php

declare(strict_types=1);

use RobertWesner\SimpleMvcPhp\Route;
use RobertWesner\SimpleMvcPhp\Tests\Route\Class\Controller\UserController;

Route::get('/api/users', [UserController::class, 'all']);
Route::get('/api/users/(?<userId>\d+)', [UserController::class, 'get']);
Route::post('/api/users', [UserController::class, 'create']);
Route::delete('/api/users/(?<userId>\d+)', [UserController::class, 'delete']);
