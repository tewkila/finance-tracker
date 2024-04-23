<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;



// Home Page
Route::get('/', function () {
    return view('index');
})->name('home');

// Registration Routes
Route::get('/registration', [RegistrationController::class, 'showRegistrationForm'])->name('registration');
Route::post('/registration', [RegistrationController::class, 'handleRegistration'])->name('registration.submit');

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Dashboard - Temporarily remove auth middleware
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Income Routes
Route::get('/income', [IncomeController::class, 'index'])->name('income');

// Expense Routes
Route::get('/expense', [ExpenseController::class, 'index'])->name('expense');

// Budget Routes
Route::get('/budget', [BudgetController::class, 'index'])->name('budget');

// Profile Routes - Edit, Update, Delete, remain under auth middleware
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Test Route for Blade Views
Route::get('/test-blade', function () {
    return view('test');
});

// Additional Auth Routes (if necessary)
require __DIR__.'/auth.php';
