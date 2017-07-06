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


Route::get('/vend', function() {
    return view('about');
});

Route::post('/api/sync', 'SyncController@sync');

Route::get('/api/products', function () {
	$products = DB::table('inventory')->get();

    return $products;
});

Route::get('/api/products/{productid}', function ($id) {
	$product = DB::table('inventory')->where('id', "=", $id)->get();
	return $product;
});
    // Continue.. 

