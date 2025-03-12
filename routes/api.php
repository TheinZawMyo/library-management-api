<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserManagementController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\CategoryController;

// auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // member
    // ==== browse books by category || author || title ====//
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    
    // ==== reserve book ====// book_id, member_id, reserve_date
    Route::post('/book/reserve', [BookController::class, 'reserveBook']);
    // ==== cancel reservation ====// book_id, member_id
    Route::post('/book/cancel-reserve', [BookController::class, 'cancelReservation']);

    // ==== get histories by status for auth user ====//
    Route::get('/book/user/histories', [BookController::class, 'userBorrowHistories']);

    // librarian && admin
    Route::middleware(['role:librarian,admin'])->group(function() {
        // ==== add book ====// title, author, category, quantity
        Route::post('/books', [BookController::class, 'store']);
        // ==== update book ====// book_id, title, author, category, quantity
        Route::put('/books/{id}', [BookController::class, 'update']);
        // ==== delete book ====// book_id
        Route::delete('/books/{id}', [BookController::class, 'destroy']);
        // ==== borrow book || return book || overdue ====//
        // status => 1: borrowed, 2: returned, 5: overdue with borrow_date
        Route::post('/book/borrow-status/{status}', [BookController::class, 'changeBorrowStatus']);
        // ==== update book status ====// book_id, available or not // manually
        Route::put('/book/{id}/update-status', [BookController::class, 'updateBookStatus']);
        // ==== get borrowed histories for all books by status =====// filter with borrow date || member_id || book_id
        Route::get('/book/histories', [BookController::class, 'borrowHistories']);

        // ======= Get all members ======//
        Route::get('/members', [UserManagementController::class, 'index']);
        // ==== get user by id ====//
        Route::get('/members/{id}', [UserManagementController::class, 'show']);


        // ==== get all categories ====//
        Route::get('/categories', [CategoryController::class, 'index']);
        // ==== add category ====//
        Route::post('/category', [CategoryController::class, 'store']);
        // ==== get category by id ====//
        Route::get('/category/{id}', [CategoryController::class, 'show']);
        // ==== update category ====//
        Route::put('/category/{id}', [CategoryController::class, 'update']);
        // ==== delete category ====//
        Route::delete('/category/{id}', [CategoryController::class, 'delete']);
    });

    // admin
    Route::middleware(['role:admin'])->group(function() {
        // ==== add user ====//
        Route::post('/user', [UserManagementController::class, 'store']);
        // ==== update user ====//
        Route::put('/user/{id}', [UserManagementController::class, 'update']);
        // ==== delete user ====//
        Route::delete('/user/{id}', [UserManagementController::class, 'destroy']);
    });
});








