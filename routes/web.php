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

// Route::middleware(['permission:product-create,product-read,product-update,product-delete'])->group(function () {
    // Product
    Route::post('/home/addProduct', 'HomeController@add_product')->middleware('permission:product-create')->name('add.product'); //product-create
    Route::get('/home/showProduct', 'HomeController@show_product')->middleware('permission:product-read')->name('show.product'); //product-read
    Route::post('/home/editProduct', 'HomeController@edit_product')->middleware('permission:product-update')->name('edit.product'); //product-update
    Route::delete('/home/deleteProduct', 'HomeController@delete_product')->middleware('permission:product-delete')->name('delete.product'); //product-delete

    // Order
    Route::post('/home/addOrder', 'HomeController@add_order')->middleware('permission:order-create')->name('add.order'); //order-create
    Route::get('/home/showOrders', 'HomeController@show_order')->middleware('permission:order-read')->name('show.order'); //order-read
    Route::get('/home/orderData', 'HomeController@order_data')->name('order.data'); //order- show total count
// });



// Route::middleware(['permission:user-create,user-read,user-update,user-delete'])->group(function () {
    // User
    Route::get('/home/showUsers', 'HomeController@show_users')->middleware('permission:user-read')->name('show.users'); // user-read
    Route::post('/home/addUser', 'HomeController@add_user')->middleware('permission:user-create')->name('add.user'); //user-create
    Route::post('/home/editUser', 'HomeController@edit_user')->middleware('permission:user-update')->name('edit.user'); //user-update
    Route::get('/home/getUser', 'HomeController@get_user')->middleware('permission:user-update')->name('get.user'); //user- find user via id controller

    // Permission
    Route::get('/home/showPermission', 'HomeController@show_permissions')->middleware('permission:permission-read')->name('show.permissions'); //permission-read
    Route::post('/home/addPermission', 'HomeController@add_permission')->middleware('permission:permission-create')->name('add.permission'); //permision-create
    Route::post('/home/editPermission', 'HomeController@edit_permission')->middleware('permission:permission-update')->name('edit.permission'); //permission-update
    Route::get('/home/getPermission', 'HomeController@get_permission')->middleware('permission:user-update')->name('get.permission'); //permission- find permission via id controller
    
// });