<?php

use Illuminate\Support\Facades\Route;

Route::prefix('demo')->group(function () {
    Route::get('/', function () {
        return view('demo.dashboard');
    });

    Route::get('/basic-table', function () {
        return view('demo.basic-table');
    });

    Route::get('/email', function () {
        return view('demo.email');
    });

    Route::get('/compose', function () {
        return view('demo.compose');
    });

    Route::get('/calendar', function () {
        return view('demo.calendar');
    });

    Route::get('/chat', function () {
        return view('demo.chat');
    });

    Route::get('/charts', function () {
        return view('demo.charts');
    });

    Route::get('/forms', function () {
        return view('demo.forms');
    });

    Route::get('/ui', function () {
        return view('demo.ui');
    });

    Route::get('/datatable', function () {
        return view('demo.datatable');
    });

    Route::get('/google-maps', function () {
        return view('demo.google-maps');
    });

    Route::get('/vector-maps', function () {
        return view('demo.vector-maps');
    });

    Route::get('/blank', function () {
        return view('demo.blank');
    });

    Route::get('/404', function () {
        return view('demo.404');
    });

    Route::get('/500', function () {
        return view('demo.500');
    });

    Route::get('/signin', function () {
        return view('demo.signin');
    });

    Route::get('/signup', function () {
        return view('demo.signup');
    });
});
