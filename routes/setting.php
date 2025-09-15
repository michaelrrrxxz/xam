<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingController;


Route::prefix('v1')->group(function () {
Route::get('/settings', [SettingController::class, 'index']);
Route::post('/admin/settings', [SettingController::class, 'store']);
});
