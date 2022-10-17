<?php


Auth::routes(['logout']);
Route::get('/home', function () {
    return view('home');
})->name('home');
