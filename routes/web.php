<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use App\Http\Controllers\LegacyStatusController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return 'Hello from Hubzero 3.0 on Laravel!';
});

Route::get('/', function () {
    return 'Hubzero 3.0 — Laravel is running.';
});

Route::get('/status', StatusController::class);
Route::get('/status/legacy', LegacyStatusController::class);
