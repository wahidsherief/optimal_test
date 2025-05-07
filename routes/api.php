<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookshelfController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\PageController;


Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('bookshelves', BookshelfController::class);
    Route::apiResource('bookshelves.books', BookController::class);
    Route::apiResource('books.chapters', ChapterController::class);
    Route::apiResource('chapters.pages', PageController::class);

    Route::get('/books/search', [BookController::class, 'search']);
    Route::get('/chapters/{chapter}/full-content', [ChapterController::class, 'fullContent']);
});
