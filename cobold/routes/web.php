<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/team', [HomeController::class, 'team'])->name('team');
Route::post('/team-store', [HomeController::class, 'teamStore'])->name('team.store');

Route::get('/expenses', [HomeController::class, 'expenses'])->name('expenses');
Route::post('/expenses-store', [HomeController::class, 'expenseStore'])->name('expenses.store');
Route::get('/expenses-edit/{id}', [HomeController::class, 'expensesEdit'])->name('expenses.edit');
Route::put('/expenses-update', [HomeController::class, 'expensesUpdate'])->name('expenses.update');