<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/home/addProduct', 'HomeController@add_product')->name('add.product');

Route::get('/home/showProduct', 'HomeController@show_product')->name('show.product');

Route::post('/home/editProduct', 'HomeController@edit_product')->name('edit.product');

Route::delete('/home/deleteProduct', 'HomeController@delete_product')->name('delete.product');

Route::post('/home/addOrder', 'HomeController@add_order')->name('add.order');

Route::get('/home/showOrders', 'HomeController@show_order')->name('show.order');

Route::get('/home/orderData', 'HomeController@order_data')->name('order.data');

