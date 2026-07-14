<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;

Route::post('/events', [EventController::class, 'store']);
Route::post('/heartbeat', [\App\Http\Controllers\Api\HeartbeatController::class, 'ping']);
Route::get('/status', [\App\Http\Controllers\Api\StatusController::class, 'live']);
Route::post('/command/stop-buzzer', [\App\Http\Controllers\Api\CommandController::class, 'stopBuzzer']);
