
<?php


Auth::routes(['logout']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
