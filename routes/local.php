<?php

use Illuminate\Support\Facades\Route;
use App\Support\PostFixtures;

Route::middleware('api')->group(function () {
    Route::get('post-content', function () {
        return PostFixtures::getFixtures()->random();
    })->name('local.post-content');
});

Route::middleware('web')->group(function () {});
