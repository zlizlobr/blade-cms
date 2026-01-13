<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', function () {
        return view('blog::index');
    })->name('index');

    Route::get('/posts/{slug}', function (string $slug) {
        return view('blog::post', ['slug' => $slug]);
    })->name('post');
});
