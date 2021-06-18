<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//auth routes
Route::post('register', [AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);

//group routes
Route::group(['middleware' => ['jwt.auth']], function() {
    //logout routes
    Route::get('logout', [AuthController::class,'logout']);
    //Loan routes
    Route::post('loan_request', [LoanController::class,'loanRequest']);
    Route::post('loan_approve', [LoanController::class,'approveLoan']);
    Route::get('loan_status', [LoanController::class,'getLoanStatus']);
    Route::post('loan_repayment', [LoanController::class,'repaymentLoan']);
});
