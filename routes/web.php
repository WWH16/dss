<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentEvaluationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;


/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('about');
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Student
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');

    // Evaluation Form
    Route::get('/student/evaluation', [StudentEvaluationController::class, 'index'])
        ->name('student.evaluation');

    // Submit Evaluation
    Route::post('/student/evaluation', [StudentEvaluationController::class, 'store'])
        ->name('student.evaluation.store');
    
    Route::get('/evaluation', [StudentEvaluationController::class, 'index'])
    ->name('evaluation');

});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/admin/dashboard',
        [AdminController::class,'dashboard'])
        ->name('admin.dashboard');

    Route::post('/admin/stall/add',
        [AdminController::class,'addStall'])
        ->name('admin.stall.add');

    Route::delete('/admin/stall/{id}',
        [AdminController::class,'deleteStall'])
        ->name('admin.stall.delete');

});
/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])
        ->name('staff.dashboard');

});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {

    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/login');

})->name('logout');