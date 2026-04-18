<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->file(public_path('frontend/index.html'));
});

Route::get('/{any}', function () {
    return response()->file(public_path('frontend/index.html'));
})->where('any', '.*');

// Xử lý CORS Preflight OPTIONS requests
Route::options('/{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', env('FRONTEND_URL', 'http://127.0.0.1:5173'))
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With');
})->where('any', '.*');
