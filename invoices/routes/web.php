<?php

use App\Http\Controllers\InvoiceController;
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

Route::get('/', function () {
    return view('home');
});

Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');

// Showing the form for creating a new invoice
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');

// Storing a newly created invoice
Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');

// // Showing a specific invoice
// Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

// Showing the form for editing a specific invoice
Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');

// Updating a specific invoice
Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');

// Deleting a specific invoice
Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');