<?php

use App\Http\Controllers\ShoppingListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ShoppingListController::class, 'showAll']);

Route::view('/item', 'add-item');
Route::post('/v1/item', [ShoppingListController::class, 'add']);
Route::post('/v1/item/{id}/bought', [ShoppingListController::class, 'bought']);
Route::patch('/v1/list', [ShoppingListController::class, 'sort']);
Route::delete('/v1/item/{id}', [ShoppingListController::class, 'delete']);
