<?php

use Illuminate\Support\Facades\Route;

Route::get('/token', 'ApiController@token');
Route::post('/login', 'ApiController@login')->middleware('xtoken');
Route::post('/logout', 'ApiController@logout')->middleware('xtoken');
Route::get('/user', 'ApiController@user')->middleware('xtoken');

Route::get('/dishes', 'ApiController@dishes')->middleware('xtoken');
Route::post('/to-cart', 'ApiController@toCart')->middleware('xtoken');
Route::get('/dishes-in-cart', 'ApiController@dishesInCart')->middleware('xtoken');
Route::get('/cart', 'ApiController@cart')->middleware('xtoken');
Route::post('/cart/remove', 'ApiController@removeFromCart')->middleware('xtoken');
Route::post('/cart/change-amount', 'ApiController@changeAmountInCart')->middleware('xtoken');
Route::post('/order', 'ApiController@order')->middleware('xtoken');

Route::get('/history', 'ApiController@history')->middleware('xtoken');
